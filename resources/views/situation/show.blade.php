@extends('layouts.parent')

@push('css')
@vite('resources/css/situation.css')
@endpush

@section('content')
{{ Breadcrumbs::render('situation.show', $situation->id) }}
<div class="d-flex flex-wrap">
    <div class="col-6 mb-3 pe-2">
        <label class="form-label">シチュエーション名</label>
        <div>{{ $situation->name }}</div>
    </div>
    <div class="col-6 mb-3 ps-2">
        <label class="form-label"> 受信イベント</label>
        <div>{{ $situation->receive_event }}</div>
    </div>
    <div class="col-12 mb-3">
        <div class="accordion mb-3" id="accordionMessages">
            @foreach ($situation->messages as $i => $message)
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#message_{{ $i }}">
                        メッセージ{{ $loop->iteration }}
                    </button>
                </h2>
                <div id="message_{{ $i }}" class="accordion-collapse collapse show">
                    <div class="accordion-body">
                        <div class="d-flex">
                            <div class="col-8 d-flex flex-column gap-3 pe-4">
                                <div>
                                    <label class="form-label">メッセージタイプ</label>
                                    <div>{{ config("situation.message_type.{$message->message_type}") }}</div>
                                </div>
                                <div>
                                    <label class="form-label">送信タイプ</label>
                                    <div>{{ config("situation.send_type.{$message->message_send_type}") }}</div>
                                </div>
                                <div>
                                    <label class="form-label">応答キーワード</label>
                                    <div>{{ $message->keyword ?? 'なし' }}</div>
                                </div>
                                @if ($message->message_type == 'text')
                                <div>
                                    <label class="form-label">メッセージ内容</label>
                                    <div>{{ $message->text }}</div>
                                </div>
                                @elseif ($message->message_type == 'carousel')
                                <div class="type-template">
                                    <div>
                                        <label class="form-label">代替テキスト</label>
                                        <div>{{ $message->alt_text }}</div>
                                    </div>
                                    <div>
                                        <div>
                                            <label class="form-label">カルーセル</label>
                                            <div class="carousel-group">
                                                @foreach ($message->carousels as $carousel)
                                                <div class="card">
                                                    {{-- @if ($carousel->thumbnail_image_url)
                                                    <div class="card-img-top preview-img">
                                                        <img class="" src="{{ asset("storage/{$carousel->thumbnail_image_url}") }}" class="img-thumbnail">
                                                    </div>
                                                    @else
                                                    <label class="card-img-top sample-img">
                                                        <span>画像<span class="ms-1">(任意)</span></span>
                                                        <ul>
                                                            <li>拡張子：jpg, png</li>
                                                            <li>最大横幅：1024px</li>
                                                            <li>最大サイズ：10MB</li>
                                                        </ul>
                                                    </label>
                                                    @endif --}}
                                                    <div class="card-body d-grid gap-2">
                                                        <div>
                                                            <label class="form-label mb-0">タイトル</label>
                                                            <div>{{ $carousel->title ?? 'なし' }}</div>
                                                        </div>
                                                        <div>
                                                            <label class="form-label mb-0">メッセージ内容</label>
                                                            <div>{{ $carousel->text }}</div>
                                                        </div>
                                                        <div>
                                                            <label class="form-label mb-0">ボタン</label>
                                                            <ol class="ol-labels d-flex gap-5">
                                                                @foreach ($carousel->carouselActions as $carouselAction)
                                                                <li style="font-size: 0.8rem;">
                                                                    {{ $carouselAction->action }}
                                                                </li>
                                                                @endforeach
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
<div class="d-flex align-items-center justify-content-end mt-3 footer-buttons gap-2">
    <a href="{{ route('situation.index') }}" class="btn btn-secondary">戻る</a>
    <a href="{{ route('situation.edit', $situation->id) }}" class="btn btn-success">編集</a>
    <button type="button" class="btn btn-danger" id="deleteButton" data-route="{{ route('situation.destroy', $situation->id) }}" data-bs-toggle="modal" data-bs-target="#deleteModal">
        削除
    </button>
</div>

@push('jquery')
@vite('resources/js/situation.js')
<script defer>
    window.errors = @json($errors->get('situation'));
    window.situationOld = @json(old('situation'));
</script>
@endpush

@endsection
