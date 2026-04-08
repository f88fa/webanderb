<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Fund;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class FundController extends Controller
{
    public function index()
    {
        $funds = Fund::orderBy('code')->get();

        return view('wesal.index', [
            'page' => 'finance',
            'settings' => \App\Models\SiteSetting::getAllAsArray(),
            'formType' => 'funds',
            'funds' => $funds,
        ]);
    }

    public function edit(Fund $fund)
    {
        $funds = Fund::orderBy('code')->get();

        return view('wesal.index', [
            'page' => 'finance',
            'settings' => \App\Models\SiteSetting::getAllAsArray(),
            'formType' => 'funds',
            'funds' => $funds,
            'editingFund' => $fund,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:funds,code'],
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'restriction_type' => ['required', Rule::in(['unrestricted', 'restricted', 'endowment'])],
            'description' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        Fund::create($request->only(['code', 'name_ar', 'name_en', 'restriction_type', 'description', 'status']));

        return back()->with('success', 'تم إضافة صنف المال بنجاح');
    }

    public function update(Request $request, Fund $fund)
    {
        $request->validate([
            'code' => ['required', 'string', 'max:50', Rule::unique('funds', 'code')->ignore($fund->id)],
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'restriction_type' => ['required', Rule::in(['unrestricted', 'restricted', 'endowment'])],
            'description' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $fund->update($request->only(['code', 'name_ar', 'name_en', 'restriction_type', 'description', 'status']));

        return back()->with('success', 'تم تحديث صنف المال بنجاح');
    }

    public function destroy(Fund $fund)
    {
        $fund->delete();
        return back()->with('success', 'تم حذف صنف المال');
    }
}
