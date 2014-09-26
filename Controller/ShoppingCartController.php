<?php

App::uses('WebshopShoppingCartAppController', 'WebshopShoppingCart.Controller');

class ShoppingCartController extends WebshopShoppingCartAppController {

	public $uses = array('Webshop.Product');

	public function index() {
		$cart_items = $this->Product->getCartItems();

		$products = $this->Product->find('all', array(
			'conditions' => array(
				'id' => array_keys($cart_items)
			)
		));
		$this->set(compact('cart_items', 'products'));
	}

	public function add_product($id) {
		$this->Product->id = $id;
		if (!$this->Product->exists()) {
			throw new NotFoundException();
		}

		$this->Product->addToCart($id);

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

	public function checkout() {
		debug($this->request->data);
	}

}