<?php

namespace Webshop\ShoppingBasket\View\Cell;

use Cake\View\Cell;
use Webshop\ShoppingBasket\Model\Table\ShoppingBasketsTable;

/**
 * @property ShoppingBasketsTable ShoppingBaskets
 */
class ShoppingBasketsCell extends Cell
{

    public function sidebar()
    {
        $this->loadModel('Webshop/ShoppingBasket.ShoppingBaskets');

        $shoppingBasket = $this->ShoppingBaskets->get($this->currentBasketId(), [
            'contain' => [
                'Items' => [
                    'Products'
                ]
            ]
        ]);

        $this->set('shoppingBasket', $shoppingBasket);
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
