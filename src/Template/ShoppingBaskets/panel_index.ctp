<?php
$this->Title->addSegment(__d('webshop', 'Dashboard'));
$this->Title->setPageTitle(__d('webshop_shopping_basket', 'Stored shopping baskets'));

$this->Title->addCrumbs(array(
	array('controller' => 'customers', 'action' => 'dashboard'),
	array('action' => 'index')
));

$this->extend('Webshop.Common/panel_index');

$this->set('displayFields', array(
	'name' => array(
		'label' => __d('webshop_shopping_basket', 'Name'),
		'url' => array(
			'panel' => false,
			'action' => 'view',
			'pass' => array(
				'id'
			)
		),
		'sort' => true
	),
));
