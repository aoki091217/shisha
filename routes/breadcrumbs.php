<?php

use Diglactic\Breadcrumbs\Breadcrumbs;

Breadcrumbs::for('shop.index', function ($trail) {
    $trail->push('一覧', route('shop.index'));
});

Breadcrumbs::for('shop.edit', function ($trail, $shop_id) {
    $trail->parent('shop.index');
    $trail->push('編集', route('shop.edit', $shop_id));
});


?>
