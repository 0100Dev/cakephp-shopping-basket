<?php

class CartItemBehavior extends ModelBehavior {

	public function getCartItems(Model $Model) {
		$products = CakeSession::read('WebshopShoppingCart.products');
		if ($products === null) {
			$products = array();
		}

		return $products;
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