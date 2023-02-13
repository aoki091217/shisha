<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BillRequest;
use App\Repositories\BillRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\FlavorRepository;
use App\Repositories\ShopRepository;
use App\Services\SessionService;
use Illuminate\Http\Request;

class BillController extends Controller
{
    public function __construct(
        private BillRepository $billRepository,
        private FlavorRepository $flavorRepository,
        private ShopRepository $shopRepository,
        private CustomerRepository $customerRepository,
        private SessionService $sessionService
    ) {}

    public function index(Request $request)
    {
        $shops = $this->shopRepository->get();
        $bills = $this->billRepository->relate(['shop', 'member'])->search($request->bill)->paginate();
        return view('bill.index', compact('bills', 'shops'));
    }

    public function create()
    {
        $shops = $this->shopRepository->relate()->get();
        $customers = $this->customerRepository->get();
        $flavors = $this->flavorRepository->get();
        return view('bill.create', compact('shops', 'customers', 'flavors'));
    }

    public function store(BillRequest $request)
    {
        $this->billRepository->store($request->bill);
        $this->sessionService->putFlashMessage(config('const.session.flash.stored'));
        return redirect()->route('bill.index');
    }

    public function show($id)
    {
        $bill = $this->billRepository->relate(['shop', 'member', 'customer', 'billOrders.flavor'])->find($id);
        return view('bill.show', compact('bill'));
    }

    public function edit($id)
    {
        $shops = $this->shopRepository->relate()->get();
        $customers = $this->customerRepository->get();
        $flavors = $this->flavorRepository->get();
        $bill = $this->billRepository->relate(['shop', 'member', 'customer', 'billOrders.flavor'])->find($id);
        $grouped_orders = $bill->billOrders->groupBy('order_id');
        return view('bill.edit', compact('shops', 'customers', 'flavors', 'bill', 'grouped_orders'));
    }

    public function update(BillRequest $request, $id)
    {
        $this->billRepository->update($request->bill, $id);
        $this->sessionService->putFlashMessage(config('const.session.flash.updated'));
        return redirect()->route('bill.index');
    }

    public function destroy($id)
    {
        $this->billRepository->delete($id);
        $this->sessionService->putFlashMessage(config('const.session.flash.deleted'));
        return redirect()->route('bill.index');
    }

    public function getMembers(Request $request)
    {
        $shop = $this->shopRepository->relate()->find($request->shop_id);
        $members = $this->shopRepository->getMembers($shop);
        return response()->json($members);
    }
}
