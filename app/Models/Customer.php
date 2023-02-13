<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory;

    protected $primaryKey = 'customer_id';

    protected $fillable = [
        'line_token',
        'name',
        'sex',
        'generation',
        'reason',
        'customer_date',
        'step'
    ];

    public function findCustomer($line_token)
    {
        return $this->where('line_token', $line_token)->first();
    }

    public function storeCustomer($line_token)
    {
        $this->line_token = $line_token;
        $this->customer_date = Carbon::today()->toDateString();
        $this->step = 1;
        $this->save();

        return $this;
    }

    public function storeStep($customer, $step)
    {
        $customer->step = $step;
        $customer->save();
    }

    public function updateCustomer($customer, $fills)
    {
        foreach ($fills as $key => $value) {
            $customer->{$key} = $value;
        }

        $customer->save();
    }

    public function deleteName($customer)
    {
        $customer->name = null;
        $customer->step = 1;
        $customer->save();
    }

    // public function deleteCustomer($line_token)
    // {
    //     \Log::debug(111111);
    //     $customer = $this->findCustomer($line_token);
    //     if (!is_null($customer)) {
    //         $customer->delete();
    //     }
    // }
}
