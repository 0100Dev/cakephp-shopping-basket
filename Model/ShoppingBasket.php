<?php

App::uses('WebshopShoppingBasketAppModel', 'WebshopShoppingCart.Model');

class ShoppingBasket extends WebshopShoppingBasketAppModel {

	public $hasMany = array(
		'Item' => array(
			'className' => 'WebshopShoppingCart.ShoppingBasketItem',
		)
	);

	public function currentBasketId() {
		if (!CakeSession::check('WebshopShoppingBasket.current_basket_id')) {
			$basketId = $this->createBasket();

			if (!$basketId) {
				return false;
			}

			CakeSession::write('WebshopShoppingBasket.current_basket_id', $basketId);
		}

		return CakeSession::read('WebshopShoppingBasket.current_basket_id');
	}

	public function createBasket() {
		$this->create();
		$shoppingBasket = $this->save();

		return $shoppingBasket[$this->alias]['id'];
	}

}
