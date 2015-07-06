<?php

namespace Webshop\ShoppingBasket\Controller\Component;

use Cake\Controller\Component;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Webshop\ShoppingBasket\Model\Table\ShoppingBasketsTable;

class ShoppingBasketComponent extends Component
{

    /**
     * @var ShoppingBasketsTable
     */
    public $ShoppingBaskets;

    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->ShoppingBaskets = TableRegistry::get('Webshop/ShoppingBasket.ShoppingBaskets');
    }


    public function currentBasketId() {
        if (!$this->request->session()->check('WebshopShoppingBasket.current_basket_id')) {
            $basketId = $this->ShoppingBaskets->createBasket();

            if (!$basketId) {
                return false;
            }

            $this->request->session()->write('WebshopShoppingBasket.current_basket_id', $basketId);
        }

        return $this->request->session()->read('WebshopShoppingBasket.current_basket_id');
    }

}
