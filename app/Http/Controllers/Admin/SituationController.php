<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SituationRequest;
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

            foreach ($request->situation['messages'] as $index => $template) {
                if ($template['send_type'] == 'push') {
                    $sendType = 1;
                } else {
                    $sendType = 2;
                }

                $insert = array_merge($template, ['situation_id' => $situation->id], ['send_type' => $sendType]);
                $actions = [];
                switch ($template['message_type']) {
                    case 'text':
                        $insert = array_merge($insert, ['type' => 1]);

                        $message = new Message();
                        $message->fill($insert)->save();
                        break;
                    case 'buttons':
                        $filePath = null;
                        if ($request->file("situation.messages.{$index}.thumbnail_image_url")) {
                            $dir = "template/{$situation->id}/{$template['turn']}";
                            $file_name = $request->file("situation.messages.{$index}.thumbnail_image_url")->getClientOriginalName();

                            $request->file("situation.messages.{$index}.thumbnail_image_url")->storeAs("public/{$dir}", $file_name);

                            $filePath = sprintf('%s/%s', $dir, $file_name);
                            Storage::disk('public')->url($filePath);
                        }

                        $insert = array_merge($insert, [
                            'type' => 2,
                            'thumbnail_image_url' => $filePath
                        ]);

                        $message = new Message();
                        $message->fill($insert)->save();

                        foreach ($template['actions'] as $action) {
                            switch ($action['type']) {
                                case 'message':
                                    $type = 1;
                                    break;
                                case 'uri':
                                    $type = 2;
                                    break;
                                default:
                                    $type = 0;
                                    break;
                            }

                            $actions[] = [
                                'type' => $type,
                                'label' => $action['label'],
                                'action' => $action['trigger'],
                            ];
                        }

                        $message->messageActions()->createMany($actions);
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
        $situation = Situation::with('messages.messageActions')->find($id);
        if (is_null($situation)) {
            $current = strstr(Route::currentRouteName(), '.', true);
            return redirect()->route("{$current}.index");
        }

        return view('situation.show', compact('situation'));
    }

    public function edit($id)
    {
        $situation = Situation::with('messages.messageActions')->find($id);
        if (is_null($situation)) {
            $current = strstr(Route::currentRouteName(), '.', true);
            return redirect()->route("{$current}.index");
        }

        return view('situation.edit', compact('situation'));
    }

    public function update(SituationRequest $request, $id)
    {
        DB::transaction(function () use ($request, $id) {
            $situation = Situation::with('messages.messageActions')->find($id);
            $situation->fill($request->situation)->save();

            foreach ($request->situation['messages'] as $index => $template) {
                if ($template['send_type'] == 'push') {
                    $sendType = 1;
                } else {
                    $sendType = 2;
                }

                $insert = array_merge($template, ['situation_id' => $situation->id], ['send_type' => $sendType]);
                $storedMessage = $situation->messages->where('turn', $template['turn'])->first();

                $actions = [];
                switch ($template['message_type']) {
                    case 'text':
                        $insert = array_merge($insert, [
                            'type' => 1,
                            'send_type' => $sendType,
                            'situation_id' => $situation->id
                        ]);
                        break;
                    case 'buttons':
                        $filePath = $storedMessage?->thumbnail_image_url;

                        if ($request->file("situation.messages.{$index}.thumbnail_image_url")) {
                            if ($storedMessage?->thumbnail_image_url) {
                                Storage::disk('public')->delete($storedMessage->thumbnail_image_url);
                            }

                            $dir = "template/{$situation->id}/{$template['turn']}";
                            $file_name = $request->file("situation.messages.{$index}.thumbnail_image_url")->getClientOriginalName();

                            $request->file("situation.messages.{$index}.thumbnail_image_url")->storeAs("public/{$dir}", $file_name);

                            Storage::disk('public')->url("{$dir}/{$file_name}");

                            $filePath = sprintf('storage/%s/%s', $dir, $file_name);
                        }

                        $insert = array_merge($insert, [
                            'type' => 2,
                            'thumbnail_image_url' => $filePath
                        ]);

                        if (!is_null($storedMessage)) {
                            $storedMessage->messageActions()->delete();
                        }

                        foreach ($template['actions'] as $action) {
                            switch ($action['type']) {
                                case 'message':
                                    $type = 1;
                                    break;
                                case 'uri':
                                    $type = 2;
                                    break;
                                default:
                                    $type = 0;
                                    break;
                            }

                            $actions[] = [
                                'type' => $type,
                                'label' => $action['label'],
                                'action' => $action['trigger'],
                            ];
                        }
                        break;
                    default:
                        break;
                }

                if (!is_null($storedMessage)) {
                    $storedMessage->delete();
                }

                $situation->messages->where('turn', '<>', $template['turn'])->first()?->delete();

                $message = new Message();
                $message->fill($insert)->save();

                if ($message->type == 2) {
                    $actions = collect($actions)->map(function ($action) use ($message) {
                        return array_merge($action, ['message_id' => $message->id]);
                    });

                    $message->messageActions()->createMany($actions);
                }
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
