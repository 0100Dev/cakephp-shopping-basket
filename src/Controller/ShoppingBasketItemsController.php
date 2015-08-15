<?php

namespace Webshop\ShoppingBasket\Controller;

use Croogo\Core\Controller\CroogoAppController;
use Webshop\Model\Entity\Product;
use Webshop\ShoppingBasket\Model\Table\ShoppingBasketItemsTable;


/**
 * Class ShoppingBasketItemsController
 *
 * @property ShoppingBasketItemsTable ShoppingBasketItems
 */
class ShoppingBasketItemsController extends CroogoAppController {

	/**
	 * @param null $productId
	 */
	public function add($productId = null) {
		if ($productId) {
			$this->request->data('product_id', $productId);
		}

        $shoppingBasketItem = $this->ShoppingBasketItems->newEntity([
            'shopping_basket_id' => $this->ShoppingBasket->currentBasketId()
        ]);

        /** @var Product $product */
        $product = $this->ShoppingBasketItems->Products->find()
            ->where([
                'Products.id' => $this->request->data('product_id')
            ])
            ->contain([
                'ConfigurationValues'
            ])
            ->find('options')
            ->firstOrFail();

		$options = $product->options();

		$this->set(compact('stackable', 'product', 'options', 'shoppingBasketItem'));

		if (!$this->request->is('post')) {
			return;
		}

		$productNonOverridableValues = $this->ShoppingBasketItems->Products->find()->where([
            'Products.id' => $this->request->data('product_id')
        ])->contain([
            'ConfigurationValues' => [
                'conditions' => [
                    'ConfigurationValues.overridable' => false
                ]
            ]
        ])->firstOrFail();

		if (!$this->ShoppingBasketItems->Products->addToBasket($product, [
			'basket' => $this->request->data('shopping_basket_id'),
			'amount' => ($product->stackable) ? $this->request->data('amount') : 1,
			'configuration' => $this->ShoppingBasketItems->parseConfiguration(
				$this->request->data['configuration_values']
			),
			'non_overridable' => $this->ShoppingBasketItems->parseConfiguration(
				$productNonOverridableValues->configuration_values
			)
        ])) {
			return;
		}

		$this->redirect(array(
			'controller' => 'ShoppingBaskets',
			'action' => 'view',
            $this->request->data('shopping_basket_id')
		));
	}


	public function edit($id) {
		$shoppingBasketItem = $this->ShoppingBasketItems->get($id, [
			'contain' => [
				'Products' => [
					'ConfigurationValues'
                ],
				'ConfigurationValues' => [
					'ConfigurationOptions'
                ]
            ]
        ]);

        /** @var Product $product */
        $product = $this->ShoppingBasketItems->Products->find('options')->where(array(
            'Products.id' => $shoppingBasketItem->product->id
		))->firstOrFail();
        $options = $product->options();

		$this->set(compact('shoppingBasketItem', 'options'));

		if (!$this->request->is('put')) {
			return;
		}

        $shoppingBasketItem = $this->ShoppingBasketItems->patchEntity($shoppingBasketItem, $this->request->data);

		if (!$this->ShoppingBasketItems->save($shoppingBasketItem)) {
			debug(':(');

			return;
		}

		$this->redirect([
            'controller' => 'ShoppingBaskets',
            'action' => 'view',
            $shoppingBasketItem->shopping_basket_id
        ]);
	}

}
