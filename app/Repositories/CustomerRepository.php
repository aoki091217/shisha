<?php

namespace App\Repositories;

use App\Models\Customer;

class CustomerRepository
{
    public function get()
    {
        return Customer::get();
    }
}

?>
