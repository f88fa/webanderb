<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    /**
     * Show projects page in dashboard
     */
    public function index()
    {
        $projects = Project::getAllOrdered();
        $settings = SiteSetting::getAllAsArray();
        
        return view('dashboard.index', [
            'page' => 'projects',
            'projects' => $projects,
            'settings' => $settings
        ]);
    }

    /**
     * Store a new project
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            'donate_link' => 'nullable|url|max:500',
            'donate_button_text' => 'nullable|string|max:100',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $request->only(['name', 'description', 'donate_link', 'donate_button_text', 'order']);
        $data['order'] = $data['order'] ?? 0;
        $data['is_active'] = $request->has('is_active') ? true : false;
        $data['donate_button_text'] = $data['donate_button_text'] ?? 'تبرع الآن';

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = 'project_' . time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('uploads', $imageName, 'public');
            // Store relative path only (uploads/filename.jpg)
            $data['image'] = 'uploads/' . $imageName;
            
            // Debug: verify file exists
            if (!file_exists(storage_path('app/public/' . $data['image']))) {
                \Log::error('Image upload failed: ' . storage_path('app/public/' . $data['image']));
            }
        }

        Project::create($data);

        return back()->with('success_message', 'تم إضافة المشروع بنجاح!');
    }

    /**
     * Update a project
     */
    public function update(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            'donate_link' => 'nullable|url|max:500',
            'donate_button_text' => 'nullable|string|max:100',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $request->only(['name', 'description', 'donate_link', 'donate_button_text', 'order']);
        $data['is_active'] = $request->has('is_active') ? true : false;
        $data['donate_button_text'] = $data['donate_button_text'] ?? 'تبرع الآن';

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($project->image) {
                $oldImagePath = str_replace('storage/', '', $project->image);
                $oldImagePath = ltrim($oldImagePath, '/');
                if (Storage::disk('public')->exists($oldImagePath)) {
                    Storage::disk('public')->delete($oldImagePath);
                }
            }
            
            $image = $request->file('image');
            $imageName = 'project_' . time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('uploads', $imageName, 'public');
            // Store relative path only (uploads/filename.jpg)
            $data['image'] = 'uploads/' . $imageName;
            
            // Debug: verify file exists
            if (!file_exists(storage_path('app/public/' . $data['image']))) {
                \Log::error('Image upload failed: ' . storage_path('app/public/' . $data['image']));
            }
        } else {
            // Keep existing image
            $data['image'] = $project->image;
        }

        $project->update($data);

        return back()->with('success_message', 'تم تحديث المشروع بنجاح!');
    }

    /**
     * Delete a project
     */
    public function destroy($id)
    {
        $project = Project::findOrFail($id);
        
        // Delete image file
        if ($project->image) {
            $imagePath = str_replace('storage/', '', $project->image);
            $imagePath = ltrim($imagePath, '/');
            if (Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
        }
        
        $project->delete();

        return back()->with('success_message', 'تم حذف المشروع بنجاح!');
    }
}
