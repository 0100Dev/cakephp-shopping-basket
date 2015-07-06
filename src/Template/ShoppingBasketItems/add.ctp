<?php echo $this->Form->create($shoppingBasketItem); ?>

<?php echo $this->Form->hidden('shopping_basket_id'); ?>
<?php echo $this->Form->hidden('product_id'); ?>

<?php if ($product->stackable): ?>
	<?php echo $this->Form->input('amount'); ?>
<?php endif; ?>

<?php

$configurationParts = array();
$this->ConfigurationOption->setOptions($options);
$this->ConfigurationOption->setProductDefaults($product->configuration_values);

echo $this->ConfigurationOption->form(array(
	'input' => array(
		'prefix' => 'configuration_values.'
	),
	'relation' => array(
		'model' => 'ShoppingBasketItem',
	)
));
?>

<?php echo $this->Form->submit(__d('webshop_shopping_basket', 'Add')); ?>
<?php echo $this->Form->end(); ?>
