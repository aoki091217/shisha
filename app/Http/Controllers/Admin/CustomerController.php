<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerShop;
use App\Repositories\CustomerShopRepository;
use App\Repositories\ShopRepository;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function __construct(
        private CustomerShopRepository $customerShopRepository,
        private ShopRepository $shopRepository
    ) {}

    public function index(Request $request)
    {
        $shops = $this->shopRepository->get();
        $customerShops = $this->customerShopRepository->relate()->orderBy('visited_at', 'desc')->search($request->customer)->paginate();
        return view('customer.index', compact('customerShops', 'shops'));
    }
}
