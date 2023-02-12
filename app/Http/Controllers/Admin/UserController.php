<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Repositories\UserRepository;
use App\Services\SessionService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(
        private UserRepository $userRepository,
        private SessionService $sessionService
    ){}

    public function index(Request $request)
    {
        $users = $this->userRepository->search($request->user)->paginate();
        return view('user.index', compact('users'));
    }

    public function create()
    {
        return view('user.create');
    }

    public function store(UserRequest $request)
    {
        $this->userRepository->store($request->user);
        $this->sessionService->putFlashMessage(config('const.session.flash.stored'));
        return redirect()->route('user.index');
    }

    public function edit($code)
    {
        $user = $this->userRepository->find($code);
        return view('user.edit', compact('user'));
    }

    public function update(UserRequest $request, $code)
    {
        $this->userRepository->update($request->user, $code);
        $this->sessionService->putFlashMessage(config('const.session.flash.updated'));
        return redirect()->route('user.index');
    }

    public function destroy($code)
    {
        $this->userRepository->delete($code);
        $this->sessionService->putFlashMessage(config('const.session.flash.deleted'));
        return redirect()->route('user.index');
    }
}
