<?php

namespace Webshop\ShoppingBasket\Model\Behavior;

use Cake\ORM\Behavior;
use Cake\ORM\Entity;
use Cake\Utility\Hash;
use Webshop\Model\Entity\Product;

class CartItemBehavior extends Behavior {

    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->_table->belongsTo('ShoppingBasketItems', [
            'className' => 'Webshop/ShoppingBasket.ShoppingBasketItems'
        ]);
    }

//	public function getCartItems(Model $Model) {
//		$products = CakeSession::read('WebshopShoppingCart.products');
//		if ($products === null) {
//			$products = array();
//		}
//
//		return $products;
//	}

	public function addToBasket(Product $product, array $options = []) {
		$options = Hash::merge(array(
			'amount' => 1,
			'configuration' => array(),
			'non_overridable' => array()
		), array_filter($options));

		$configurationGroupIds = $this->_table->ItemConfigurationGroups->find('list', [
            'valueField' => 'configuration_group_id'
        ])->where([
            'ItemConfigurationGroups.foreign_key' => $product->id,
            'ItemConfigurationGroups.model' => get_class($this->_table),
        ])->toArray();

		$valueData = $this->_table->ShoppingBasketItems->ConfigurationValues->generateValueData(
            get_class($this->_table->ShoppingBasketItems->target()),
            $configurationGroupIds,
            $options['configuration'],
            $options['non_overridable']
		);

		if ($product->stackable) {
			$shoppingBasketItemId = $Model->ShoppingBasketItem->field('id', array(
				'ShoppingBasketItem.shopping_basket_id' => $options['basket'],
				'ShoppingBasketItem.product_id' => $id
			));
			if (!$shoppingBasketItemId) {
				$Model->ShoppingBasketItem->create();
				$data = $Model->ShoppingBasketItem->saveAssociated(array(
					'ShoppingBasketItem' => array(
						'shopping_basket_id' => $options['basket'],
						'product_id' => $id,
						'amount' => $options['amount']
					),
					'ConfigurationValue' => $valueData
				));

				return $data;
			}

			$Model->ShoppingBasketItem->id = $shoppingBasketItemId;

			$amount = $Model->ShoppingBasketItem->field('amount');

			return $Model->ShoppingBasketItem->saveField('amount', $amount + $options['amount']);
		}

		for ($number = 1; $number <= $options['amount']; $number++) {
            $shoppingBasketItem = $this->_table->ShoppingBasketItems->newEntity([
                'shopping_basket_id' => $options['basket'],
                'product_id' => $product->id,
                'amount' => 1,
                'configuration_values' => $valueData
            ]);
			$data = $this->_table->ShoppingBasketItems->save($shoppingBasketItem);
			if (!$data) {
				return false;
			}
		}

		return true;
	}

	public function addToCart(Model $Model, $id, $amount = 1) {
		$products = CakeSession::read('WebshopShoppingCart.products');
		if ($products === null) {
			$products = array();
		}

		if (isset($products[$id])) {
			$products[$id]['amount'] += $amount;
		} else {
			$products[$id] = array(
				'amount' => $amount,
				'configuration' => array()
			);
		}

		CakeSession::write('WebshopShoppingCart.products', $products);
	}

	public function removeFromCart(Model $Model, $id) {
		$products = CakeSession::read('WebshopShoppingCart.products');
		if ($products === null) {
			$products = array();
		}

		unset($products[$id]);

		CakeSession::write('WebshopShoppingCart.products', $products);
	}

	public function setCartConfiguration(Model $Model, $id, $configuration) {
		$products = CakeSession::read('WebshopShoppingCart.products');
		if ($products === null) {
			$products = array();
		}

		$products[$id]['configuration'] = $configuration;

		CakeSession::write('WebshopShoppingCart.products', $products);
	}

	public function clearCart() {
		CakeSession::delete('WebshopShoppingCart.products');
	}

}
