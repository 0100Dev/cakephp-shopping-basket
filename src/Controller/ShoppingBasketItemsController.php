<?php

App::uses('WebshopShoppingCartAppController', 'WebshopShoppingCart.Controller');

/**
 * Class ShoppingBasketItemsController
 *
 * @property ShoppingBasketItem ShoppingBasketItem
 */
class ShoppingBasketItemsController extends WebshopShoppingCartAppController {

	/**
	 * @param null $productId
	 */
	public function add($productId = null) {
		if ($productId) {
			$this->request->data('ShoppingBasketItem.product_id', $productId);
		}

		$this->request->data('ShoppingBasketItem.shopping_basket_id', $this->ShoppingBasketItem->ShoppingBasket->currentBasketId());

		$stackable = $this->ShoppingBasketItem->Product->field('stackable', array(
			'Product.id' => $this->request->data('ShoppingBasketItem.product_id')
		));

		$product = $this->ShoppingBasketItem->Product->find('first', array(
			'conditions' => array(
				'Product.id' => $this->request->data('ShoppingBasketItem.product_id')
			),
			'contain' => array(
				'ConfigurationValue'
			)
		));

		$options = $this->ShoppingBasketItem->Product->find('options', array(
			'conditions' => array(
				'Product.id' => $this->request->data('ShoppingBasketItem.product_id')
			),
		));
		if (isset($options[0])) {
			$options = $options[0];
		}

		$this->set(compact('stackable', 'product', 'options'));

		if (!$this->request->is('post')) {
			return;
		}

		$productNonOverridableValues = $this->ShoppingBasketItem->Product->find('first', array(
			'fields' => array(),
			'conditions' => array(
				'Product.id' => $this->request->data('ShoppingBasketItem.product_id')
			),
			'contain' => array(
				'ConfigurationValue' => array(
					'conditions' => array(
						'ConfigurationValue.overridable' => false
					)
				)
			)
		));

		if (!$this->ShoppingBasketItem->Product->addToBasket($this->request->data('ShoppingBasketItem.product_id'), array(
			'basket' => $this->request->data('ShoppingBasketItem.shopping_basket_id'),
			'amount' => ($stackable) ? $this->request->data('ShoppingBasketItem.amount') : 1,
			'configuration' => $this->ShoppingBasketItem->parseConfiguration(
				$this->request->data['ConfigurationValue']
			),
			'non_overridable' => $this->ShoppingBasketItem->parseConfiguration(
				$productNonOverridableValues['ConfigurationValue']
			)
		))) {
			return;
		}

		$this->redirect(array(
			'controller' => 'shopping_baskets',
			'action' => 'view',
			$this->ShoppingBasketItem->ShoppingBasket->currentBasketId()
		));
	}


	public function edit($id) {
		$shoppingBasketItem = $this->ShoppingBasketItem->find('first', array(
			'conditions' => array(
				'ShoppingBasketItem.id' => $id
			),
			'contain' => array(
				'Product' => array(
					'ConfigurationValue'
				),
				'ConfigurationValue' => array(
					'ConfigurationOption'
				)
			)
		));

		$options = $this->ShoppingBasketItem->Product->find('options', array(
			'conditions' => array(
				'Product.id' => $shoppingBasketItem['Product']['id']
			),
		));
		if (isset($options[0])) {
			$options = $options[0];
		}

		if (empty($this->request->data)) {
			$this->request->data = $shoppingBasketItem;
		}
//
		$this->set(compact('shoppingBasketItem', 'options'));

		if (!$this->request->is('put')) {
			return;
		}

		if (!$this->ShoppingBasketItem->saveAssociated($this->request->data)) {
			debug(':(');

			return;
		}

		debug($this->request->data);
	}
	
}
