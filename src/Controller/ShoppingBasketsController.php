<?php

namespace Webshop\ShoppingBasket\Controller;

use Cake\Network\Response;
use Croogo\Core\Controller\CroogoAppController;
use Webshop\Orders\Model\Table\OrdersTable;
use Webshop\ShoppingBasket\Model\Entity\ShoppingBasket;

/**
 * @property OrdersTable Orders
 */
class ShoppingBasketsController extends CroogoAppController {

    public function initialize()
    {
        parent::initialize();

        $this->loadComponent('Paginator');
    }


    public $components = array(
		'Paginator'
	);

	public function index() {
		debug($this->ShoppingBaskets->find('all', array(

		)));
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

	public function view($id = null) {
		if (!$id) {
			$this->redirect([
				'action' => 'view',
				$this->ShoppingBasket->currentBasketId()
            ]);

			return;
		}

		$shoppingBasket = $this->ShoppingBaskets->get($id, [
            'contain' => [
                'Items' => array(
                    'Products' => array(
//                        'Tax'
                    ),
                    'ConfigurationValues' => array(
                        'ConfigurationOptions'
                    )
                )
            ]
        ]);

		$this->set(compact('shoppingBasket'));
	}

	public function edit($id) {

	}

	public function checkout($id) {
        /** @var ShoppingBasket $shoppingBasket */
        $shoppingBasket = $this->ShoppingBaskets->get($id, [
            'contain' => [
                'Items' => [
                    'ConfigurationValues' => [
                        'ConfigurationOptions'
                    ]
                ]
            ]
        ]);
        if ($shoppingBasket->isEmpty()) {
            $this->Flash->set(__d('webshop/shopping_basket', 'Please add something to your shopping basket'));

            return $this->redirect([
                'action' => 'view',
                $id
            ]);
        }

        if ($this->CustomerAccess->getCustomerId() instanceof Response) {
            return $this->CustomerAccess->getCustomerId();
        }

        $this->loadModel('Webshop/Orders.Orders');

        $order = $this->Orders->prepare($this->CustomerAccess->getCustomer());

        $order->duplicateItemContainer($shoppingBasket, $this->Orders->Items->target());

        debug($this->CustomerAccess->getCustomer());
        exit();

		$order = $this->Orders->createFromCart($this->CustomerAccess->getCustomerId());
        debug($order);exit();
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

	public function panel_index() {
		$shoppingBaskets = $this->Paginator->paginate('ShoppingBasket', array(
			'ShoppingBasket.customer_id' => $this->CustomerAccess->getCustomerId()
		));

		$this->set(compact('shoppingBaskets'));
	}

	public function panel_save($id) {
		$shoppingBasket = $this->ShoppingBaskets->find('first', array(
			'conditions' => array(
				'ShoppingBasket.id' => $id,
				'ShoppingBasket.customer_id IS NULL'
			)
		));

		if (!$shoppingBasket) {
			throw new NotFoundException();
		}

		if (empty($this->request->data)) {
			$this->request->data = $shoppingBasket;
		}

		$this->set(compact('shoppingBasket'));

		if (!$this->request->is('put')) {
			return;
		}

		$this->request->data('ShoppingBasket.customer_id', $this->CustomerAccess->getCustomerId());

		if (!$this->ShoppingBaskets->save($this->request->data)) {
			$this->Session->setFlash(__d('webshop_shopping_basket', 'Could not save shopping basket'), 'alert', array(
				'plugin' => 'BoostCake',
				'class' => 'alert-danger'
			));

			return;
		}

		$this->Session->setFlash(__d('webshop_shopping_basket', 'Saved shopping basket'), 'alert', array(
			'plugin' => 'BoostCake',
			'class' => 'alert-success'
		));

		$this->redirect(array(
			'action' => 'index'
		));
	}

}
