<?php if (empty($shoppingBasket->items)): ?>
    <?= h(__d('webshop/shopping_basket', 'No items in shopping basket')); ?>
<?php else: ?>

    <?= $this->Form->postLink(__d('webshop/shopping_basket', 'Clear'), ['prefix' => false, 'plugin' => 'Webshop/ShoppingBasket', 'controller' => 'ShoppingBaskets', 'action' => 'clear', $shoppingBasket->id]); ?>
<?php endif; ?>
