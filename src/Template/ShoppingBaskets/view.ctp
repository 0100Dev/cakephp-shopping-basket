<?php
$this->assign('title', __d('webshop_shopping_basket', 'Shopping cart'));

/** @var \Webshop\ShoppingBasket\Model\Entity\ShoppingBasket $shoppingBasket */
if ($shoppingBasket->name):
    $this->assign('title', __d('webshop_shopping_basket', 'Shopping cart: %1$s', $shoppingBasket->name));
endif;
?>

<?= $this->Form->create($shoppingBasket, array(
	'url' => array(
		'action' => 'edit',
        $shoppingBasket->id
	)
)); ?>
<?php if (!empty($shoppingBasket->items)): ?>
	<table class="table">
		<thead>
		<tr>
			<th>Amount</th>
			<th>Product</th>
			<th>Price</th>
		</tr>
		</thead>
		<tbody>
		<?php
        /** @var \Webshop\ShoppingBasket\Model\Entity\ShoppingBasketItem $item */
        foreach ($shoppingBasket->items as $index => $item):
        ?>
			<?php
			$stackable = isset($item->product->stackage) ? $item->product->stackage : true;
			?>
			<tr>
				<td><?= $this->Form->input('items.' . $index . '.amount', array('label' => false, 'wrapInput' => false, 'div' => false, 'disabled' => !$stackable)); ?></td>
				<td><?= $this->Html->link($item->product->title, array('plugin' => 'Croogo/Nodes', 'controller' => 'Nodes', 'action' => 'view', 'type' => 'product', 'slug' => $item->product->slug)); ?></td>
				<td><?= $this->Number->currency($item->price()->subTotal(), 'EUR'); ?></td>
				<td>
					<div class="btn-group">
						<?= $this->Html->link(__d('webshop_shopping_basket', 'Edit'), array('controller' => 'ShoppingBasketItems', 'action' => 'edit', $item->id), array('class' => 'btn btn-primary')); ?>
						<?= $this->Form->postLink(__d('webshop_shopping_basket', 'Remove'), array('action' => 'delete_product', $item->product->id), array('class' => 'btn btn-primary', 'inline' => false)); ?>
					</div>
				</td>
			</tr>
		<?php endforeach; ?>
        <?php foreach ($shoppingBasket->price()->taxes()->vat() as $percentage => $amount): ?>
            <tr>
                <th><?= $percentage; ?>%</th>
                <td></td>
                <td><?= h($this->Number->currency($amount, 'EUR')); ?></td>
            </tr>
        <?php endforeach; ?>
		<tr>
			<th>Total</th>
            <td></td>
			<td><?= h($this->Number->currency($shoppingBasket->price()->total(), 'EUR')); ?></td>
		</tr>

		</tbody>
	</table>
<?php else: ?>
	<p>Your shopping cart is empty</p>
<?php endif; ?>
	<div class="btn-group">
		<?= $this->Form->submit(__d('webshop_shopping_basket', 'Update'), array('div' => false)); ?>
<!--		--><?php //echo $this->Form->postLink(__d('webshop_shopping_basket', 'Checkout'), array('action' => 'checkout'), array('class' => 'btn btn-primary')); ?>
<!--		--><?php //echo $this->Form->postLink(__d('webshop_shopping_basket', 'Empty'), array('action' => 'clear'), array('class' => 'btn btn-danger')); ?>
	</div>
<?php
echo $this->Form->end();
?>
<?php echo $this->fetch('postLink'); ?>
