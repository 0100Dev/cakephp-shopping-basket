<?php

class CartOrderBehavior extends ModelBehavior {

	public function createFromCart(Model $Model, $customerId) {
		return $Model->createFromProductList($customerId, $Model->OrderProduct->Product->getCartItems());
	}

}