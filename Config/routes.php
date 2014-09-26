<?php

CroogoRouter::connect('/shoppingcart', array(
	'plugin'     => 'webshop_shopping_cart',
	'controller' => 'shopping_cart',
	'action'     => 'index'
));

CroogoRouter::connect('/shoppingcart/:action/*', array(
	'plugin'     => 'webshop_shopping_cart',
	'controller' => 'shopping_cart',
));
