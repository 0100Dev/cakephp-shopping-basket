<?php

$configurationParts = array();
$this->ConfigurationOption->setOptions($options);
$this->ConfigurationOption->setProductDefaults($shoppingBasketItem['Product']['ConfigurationValue']);
$this->ConfigurationOption->setValues($shoppingBasketItem['ShoppingBasketItem']['configuration']);
$this->ConfigurationOption->setValueStoreIds(Hash::combine($shoppingBasketItem, 'ConfigurationValue.{n}.ConfigurationOption.alias', 'ConfigurationValue.{n}.id'));

echo $this->Form->create('ShoppingBasketItem');

echo $this->Form->input('amount');

echo $this->ConfigurationOption->form(array(
	'input' => array(
		'prefix' => 'ConfigurationValue.'
	),
	'relation' => array(
		'model' => 'ShoppingBasketItem',
		'id' => $shoppingBasketItem['ShoppingBasketItem']['id']
	)
));

echo $this->Form->submit('Edit');

echo $this->Form->end();
