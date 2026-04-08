<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

/**
 * NewsController
 * Migrated from Plain PHP: pages/news.php
 * Handles news management (CRUD operations)
 */
class NewsController extends Controller
{
    /**
     * Show news management page
     * Replaces: pages/news.php GET request handling
     */
    public function index(Request $request)
    {
        $news = News::getAllOrdered();
        $editNews = null;
        $settings = SiteSetting::getAllAsArray();
        
        // Get news for editing - replaces: SELECT * FROM news WHERE id = $edit_id
        if ($request->has('edit')) {
            $editNews = News::find($request->get('edit'));
        }
        
        return view('dashboard.index', [
            'page' => 'news',
            'news' => $news,
            'editNews' => $editNews,
            'settings' => $settings
        ]);
    }

    /**
     * Store new news
     * Replaces: pages/news.php POST add_news with mysqli INSERT
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $request->only(['title', 'content', 'status']);

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = 'news_' . time() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('uploads', $imageName, 'public');
            
            // Store relative path only (uploads/filename.jpg)
            $data['image'] = 'uploads/' . $imageName;
            
            // Debug: verify file exists
            if (!file_exists(storage_path('app/public/' . $data['image']))) {
                \Log::error('Image upload failed: ' . storage_path('app/public/' . $data['image']));
            }
        }

        News::create($data);

        return back()->with('success_message', 'تم إضافة الخبر بنجاح!');
    }

    /**
     * Update existing news
     * Replaces: pages/news.php POST update_news with mysqli UPDATE
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $news = News::findOrFail($id);
        $data = $request->only(['title', 'content', 'status']);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($news->image) {
                $oldImagePath = str_replace('storage/', '', $news->image);
                $oldImagePath = ltrim($oldImagePath, '/');
                if (Storage::disk('public')->exists($oldImagePath)) {
                    Storage::disk('public')->delete($oldImagePath);
                }
            }
            
            $image = $request->file('image');
            $imageName = 'news_' . time() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('uploads', $imageName, 'public');
            
            // Store relative path only (uploads/filename.jpg)
            $data['image'] = 'uploads/' . $imageName;
            
            // Debug: verify file exists
            if (!file_exists(storage_path('app/public/' . $data['image']))) {
                \Log::error('Image upload failed: ' . storage_path('app/public/' . $data['image']));
            }
        }

        $news->update($data);

        return redirect()->route('dashboard', ['page' => 'news'])->with('success_message', 'تم تحديث الخبر بنجاح!');
    }

    /**
     * Delete news
     * Replaces: pages/news.php POST delete_news with mysqli DELETE
     */
    public function destroy($id)
    {
        $news = News::findOrFail($id);
        $news->delete();

        return back()->with('success_message', 'تم حذف الخبر بنجاح!');
    }
}
