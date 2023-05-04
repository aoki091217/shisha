<?php

use Diglactic\Breadcrumbs\Breadcrumbs;

Breadcrumbs::for('shop.index', function ($trail) {
    $trail->push('一覧', route('shop.index'));
});
Breadcrumbs::for('shop.create', function ($trail) {
    $trail->parent('shop.index');
    $trail->push('登録', route('shop.create'));
});
Breadcrumbs::for('shop.edit', function ($trail, $shop_id) {
    $trail->parent('shop.index');
    $trail->push('編集', route('shop.edit', $shop_id));
});



Breadcrumbs::for('member.index', function ($trail) {
    $trail->push('一覧', route('member.index'));
});
Breadcrumbs::for('member.create', function ($trail) {
    $trail->parent('member.index');
    $trail->push('登録', route('member.create'));
});
Breadcrumbs::for('member.edit', function ($trail, $member_id) {
    $trail->parent('member.index');
    $trail->push('編集', route('member.edit', $member_id));
});



Breadcrumbs::for('bland.index', function ($trail) {
    $trail->push('一覧', route('bland.index'));
});
Breadcrumbs::for('bland.create', function ($trail) {
    $trail->parent('bland.index');
    $trail->push('登録', route('bland.create'));
});
Breadcrumbs::for('bland.edit', function ($trail, $bland_id) {
    $trail->parent('bland.index');
    $trail->push('編集', route('bland.edit', $bland_id));
});



Breadcrumbs::for('flavor.index', function ($trail) {
    $trail->push('一覧', route('flavor.index'));
});
Breadcrumbs::for('flavor.create', function ($trail) {
    $trail->parent('flavor.index');
    $trail->push('登録', route('flavor.create'));
});
Breadcrumbs::for('flavor.edit', function ($trail, $flavor_id) {
    $trail->parent('flavor.index');
    $trail->push('編集', route('flavor.edit', $flavor_id));
});



Breadcrumbs::for('bill.index', function ($trail) {
    $trail->push('一覧', route('bill.index'));
});
Breadcrumbs::for('bill.create', function ($trail) {
    $trail->parent('bill.index');
    $trail->push('登録', route('bill.create'));
});
Breadcrumbs::for('bill.show', function ($trail, $bill_id) {
    $trail->parent('bill.index');
    $trail->push('詳細', route('bill.show', $bill_id));
});
Breadcrumbs::for('bill.edit', function ($trail, $bill_id) {
    $trail->parent('bill.show', $bill_id);
    $trail->push('編集', route('bill.edit', $bill_id));
});



Breadcrumbs::for('user.index', function ($trail) {
    $trail->push('一覧', route('user.index'));
});
Breadcrumbs::for('user.create', function ($trail) {
    $trail->parent('user.index');
    $trail->push('登録', route('user.create'));
});
Breadcrumbs::for('user.edit', function ($trail, $id) {
    $trail->parent('user.index');
    $trail->push('編集', route('user.edit', $id));
});



Breadcrumbs::for('customer.index', function ($trail) {
    $trail->push('一覧', route('customer.index'));
});



Breadcrumbs::for('mix.index', function ($trail) {
    $trail->push('一覧', route('mix.index'));
});
Breadcrumbs::for('mix.create', function ($trail) {
    $trail->parent('mix.index');
    $trail->push('登録', route('mix.create'));
});
Breadcrumbs::for('mix.show', function ($trail, $id) {
    $trail->parent('mix.index');
    $trail->push('詳細', route('mix.show', $id));
});
Breadcrumbs::for('mix.edit', function ($trail, $id) {
    $trail->parent('mix.show', $id);
    $trail->push('編集', route('mix.edit', $id));
});



Breadcrumbs::for('situation.index', function ($trail) {
    $trail->push('一覧', route('situation.index'));
});
Breadcrumbs::for('situation.create', function ($trail) {
    $trail->parent('situation.index');
    $trail->push('登録', route('situation.create'));
});
Breadcrumbs::for('situation.show', function ($trail, $id) {
    $trail->parent('situation.index');
    $trail->push('詳細', route('situation.show', $id));
});
Breadcrumbs::for('situation.edit', function ($trail, $id) {
    $trail->parent('situation.show', $id);
    $trail->push('編集', route('situation.edit', $id));
});
?>
