<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanySelectionController extends Controller
{
    public function index()
    {
        $companies = Auth::user()->companies;

        // Si solo tiene una, no hay nada que elegir -- se activa sola y seguimos.
        if ($companies->count() === 1) {
            session(['active_company_id' => $companies->first()->id]);

            return redirect()->intended(route('dashboard'));
        }

        return view('company.select', compact('companies'));
    }

    public function select(Request $request)
    {
        $validated = $request->validate(['company_id' => 'required|integer']);

        $belongs = Auth::user()->companies()->where('companies.id', $validated['company_id'])->exists();
        abort_unless($belongs, 403, 'No perteneces a esa empresa.');

        session(['active_company_id' => $validated['company_id']]);

        return redirect()->intended(route('dashboard'));
    }
}
