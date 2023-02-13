@extends('layouts.parent')

@section('content')
{{ Breadcrumbs::render('member.edit', $member->member_id) }}
<form action="{{ route('member.update', $member->member_id) }}" method="POST" autocomplete="off">
    @csrf
    @method('PATCH')
    <div>
        <label for="shopName" class="form-label">店舗<span class="text-danger">※</span></label>
        <div class="col-8 d-flex justify-content-between align-items-center mb-3">
            <select name="member[shop_id]" id="shopName" class="form-select">
                @foreach ($shops as $shop)
                    <option value="{{ $shop->shop_id }}"
                            {{ old('member.shop_id', $member->shop_id) == $shop->shop_id ? 'selected' : '' }}>
                        {{ $shop->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <span class="text-danger">{{ $errors->first('member.shop_id') }}</span>
        <label for="memberName" class="form-label">スタッフ<span class="text-danger">※</span></label>
        <div class="col-8 d-flex justify-content-between align-items-center">
            {{ Form::text(
                'member[name]',
                old('member.name', $member->name),
                [
                    'class' => 'form-control',
                    'id' => 'memberName'
                ]
            ) }}
        </div>
        <span class="text-danger">{{ $errors->first('member.name') }}</span>
    </div>
    <div class="col-8 d-flex align-items-center justify-content-end mt-3 footer-buttons gap-2">
        <a href="{{ route('member.index') }}" class="btn btn-secondary">戻る</a>
        <button type="submit" class="btn btn-primary">更新</button>
    </div>
</form>


@endsection
