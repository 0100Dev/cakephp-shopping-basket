<?php

echo $this->Form->create('ShoppingBasket');

echo $this->Form->hidden('id');

echo $this->Form->input('name');

echo $this->Form->submit(__d('webshop_shopping_basket', 'Save'));

echo $this->Form->end();