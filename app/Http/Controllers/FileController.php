<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileController extends Controller
{
    /**
     * Show files page in dashboard
     */
    public function index()
    {
        $files = File::getAllOrdered();
        $settings = SiteSetting::getAllAsArray();
        
        return view('dashboard.index', [
            'page' => 'files',
            'files' => $files,
            'settings' => $settings
        ]);
    }

    /**
     * Store a new file
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip,rar|max:51200',
            'description' => 'nullable|string|max:500',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $request->only(['name', 'description', 'order']);
        $data['order'] = $data['order'] ?? 0;
        $data['is_active'] = $request->has('is_active') ? true : false;

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            
            // Upload file using helper function
            $uploadResult = upload_file_safely($file, 'file');
            
            if (!$uploadResult['success']) {
                return back()->withErrors(['file' => $uploadResult['message']])->withInput();
            }
            
            $data['file_path'] = $uploadResult['path'];
            $data['file_type'] = strtolower($file->getClientOriginalExtension());
            $data['file_size'] = $file->getSize();
        }

        File::create($data);

        return back()->with('success_message', 'تم رفع الملف بنجاح!');
    }

    /**
     * Update a file
     */
    public function update(Request $request, $id)
    {
        $file = File::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip,rar|max:51200',
            'description' => 'nullable|string|max:500',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
            'file_remove' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $request->only(['name', 'description', 'order']);
        $data['is_active'] = $request->has('is_active') ? true : false;

        // Handle file upload or removal
        if ($request->hasFile('file')) {
            // Delete old file if exists
            if ($file->file_path) {
                $oldFilePath = str_replace('storage/', '', $file->file_path);
                $oldFilePath = ltrim($oldFilePath, '/');
                if (Storage::disk('public')->exists($oldFilePath)) {
                    Storage::disk('public')->delete($oldFilePath);
                }
            }
            
            // Upload new file
            $uploadedFile = $request->file('file');
            $uploadResult = upload_file_safely($uploadedFile, 'file');
            
            if (!$uploadResult['success']) {
                return back()->withErrors(['file' => $uploadResult['message']])->withInput();
            }
            
            $data['file_path'] = $uploadResult['path'];
            $data['file_type'] = strtolower($uploadedFile->getClientOriginalExtension());
            $data['file_size'] = $uploadedFile->getSize();
        } elseif ($request->has('file_remove')) {
            // Delete file if requested
            if ($file->file_path) {
                $oldFilePath = str_replace('storage/', '', $file->file_path);
                $oldFilePath = ltrim($oldFilePath, '/');
                if (Storage::disk('public')->exists($oldFilePath)) {
                    Storage::disk('public')->delete($oldFilePath);
                }
            }
            $data['file_path'] = null;
            $data['file_type'] = null;
            $data['file_size'] = null;
        }

        $file->update($data);

        return back()->with('success_message', 'تم تحديث الملف بنجاح!');
    }

    /**
     * Delete a file
     */
    public function destroy($id)
    {
        $file = File::findOrFail($id);
        
        // Delete physical file
        if ($file->file_path) {
            $filePath = str_replace('storage/', '', $file->file_path);
            $filePath = ltrim($filePath, '/');
            if (Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
        }
        
        $file->delete();

        return back()->with('success_message', 'تم حذف الملف بنجاح!');
    }
}
