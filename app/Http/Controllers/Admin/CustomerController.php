<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BillCustomer;
use App\Models\Customer;
use App\Models\CustomerShop;
use App\Repositories\CustomerRepository;
use App\Repositories\CustomerShopRepository;
use App\Repositories\ShopRepository;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function __construct(
        private CustomerShopRepository $customerShopRepository,
        private CustomerRepository $customerRepository,
        private ShopRepository $shopRepository
    ) {}

    public function index(Request $request)
    {
        $shops = $this->shopRepository->get();
        $customers = $this->customerRepository->getCustomersByCheckin()->search($request->customer)->paginate();

        return view('customer.index', compact('customers', 'shops'));
    }

    public function show($id)
    {
        $customers = $this->customerRepository->getCustomersByCheckin();
        $customer = $customers->first()->load(['answers.carousel', 'answers.carouselAction']);
        $billCustomer = BillCustomer::with(['billOrders.mix.mixes', 'billOrders.bill.shop'])->where('customer_id', $id)->first();

        return view('customer.show', compact('customers', 'customer', 'billCustomer'));
    }
}
