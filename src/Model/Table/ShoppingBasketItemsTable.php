<?php

App::uses('WebshopShoppingBasketAppModel', 'WebshopShoppingCart.Model');

class ShoppingBasketItem extends WebshopShoppingBasketAppModel {

	public $actsAs = array(
		'Webshop.ConfigurationValueHost',
	);

	public $belongsTo = array(
		'ShoppingBasket' => array(
			'className' => 'WebshopShoppingCart.ShoppingBasket'
		),
		'Product' => array(
			'className' => 'Webshop.Product'
		)
	);

}
