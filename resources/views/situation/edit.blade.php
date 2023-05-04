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
                @foreach ($situation->messages as $i => $template)
                <div class="accordion-item">
                    <input type="hidden" name="situation[messages][{{ $i }}][turn]" class="turn" value="{{ $template->turn }}" data-name="turn">
                    <h2 class="accordion-header">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#message_{{ $i }}">
                            メッセージ{{ $loop->iteration }}
                        </button>
                    </h2>
                    <div id="message_{{ $i }}" class="accordion-collapse collapse show">
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
                                                    "situation[messages][{$i}][message_type]",
                                                    $key,
                                                    old("situation.messages.{$i}.message_type", $template->message_type) == $key ? 'checked' : '',
                                                    [
                                                        'class' => 'form-check-input form-check-radio',
                                                        'id' => "message_type_{$key}_{$i}",
                                                        'data-name' => 'message_type'
                                                    ]
                                                ) }}
                                                <label for="message_type_{{ $key }}_{{ $i }}" class="form-check-label">{{ $message_type }}</label>
                                            </div>
                                            @endforeach
                                        </div>
                                        <span class="text-danger">{{ $errors->first("situation.messages.{$i}.message_type") }}</span>
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
                                                        "situation[messages][{$i}][send_type]",
                                                        $key,
                                                        old("situation.messages.{$i}.send_type", $template->message_send_type) == $key || $loop->first ? 'checked' : '',
                                                        [
                                                            'class' => 'form-check-input form-check-radio',
                                                            'id' => "send_type_{$key}_{$i}",
                                                            'data-name' => 'send_type'
                                                        ]
                                                    ) }}
                                                    <label for="send_type_{{ $key }}_{{ $i }}" class="form-check-label">{{ $send_type }}</label>
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
                                            <small class="text-secondary">
                                                キーワードが含まれるメッセージを受信したときにのみ自動で返信します。<br>設定しなければ、前のメッセージに続いて送信されます。<br>
                                            </small>
                                            <div>
                                                {{ Form::text(
                                                    "situation[messages][{$i}][keyword]",
                                                    old("situation.messages.{$i}.keyword", $template->keyword),
                                                    [
                                                        'class' => 'form-control',
                                                        'maxlength' => 50,
                                                        'data-name' =>'keyword',
                                                        old('situation.event_type', $situation->event_type) != 2 ? 'disabled' : ''
                                                    ]
                                                ) }}
                                                <span class="text-danger">{{ $errors->first("situation.messages.{$i}.keyword") }}</span>
                                            </div>
                                        </div>
                                        <div class="type-text {{ old("situation.messages.{$i}.message_type", $template->message_type) == 'buttons' ? 'd-none' : '' }}">
                                            <div>
                                                <div>
                                                    <label class="form-label">メッセージ内容</label>
                                                    <span class="text-danger">※</span>
                                                    <small class="text-secondary ms-2">最大文字数：5000</small>
                                                </div>
                                                <div>
                                                    <textarea maxlength="5000" name="situation[messages][{{ $i }}][text]" class="form-control" data-name="text">{{ old("situation.messages.{$i}.text", $template->text) }}</textarea>
                                                    <span class="text-danger">{{ $errors->first("situation.messages.{$i}.text") }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="type-template {{ old("situation.messages.{$i}.message_type", $template->message_type) == 'text' ? 'd-none' : '' }}">
                                            <div>
                                                <div>
                                                    <label class="form-label">代替テキスト</label>
                                                    <span class="text-danger">※</span>
                                                    <small class="text-secondary ms-2">最大文字数：400</small>
                                                </div>
                                                <small class="text-secondary">相手がメッセージを受信した際に、端末の通知やトークリストでメッセージの代替として表示されます。<br>例）新着メッセージを受信しました。</small>
                                                <div class="col-6">
                                                    <input type="text" maxlength="400" name="situation[messages][{{ $i }}][alt_text]" class="form-control" data-name="alt_text" value="{{ old("situation.messages.{$i}.alt_text", $template->alt_text ?? '') }}">
                                                    <span class="text-danger">{{ $errors->first("situation.messages.{$i}.alt_text") }}</span>
                                                </div>
                                            </div>
                                            <div>
                                                <div>
                                                    <label class="form-label">サムネイル画像</label>
                                                    <span class="text-secondary">(任意)</span>
                                                    <small class="text-secondary ms-2">jpgまたはpng、最大横幅サイズ：1024px、最大ファイルサイズ：10MB</small>
                                                </div>
                                                <div>
                                                    @if ($template->thumbnail_image_url)
                                                    <img src="{{ asset('storage/'.strstr($template->thumbnail_image_url, 'template')) }}" class="img-thumbnail mb-2">
                                                    @endif
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="col-6">
                                                            <input type="file" accept=".jpeg, .jpg, .png" name="situation[messages][{{ $i }}][thumbnail_image_url]" class="form-control" data-name="thumbnail_image_url" {{ $template->thumbnail_image_url ? 'disabled' : '' }}>
                                                        </div>
                                                        @if ($template->thumbnail_image_url)
                                                        <input type="hidden" name="situation[messages][{{ $i }}][delete_turns][]" value="{{ $template->turn }}" data-name="delete_turns" disabled>
                                                        {{ Form::checkbox(
                                                            '',
                                                            1,
                                                            '',
                                                            [
                                                                'id' => "isChange_{$i}",
                                                                'class' => 'form-checkbox change-img'
                                                            ]
                                                        ) }}
                                                        <label for="isChange_{{ $i }}" class="form-check-label">変更する</label>
                                                        @endif
                                                    </div>
                                                    <span class="text-danger">{{ $errors->first("situation.messages.{$i}.thumbnail_image_url") }}</span>
                                                </div>
                                            </div>
                                            <div>
                                                <div>
                                                    <label class="form-label">タイトル</label>
                                                    <span class="text-secondary">(任意)</span>
                                                    <small class="text-secondary ms-2">最大文字数：40</small>
                                                </div>
                                                <div class="col-6">
                                                    <input type="text" maxlength="40" name="situation[messages][{{ $i }}][title]" class="form-control" data-name="title" value="{{ old("situation.messages.{$i}.title", $template->title) }}">
                                                    <span class="text-danger">{{ $errors->first("situation.messages.{$i}.title") }}</span>
                                                </div>
                                            </div>
                                            <div>
                                                <div>
                                                    <label class="form-label">メッセージ内容</label>
                                                    <span class="text-danger">※</span>
                                                    <small class="text-secondary ms-2">最大文字数：120、画像またはタイトルを指定する場合の最大文字数：60</small>
                                                </div>
                                                <div>
                                                    <textarea maxlength="120" name="situation[messages][{{ $i }}][text]" class="form-control" data-target-accordion="#" data-name="text">{{ old("situation.messages.{$i}.text", $template->text) }}</textarea>
                                                    <span class="text-danger">{{ $errors->first("situation.messages.{$i}.text") }}</span>
                                                </div>
                                            </div>
                                            <div>
                                                <div>
                                                    <label class="form-label">ボタン</label>
                                                    <span class="text-danger">※</span>
                                                </div>
                                                <small class="text-secondary">
                                                    ラベルに表示したいボタンの名前、アクションに送信させたい文字または、遷移させたいURIを入力ください。<br>
                                                    メッセージ・・・ユーザがボタンをタップすると、文字が送信されます。<br>
                                                    リンク・・・ユーザがボタンをタップすると、リンクのページに遷移します。
                                                </small>
                                                <ol class="ol-labels d-flex gap-5">
                                                    @php
                                                        $count = 0;
                                                    @endphp
                                                    @foreach (range(0, 3) as $index)
                                                    <li>
                                                        <div class="radio-group d-flex flex-column">
                                                            @foreach (config('situation.action_type') as $key => $type)
                                                            @php
                                                                $action = null;
                                                                if (isset($template->messageActions[$index])) {
                                                                    $action = $template->messageActions[$index];
                                                                }
                                                            @endphp
                                                            <div>
                                                                {{ Form::radio(
                                                                    "situation[messages][{$i}][actions][{$index}][type]",
                                                                    $key,
                                                                    old("situation.messages.{$i}.actions.{$index}.type", $action?->action_type) == $key || $loop->first ? 'checked' : '',
                                                                    [
                                                                        'class' => 'form-check-input form-check-radio',
                                                                        'id' => "action_{$i}_{$count}",
                                                                        'data-name' => 'actions'
                                                                    ]
                                                                ) }}
                                                                <label for="action_{{ $i }}_{{ $count++ }}" class="form-check-label">{{ $type }}</label>
                                                            </div>
                                                            @endforeach
                                                        </div>
                                                        <div>
                                                            <label>ラベル</label>
                                                            {{ Form::text(
                                                                "situation[messages][{$i}][actions][{$index}][label]",
                                                                old("situation.messages.{$i}.actions.{$index}.label", $action?->label),
                                                                [
                                                                    'class' => 'form-control',
                                                                    'data-name' => 'actions-label'
                                                                ]
                                                            ) }}
                                                        </div>
                                                        <div>
                                                            <label>アクション</label>
                                                            {{ Form::text(
                                                                "situation[messages][{$i}][actions][{$index}][trigger]",
                                                                old("situation.messages.{$i}.actions.{$index}.trigger", $action?->action),
                                                                [
                                                                    'class' => 'form-control',
                                                                    'data-name' => 'actions-trigger'
                                                                ]
                                                            ) }}
                                                        </div>
                                                    </li>
                                                    @endforeach
                                                </ol>
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
