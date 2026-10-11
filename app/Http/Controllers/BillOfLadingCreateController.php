<?php

namespace App\Http\Controllers;

use App\Models\BillOfLading;

class BillOfLadingCreateController extends Controller
{
    public function show(?BillOfLading $billOfLading = null)
    {
        return view('bl.create-wrapper', compact('billOfLading'));
    }
}
