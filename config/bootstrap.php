<?php

use Croogo\Core\Croogo;
use Croogo\Core\Nav;

Croogo::hookComponent('*', 'Webshop/ShoppingBasket.ShoppingBasket');

Croogo::hookBehavior('Webshop.Products', 'Webshop/ShoppingBasket.CartItem');
Croogo::hookBehavior('Order', 'WebshopShoppingCart.CartOrder');

Nav::add('node-menu-product', 'add-to-cart', array(
	'icon'  => array('comments', 'large'),
	'title' => __d('webshop', 'Add to cart'),
	'url'   => array(
		'plugin'     => 'Webshop/ShoppingBasket',
		'controller' => 'ShoppingBasketItems',
		'action'     => 'add',
		'_id'
	)
));
