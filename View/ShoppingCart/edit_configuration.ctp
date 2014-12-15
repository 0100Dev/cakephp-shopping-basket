<?php

$configurationParts = array();
foreach ($product['ProductConfigurationGroup'] as $productConfigurationGroup):
	$configurationGroup = $this->requestAction(array('plugin' => 'webshop', 'controller' => 'configuration_groups', 'action' => 'view', $productConfigurationGroup['configuration_group_id']));

	$this->ConfigurationOption->setConfigurationGroupDetails($configurationGroup);
	$this->ConfigurationOption->setOverwrites($this->requestAction(array('plugin' => 'webshop', 'controller' => 'product_configuration_options', 'action' => 'product', $product['Product']['id'])));
	$this->ConfigurationOption->setValues($cartItem['configuration']);
endforeach;

echo $this->Form->create(false);

echo $this->ConfigurationOption->form(array(
	'input' => array(
		'prefix' => 'Configuration.'
	)
));

echo $this->Form->submit('Edit');

echo $this->Form->end();

//debug($product);

