<?php

namespace App\Http\Controllers;

use App\Models\MediaVideo;
use App\Models\MediaSlide;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    /**
     * Show media center page in dashboard
     */
    public function index()
    {
        $videos = MediaVideo::getAllOrdered();
        $slides = MediaSlide::getAllOrdered();
        $settings = SiteSetting::getAllAsArray();
        
        return view('dashboard.index', [
            'page' => 'media',
            'videos' => $videos,
            'slides' => $slides,
            'settings' => $settings
        ]);
    }

    /**
     * Store a new video
     */
    public function storeVideo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'youtube_url' => 'required|url|max:500',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $request->only(['title', 'youtube_url', 'order']);
        $data['order'] = $data['order'] ?? 0;
        $data['is_active'] = $request->has('is_active') ? true : false;

        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            $thumbnail = $request->file('thumbnail');
            $thumbnailName = 'video_thumb_' . time() . '_' . uniqid() . '.' . $thumbnail->getClientOriginalExtension();
            $thumbnailPath = $thumbnail->storeAs('uploads', $thumbnailName, 'public');
            // Store relative path only (uploads/filename.jpg)
            $data['thumbnail'] = 'uploads/' . $thumbnailName;
            
            // Debug: verify file exists
            if (!file_exists(storage_path('app/public/' . $data['thumbnail']))) {
                \Log::error('Thumbnail upload failed: ' . storage_path('app/public/' . $data['thumbnail']));
            }
        }

        MediaVideo::create($data);

        return back()->with('success_message', 'تم إضافة الفيديو بنجاح!');
    }

    /**
     * Update a video
     */
    public function updateVideo(Request $request, $id)
    {
        $video = MediaVideo::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'youtube_url' => 'required|url|max:500',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $request->only(['title', 'youtube_url', 'order']);
        $data['is_active'] = $request->has('is_active') ? true : false;

        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            // Delete old thumbnail
            if ($video->thumbnail) {
                $oldThumbnailPath = str_replace('storage/', '', $video->thumbnail);
                $oldThumbnailPath = ltrim($oldThumbnailPath, '/');
                if (Storage::disk('public')->exists($oldThumbnailPath)) {
                    Storage::disk('public')->delete($oldThumbnailPath);
                }
            }
            
            $thumbnail = $request->file('thumbnail');
            $thumbnailName = 'video_thumb_' . time() . '_' . uniqid() . '.' . $thumbnail->getClientOriginalExtension();
            $thumbnailPath = $thumbnail->storeAs('uploads', $thumbnailName, 'public');
            // Store relative path only (uploads/filename.jpg)
            $data['thumbnail'] = 'uploads/' . $thumbnailName;
            
            // Debug: verify file exists
            if (!file_exists(storage_path('app/public/' . $data['thumbnail']))) {
                \Log::error('Thumbnail upload failed: ' . storage_path('app/public/' . $data['thumbnail']));
            }
        } else {
            // Keep existing thumbnail
            $data['thumbnail'] = $video->thumbnail;
        }

        $video->update($data);

        return back()->with('success_message', 'تم تحديث الفيديو بنجاح!');
    }

    /**
     * Delete a video
     */
    public function destroyVideo($id)
    {
        $video = MediaVideo::findOrFail($id);
        
        // Delete thumbnail file
        if ($video->thumbnail) {
            $thumbnailPath = str_replace('storage/', '', $video->thumbnail);
            $thumbnailPath = ltrim($thumbnailPath, '/');
            if (Storage::disk('public')->exists($thumbnailPath)) {
                Storage::disk('public')->delete($thumbnailPath);
            }
        }
        
        $video->delete();

        return back()->with('success_message', 'تم حذف الفيديو بنجاح!');
    }

    /**
     * Store a new slide
     */
    public function storeSlide(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:image,video',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'required_if:type,image|nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            'video_url' => 'required_if:type,video|nullable|url|max:500',
            'link' => 'nullable|url|max:500',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $request->only(['type', 'title', 'description', 'video_url', 'link', 'order']);
        $data['type'] = $data['type'] ?? 'image';
        $data['order'] = $data['order'] ?? 0;
        $data['is_active'] = $request->has('is_active') ? true : false;

        // Handle image upload (only for image type)
        if ($data['type'] === 'image' && $request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = 'slide_' . time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('uploads', $imageName, 'public');
            // Store relative path only (uploads/filename.jpg)
            $data['image'] = 'uploads/' . $imageName;
            
            // Debug: verify file exists
            if (!file_exists(storage_path('app/public/' . $data['image']))) {
                \Log::error('Slide image upload failed: ' . storage_path('app/public/' . $data['image']));
            }
        } else {
            $data['image'] = null;
        }

        MediaSlide::create($data);

        return back()->with('success_message', 'تم إضافة الشريحة بنجاح!');
    }

    /**
     * Update a slide
     */
    public function updateSlide(Request $request, $id)
    {
        $slide = MediaSlide::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'type' => 'required|in:image,video',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'required_if:type,image|nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            'video_url' => 'required_if:type,video|nullable|url|max:500',
            'link' => 'nullable|url|max:500',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $request->only(['type', 'title', 'description', 'video_url', 'link', 'order']);
        $data['type'] = $data['type'] ?? 'image';
        $data['is_active'] = $request->has('is_active') ? true : false;

        // Handle image upload (only for image type)
        if ($data['type'] === 'image' && $request->hasFile('image')) {
            // Delete old image
            if ($slide->image) {
                $oldImagePath = str_replace('storage/', '', $slide->image);
                $oldImagePath = ltrim($oldImagePath, '/');
                if (Storage::disk('public')->exists($oldImagePath)) {
                    Storage::disk('public')->delete($oldImagePath);
                }
            }
            
            $image = $request->file('image');
            $imageName = 'slide_' . time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('uploads', $imageName, 'public');
            // Store relative path only (uploads/filename.jpg)
            $data['image'] = 'uploads/' . $imageName;
            
            // Debug: verify file exists
            if (!file_exists(storage_path('app/public/' . $data['image']))) {
                \Log::error('Slide image upload failed: ' . storage_path('app/public/' . $data['image']));
            }
        } elseif ($data['type'] === 'image') {
            // Keep existing image if type is still image
            $data['image'] = $slide->image;
        } else {
            // If changing to video, delete old image
            if ($slide->image) {
                $oldImagePath = str_replace('storage/', '', $slide->image);
                $oldImagePath = ltrim($oldImagePath, '/');
                if (Storage::disk('public')->exists($oldImagePath)) {
                    Storage::disk('public')->delete($oldImagePath);
                }
            }
            $data['image'] = null;
        }

        $slide->update($data);

        return back()->with('success_message', 'تم تحديث الشريحة بنجاح!');
    }

    /**
     * Delete a slide
     */
    public function destroySlide($id)
    {
        $slide = MediaSlide::findOrFail($id);
        
        // Delete image file
        if ($slide->image) {
            $imagePath = str_replace('storage/', '', $slide->image);
            $imagePath = ltrim($imagePath, '/');
            if (Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
        }
        
        $slide->delete();

        return back()->with('success_message', 'تم حذف الشريحة بنجاح!');
    }
}
