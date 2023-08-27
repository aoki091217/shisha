<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SituationRequest;
use App\Models\Carousel;
use App\Models\CarouselAction;
use App\Models\Message;
use App\Models\Situation;
use App\Repositories\ShopRepository;
use App\Services\LineBotService;
use App\Services\SessionService;
use DB;
use Illuminate\Http\Request;
use Route;
use Storage;

class SituationController extends Controller
{
    public function __construct(
        private LineBotService $lineBotService,
        private ShopRepository $shopRepository,
        private SessionService $sessionService
    ) {}

    public function index(Request $request)
    {
        if (auth()->user()->role_id === 1) {
            $situations = Situation::with('messages')->paginate();
        } else {
            $situations = Situation::with('messages')->where('shop_id', auth()->user()->member->shop_id)->paginate();
        }

        return view('situation.index', compact('situations'));
    }

    public function create()
    {
        $shops = $this->shopRepository->get();

        return view('situation.create', compact('shops'));
    }

    public function store(SituationRequest $request)
    {
        DB::transaction(function () use ($request) {
            $situation = new Situation();
            $situation->fill($request->situation)->save();

            foreach ($request->situation['messages'] as $templateIndex => $template) {
                if ($template['send_type'] == 'push') {
                    $sendType = 1;
                } else {
                    $sendType = 2;
                }

                $insert = array_merge($template, ['situation_id' => $situation->id], ['send_type' => $sendType]);
                switch ($template['message_type']) {
                    case 'text':
                        $insert = array_merge($insert, ['type' => 1]);

                        $message = new Message();
                        $message->fill($insert)->save();
                        break;
                    case 'carousel':
                        $insert = array_merge($insert, ['type' => 2]);

                        $message = new Message();
                        $message->fill($insert)->save();

                        foreach ($template['carousels'] as $carouselIndex => $carousel) {
                            $newCarousel = new Carousel();

                            $filePath = null;
                            // if ($request->file("situation.messages.{$templateIndex}.carousels.{$carouselIndex}.thumbnail_image_url")) {
                            //     $dir = "template/{$situation->id}/{$message->id}/{$carouselIndex}";

                            //     $file_name = $request->file("situation.messages.{$templateIndex}.carousels.{$carouselIndex}.thumbnail_image_url")->getClientOriginalName();

                            //     $request->file("situation.messages.{$templateIndex}.carousels.{$carouselIndex}.thumbnail_image_url")->storeAs("public/{$dir}", $file_name);

                            //     $filePath = sprintf('%s/%s', $dir, $file_name);
                            //     Storage::disk('public')->url($filePath);
                            // }

                            $insert = array_merge($insert, [
                                'message_id' => $message->id,
                                'thumbnail_image_url' => $filePath,
                                'title' => $carousel['title'],
                                'text' => $carousel['text']
                            ]);

                            $newCarousel->fill($insert)->save();

                            foreach ($carousel['actions'] as $action) {
                                if (is_null($action['action'])) continue;

                                $carouselAction = new CarouselAction();
                                $carouselAction->fill(array_merge($action, ['carousel_id' => $newCarousel->id]))->save();
                            }
                        }
                        break;
                    default:
                        break;
                }
            }
        });

        $this->sessionService->putFlashMessage(config('const.session.flash.stored'));

        return redirect()->route('situation.index');
    }

    public function show($id)
    {
        $situation = Situation::with('messages.carousels.carouselActions')->find($id);
        if (is_null($situation)) {
            $current = strstr(Route::currentRouteName(), '.', true);
            return redirect()->route("{$current}.index");
        }

        return view('situation.show', compact('situation'));
    }

    public function edit($id)
    {
        $shops = $this->shopRepository->get();

        $situation = Situation::with('messages.carousels.carouselActions')->find($id);
        if (is_null($situation)) {
            $current = strstr(Route::currentRouteName(), '.', true);
            return redirect()->route("{$current}.index");
        }

        return view('situation.edit', compact('situation', 'shops'));
    }

    public function update(SituationRequest $request, $id)
    {
        DB::transaction(function () use ($request, $id) {
            $situation = Situation::with('messages.carousels.carouselActions')->find($id);
            $situation->fill($request->situation)->save();

            foreach ($situation->messages as $deleteMessage) {
                foreach ($deleteMessage->carousels as $deleteCarousel) {
                    $deleteCarousel->carouselActions()->delete();
                }
                $deleteMessage->carousels()->delete();
                $deleteMessage->delete();
            }

            foreach ($request->situation['messages'] as $templateIndex => $template) {
                if ($template['send_type'] == 'push') {
                    $sendType = 1;
                } else {
                    $sendType = 2;
                }

                $insert = array_merge($template, ['situation_id' => $situation->id], ['send_type' => $sendType]);
                switch ($template['message_type']) {
                    case 'text':
                        $insert = array_merge($insert, ['type' => 1]);

                        $message = new Message();
                        $message->fill($insert)->save();
                        break;
                    case 'carousel':
                        $insert = array_merge($insert, ['type' => 2]);

                        $message = new Message();
                        $message->fill($insert)->save();

                        foreach ($template['carousels'] as $carouselIndex => $carousel) {
                            $filePath = null;
                            // if ($request->file("situation.messages.{$templateIndex}.carousels.{$carouselIndex}.thumbnail_image_url")) {
                            //     $dir = "template/{$situation->id}/{$message->id}/{$carouselIndex}";

                            //     $file_name = $request->file("situation.messages.{$templateIndex}.carousels.{$carouselIndex}.thumbnail_image_url")->getClientOriginalName();

                            //     $request->file("situation.messages.{$templateIndex}.carousels.{$carouselIndex}.thumbnail_image_url")->storeAs("public/{$dir}", $file_name);

                            //     $filePath = sprintf('%s/%s', $dir, $file_name);
                            //     Storage::disk('public')->url($filePath);
                            // }

                            $insert = array_merge($insert, [
                                'message_id' => $message->id,
                                'thumbnail_image_url' => $filePath,
                                'title' => $carousel['title'],
                                'text' => $carousel['text']
                            ]);

                            $newCarousel = new Carousel();
                            $newCarousel->fill($insert)->save();

                            foreach ($carousel['actions'] as $action) {
                                if (is_null($action['action'])) continue;

                                $carouselAction = new CarouselAction();
                                $carouselAction->fill(array_merge($action, ['carousel_id' => $newCarousel->id]))->save();
                            }
                        }
                        break;
                    default:
                        break;
                }
            }
        });

        $this->sessionService->putFlashMessage(config('const.session.flash.updated'));

        return redirect()->route('situation.index');
    }

    public function destroy($id)
    {
        $situation = Situation::with('messages.carousels.carouselActions')->find($id);
        if (is_null($situation)) {
            $current = strstr(Route::currentRouteName(), '.', true);
            return redirect()->route("{$current}.index");
        }

        foreach ($situation->messages as $message) {
            foreach ($message->carousels as $carousel) {
                $carousel->delete();
            }
        }

        $situation->delete();
        return redirect()->route('situation.index');
    }
}
