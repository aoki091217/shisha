<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\CustomerShopRepository;
use Illuminate\Http\Request;

class HomeViewController extends Controller
{
    public function __construct(
        private CustomerShopRepository $customerShopRepository
    ) {}

    public function index(Request $request)
    {
        $sales = $this->customerShopRepository->getSales();
        $customers = $this->customerShopRepository->getCustomers();

        return view('home.index', compact('sales', 'customers'));
    }
}
