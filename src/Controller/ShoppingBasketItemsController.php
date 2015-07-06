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
		$shoppingBasketItem = $this->ShoppingBasketItems->find('first', array(
			'conditions' => array(
				'ShoppingBasketItem.id' => $id
			),
			'contain' => array(
				'Product' => array(
					'ConfigurationValue'
				),
				'ConfigurationValue' => array(
					'ConfigurationOption'
				)
			)
		));

		$options = $this->ShoppingBasketItems->Products->find('options', array(
			'conditions' => array(
				'Product.id' => $shoppingBasketItem['Product']['id']
			),
		));
		if (isset($options[0])) {
			$options = $options[0];
		}

		if (empty($this->request->data)) {
			$this->request->data = $shoppingBasketItem;
		}
//
		$this->set(compact('shoppingBasketItem', 'options'));

		if (!$this->request->is('put')) {
			return;
		}

		if (!$this->ShoppingBasketItems->saveAssociated($this->request->data)) {
			debug(':(');

			return;
		}

		debug($this->request->data);
	}
	
}
