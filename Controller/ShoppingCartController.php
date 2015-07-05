<?php

App::uses('WebshopShoppingCartAppController', 'WebshopShoppingCart.Controller');

class ShoppingCartController extends WebshopShoppingCartAppController {

	public $uses = array('Webshop.Product', 'Webshop.Customer', 'Webshop.AddressDetail', 'WebshopOrders.Order', 'WebshopShipping.ShippingMethod');

	public function index() {
		$cartItems = $this->Product->getCartItems();

//		foreach ($cart_items as $productId => $options) {
//			debug($this->Product->getPrice($productId, $options['configuration']));
//		}
//
//		debug($cart_items);

		$products = $this->Product->find('all', array(
			'conditions' => array(
				'id' => array_keys($cartItems)
			)
		));

		$this->set(compact('products', 'cartItems'));
		$this->set('cart_items', $cartItems);
	}

	public function edit_configuration($id) {
		$this->Product->id = $id;

		$cartItems = $this->Product->getCartItems();

		$this->set('cartItem', $cartItems[$id]);

		$this->set('product', $this->Product->read());

		if ($this->request->is('post')) {

			$this->Product->setCartConfiguration($id, $this->request->data['Configuration']);

			$this->redirect(array('action' => 'index'));
		}
	}

	public function add_product($id, $amount = 1) {
		$this->Product->id = $id;
		if (!$this->Product->exists()) {
			throw new NotFoundException();
		}

		if ($amount < 1) {
			throw new BadRequestException();
		}

		$this->Product->addToCart($id, $amount);

		$this->redirect(array('action' => 'index'));
	}

	public function delete_product($id) {
		$this->Product->id = $id;
		if (!$this->Product->exists()) {
			throw new NotFoundException();
		}

		$this->Product->removeFromCart($id);

		$this->redirect(array('action' => 'index'));
	}

	public function load() {
		$this->Product->clearCart();

		$products = array();
		foreach ($this->request->query as $parameter => $value) {
			if (strpos($parameter, 'product-') !== 0) {
				continue;
			}

			$products[(int) substr($parameter, 8)] = (int) $value;
		}

		$existingProducts = $this->Product->find('list', array(
			'conditions' => array(
				'Product.id' => array_keys($products)
			)
		));

		foreach ($existingProducts as $productId => $name) {
			$this->Product->addToCart($productId, $products[$productId]);
		}

		if (empty($missingProducts)) {
			$this->Session->setFlash(__d(
				'webshop_shopping_cart',
				'The shopping cart has been imported'
			));
		} else {
			$this->Session->setFlash(__dn(
				'webshop_shopping_cart',
				'The shopping cart has been imported, the products with the following ids\'s did not exist however: %1$s',
				'The shopping cart has been imported, the product with the following id did not exist however: %1$s',
				count($missingProducts),
				implode(', ', array_diff(array_keys($products), array_keys($existingProducts)))
			));
		}

		$this->redirect(array('action' => 'index'));
	}

	public function clear() {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}

		$this->Product->clearCart();

		$this->redirect(array('action' => 'index'));
	}

	public function is_empty() {
		$cart_items = $this->Product->getCartItems();

		$empty = (count($cart_items) === 0);

		if ($this->request->is('requested')) {
			return $empty;
		}

		$this->set('empty', $empty);
		$this->set('_serialize', array('empty'));
	}

	public function checkout() {
		if (count($this->Product->getCartItems()) === 0) {
			$this->Session->setFlash(__d(
				'webshop_shopping_cart',
				'Please put some products in your shopping cart'
			));

			$this->redirect(array('action' => 'index'));
			return;
		}

		$shippingMethods = $this->ShippingMethod->find('list', array(
			'conditions' => array(
				$this->ShippingMethod->alias . '.active' => true,
				$this->ShippingMethod->alias . '.available' => true
			)
		));
		$addressDetails = $this->AddressDetail->find('list', array(
			'fields' => array(
				$this->AddressDetail->alias . '.id',
				$this->AddressDetail->alias . '.name',
				$this->AddressDetail->Customer->alias . '.name',
			),
			'conditions' => array(
				$this->AddressDetail->alias . '.customer_id' => $this->CustomerAccess->getCustomerId()
			),
			'recursive' => 0
		));

		$hasPhysicalProducts = ($this->Product->find('count', array(
			'conditions' => array(
				'id' => array_keys($this->Product->getCartItems()),
				'digital' => 0
			)
		)) > 0);

		$this->set(compact('customers', 'shippingMethods', 'addressDetails', 'hasPhysicalProducts'));

		if (!$this->request->is('post')) {
			return;
		}

		$order = $this->Order->createFromCart($this->CustomerAccess->getCustomerId());
		$order['Order'] = Hash::merge($order['Order'], $this->request->data['Order']);
		$this->Order->save($order, array(
			'Order.invoice_address_detail_id',
			'Order.comment'
		));

		if ($hasPhysicalProducts) {
			$this->Order->createShipment(
				$order['Order']['id'],
				$this->request->data['Order']['OrderShipment']['Shipment']['shipping_method_id'],
				$this->request->data['Order']['OrderShipment']['Shipment']['address_detail_id']
			);
		}
		$this->Session->setFlash(__d(
			'webshop_shopping_cart',
			'An order has been created from your shopping cart with number #%1$d',
			$order['Order']['number']
		));

		$this->redirect(array(
			'panel' => true,
			'plugin' => 'webshop_orders',
			'controller' => 'orders',
			'action' => 'pay',
			$order['Order']['id']
		));
	}

}