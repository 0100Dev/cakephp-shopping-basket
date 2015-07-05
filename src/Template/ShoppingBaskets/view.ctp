<?php
$this->assign('title', __d('webshop_shopping_basket', 'Shopping cart'));

if ($shoppingBasket['ShoppingBasket']['name']):
	$this->assign('title', __d('webshop_shopping_basket', 'Shopping cart: %1$s', $shoppingBasket['ShoppingBasket']['name']));
endif;

$total = 0;
echo $this->Form->create('ShoppingBasket', array(
	'url' => array(
		'action' => 'edit'
	)
));

?>
<?php if (!empty($shoppingBasket['Item'])): ?>
	<table class="table">
		<thead>
		<tr>
			<th>Amount</th>
			<th>Product</th>
			<th>Price</th>
		</tr>
		</thead>
		<tbody>
		<?php foreach ($shoppingBasket['Item'] as $index => $item): ?>
			<?php
			$stackable = isset($item['Product']['stackable']) ? $item['Product']['stackable'] : true;
			?>
			<tr>
				<td><?php echo $this->Form->input('Item.' . $index . '.amount', array('label' => false, 'wrapInput' => false, 'div' => false, 'disabled' => !$stackable)); ?></td>
				<td><?php echo $this->Html->link($item['Product']['title'], array('plugin' => 'nodes', 'controller' => 'nodes', 'action' => 'view', 'type' => 'product', 'slug' => $item['Product']['slug'])); ?></td>
				<td><?php echo $this->Number->currency($item['Product']['price'], 'EUR'); ?></td>
				<td>
					<div class="btn-group">
						<?php echo $this->Html->link(__d('webshop_shopping_basket', 'Edit'), array('controller' => 'shopping_basket_items', 'action' => 'edit', $item['id']), array('class' => 'btn btn-primary')); ?>
						<?php echo $this->Form->postLink(__d('webshop_shopping_basket', 'Remove'), array('action' => 'delete_product', $item['Product']['id']), array('class' => 'btn btn-primary', 'inline' => false)); ?>
					</div>
				</td>
			</tr>
		<?php endforeach; ?>
		<tr>
			<th>Total</th>
			<td><?php echo $total; ?></td>
		</tr>
		</tbody>
	</table>
<?php else: ?>
	<p>Your shopping cart is empty</p>
<?php endif; ?>
	<div class="btn-group">
		<?php echo $this->Form->submit(__d('webshop_shopping_basket', 'Update'), array('div' => false)); ?>
<!--		--><?php //echo $this->Form->postLink(__d('webshop_shopping_basket', 'Checkout'), array('action' => 'checkout'), array('class' => 'btn btn-primary')); ?>
<!--		--><?php //echo $this->Form->postLink(__d('webshop_shopping_basket', 'Empty'), array('action' => 'clear'), array('class' => 'btn btn-danger')); ?>
	</div>
<?php
echo $this->Form->end();
?>
<?php echo $this->fetch('postLink'); ?>