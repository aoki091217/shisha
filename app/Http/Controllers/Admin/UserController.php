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

    public function edit($id)
    {
        $user = $this->userRepository->find($id);
        return view('user.edit', compact('user'));
    }

    public function update(UserRequest $request, $id)
    {
        $this->userRepository->update($request->user, $id);
        $this->sessionService->putFlashMessage(config('const.session.flash.updated'));
        return redirect()->route('user.index');
    }

    public function destroy($id)
    {
        if (auth()->user()->id == $id) {
            $this->userRepository->delete($id);
            $this->sessionService->putFlashMessage(config('const.session.flash.deleted'));
        } else {
            $this->sessionService->putFlashMessage(config('const.session.flash.auth'));
        }
        return redirect()->route('user.index');
    }
}
