<?php

namespace App\Http\Controllers;

use App\Models\RegulationCategory;
use App\Models\Regulation;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class RegulationController extends Controller
{
    /**
     * Show regulations page in dashboard
     */
    public function index()
    {
        $categories = RegulationCategory::getAllOrdered();
        $settings = SiteSetting::getAllAsArray();
        
        return view('dashboard.index', [
            'page' => 'regulations',
            'categories' => $categories,
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

        RegulationCategory::create($data);

        return back()->with('success_message', 'تم إضافة التصنيف بنجاح!');
    }

    /**
     * Update a category
     */
    public function updateCategory(Request $request, $id)
    {
        $category = RegulationCategory::findOrFail($id);

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
        $category = RegulationCategory::findOrFail($id);
        
        // Delete all regulations files
        foreach ($category->regulations as $regulation) {
            if ($regulation->file) {
                $filePath = str_replace('storage/', '', $regulation->file);
                $filePath = ltrim($filePath, '/');
                if (Storage::disk('public')->exists($filePath)) {
                    Storage::disk('public')->delete($filePath);
                }
            }
        }
        
        $category->delete();

        return back()->with('success_message', 'تم حذف التصنيف بنجاح!');
    }

    /**
     * Store a new regulation
     */
    public function storeRegulation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:regulations_categories,id',
            'title' => 'required|string|max:255',
            'file' => 'required|mimes:pdf|max:10240',
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
            $fileName = 'regulation_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('uploads', $fileName, 'public');
            // Store relative path only (uploads/filename.pdf)
            $data['file'] = 'uploads/' . $fileName;
        }

        Regulation::create($data);

        return back()->with('success_message', 'تم إضافة اللائحة بنجاح!');
    }

    /**
     * Update a regulation
     */
    public function updateRegulation(Request $request, $id)
    {
        $regulation = Regulation::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:regulations_categories,id',
            'title' => 'required|string|max:255',
            'file' => 'nullable|mimes:pdf|max:10240',
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
            if ($regulation->file) {
                $oldFilePath = str_replace('storage/', '', $regulation->file);
                $oldFilePath = ltrim($oldFilePath, '/');
                if (Storage::disk('public')->exists($oldFilePath)) {
                    Storage::disk('public')->delete($oldFilePath);
                }
            }
            
            $file = $request->file('file');
            $fileName = 'regulation_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('uploads', $fileName, 'public');
            // Store relative path only (uploads/filename.pdf)
            $data['file'] = 'uploads/' . $fileName;
        } else {
            // Keep existing file
            $data['file'] = $regulation->file;
        }

        $regulation->update($data);

        return back()->with('success_message', 'تم تحديث اللائحة بنجاح!');
    }

    /**
     * Delete a regulation
     */
    public function destroyRegulation($id)
    {
        $regulation = Regulation::findOrFail($id);
        
        // Delete file
        if ($regulation->file) {
            $filePath = str_replace('storage/', '', $regulation->file);
            $filePath = ltrim($filePath, '/');
            if (Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
        }
        
        $regulation->delete();

        return back()->with('success_message', 'تم حذف اللائحة بنجاح!');
    }
}
