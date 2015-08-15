<?php

namespace Webshop\ShoppingBasket\Model\Entity;

use Cake\ORM\Entity;
use Webshop\Model\Entity\ConfigurableItemTrait;
use Webshop\Price;

class ShoppingBasketItem extends Entity
{

    use ConfigurableItemTrait {
        configurationPrice as protected configurableItemPrice;
    }

    /**
     * @return Price
     */
    public function basePrice()
    {
        return Price::create()
            ->addCollection($this->configurableItemPrice())
            ->repeat($this->amount);
    }

    /**
     * @return Price
     */
    public function price()
    {
        return Price::create()
            ->basePrice($this->basePrice())
            ->addVat(21);
    }

}

