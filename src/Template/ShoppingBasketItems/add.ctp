<?php echo $this->Form->create('ShoppingBasketItem'); ?>

<?php echo $this->Form->hidden('ShoppingBasketItem.shopping_basket_id'); ?>
<?php echo $this->Form->hidden('ShoppingBasketItem.product_id'); ?>

<?php if ($stackable): ?>
	<?php echo $this->Form->input('ShoppingBasketItem.amount'); ?>
<?php endif; ?>

<?php

$configurationParts = array();
$this->ConfigurationOption->setOptions($options);
$this->ConfigurationOption->setProductDefaults($product['ConfigurationValue']);

echo $this->ConfigurationOption->form(array(
	'input' => array(
		'prefix' => 'ConfigurationValue.'
	),
	'relation' => array(
		'model' => 'ShoppingBasketItem',
	)
));
?>

<?php echo $this->Form->submit(__d('webshop_shopping_basket', 'Add')); ?>
<?php echo $this->Form->end(); ?>