<?php
echo $this->Form->create('Order');

echo $this->Form->input('Order.customer_id');
if ($hasPhysicalProducts):
	echo $this->Form->input('Order.OrderShipment.Shipment.shipping_method_id');
endif;
echo $this->Form->input('Order.OrderShipment.Shipment.address_detail_id');

echo $this->Form->submit('Create order');

echo $this->Form->end();
?>

Ben je al klant of staat je naam niet in de lijst? Log dan in of gebruik de link bij je vorige aankoop.


