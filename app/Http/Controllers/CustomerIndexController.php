<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerIndexController extends Controller
{
    function __invoke() {
        return view('customer.index',[
    'customers' => Customer::latest()->paginate(100)
    ]);
    }
}
