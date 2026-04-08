<?php

namespace App\Http\Controllers;

use App\Models\AboutUs;
use App\Models\AboutStat;
use App\Models\AboutFeature;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

/**
 * AboutController
 * Migrated from Plain PHP: pages/about.php
 * Handles "About Us" content management
 */
class AboutController extends Controller
{
    /**
     * Show about page
     * Replaces: pages/about.php GET request handling
     */
    public function index()
    {
        $about = AboutUs::getLatest();
        $settings = SiteSetting::getAllAsArray();
        $stats = AboutStat::getAllOrdered();
        $features = AboutFeature::getAllOrdered();
        
        return view('dashboard.index', [
            'page' => 'about',
            'about' => $about,
            'stats' => $stats,
            'features' => $features,
            'settings' => $settings
        ]);
    }

    /**
     * Store or update about us content
     * Replaces: pages/about.php POST request with mysqli INSERT/UPDATE
     */
    public function store(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'section_title' => 'nullable|string|max:255',
            'content' => 'nullable|string', // Changed to nullable to allow saving executive director data without content
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            'cta_text' => 'nullable|string|max:255',
            'cta_link' => 'nullable|string|max:255',
            'executive_director_name' => 'nullable|string|max:255',
            'executive_director_position' => 'nullable|string|max:255',
            'executive_director_image' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'executive_director_image_remove' => 'nullable|boolean',
            'executive_director_visible' => 'nullable|boolean',
            'executive_director_email' => 'nullable|email|max:255',
            'executive_director_phone' => 'nullable|string|max:50',
            'executive_director_bio' => 'nullable|string|max:5000',
            'executive_director_facebook' => 'nullable|url|max:255',
            'executive_director_twitter' => 'nullable|url|max:255',
            'executive_director_instagram' => 'nullable|url|max:255',
            'executive_director_linkedin' => 'nullable|url|max:255',
            'executive_director_whatsapp' => 'nullable|string|max:50',
            'executive_director_telegram' => 'nullable|url|max:255',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Only update AboutUs if content or section_title is provided (for "About Us" page)
        // If only executive director data is being saved, skip AboutUs update
        if ($request->filled('content') || $request->filled('section_title') || $request->hasFile('image_file')) {
            // Check if record exists - replaces: SELECT id FROM about_us LIMIT 1
            $about = AboutUs::getLatest();
            
            $data = $request->only(['section_title', 'content', 'cta_text', 'cta_link']);
            
            // Set default title if not exists (for backward compatibility)
            if (!$about || !$about->title) {
                $data['title'] = 'من نحن';
            } else {
                $data['title'] = $about->title;
            }

            // Handle image upload
            if ($request->hasFile('image_file')) {
                // Delete old image if exists
                if ($about && $about->image) {
                    $oldImagePath = str_replace('storage/', '', $about->image);
                    $oldImagePath = ltrim($oldImagePath, '/');
                    if (Storage::disk('public')->exists($oldImagePath)) {
                        Storage::disk('public')->delete($oldImagePath);
                    }
                }
                
                // Upload image using helper function
                $uploadResult = upload_image_safely($request->file('image_file'), 'about');
                
                if (!$uploadResult['success']) {
                    return back()->withErrors(['image_file' => $uploadResult['message']])->withInput();
                }
                
                $data['image'] = $uploadResult['path'];
            } elseif ($request->has('image') && $about) {
                // Keep existing image if no new file uploaded
                $data['image'] = $about->image;
            }
            
            if ($about) {
                // Update existing - replaces: UPDATE about_us SET title = ?, content = ?, image = ? WHERE id = ...
                $about->update($data);
            } else {
                // Create new - replaces: INSERT INTO about_us (title, content, image) VALUES (?, ?, ?)
                AboutUs::create($data);
            }
        }

        // Handle stats
        if ($request->has('stats')) {
            $stats = $request->input('stats', []);
            
            // Delete existing stats
            AboutStat::truncate();
            
            // Create new stats
            foreach ($stats as $index => $stat) {
                if (!empty($stat['number']) && !empty($stat['label'])) {
                    AboutStat::create([
                        'icon' => $stat['icon'] ?? 'fas fa-star',
                        'number' => $stat['number'],
                        'label' => $stat['label'],
                        'order' => $index,
                    ]);
                }
            }
        }

        // Handle features
        if ($request->has('features')) {
            $features = $request->input('features', []);
            
            // Delete existing features
            AboutFeature::truncate();
            
            // Create new features
            foreach ($features as $index => $feature) {
                if (!empty($feature['title']) && !empty($feature['text'])) {
                    AboutFeature::create([
                        'icon' => $feature['icon'] ?? 'fas fa-check-circle',
                        'title' => $feature['title'],
                        'text' => $feature['text'],
                        'order' => $index,
                    ]);
                }
            }
        }

        // Handle executive director settings - save all fields
        SiteSetting::setValue('executive_director_visible', $request->has('executive_director_visible') ? '1' : '0');
        
        // Save executive director fields - always save values (even if empty)
        $executiveName = $request->input('executive_director_name', '');
        $executiveEmail = $request->input('executive_director_email', '');
        $executivePhone = $request->input('executive_director_phone', '');
        $executiveBio = $request->input('executive_director_bio', '');
        
        SiteSetting::setValue('executive_director_name', $executiveName);
        SiteSetting::setValue('executive_director_position', $request->input('executive_director_position', ''));
        SiteSetting::setValue('executive_director_email', $executiveEmail);
        SiteSetting::setValue('executive_director_phone', $executivePhone);
        SiteSetting::setValue('executive_director_bio', $executiveBio);
        SiteSetting::setValue('executive_director_facebook', $request->input('executive_director_facebook', ''));
        SiteSetting::setValue('executive_director_twitter', $request->input('executive_director_twitter', ''));
        SiteSetting::setValue('executive_director_instagram', $request->input('executive_director_instagram', ''));
        SiteSetting::setValue('executive_director_linkedin', $request->input('executive_director_linkedin', ''));
        SiteSetting::setValue('executive_director_whatsapp', $request->input('executive_director_whatsapp', ''));
        SiteSetting::setValue('executive_director_telegram', $request->input('executive_director_telegram', ''));
        
        // Debug logging (commented out - uncomment if needed)
        // \Log::info('Executive Director Data Saved:', [
        //     'name' => $executiveName,
        //     'email' => $executiveEmail,
        //     'phone' => $executivePhone,
        //     'bio' => $executiveBio ? 'Has bio' : 'No bio',
        // ]);

        // Handle executive director image upload
        if ($request->hasFile('executive_director_image')) {
            $executiveImage = $request->file('executive_director_image');
            $uploadResult = upload_image_safely($executiveImage, 'executive_director');
            if ($uploadResult['success'] && !empty($uploadResult['path'])) {
                SiteSetting::setValue('executive_director_image', $uploadResult['path']);
            }
        } elseif ($request->has('executive_director_image_remove')) {
            // Remove executive director image if requested
            $oldExecutiveImage = SiteSetting::getValue('executive_director_image');
            if ($oldExecutiveImage) {
                $oldExecutiveImagePath = str_replace('storage/', '', $oldExecutiveImage);
                $oldExecutiveImagePath = ltrim($oldExecutiveImagePath, '/');
                if (Storage::disk('public')->exists($oldExecutiveImagePath)) {
                    Storage::disk('public')->delete($oldExecutiveImagePath);
                }
            }
            SiteSetting::setValue('executive_director_image', '');
        }

        return back()->with('success_message', 'تم حفظ المعلومات بنجاح!');
    }
}
