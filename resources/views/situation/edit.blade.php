@extends('layouts.parent')

@push('css')
@vite('resources/css/situation.css')
@endpush

@section('content')
{{ Breadcrumbs::render('situation.edit', $situation->id) }}
<form action="{{ route('situation.update', $situation->id) }}" method="POST" autocomplete="off" enctype="multipart/form-data">
    @csrf
    @method('PATCH')
    <div class="d-flex flex-wrap">
        <div class="col-6 mb-3 pe-2">
            <div>
                <div>
                    <label for="situationName" class="form-label">シチュエーション名</label>
                    <span class="text-danger">※</span>
                    <small class="text-secondary ms-2">最大文字数：50</small>
                </div>
                <small class="text-secondary">例）あいさつ、アンケート等</small>
            </div>
            <div>
                {{ Form::text(
                    'situation[name]',
                    old('situation.name', $situation->name),
                    [
                        'class' => 'form-control',
                        'id' => 'situationName',
                        'maxlength' => 50
                    ]
                ) }}
                <span class="text-danger">{{ $errors->first('situation.name') }}</span>
            </div>
        </div>
        <div class="col-6 mb-3 ps-2">
            <div>
                <div>
                    <label class="form-label"> 受信イベント</label>
                </div>
                <small class="text-secondary">カスタムした一連のメッセージの送信するタイミングを変更できます。</small>
            </div>
            <div class="d-flex">
                @foreach (config('situation.event_type') as $key => $event_type)
                <div class="radio-group">
                    {{ Form::radio(
                        'situation[event_type]',
                        $loop->iteration,
                        old('situation.event_type', $situation->event_type) == $loop->iteration || $loop->first ? 'checked' : '',
                        [
                            'class' => 'form-check-input form-check-radio',
                            'id' => $key
                        ]
                    ) }}
                    <label for="{{ $key }}" class="form-check-label">{{ $event_type }}</label>
                </div>
                @endforeach
                <span class="text-danger">{{ $errors->first('situation.event_type') }}</span>
            </div>
        </div>
        <div class="col-12 mb-3">
            <div class="accordion mb-3" id="accordionMessages">
                @foreach ($situation->messages as $messageIndex => $message)
                <div class="accordion-item">
                    <input type="hidden" name="situation[messages][{{ $messageIndex }}][disabled]" data-name="disabled" value="0">
                    <input type="hidden" name="situation[messages][{{ $messageIndex }}][id]" class="id" value="{{ $message->id }}" data-name="id">
                    <input type="hidden" name="situation[messages][{{ $messageIndex }}][turn]" class="turn" value="{{ $message->turn }}" data-name="turn">
                    <h2 class="accordion-header">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#message_{{ $messageIndex }}">
                            メッセージ{{ $loop->iteration }}
                        </button>
                    </h2>
                    <div id="message_{{ $messageIndex }}" class="accordion-collapse collapse show">
                        <div class="accordion-body">
                            <div class="d-flex">
                                <div class="col-8 d-flex flex-column gap-2 pe-4">
                                    <div>
                                        <div>
                                            <div>
                                                <label class="form-label">メッセージタイプ</label>
                                                <span class="text-danger">※</span>
                                            </div>
                                            <small class="text-secondary">送信するメッセージの見た目を変更できます。</small>
                                        </div>
                                        <div class="d-flex message-type-switch">
                                            @foreach (config('situation.message_type') as $key => $message_type)
                                            <div class="radio-group">
                                                {{ Form::radio(
                                                    "situation[messages][{$messageIndex}][message_type]",
                                                    $key,
                                                    old("situation.messages.{$messageIndex}.message_type", $message->message_type) == $key ? 'checked' : '',
                                                    [
                                                        'class' => 'form-check-input form-check-radio',
                                                        'id' => "message_type_{$key}_{$messageIndex}",
                                                        'data-name' => 'message_type'
                                                    ]
                                                ) }}
                                                <label for="message_type_{{ $key }}_{{ $messageIndex }}" class="form-check-label">{{ $message_type }}</label>
                                            </div>
                                            @endforeach
                                        </div>
                                        <span class="text-danger">{{ $errors->first("situation.messages.{$messageIndex}.message_type") }}</span>
                                    </div>
                                    <div class="type-wrapper">
                                        <div>
                                            <div>
                                                <div>
                                                    <label class="form-label">送信タイプ</label>
                                                    <span class="text-danger">※</span>
                                                </div>
                                                <small class="text-secondary">プッシュメッセージ・・・ユーザに対してメッセージを送信します。<br>リプライメッセージ・・・ユーザからのアクションに対してメッセージを送信します。</small>
                                            </div>
                                            <div class="d-flex">
                                                @foreach (config('situation.send_type') as $key => $send_type)
                                                <div class="radio-group">
                                                    {{ Form::radio(
                                                        "situation[messages][{$messageIndex}][send_type]",
                                                        $key,
                                                        old("situation.messages.{$messageIndex}.send_type", $message->message_send_type) == $key || $loop->first ? 'checked' : '',
                                                        [
                                                            'class' => 'form-check-input form-check-radio',
                                                            'id' => "send_type_{$key}_{$messageIndex}",
                                                            'data-name' => 'send_type'
                                                        ]
                                                    ) }}
                                                    <label for="send_type_{{ $key }}_{{ $messageIndex }}" class="form-check-label">{{ $send_type }}</label>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        <div>
                                            <div>
                                                <label class="form-label">応答キーワード</label>
                                                <span class="text-danger">※</span>
                                                <small class="text-secondary ms-2">最大文字数：50</small>
                                            </div>
                                            <small class="text-secondary">キーワードが含まれるメッセージを受信したときにのみ自動で返信します。</small>
                                            <div>
                                                {{ Form::text(
                                                    "situation[messages][{$messageIndex}][keyword]",
                                                    old("situation.messages.{$messageIndex}.keyword", $message->keyword),
                                                    [
                                                        'class' => 'form-control',
                                                        'maxlength' => 50,
                                                        'data-name' =>'keyword',
                                                        old('situation.event_type', $situation->event_type) != 2 ? 'disabled' : ''
                                                    ]
                                                ) }}
                                                <span class="text-danger">{{ $errors->first("situation.messages.{$messageIndex}.keyword") }}</span>
                                            </div>
                                        </div>
                                        <div class="type-text {{ old("situation.messages.{$messageIndex}.message_type", $message->message_type) == 'carousel' ? 'd-none' : '' }}">
                                            <div>
                                                <div>
                                                    <label class="form-label">メッセージ内容</label>
                                                    <span class="text-danger">※</span>
                                                    <small class="text-secondary ms-2">最大文字数：5000</small>
                                                </div>
                                                <div>
                                                    <textarea maxlength="5000" name="situation[messages][{{ $messageIndex }}][text]" class="form-control" data-name="text">{{ old("situation.messages.{$messageIndex}.text", $message->text) }}</textarea>
                                                    <span class="text-danger">{{ $errors->first("situation.messages.{$messageIndex}.text") }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="type-template {{ old("situation.messages.{$messageIndex}.message_type", $message->message_type) == 'text' ? 'd-none' : '' }}">
                                            <div>
                                                <div>
                                                    <label class="form-label">代替テキスト</label>
                                                    <span class="text-danger">※</span>
                                                    <small class="text-secondary ms-2">最大文字数：400</small>
                                                </div>
                                                <small class="text-secondary">相手がメッセージを受信した際に、端末の通知やトークリストでメッセージの代替として表示されます。<br>例）新着メッセージを受信しました。</small>
                                                <div class="col-6">
                                                    <input type="text" maxlength="400" name="situation[messages][{{ $messageIndex }}][alt_text]" class="form-control" data-name="alt_text" value="{{ old("situation.messages.{$messageIndex}.alt_text", $message->alt_text ?? '') }}">
                                                    <span class="text-danger">{{ $errors->first("situation.messages.{$messageIndex}.alt_text") }}</span>
                                                </div>
                                            </div>
                                            <div>
                                                <div>
                                                    <label class="form-label">カルーセル</label>
                                                    <small class="text-secondary ms-2">最大枚数：5</small>
                                                    <div class="text-danger">※必ず1枚は設定してください。</div>
                                                    <div class="text-danger">※画像を設定する場合は、すべてのカルーセルに設定してください。</div>
                                                    <div class="text-danger">※タイトルを設定する場合は、すべてのカルーセルに設定してください。</div>
                                                </div>
                                                <div class="carousel-group">
                                                    @foreach (range(0, 4) as $carouselIndex)
                                                    @php
                                                        $carousel = null;
                                                        if (isset($message->carousels[$carouselIndex])) {
                                                            $carousel = $message->carousels[$carouselIndex];
                                                        }
                                                    @endphp
                                                    <div class="card">
                                                        {{-- <div class="img-remove {{ is_null($carousel?->thumbnail_image_url) ? 'd-none' : '' }}">×</div>
                                                        {{ Form::file(
                                                            "situation[messages][{$messageIndex}][carousels][{$carouselIndex}][thumbnail_image_url]",
                                                            [
                                                                'class' => 'card-img-top d-none',
                                                                'accept' => '.jpeg, .jpg, .png',
                                                                'data-name' => 'thumbnail_image_url',
                                                                'id' => "thumbnail-image-{$messageIndex}-{$carouselIndex}"
                                                            ]
                                                        ) }}
                                                        <input type="hidden" name="{{ "situation[messages][{$messageIndex}][carousels][{$carouselIndex}][carousel_id]" }}" value="{{ $carousel?->id }}">
                                                        <input type="hidden" class="file-path" name="{{ "situation[messages][{$messageIndex}][carousels][{$carouselIndex}][thumbnail_image_url]" }}" value="{{ $carousel?->thumbnail_image_url }}">
                                                        <label class="card-img-top preview-img {{ !is_null($carousel?->thumbnail_image_url) ? '' : 'd-none' }}" for="{{ "thumbnail-image-{$messageIndex}-{$carouselIndex}" }}">
                                                            <img src="{{ !is_null($carousel?->thumbnail_image_url) ? asset("storage/{$carousel->thumbnail_image_url}") : '' }}" alt="">
                                                        </label>
                                                        <label class="card-img-top sample-img {{ !is_null($carousel?->thumbnail_image_url) ? 'd-none' : '' }}" for="{{ "thumbnail-image-{$messageIndex}-{$carouselIndex}" }}">
                                                            <span>画像<span class="ms-1">(任意)</span></span>
                                                            <ul>
                                                                <li>拡張子：jpg, png</li>
                                                                <li>最大横幅：1024px</li>
                                                                <li>最大サイズ：10MB</li>
                                                            </ul>
                                                        </label> --}}
                                                        <div class="card-body">
                                                            <div>
                                                                <div>
                                                                    <div>
                                                                        <label class="form-label">タイトル</label>
                                                                        <span class="text-secondary">(任意)</span>
                                                                        <small class="text-secondary ms-2">最大文字数：40</small>
                                                                    </div>
                                                                    {{ Form::text(
                                                                        "situation[messages][{$messageIndex}][carousels][{$carouselIndex}][title]",
                                                                        old("situation.messages.{$messageIndex}.carousels.{$carouselIndex}.title", $carousel?->title),
                                                                        [
                                                                            'class' => 'form-control',
                                                                            'maxlength' => 40,
                                                                            'data-name' => 'title'
                                                                        ]
                                                                    ) }}
                                                                </div>
                                                                <div>
                                                                    <div>
                                                                        <label class="form-label mb-0">メッセージ内容</label>
                                                                        <span class="text-danger">※</span>
                                                                        <small class="text-secondary ms-2"><br>最大文字数：120、画像またはタイトルを指定する場合の最大文字数：60</small>
                                                                    </div>
                                                                    <div>
                                                                        {{ Form::textarea(
                                                                            "situation[messages][{$messageIndex}][carousels][{$carouselIndex}][text]",
                                                                            old("situation.messages.{$messageIndex}.carousels.{$carouselIndex}.text", $carousel?->text),
                                                                            [
                                                                                'class' => 'form-control',
                                                                                'maxlength' => 120,
                                                                                'data-name' => 'text'
                                                                            ]
                                                                        ) }}
                                                                    </div>
                                                                </div>
                                                                <div class="d-flex flex-column justify-content-center">
                                                                    <div>
                                                                        <label class="form-label">ボタン</label>
                                                                        <span class="text-danger">※</span>
                                                                        <small class="text-secondary ms-2">最大文字数：12</small>
                                                                    </div>
                                                                    <div class="d-grid gap-1">
                                                                        @foreach (range(0, 2) as $actionIndex)
                                                                        @php
                                                                            $action = null;
                                                                            if (isset($carousel->carouselActions[$actionIndex])) {
                                                                                $action = $carousel->carouselActions[$actionIndex];
                                                                            }
                                                                        @endphp
                                                                            {{ Form::text(
                                                                                "situation[messages][{$messageIndex}][carousels][{$carouselIndex}][actions][{$actionIndex}][action]",
                                                                                old("situation.messages.{$messageIndex}.carousels.{$carouselIndex}.actions.{$actionIndex}.action", $action?->action),
                                                                                [
                                                                                    'class' => 'form-control',
                                                                                    'maxlength' => 12,
                                                                                    'data-name' => 'actions',
                                                                                    'data-action' => 'action'
                                                                                ]
                                                                            ) }}
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" class="col-4 btn btn-danger btn-remove" {{ $loop->count == 1 ? 'disabled' : '' }}>このメッセージを削除する</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="d-flex justify-content-end">
                <button type="button" class="btn btn-primary" id="addMessage">
                    <i class="fa-solid fa-plus"></i>
                </button>
            </div>
        </div>
    </div>
    <div class="d-flex align-items-center justify-content-end mt-3 footer-buttons gap-2">
        <a href="{{ route('situation.show', $situation->id) }}" class="btn btn-secondary">戻る</a>
        <button type="submit" class="btn btn-primary">更新</button>
    </div>
</form>

@push('jquery')
@vite('resources/js/situation.js')
<script defer>
    window.errors = @json($errors->get('default'));
    window.situationOld = @json(old('situation'));
</script>
@endpush

@endsection
