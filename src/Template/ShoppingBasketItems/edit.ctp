<?php

/** @var \Webshop\ShoppingBasket\Model\Entity\ShoppingBasketItem $shoppingBasketItem */

$configurationParts = array();
$this->ConfigurationOption->setOptions($options);
$this->ConfigurationOption->setProductDefaults($shoppingBasketItem->product->configuration_values);
$this->ConfigurationOption->setValues($shoppingBasketItem->configuration());
$this->ConfigurationOption->setValueStoreIds($shoppingBasketItem->configurationValueIds());

echo $this->Form->create($shoppingBasketItem);

echo $this->Form->input('amount');

echo $this->ConfigurationOption->form(array(
	'input' => array(
		'prefix' => 'configuration_values.'
	),
	'relation' => array(
		'model' => 'Webshop\ShoppingBasket\Model\Table\ShoppingBasketItemsTable',
		'id' => $shoppingBasketItem->id
	)
));

echo $this->Form->submit('Edit');

echo $this->Form->end();
