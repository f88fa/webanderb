<?php

namespace App\Http\Controllers;

use App\Models\Testimonial;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class TestimonialController extends Controller
{
    /**
     * Show testimonials page in dashboard
     */
    public function index()
    {
        $testimonials = Testimonial::getAllOrdered();
        $settings = SiteSetting::getAllAsArray();
        
        return view('dashboard.index', [
            'page' => 'testimonials',
            'testimonials' => $testimonials,
            'settings' => $settings
        ]);
    }

    /**
     * Store a new testimonial
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'text' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $request->only(['name', 'text', 'order']);
        $data['order'] = $data['order'] ?? 0;
        $data['is_active'] = $request->has('is_active') ? true : false;

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = 'testimonial_' . time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('uploads', $imageName, 'public');
            // Store relative path only (uploads/filename.jpg)
            $data['image'] = 'uploads/' . $imageName;
            
            // Debug: verify file exists
            if (!file_exists(storage_path('app/public/' . $data['image']))) {
                \Log::error('Image upload failed: ' . storage_path('app/public/' . $data['image']));
            }
        }

        Testimonial::create($data);

        return back()->with('success_message', 'تم إضافة الشهادة بنجاح!');
    }

    /**
     * Update a testimonial
     */
    public function update(Request $request, $id)
    {
        $testimonial = Testimonial::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'text' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $request->only(['name', 'text', 'order']);
        $data['is_active'] = $request->has('is_active') ? true : false;

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($testimonial->image) {
                $oldImagePath = str_replace('storage/', '', $testimonial->image);
                $oldImagePath = ltrim($oldImagePath, '/');
                if (Storage::disk('public')->exists($oldImagePath)) {
                    Storage::disk('public')->delete($oldImagePath);
                }
            }
            
            $image = $request->file('image');
            $imageName = 'testimonial_' . time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('uploads', $imageName, 'public');
            // Store relative path only (uploads/filename.jpg)
            $data['image'] = 'uploads/' . $imageName;
            
            // Debug: verify file exists
            if (!file_exists(storage_path('app/public/' . $data['image']))) {
                \Log::error('Image upload failed: ' . storage_path('app/public/' . $data['image']));
            }
        } else {
            // Keep existing image
            $data['image'] = $testimonial->image;
        }

        $testimonial->update($data);

        return back()->with('success_message', 'تم تحديث الشهادة بنجاح!');
    }

    /**
     * Delete a testimonial
     */
    public function destroy($id)
    {
        $testimonial = Testimonial::findOrFail($id);
        
        // Delete image file
        if ($testimonial->image) {
            $imagePath = str_replace('storage/', '', $testimonial->image);
            $imagePath = ltrim($imagePath, '/');
            if (Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
        }
        
        $testimonial->delete();

        return back()->with('success_message', 'تم حذف الشهادة بنجاح!');
    }
}
