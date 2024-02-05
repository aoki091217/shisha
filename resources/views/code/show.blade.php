@extends('layouts.parent')

@section('content')
{{ Breadcrumbs::render('code.show', $code->getCodeId()) }}
<div>
    <div class="col-12 mb-3">
        <label class="form-label">店舗名</label>
        <div>
            <span>{{ $code->shop->name }}</span>
        </div>
    </div>
    <div class="col-12 mb-3">
        <label class="form-label">コード名</label>
        <div>
            <span>{{ $code->getName() }}</span>
        </div>
    </div>
    <div class="col-12 mb-3">
        <label class="form-label">コード種別</label>
        <div>
            <span>{{ $code->getKind() == 1 ? '流入経路計測' : 'チェックイン' }}</span>
        </div>
    </div>
    <div class="col-12 mb-3">
        <label class="form-label">友達追加時送信メッセージ</label>
        <div>
            <span>{{ $code->situation->name }}</span>
        </div>
    </div>
    <div class="col-12 mb-3">
        <label class="form-label">パラメータ</label>
        <div>
            <span>{{ $code->getParameter() }}</span>
        </div>
    </div>
    <div class="col-12 mb-3">
        <label class="form-label">埋め込みスクリプト</label>
        <div>
            <span>{!! nl2br(e($code->getScript())) !!}</span>
        </div>
    </div>
    <div class="col-12 mb-3">
        <label class="form-label">備考欄</label>
        <div>
            <span>{!! nl2br(e($code->getNotes())) !!}</span>
        </div>
    </div>
</div>
<div class="d-flex align-items-center justify-content-end mt-3 footer-buttons gap-2">
    <a href="{{ route('code.index') }}" class="btn btn-secondary">戻る</a>
    <a href="{{ route('code.edit', $code->getCodeId()) }}" class="btn btn-success">編集</a>
    <button type="button" class="btn btn-danger" id="deleteButton" data-route="{{ route('code.destroy', $code->getCodeId()) }}" data-bs-toggle="modal" data-bs-target="#deleteModal">
        削除
    </button>
</div>

@endsection
