<?php

namespace Webshop\ShoppingBasket\Model\Table;

use Cake\ORM\Table;

class ShoppingBasketItemsTable extends Table {

    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->addBehavior('Webshop.ConfigurationValueHost');
        $this->belongsTo('ShoppingBaskets', [
            'className' => 'Webshop/ShoppingBasket.ShoppingBaskets'
        ]);
        $this->belongsTo('Products', [
            'className' => 'Webshop.Products'
        ]);
    }


}
