<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BillRequest;
use App\Models\Situation;
use App\Repositories\BillRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\CustomerShopRepository;
use App\Repositories\MemberRepository;
use App\Repositories\MixRepository;
use App\Repositories\ShopRepository;
use App\Services\LineBotService;
use App\Services\SessionService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class BillController extends Controller
{
    public function __construct(
        private BillRepository $billRepository,
        private MixRepository $mixRepository,
        private ShopRepository $shopRepository,
        private MemberRepository $memberRepository,
        private CustomerRepository $customerRepository,
        private CustomerShopRepository $customerShopRepository,
        private SessionService $sessionService
    ) {}

    public function index(Request $request)
    {
        $shops = $this->shopRepository->get();
        $bills = $this->billRepository->relate(['shop', 'member'])->orderByDesc('bill_date')->search($request->bill)->paginate();

        return view('bill.index', compact('bills', 'shops'));
    }

    public function create()
    {
        $shops = $this->shopRepository->relate()->get();
        $members = $this->memberRepository->get();
        $mixPresets = $this->mixRepository->relate()->get();
        $customerShops = $this->customerShopRepository->relate()->orderByDesc('visited_at')->get();

        $latests = new Collection();
        foreach ($customerShops->groupBy('customer_id') as $i => $customerShop) {
            $latests = $latests->put($i, $customerShop->first());
        }

        if (auth()->user()->role_id !== 1) {
            $shops = $shops->where('shop_id', auth()->user()->member->shop_id);
            $mixPresets = $mixPresets->where('shop_id', auth()->user()->member->shop_id);
            $customerShops = $latests->where('shop_id', auth()->user()->member->shop_id);
        }

        $customerShops = $latests->reject(function ($item) {
            return is_null($item->customer);
        });

        return view('bill.create', compact('shops', 'members', 'customerShops', 'mixPresets'));
    }

    public function store(BillRequest $request)
    {
        $this->billRepository->store($request->bill);
        $this->sessionService->putFlashMessage(config('const.session.flash.stored'));

        $shopId = !is_null(auth()->user()->member) ? auth()->user()->member->shop_id : (int) $request['bill']['shop_id'];

        $service = new LineBotService($shopId);
        $situation = Situation::with('messages.carousels.carouselActions')->where('shop_id', $shopId)->where('event_type', 4)->first();

        $customers = $request['bill']['customers'];
        foreach ($customers as $customer) {
            $customer = $this->customerRepository->findById((int) $customer['customer_id']);

            foreach ($situation->messages as $message) {
                $service->buildPushMessage($customer->line_token, $message->text);
            }
        }

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
        $members = $this->memberRepository->get();
        $customerShops = $this->customerShopRepository->relate()->orderByDesc('visited_at')->get();
        $mixPresets = $this->mixRepository->relate()->get();
        $bill = $this->billRepository->relate(['shop', 'member', 'billCustomers.customer', 'billOrders.mix'])->find($id);

        $latests = new Collection();
        foreach ($customerShops->groupBy('customer_id') as $i => $customerShop) {
            $latests = $latests->put($i, $customerShop->first());
        }

        if (auth()->user()->role_id !== 1) {
            $shops = $shops->where('shop_id', auth()->user()->member->shop_id);
            $mixPresets = $mixPresets->where('shop_id', auth()->user()->member->shop_id);
            $customerShops = $latests->where('shop_id', auth()->user()->member->shop_id);
        }

        $customerShops = $latests->reject(function ($item) {
            return is_null($item->customer);
        });

        return view('bill.edit', compact('shops', 'members', 'customerShops', 'mixPresets', 'bill'));
    }

    public function update(BillRequest $request, $id)
    {
        $this->billRepository->update($request->bill, $id);
        $this->sessionService->putFlashMessage(config('const.session.flash.updated'));

        $shopId = !is_null(auth()->user()->member) ? auth()->user()->member->shop_id : (int) $request['bill']['shop_id'];

        $service = new LineBotService($shopId);
        $situation = Situation::with('messages.carousels.carouselActions')->where('shop_id', $shopId)->where('event_type', 4)->first();

        $customers = $request['bill']['customers'];
        foreach ($customers as $customer) {
            $customer = $this->customerRepository->findById((int) $customer['customer_id']);

            foreach ($situation->messages as $message) {
                $service->buildPushMessage($customer->line_token, $message->text);
            }
        }

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
