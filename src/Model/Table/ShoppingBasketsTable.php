<?php

namespace Webshop\ShoppingBasket\Model\Table;

use Cake\ORM\Table;

class ShoppingBasketsTable extends Table {

    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->hasMany('Items', [
            'className' => 'Webshop/ShoppingBasket.ShoppingBasketItems',
        ]);
    }


    public function createBasket() {
		$shoppingBasket = $this->newEntity();
		$shoppingBasket = $this->save($shoppingBasket);

		return $shoppingBasket->id;
	}

}
