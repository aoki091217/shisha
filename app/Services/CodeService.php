<?php

namespace App\Services;

use App\Models\Code;
use App\Repositories\CodeRepository;
use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;

class CodeService
{
    public function __construct(
        private CodeRepository $codeRepository
    ){}

    public function get(array $request)
    {
        $code = isset($request['code']) ? $request['code'] : [];

        return Code::with(['shop', 'situation'])->search($code)->paginate();
    }

    public function getEncodeStr(string $string):string
    {
        return Hashids::encode($string);
    }

    public function getHashedUrl(int $codeId)
    {
        $domain = app()->isProduction() ? config('app.url') . '/shisha' : config('app.ngrok');

        $code = Code::find($codeId);
        $hash = $code->getHash();
        $kind = $code->getKind();
        // TODO: `route()`を使ってURLを作るようにする
        $prefix = $kind == 1 ? $domain . "/line/liff/{$code->shop_id}/code" : $domain . '/line/checkin';

        return $prefix . '?' . http_build_query(['hash' => $hash]);
    }

    public function getCheckinDecode(Request $request): array
    {
        $codeStr = collect($request->all())->keys()->first();
        $code = Code::where('hash', $codeStr)->first();
        if (is_null($code)) {
            return [];
        }

        return $this->getParams($code->getParameter());
    }

    private function getParams(string $parameter): array
    {
        $params = [];
        foreach (explode('&', $parameter) as $param) {
            $values = explode('=', $param);
            $params[$values[0]] = $values[1];
        }

        return $params;
    }
}
