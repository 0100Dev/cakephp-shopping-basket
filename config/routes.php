<?php
//
//CroogoRouter::connect('/winkelwagentje/betaal/*', array(
//	'locale' => 'nld',
//	'plugin' => 'webshop_shopping_cart',
//	'controller' => 'shopping_cart',
//	'action' => 'checkout'
//));
//
//CroogoRouter::connect('/winkelwagentje', array(
//	'locale' => 'nld',
//	'plugin' => 'webshop_shopping_cart',
//	'controller' => 'shopping_cart',
//	'action' => 'index'
//));
//
//CroogoRouter::connect('/winkelwagentje/:action/*', array(
//	'locale' => 'nld',
//	'plugin' => 'webshop_shopping_cart',
//	'controller' => 'shopping_cart',
//));
//
use Croogo\Core\CroogoRouter;

CroogoRouter::connect('/cart/:action/*', array(
	'plugin'     => 'Webshop/ShoppingBasket',
	'controller' => 'ShoppingBaskets',
));

CroogoRouter::connect('/cart/items/:action/*', array(
    'plugin'     => 'Webshop/ShoppingBasket',
    'controller' => 'ShoppingBasketItems',
));

//
//CroogoRouter::connect('/cart/:action/*', array(
//	'plugin'     => 'webshop_shopping_cart',
//	'controller' => 'shopping_cart',
//));
