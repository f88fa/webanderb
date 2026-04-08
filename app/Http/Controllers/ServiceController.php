<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServiceController extends Controller
{
    /**
     * Show services page in dashboard
     */
    public function index()
    {
        $services = Service::getAllOrdered();
        $settings = SiteSetting::getAllAsArray();
        
        return view('dashboard.index', [
            'page' => 'services',
            'services' => $services,
            'settings' => $settings
        ]);
    }

    /**
     * Store a new service
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $request->only(['title', 'description', 'icon', 'order', 'is_active']);
        $data['icon'] = $data['icon'] ?? 'fas fa-star';
        $data['order'] = $data['order'] ?? 0;
        $data['is_active'] = $request->has('is_active') ? true : false;

        Service::create($data);

        return back()->with('success_message', 'تم إضافة الخدمة بنجاح!');
    }

    /**
     * Update a service
     */
    public function update(Request $request, $id)
    {
        $service = Service::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $request->only(['title', 'description', 'icon', 'order']);
        $data['is_active'] = $request->has('is_active') ? true : false;

        $service->update($data);

        return back()->with('success_message', 'تم تحديث الخدمة بنجاح!');
    }

    /**
     * Delete a service
     */
    public function destroy($id)
    {
        $service = Service::findOrFail($id);
        $service->delete();

        return back()->with('success_message', 'تم حذف الخدمة بنجاح!');
    }
}
