<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MonthController extends Controller
{
    public function index(Request $request)
    {
        return view('welcome', [
            'data_start' => $request->data_start,
            'data_end' => $request->data_end
        ]);
    }
}
