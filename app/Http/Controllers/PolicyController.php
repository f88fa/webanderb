<?php

namespace App\Http\Controllers;

use App\Models\PolicyCategory;
use App\Models\Policy;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class PolicyController extends Controller
{
    /**
     * Show policies page in dashboard
     */
    public function index()
    {
        $categories = PolicyCategory::getAllOrdered();
        $policies = Policy::getAllOrdered();
        $settings = SiteSetting::getAllAsArray();
        
        return view('dashboard.index', [
            'page' => 'policies',
            'categories' => $categories,
            'policies' => $policies,
            'settings' => $settings
        ]);
    }

    /**
     * Store a new category
     */
    public function storeCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $request->only(['name', 'order']);
        $data['order'] = $data['order'] ?? 0;
        $data['is_active'] = $request->has('is_active') ? true : false;

        PolicyCategory::create($data);

        return back()->with('success_message', 'تم إضافة التصنيف بنجاح!');
    }

    /**
     * Update a category
     */
    public function updateCategory(Request $request, $id)
    {
        $category = PolicyCategory::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $request->only(['name', 'order']);
        $data['is_active'] = $request->has('is_active') ? true : false;

        $category->update($data);

        return back()->with('success_message', 'تم تحديث التصنيف بنجاح!');
    }

    /**
     * Delete a category
     */
    public function destroyCategory($id)
    {
        $category = PolicyCategory::findOrFail($id);
        $category->delete();

        return back()->with('success_message', 'تم حذف التصنيف بنجاح!');
    }

    /**
     * Store a new policy
     */
    public function storePolicy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:policies_categories,id',
            'title' => 'required|string|max:255',
            'file' => 'required|mimes:pdf|max:51200',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $request->only(['category_id', 'title', 'order']);
        $data['order'] = $data['order'] ?? 0;
        $data['is_active'] = $request->has('is_active') ? true : false;

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = 'policy_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('uploads', $fileName, 'public');
            // Store relative path only (uploads/filename.pdf)
            $data['file'] = 'uploads/' . $fileName;
        }

        Policy::create($data);

        return back()->with('success_message', 'تم إضافة اللائحة/السياسة بنجاح!');
    }

    /**
     * Update a policy
     */
    public function updatePolicy(Request $request, $id)
    {
        $policy = Policy::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:policies_categories,id',
            'title' => 'required|string|max:255',
            'file' => 'nullable|mimes:pdf|max:51200',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $request->only(['category_id', 'title', 'order']);
        $data['is_active'] = $request->has('is_active') ? true : false;

        // Handle file upload
        if ($request->hasFile('file')) {
            // Delete old file
            if ($policy->file) {
                $oldFilePath = str_replace('storage/', '', $policy->file);
                $oldFilePath = ltrim($oldFilePath, '/');
                if (Storage::disk('public')->exists($oldFilePath)) {
                    Storage::disk('public')->delete($oldFilePath);
                }
            }
            
            $file = $request->file('file');
            $fileName = 'policy_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('uploads', $fileName, 'public');
            // Store relative path only (uploads/filename.pdf)
            $data['file'] = 'uploads/' . $fileName;
        } else {
            // Keep existing file
            $data['file'] = $policy->file;
        }

        $policy->update($data);

        return back()->with('success_message', 'تم تحديث اللائحة/السياسة بنجاح!');
    }

    /**
     * Delete a policy
     */
    public function destroyPolicy($id)
    {
        $policy = Policy::findOrFail($id);
        
        // Delete file
        if ($policy->file && strpos($policy->file, 'storage/') !== false) {
            $filePath = str_replace('storage/', 'uploads/', $policy->file);
            if (Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
        }
        
        $policy->delete();

        return back()->with('success_message', 'تم حذف اللائحة/السياسة بنجاح!');
    }
}
