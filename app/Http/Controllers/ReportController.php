<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    /**
     * Show reports page in dashboard
     */
    public function index()
    {
        $reports = Report::getAllOrdered();
        $settings = SiteSetting::getAllAsArray();
        
        return view('dashboard.index', [
            'page' => 'reports',
            'reports' => $reports,
            'settings' => $settings
        ]);
    }

    /**
     * Store a new report
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            'link' => 'nullable|string|max:500',
            'description' => 'nullable|string|max:1000',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $request->only(['title', 'link', 'description', 'order']);
        $data['order'] = $data['order'] ?? 0;
        $data['is_active'] = $request->has('is_active') ? true : false;

        // Handle image upload
        if ($request->hasFile('image')) {
            $uploadResult = upload_image_safely($request->file('image'), 'report');
            if (!$uploadResult['success']) {
                return back()->withErrors(['image' => $uploadResult['message']])->withInput();
            }
            $data['image'] = $uploadResult['path'];
        }

        Report::create($data);

        return back()->with('success_message', 'تم إضافة التقرير بنجاح!');
    }

    /**
     * Update a report
     */
    public function update(Request $request, $id)
    {
        $report = Report::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            'link' => 'nullable|string|max:500',
            'description' => 'nullable|string|max:1000',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $request->only(['title', 'link', 'description', 'order']);
        $data['is_active'] = $request->has('is_active') ? true : false;

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($report->image) {
                $oldImagePath = str_replace('storage/', '', $report->image);
                $oldImagePath = ltrim($oldImagePath, '/');
                if (Storage::disk('public')->exists($oldImagePath)) {
                    Storage::disk('public')->delete($oldImagePath);
                }
            }
            
            $uploadResult = upload_image_safely($request->file('image'), 'report');
            if (!$uploadResult['success']) {
                return back()->withErrors(['image' => $uploadResult['message']])->withInput();
            }
            $data['image'] = $uploadResult['path'];
        } else {
            // Keep existing image
            $data['image'] = $report->image;
        }

        $report->update($data);

        return back()->with('success_message', 'تم تحديث التقرير بنجاح!');
    }

    /**
     * Delete a report
     */
    public function destroy($id)
    {
        $report = Report::findOrFail($id);
        
        // Delete image file
        if ($report->image) {
            $imagePath = str_replace('storage/', '', $report->image);
            $imagePath = ltrim($imagePath, '/');
            if (Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
        }
        
        $report->delete();

        return back()->with('success_message', 'تم حذف التقرير بنجاح!');
    }
}
