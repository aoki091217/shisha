<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MemberRequest;
use App\Repositories\MemberRepository;
use App\Repositories\ShopRepository;
use App\Services\SessionService;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function __construct(
        private ShopRepository $shopRepository,
        private MemberRepository $memberRepository,
        private SessionService $sessionService
    ) {}

    public function index(Request $request)
    {
        $shops = $this->shopRepository->get();
        $members = $this->memberRepository->relate()->search($request->member)->paginate();
        return view('member.index', compact('shops', 'members'));
    }

    public function create()
    {
        $shops = $this->shopRepository->get();
        return view('member.create', compact('shops'));
    }

    public function store(MemberRequest $request)
    {
        $this->memberRepository->store($request->member);
        $this->sessionService->putFlashMessage(config('const.session.flash.stored'));
        return redirect()->route('member.index');
    }

    public function edit($id)
    {
        $shops = $this->shopRepository->get();
        $member = $this->memberRepository->relate()->find($id);
        return view('member.edit', compact('shops', 'member'));
    }

    public function update(MemberRequest $request, $id)
    {
        $this->memberRepository->update($request->member, $id);
        $this->sessionService->putFlashMessage(config('const.session.flash.updated'));
        return redirect()->route('member.index');
    }

    public function destroy($id)
    {
        $this->memberRepository->delete($id);
        $this->sessionService->putFlashMessage(config('const.session.flash.deleted'));
        return redirect()->route('member.index');
    }
}
