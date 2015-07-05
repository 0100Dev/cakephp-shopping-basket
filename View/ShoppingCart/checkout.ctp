<div class="panel">
	<div class="panel-body">
		<?php echo $this->element('WebshopCustomerUsers.user-customer-status'); ?>
	</div>
</div>

<?php if ((isset($customer) && $customer)): ?>
	<p><?php echo h(__d('webshop_shopping_cart', 'You\'re about to order as %s', $customer['Customer']['name'])); ?></p>
<?php endif; ?>

<?php
echo $this->Form->create('Order', array(
	'inputDefaults' => array(
		'div' => 'form-group',
		'label' => array(
			'class' => 'col col-md-3 control-label'
		),
		'wrapInput' => 'col col-md-9',
		'class' => 'form-control'
	),
	'class' => 'well form-horizontal'
));

if ($hasPhysicalProducts):
	echo $this->Form->input('Order.OrderShipment.Shipment.shipping_method_id', array(
		'label' => 'Shipping method'
	));
endif;
echo $this->Form->input('Order.OrderShipment.Shipment.address_detail_id', array(
	'label' => 'Shipping address',
	'options' => $addressDetails,
	'beforeInput' => '<div class="input-group">',
	'afterInput' => '<span class="input-group-btn"><a class="btn btn-default add-address-detail"><i class="fa fa-plus"></i></a></span></div>',
	'data-model' => 'AddressDetail'
));

echo $this->Form->input('Order.invoice_address_detail_id', array(
	'label' => 'Invoice address',
	'options' => $addressDetails,
	'beforeInput' => '<div class="input-group">',
	'afterInput' => '<span class="input-group-btn"><a class="btn btn-default add-address-detail"><i class="fa fa-plus"></i></a></span></div>',
	'data-model' => 'AddressDetail'
));

echo $this->Form->input('Order.comment', array(
	'label' => 'Comment',
));

echo $this->Form->submit('Order and pay', array(
	'div' => 'col col-md-9 col-md-offset-3',
	'class' => 'btn btn-default'
));

echo $this->Form->end();
