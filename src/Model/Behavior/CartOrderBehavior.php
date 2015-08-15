<?php

namespace Webshop\ShoppingBasket\Model\Behavior;

use Cake\ORM\Behavior;
use Webshop\Model\Entity\Customer;

class CartOrderBehavior extends Behavior {

	public function createFromCart(Customer $customer) {
		return $Model->createFromProductList($customerId, $Model->OrderProduct->Product->getCartItems());
	}

}
