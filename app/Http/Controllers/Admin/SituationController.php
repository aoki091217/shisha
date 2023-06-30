<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SituationRequest;
use App\Models\Carousel;
use App\Models\CarouselAction;
use App\Models\Message;
use App\Models\Situation;
use App\Services\LineBotService;
use App\Services\MonitoringService;
use DB;
use Illuminate\Http\Request;
use Route;
use Storage;

class SituationController extends Controller
{
    public function __construct(
        private LineBotService $lineBotService,
        private MonitoringService $monitoringService
    ){}

    public function index(Request $request)
    {
        $situations = Situation::with('messages')->paginate();
        return view('situation.index', compact('situations'));
    }

    public function create()
    {
        return view('situation.create');
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
                            $filePath = null;
                            if ($request->file("situation.messages.{$templateIndex}.carousels.{$carouselIndex}.thumbnail_image_url")) {
                                $dir = "template/{$situation->id}/{$template['turn']}/{$carouselIndex}";

                                $file_name = $request->file("situation.messages.{$templateIndex}.carousels.{$carouselIndex}.thumbnail_image_url")->getClientOriginalName();

                                $request->file("situation.messages.{$templateIndex}.carousels.{$carouselIndex}.thumbnail_image_url")->storeAs("public/{$dir}", $file_name);

                                $filePath = sprintf('%s/%s', $dir, $file_name);
                                Storage::disk('public')->url($filePath);
                            }

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
        $situation = Situation::with('messages.carousels.carouselActions')->find($id);
        if (is_null($situation)) {
            $current = strstr(Route::currentRouteName(), '.', true);
            return redirect()->route("{$current}.index");
        }

        return view('situation.edit', compact('situation'));
    }

    public function update(SituationRequest $request, $id)
    {
        DB::transaction(function () use ($request, $id) {
            $situation = Situation::with('messages.carousels.carouselActions')->find($id);
            $situation->fill($request->situation)->save();

            foreach ($request->situation['messages'] as $templateIndex => $template) {
                if ($template['send_type'] == 'push') {
                    $sendType = 1;
                } else {
                    $sendType = 2;
                }

                $insert = array_merge($template, ['situation_id' => $situation->id], ['send_type' => $sendType]);
                $storedMessage = $situation->messages->where('turn', $template['turn'])->first();
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
                            if (!is_null($storedMessage?->carousels)) {
                                $storedCarousels = $storedMessage->carousels;
                                foreach ($storedCarousels as $storedCarousel) {
                                    $storedCarousel->delete();
                                }
                            }

                            $filePath = null;
                            if ($request->file("situation.messages.{$templateIndex}.carousels.{$carouselIndex}.thumbnail_image_url")) {
                                $dir = "template/{$situation->id}/{$template['turn']}/{$carouselIndex}";

                                $file_name = $request->file("situation.messages.{$templateIndex}.carousels.{$carouselIndex}.thumbnail_image_url")->getClientOriginalName();

                                $request->file("situation.messages.{$templateIndex}.carousels.{$carouselIndex}.thumbnail_image_url")->storeAs("public/{$dir}", $file_name);

                                $filePath = sprintf('%s/%s', $dir, $file_name);
                                Storage::disk('public')->url($filePath);
                            } elseif (isset($carousel['thumbnail_image_url']) && !is_null($carousel['thumbnail_image_url'])) {
                                $filePath = $carousel['thumbnail_image_url'];
                            }

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

                if (!is_null($storedMessage)) {
                    $storedMessage->delete();
                }

                $situation->messages->where('turn', '<>', $template['turn'])->first()?->delete();
            }
        });

        return redirect()->route('situation.index');
    }

    public function destroy($id)
    {
        $situation = Situation::with('messages')->find($id);
        if (is_null($situation)) {
            $current = strstr(Route::currentRouteName(), '.', true);
            return redirect()->route("{$current}.index");
        }

        $situation->delete();
        return redirect()->route('situation.index');
    }
}
