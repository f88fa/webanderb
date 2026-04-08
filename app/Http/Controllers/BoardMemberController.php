<?php

namespace App\Http\Controllers;

use App\Models\BoardMember;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class BoardMemberController extends Controller
{
    /**
     * Show board members page in dashboard
     */
    public function index()
    {
        $boardMembers = BoardMember::getAllOrdered();
        $settings = SiteSetting::getAllAsArray();
        
        return view('dashboard.index', [
            'page' => 'board-members',
            'boardMembers' => $boardMembers,
            'settings' => $settings
        ]);
    }

    /**
     * Store a new board member
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $request->only(['name', 'position', 'order']);
        $data['order'] = $data['order'] ?? 0;
        $data['is_active'] = $request->has('is_active') ? true : false;

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = 'board_' . time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('uploads', $imageName, 'public');
            // Store relative path only (uploads/filename.jpg)
            $data['image'] = 'uploads/' . $imageName;
            
            // Debug: verify file exists
            if (!file_exists(storage_path('app/public/' . $data['image']))) {
                \Log::error('Image upload failed: ' . storage_path('app/public/' . $data['image']));
            }
        }

        BoardMember::create($data);

        return back()->with('success_message', 'تم إضافة عضو مجلس الإدارة بنجاح!');
    }

    /**
     * Update a board member
     */
    public function update(Request $request, $id)
    {
        $boardMember = BoardMember::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $request->only(['name', 'position', 'order']);
        $data['is_active'] = $request->has('is_active') ? true : false;

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($boardMember->image) {
                $oldImagePath = str_replace('storage/', '', $boardMember->image);
                $oldImagePath = ltrim($oldImagePath, '/');
                if (Storage::disk('public')->exists($oldImagePath)) {
                    Storage::disk('public')->delete($oldImagePath);
                }
            }
            
            $image = $request->file('image');
            $imageName = 'board_' . time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('uploads', $imageName, 'public');
            // Store relative path only (uploads/filename.jpg)
            $data['image'] = 'uploads/' . $imageName;
            
            // Debug: verify file exists
            if (!file_exists(storage_path('app/public/' . $data['image']))) {
                \Log::error('Image upload failed: ' . storage_path('app/public/' . $data['image']));
            }
        } else {
            // Keep existing image
            $data['image'] = $boardMember->image;
        }

        $boardMember->update($data);

        return back()->with('success_message', 'تم تحديث عضو مجلس الإدارة بنجاح!');
    }

    /**
     * Delete a board member
     */
    public function destroy($id)
    {
        $boardMember = BoardMember::findOrFail($id);
        
        // Delete image file
        if ($boardMember->image) {
            $imagePath = str_replace('storage/', '', $boardMember->image);
            $imagePath = ltrim($imagePath, '/');
            if (Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
        }
        
        $boardMember->delete();

        return back()->with('success_message', 'تم حذف عضو مجلس الإدارة بنجاح!');
    }
}
