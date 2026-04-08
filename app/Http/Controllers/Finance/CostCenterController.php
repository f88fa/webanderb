<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\CostCenter;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CostCenterController extends Controller
{
    public function index()
    {
        $costCenters = CostCenter::orderBy('code')->get();

        return view('wesal.index', [
            'page' => 'finance',
            'settings' => \App\Models\SiteSetting::getAllAsArray(),
            'formType' => 'cost-centers',
            'costCenters' => $costCenters,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:cost_centers,code'],
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'center_type' => ['required', Rule::in(['program', 'administrative', 'fundraising'])],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        CostCenter::create($request->only(['code', 'name_ar', 'name_en', 'description', 'center_type', 'status']));

        return back()->with('success', 'تم إضافة مركز التكلفة بنجاح');
    }

    public function update(Request $request, CostCenter $costCenter)
    {
        $request->validate([
            'code' => ['required', 'string', 'max:50', Rule::unique('cost_centers', 'code')->ignore($costCenter->id)],
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'center_type' => ['required', Rule::in(['program', 'administrative', 'fundraising'])],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $costCenter->update($request->only(['code', 'name_ar', 'name_en', 'description', 'center_type', 'status']));

        return back()->with('success', 'تم تحديث مركز التكلفة بنجاح');
    }

    public function destroy(CostCenter $costCenter)
    {
        $costCenter->delete();
        return back()->with('success', 'تم حذف مركز التكلفة');
    }
}
