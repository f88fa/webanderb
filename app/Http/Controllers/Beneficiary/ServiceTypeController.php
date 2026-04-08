<?php

namespace App\Http\Controllers\Beneficiary;

use App\Http\Controllers\Controller;
use App\Models\Beneficiary\ServiceType;
use Illuminate\Http\Request;

class ServiceTypeController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        ServiceType::create([
            'name_ar' => $request->name_ar,
            'description' => $request->description,
            'is_active' => true,
            'is_financial' => $request->boolean('is_financial'),
        ]);

        return redirect()->route('wesal.beneficiaries.show', ['section' => 'services'])->with('success', 'تمت إضافة نوع الخدمة.');
    }

    public function destroy(ServiceType $serviceType)
    {
        $serviceType->delete();
        return redirect()->route('wesal.beneficiaries.show', ['section' => 'services'])->with('success', 'تم حذف نوع الخدمة.');
    }
}
