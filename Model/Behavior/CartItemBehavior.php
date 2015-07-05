<?php

class CartItemBehavior extends ModelBehavior {

	public function setup(Model $Model, $config = array()) {
		$Model->bindModel(array(
			'belongsTo' => array(
				'ShoppingBasketItem' => array(
					'className' => 'WebshopShoppingCart.ShoppingBasketItem'
				)
			)
		), false);
	}

	public function getCartItems(Model $Model) {
		$products = CakeSession::read('WebshopShoppingCart.products');
		if ($products === null) {
			$products = array();
		}

		return $products;
	}

	public function addToBasket(Model $Model, $id, array $options = array()) {
		$options = Hash::merge(array(
			'basket' => $Model->ShoppingBasketItem->ShoppingBasket->currentBasketId(),
			'amount' => 1,
			'configuration' => array(),
			'non_overridable' => array()
		), array_filter($options));

		$configurationGroupIds = $Model->ItemConfigurationGroup->find('list', array(
			'fields' => array(
				'ConfigurationGroup.id'
			),
			'conditions' => array(
				'ItemConfigurationGroup.foreign_key' => $id,
				'ItemConfigurationGroup.model' => $Model->name,
			),
			'contain' => array(
				'ConfigurationGroup'
			)
		));

		$valueData = $Model->ShoppingBasketItem->ConfigurationValue->generateValueData(
			$Model->ShoppingBasketItem->name, $configurationGroupIds, $options['configuration'], $options['non_overridable']
		);

		$stackable = $Model->field('stackable', array(
			'id' => $id
		));

		if ($stackable) {
			$shoppingBasketItemId = $Model->ShoppingBasketItem->field('id', array(
				'ShoppingBasketItem.shopping_basket_id' => $options['basket'],
				'ShoppingBasketItem.product_id' => $id
			));
			if (!$shoppingBasketItemId) {
				$Model->ShoppingBasketItem->create();
				$data = $Model->ShoppingBasketItem->saveAssociated(array(
					'ShoppingBasketItem' => array(
						'shopping_basket_id' => $options['basket'],
						'product_id' => $id,
						'amount' => $options['amount']
					),
					'ConfigurationValue' => $valueData
				));

				return $data;
			}

			$Model->ShoppingBasketItem->id = $shoppingBasketItemId;

			$amount = $Model->ShoppingBasketItem->field('amount');

			return $Model->ShoppingBasketItem->saveField('amount', $amount + $options['amount']);
		}

		for ($number = 1; $number <= $options['amount']; $number++) {
			$Model->ShoppingBasketItem->create();
			$data = $Model->ShoppingBasketItem->saveAssociated(array(
				'ShoppingBasketItem' => array(
					'shopping_basket_id' => $options['basket'],
					'product_id' => $id,
					'amount' => 1
				),
				'ConfigurationValue' => $valueData
			));

			if (!$data) {
				return false;
			}
		}

		return true;
	}

	public function addToCart(Model $Model, $id, $amount = 1) {
		$products = CakeSession::read('WebshopShoppingCart.products');
		if ($products === null) {
			$products = array();
		}

		if (isset($products[$id])) {
			$products[$id]['amount'] += $amount;
		} else {
			$products[$id] = array(
				'amount' => $amount,
				'configuration' => array()
			);
		}

		CakeSession::write('WebshopShoppingCart.products', $products);
	}

	public function removeFromCart(Model $Model, $id) {
		$products = CakeSession::read('WebshopShoppingCart.products');
		if ($products === null) {
			$products = array();
		}

		unset($products[$id]);

		CakeSession::write('WebshopShoppingCart.products', $products);
	}

	public function setCartConfiguration(Model $Model, $id, $configuration) {
		$products = CakeSession::read('WebshopShoppingCart.products');
		if ($products === null) {
			$products = array();
		}

		$products[$id]['configuration'] = $configuration;

		CakeSession::write('WebshopShoppingCart.products', $products);
	}

	public function clearCart() {
		CakeSession::delete('WebshopShoppingCart.products');
	}

}