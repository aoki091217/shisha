<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShopRequest;
use App\Repositories\RoleRepository;
use App\Repositories\ShopRepository;
use App\Repositories\UserRepository;
use App\Services\SessionService;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function __construct(
        private ShopRepository $shopRepository,
        private UserRepository $userRepository,
        private RoleRepository $roleRepository,
        private SessionService $sessionService
    ) {}

    public function index(Request $request)
    {
        $shops = $this->shopRepository->relate()->search($request->shop)->paginate();
        return view('shop.index', compact('shops'));
    }

    public function create()
    {
        return view('shop.create');
    }

    public function store(ShopRequest $request)
    {
        $this->userRepository->store($request->user);
        $user = $this->userRepository->find($request->user['code']);
        $this->shopRepository->store(array_merge($request->shop, ['user_id' => $user->id]));
        $this->sessionService->putFlashMessage(config('const.session.flash.stored'));

        return redirect()->route('shop.index');
    }

    public function edit($id)
    {
        $shop = $this->shopRepository->relate()->find($id);
        return view('shop.edit', compact('shop'));
    }

    public function update(ShopRequest $request, $id)
    {
        $this->userRepository->update($request->user, $request->user['user_id']);
        $this->shopRepository->update($request->shop, $id);
        $this->sessionService->putFlashMessage(config('const.session.flash.updated'));
        return redirect()->route('shop.index');
    }

    public function destroy($id)
    {
        $this->shopRepository->delete($id);
        $this->sessionService->putFlashMessage(config('const.session.flash.deleted'));
        return redirect()->route('shop.index');
    }
}
