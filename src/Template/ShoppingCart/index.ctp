<?php
$this->assign('title', __d('webshop_shopping_cart', 'Shopping cart'));
$total = 0;
?>
<?php if (!empty($products)): ?>
<table class="table">
	<thead>
<tr>
	<th>Amount</th>
	<th>Product</th>
	<th>Price</th>
</tr>
	</thead>
	<tbody>
	<?php foreach ($products as $product): ?>
		<?php $total += $cart_items[$product['Product']['id']]['amount'] * $product['Product']['price']; ?>
		<tr>
			<td><?php echo h($cart_items[$product['Product']['id']]['amount']); ?></td>
			<td><?php echo $this->Html->link($product['Product']['title'], array('plugin' => 'nodes', 'controller' => 'nodes', 'action' => 'view', 'type' => 'product', 'slug' => $product['Product']['slug'])); ?></td>
			<td><?php echo $this->Number->currency($product['Product']['price'], 'EUR'); ?> (<?php echo $this->Number->currency($cart_items[$product['Product']['id']]['amount'] * $product['Product']['price'], 'EUR'); ?>)</td>
			<td><?php echo $this->Form->postLink(__d('webshop_shopping_cart', 'Remove'), array('action' => 'delete_product', $product['Product']['id'])); ?></td>
		</tr>
	<?php endforeach; ?>
	<tr>
		<th>Total</th>
		<td><?php echo $total; ?></td>
	</tr>
	</tbody>
</table>

<?php echo $this->Html->link(__d('webshop_shopping_cart', 'Checkout'), array('action' => 'checkout')); ?>
<?php echo $this->Form->postLink(__d('webshop_shopping_cart', 'Empty'), array('action' => 'clear')); ?>
<?php Router::url(array('action' => 'load', '?' => array_combine(array_map(function ($str) { return 'product-' . $str; }, array_keys($cart_items)), Hash::extract($cart_items, '{n}.amount'))), true); ?>.
<?php else: ?>
	<p>Your shopping cart is empty</p>
<?php endif; ?>

<?php $payment_methods = $this->requestAction(array('plugin' => 'webshop_payments', 'controller' => 'payment_methods', 'action' => 'index')); ?>
<table>
	<tbody>
	<?php foreach ($payment_methods as $payment_method): ?>
		<tr>
			<td><?php echo $this->Html->image($payment_method['PaymentMethod']['image_large']); ?></td>
			<td><?php echo $this->Number->currency($this->requestAction(array('plugin' => 'webshop_payments', 'controller' => 'payment_methods', 'action' => 'get_transaction_costs', $payment_method['PaymentMethod']['id'], $total)), 'EUR'); ?></td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>