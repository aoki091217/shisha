<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BillRequest;
use App\Repositories\BillRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\CustomerShopRepository;
use App\Repositories\MixRepository;
use App\Repositories\ShopRepository;
use App\Services\SessionService;
use Illuminate\Http\Request;

class BillController extends Controller
{
    public function __construct(
        private BillRepository $billRepository,
        private MixRepository $mixRepository,
        private ShopRepository $shopRepository,
        private CustomerRepository $customerRepository,
        private CustomerShopRepository $customerShopRepository,
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
        $customerShops = $this->customerShopRepository->relate()->orderByDesc('visited_at')->get()->groupBy('customer_id');
        $mixPresets = $this->mixRepository->relate()->get();
        return view('bill.create', compact('shops', 'customerShops', 'mixPresets'));
    }

    public function store(BillRequest $request)
    {
        $this->billRepository->store($request->bill);
        $this->sessionService->putFlashMessage(config('const.session.flash.stored'));
        return redirect()->route('bill.index');
    }

    public function show($id)
    {
        $bill = $this->billRepository->relate(['shop', 'member', 'billCustomers.customer', 'billOrders.mix'])->find($id);
        return view('bill.show', compact('bill'));
    }

    public function edit($id)
    {
        $shops = $this->shopRepository->relate()->get();
        $customerShops = $this->customerShopRepository->relate()->orderByDesc('visited_at')->get()->groupBy('customer_id');
        $mixPresets = $this->mixRepository->relate()->get();
        $bill = $this->billRepository->relate(['shop', 'member', 'billCustomers.customer', 'billOrders.mix'])->find($id);

        return view('bill.edit', compact('shops', 'customerShops', 'mixPresets', 'bill'));
    }

    public function update(BillRequest $request, $id)
    {
        $this->billRepository->update($request->bill, $id);
        $this->sessionService->putFlashMessage(config('const.session.flash.updated'));
        return redirect()->route('bill.index');
    }

    public function draft(BillRequest $request)
    {
        $this->billRepository->draft($request->bill);
        $this->sessionService->putFlashMessage(config('const.session.flash.drafted'));
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

    public function getCustomers(Request $request)
    {
        $customerShops = $this->customerShopRepository->relate()->search($request->search)->orderByDesc('visited_at')->get()
            ->groupBy('customer_id')
            ->map(function ($item) {
                return $item->first();
            });
        return response()->json($customerShops);
    }
}
