<?php

namespace App\Http\Controllers;

use App\Models\BannerSection;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class BannerSectionController extends Controller
{
    /**
     * Show banner sections page in dashboard
     */
    public function index()
    {
        $banners = BannerSection::getAllOrdered();
        $settings = SiteSetting::getAllAsArray();
        
        return view('dashboard.index', [
            'page' => 'banner-sections',
            'banners' => $banners,
            'settings' => $settings
        ]);
    }

    /**
     * Store a new banner section
     */
    public function store(Request $request)
    {
        @ini_set('memory_limit', '512M');
        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            'video' => 'nullable|file|mimes:mp4,webm,ogg,avi,mov,wmv|max:20480',
            'video_url' => 'nullable|string|max:500',
            'link' => 'nullable|url|max:500',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
            'background_type' => 'nullable|string|in:white,site',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $videoUrl = trim((string) $request->input('video_url', ''));
        if (!$request->hasFile('image') && !$request->hasFile('video') && $videoUrl === '') {
            return back()->withErrors(['image' => 'يرجى رفع صورة أو فيديو، أو وضع رابط يوتيوب.'])->withInput();
        }

        $data = $request->only(['title', 'link', 'order', 'background_type']);
        $data['video_url'] = $videoUrl !== '' ? $videoUrl : null;
        $data['order'] = $data['order'] ?? 0;
        $data['is_active'] = $request->has('is_active') ? true : false;
        $data['background_type'] = $data['background_type'] ?? 'white';
        if (empty($data['title'])) {
            $data['title'] = null;
        }

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = 'banner_' . time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('uploads', $imageName, 'public');
            $data['image'] = 'uploads/' . $imageName;
        } else {
            $data['image'] = null;
        }

        if ($request->hasFile('video')) {
            $video = $request->file('video');
            $ext = strtolower($video->getClientOriginalExtension()) ?: 'mp4';
            $videoName = 'banner_video_' . time() . '_' . uniqid() . '.' . $ext;
            $video->storeAs('uploads', $videoName, 'public');
            $data['video'] = 'uploads/' . $videoName;
        } else {
            $data['video'] = null;
        }

        try {
            BannerSection::create($data);
        } catch (\Throwable $e) {
            Log::error('BannerSection store error: ' . $e->getMessage(), ['exception' => $e]);
            return back()->withErrors(['video' => 'حدث خطأ أثناء الحفظ. تأكد من تشغيل: php artisan migrate'])->withInput();
        }

        return back()->with('success_message', 'تم إضافة القسم بنجاح!');
    }

    /**
     * Update a banner section
     */
    public function update(Request $request, $id)
    {
        @ini_set('memory_limit', '512M');
        $banner = BannerSection::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            'video' => 'nullable|file|mimes:mp4,webm,ogg,avi,mov,wmv|max:20480',
            'video_url' => 'nullable|string|max:500',
            'link' => 'nullable|url|max:500',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
            'background_type' => 'nullable|string|in:white,site',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $request->only(['title', 'link', 'order', 'background_type']);
        $data['is_active'] = $request->has('is_active') ? true : false;
        if (!isset($data['background_type'])) {
            $data['background_type'] = $banner->background_type ?? 'white';
        }

        if ($request->has('video_url_remove') && $request->input('video_url_remove')) {
            $data['video_url'] = null;
        } else {
            $data['video_url'] = trim((string) $request->input('video_url', '')) ?: $banner->video_url;
        }

        if ($request->hasFile('image')) {
            if ($banner->image) {
                $oldPath = ltrim(str_replace('storage/', '', $banner->image), '/');
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }
            $image = $request->file('image');
            $imageName = 'banner_' . time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('uploads', $imageName, 'public');
            $data['image'] = 'uploads/' . $imageName;
        } else {
            $data['image'] = $banner->image;
        }

        if ($request->has('video_remove') && $request->input('video_remove')) {
            if ($banner->video) {
                $oldPath = ltrim(str_replace('storage/', '', $banner->video), '/');
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }
            $data['video'] = null;
        } elseif ($request->hasFile('video')) {
            if ($banner->video) {
                $oldPath = ltrim(str_replace('storage/', '', $banner->video), '/');
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }
            $video = $request->file('video');
            $ext = strtolower($video->getClientOriginalExtension()) ?: 'mp4';
            $videoName = 'banner_video_' . time() . '_' . uniqid() . '.' . $ext;
            $video->storeAs('uploads', $videoName, 'public');
            $data['video'] = 'uploads/' . $videoName;
        } else {
            $data['video'] = $banner->video;
        }

        try {
            $banner->update($data);
        } catch (\Throwable $e) {
            Log::error('BannerSection update error: ' . $e->getMessage(), ['exception' => $e]);
            return back()->withErrors(['video' => 'حدث خطأ أثناء التحديث. تأكد من تشغيل: php artisan migrate'])->withInput();
        }

        return back()->with('success_message', 'تم تحديث القسم بنجاح!');
    }

    /**
     * Delete a banner section
     */
    public function destroy($id)
    {
        $banner = BannerSection::findOrFail($id);
        
        if ($banner->image) {
            $path = ltrim(str_replace('storage/', '', $banner->image), '/');
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }
        if ($banner->video) {
            $path = ltrim(str_replace('storage/', '', $banner->video), '/');
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }

        $banner->delete();

        return back()->with('success_message', 'تم حذف القسم بنجاح!');
    }
}
