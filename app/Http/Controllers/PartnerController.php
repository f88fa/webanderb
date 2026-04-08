<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class PartnerController extends Controller
{
    /**
     * Show partners page in dashboard
     */
    public function index()
    {
        $partners = Partner::getAllOrdered();
        $settings = SiteSetting::getAllAsArray();
        
        return view('dashboard.index', [
            'page' => 'partners',
            'partners' => $partners,
            'settings' => $settings
        ]);
    }

    /**
     * Store a new partner
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            'website' => 'nullable|url|max:255',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $request->only(['name', 'website', 'order']);
        $data['order'] = $data['order'] ?? 0;
        $data['is_active'] = $request->has('is_active') ? true : false;

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $logoName = 'partner_' . time() . '_' . uniqid() . '.' . $logo->getClientOriginalExtension();
            $logoPath = $logo->storeAs('uploads', $logoName, 'public');
            // Store relative path only (uploads/filename.jpg)
            $data['logo'] = 'uploads/' . $logoName;
            
            // Debug: verify file exists
            if (!file_exists(storage_path('app/public/' . $data['logo']))) {
                \Log::error('Logo upload failed: ' . storage_path('app/public/' . $data['logo']));
            }
        }

        Partner::create($data);

        return back()->with('success_message', 'تم إضافة الشريك بنجاح!');
    }

    /**
     * Update a partner
     */
    public function update(Request $request, $id)
    {
        $partner = Partner::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            'website' => 'nullable|url|max:255',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $request->only(['name', 'website', 'order']);
        $data['is_active'] = $request->has('is_active') ? true : false;

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo
            if ($partner->logo) {
                $oldLogoPath = str_replace('storage/', '', $partner->logo);
                $oldLogoPath = ltrim($oldLogoPath, '/');
                if (Storage::disk('public')->exists($oldLogoPath)) {
                    Storage::disk('public')->delete($oldLogoPath);
                }
            }
            
            $logo = $request->file('logo');
            $logoName = 'partner_' . time() . '_' . uniqid() . '.' . $logo->getClientOriginalExtension();
            $logoPath = $logo->storeAs('uploads', $logoName, 'public');
            // Store relative path only (uploads/filename.jpg)
            $data['logo'] = 'uploads/' . $logoName;
            
            // Debug: verify file exists
            if (!file_exists(storage_path('app/public/' . $data['logo']))) {
                \Log::error('Logo upload failed: ' . storage_path('app/public/' . $data['logo']));
            }
        } else {
            // Keep existing logo
            $data['logo'] = $partner->logo;
        }

        $partner->update($data);

        return back()->with('success_message', 'تم تحديث الشريك بنجاح!');
    }

    /**
     * Delete a partner
     */
    public function destroy($id)
    {
        $partner = Partner::findOrFail($id);
        
        // Delete logo file
        if ($partner->logo) {
            $logoPath = str_replace('storage/', '', $partner->logo);
            $logoPath = ltrim($logoPath, '/');
            if (Storage::disk('public')->exists($logoPath)) {
                Storage::disk('public')->delete($logoPath);
            }
        }
        
        $partner->delete();

        return back()->with('success_message', 'تم حذف الشريك بنجاح!');
    }
}
