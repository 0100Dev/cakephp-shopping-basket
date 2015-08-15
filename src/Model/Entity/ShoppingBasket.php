<?php

namespace Webshop\ShoppingBasket\Model\Entity;

use Cake\ORM\Entity;
use Webshop\Model\Entity\ItemContainerTrait;
use Webshop\Price;
use Webshop\PriceContainer;

class ShoppingBasket extends Entity
{

    use ItemContainerTrait;

    protected $_virtual = [
        'sub_total',
        'total'
    ];

    protected function _getSubTotal()
    {
        return $this->price()->subTotal();
    }

    protected function _getTotal()
    {
        return $this->price()->total();
    }

    /**
     * @return Price
     */
    public function price()
    {
        $priceContainer = PriceContainer::construct();

        /** @var ShoppingBasketItem $item */
        foreach ($this->items as $item) {
            $priceContainer->add($item->price());
        }

        return Price::createFromCollection($priceContainer)->subject($this);
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return !boolval(count($this->items));
    }

}
