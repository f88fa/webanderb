<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use App\Models\HeroSliderImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

/**
 * SettingsController
 * Migrated from Plain PHP: pages/settings.php
 * Handles site settings management
 */
class SettingsController extends Controller
{
    /**
     * Show settings page
     * Replaces: pages/settings.php GET request handling
     */
    public function index()
    {
        $settings = SiteSetting::getAllAsArray();
        
        return view('dashboard.index', [
            'page' => 'settings',
            'settings' => $settings
        ]);
    }

    /**
     * Update settings
     * Replaces: pages/settings.php POST request with mysqli UPDATE
     */
    public function update(Request $request)
    {
        // Validate input
        $baseRules = [
            'site_title' => 'nullable|string|max:255',
            'site_description' => 'nullable|string',
            'site_description_footer' => 'nullable|string',
            'site_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            'site_icon_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            'site_icon' => 'nullable|string|max:100',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:50',
            'contact_address' => 'nullable|string|max:255',
            'working_hours_weekdays' => 'nullable|string|max:50',
            'working_hours_weekend' => 'nullable|string|max:50',
            'working_days' => 'nullable|array',
            'working_days.*' => 'nullable|string|in:sunday,monday,tuesday,wednesday,thursday,friday,saturday',
            'working_hours_from' => 'nullable|string|max:10',
            'working_hours_to' => 'nullable|string|max:10',
            'social_facebook' => 'nullable|url|max:255',
            'social_twitter' => 'nullable|url|max:255',
            'social_instagram' => 'nullable|url|max:255',
            'social_linkedin' => 'nullable|url|max:255',
            'social_youtube' => 'nullable|url|max:255',
            'social_whatsapp' => 'nullable|string|max:50',
            'social_telegram' => 'nullable|url|max:255',
            'navbar_bg_color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'navbar_text_color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'navbar_border_color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'dashboard_primary_color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'dashboard_primary_dark' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'dashboard_secondary_color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'dashboard_accent_color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'dashboard_sidebar_bg' => 'nullable|string|max:255',
            'dashboard_content_bg' => 'nullable|string|max:255',
            'dashboard_text_primary' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'dashboard_text_secondary' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'dashboard_border_color' => 'nullable|string|max:255',
            'dashboard_bg_gradient' => 'nullable|string|max:500',
            'license_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            'hero_background_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            'hero_background_opacity' => 'nullable|integer|min:0|max:100',
            'hero_background_video' => 'nullable|file|mimes:mp4,webm,ogg,avi,mov,wmv,flv|mimetypes:video/mp4,video/webm,video/ogg,video/avi,video/quicktime,video/x-msvideo,video/x-ms-wmv,video/x-flv|max:51200',
            'hero_background_video_opacity' => 'nullable|integer|min:0|max:100',
            'hero_background_video_remove' => 'nullable|boolean',
            'reset_hero_background' => 'nullable|boolean',
            'hero_template_type' => 'nullable|string|in:default,video,slider',
            'hero_video_title_background' => 'nullable|boolean',
            'hero_video_show_contact_button' => 'nullable|boolean',
            'popup_video_enabled' => 'nullable|boolean',
            'popup_video_url' => 'nullable|string|max:500',
            'popup_video_position' => 'nullable|string|in:left,right',
            'popup_video_size' => 'nullable|string|in:small,medium,large',
            'popup_video_file' => 'nullable|file|mimes:mp4,webm,ogg|max:20480',
            'popup_video_file_remove' => 'nullable|boolean',
            'logo_background_type' => 'nullable|string|in:white,gradient,transparent,custom',
            'logo_background_color' => 'nullable|string|max:7',
            'site_logo_icon_size' => 'nullable|integer|min:40|max:120',
            'site_hero_icon_size' => 'nullable|integer|min:120|max:350',
            'hero_title_font_size' => 'nullable|integer|min:18|max:120',
            'hero_slider_images' => 'nullable|array',
            'hero_slider_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            'hero_slider_titles' => 'nullable|array',
            'hero_slider_titles.*' => 'nullable|string|max:255',
            'hero_slider_descriptions' => 'nullable|array',
            'hero_slider_descriptions.*' => 'nullable|string|max:500',
            'hero_slider_orders' => 'nullable|array',
            'hero_slider_orders.*' => 'nullable|integer',
            'hero_slider_delete' => 'nullable|array',
            'hero_slider_delete.*' => 'nullable|integer',
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
            'floating_whatsapp_enabled' => 'nullable|boolean',
            'floating_whatsapp_number' => 'nullable|string|max:50',
            'floating_donate_enabled' => 'nullable|boolean',
            'floating_donate_link' => 'nullable|url|max:500',
            'floating_donate_text' => 'nullable|string|max:100',
            'google_maps_link' => 'nullable|url|max:500',
            'letter_paper_header_type' => 'nullable|string|in:none,html,image',
            'letter_paper_header_content' => 'nullable|string|max:15000',
            'letter_paper_header_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
            'letter_paper_header_image_remove' => 'nullable|boolean',
            'letter_paper_middle_type' => 'nullable|string|in:none,html,image',
            'letter_paper_middle_content' => 'nullable|string|max:15000',
            'letter_paper_middle_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
            'letter_paper_middle_image_remove' => 'nullable|boolean',
            'letter_paper_footer_type' => 'nullable|string|in:none,html,image',
            'letter_paper_footer_content' => 'nullable|string|max:15000',
            'letter_paper_footer_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
            'letter_paper_footer_image_remove' => 'nullable|boolean',
            'section_about_visible' => 'nullable|boolean',
            'section_vision_mission_visible' => 'nullable|boolean',
            'section_services_visible' => 'nullable|boolean',
            'section_media_visible' => 'nullable|boolean',
            'section_projects_visible' => 'nullable|boolean',
            'section_testimonials_visible' => 'nullable|boolean',
            'section_partners_visible' => 'nullable|boolean',
            'section_news_visible' => 'nullable|boolean',
            'section_banner_sections_visible' => 'nullable|boolean',
            'section_staff_visible' => 'nullable|boolean',
            'section_reports_visible' => 'nullable|boolean',
            'section_about_title' => 'nullable|string|max:255',
            'section_vision_mission_title' => 'nullable|string|max:255',
            'section_services_title' => 'nullable|string|max:255',
            'section_projects_title' => 'nullable|string|max:255',
            'section_media_title' => 'nullable|string|max:255',
            'section_testimonials_title' => 'nullable|string|max:255',
            'section_partners_title' => 'nullable|string|max:255',
            'section_news_title' => 'nullable|string|max:255',
            'section_banner_sections_title' => 'nullable|string|max:255',
            'section_staff_title' => 'nullable|string|max:255',
            'section_reports_title' => 'nullable|string|max:255',
            'page_board_members_title' => 'nullable|string|max:255',
            'page_staff_title' => 'nullable|string|max:255',
            'section_reports_description' => 'nullable|string|max:500',
            'section_reports_icon' => 'nullable|string|max:100',
            'section_about_icon' => 'nullable|string|max:100',
            'section_vision_mission_icon' => 'nullable|string|max:100',
            'section_services_icon' => 'nullable|string|max:100',
            'section_projects_icon' => 'nullable|string|max:100',
            'section_media_icon' => 'nullable|string|max:100',
            'section_testimonials_icon' => 'nullable|string|max:100',
            'section_partners_icon' => 'nullable|string|max:100',
            'section_news_icon' => 'nullable|string|max:100',
            'section_banner_sections_icon' => 'nullable|string|max:100',
            'section_staff_icon' => 'nullable|string|max:100',
            'section_reports_icon' => 'nullable|string|max:100',
            'section_services_description' => 'nullable|string|max:500',
            'section_about_features_title' => 'nullable|string|max:255',
            'section_about_features_icon' => 'nullable|string|max:100',
            'section_about_features_description' => 'nullable|string|max:500',
            // Section background images
            'section_about_bg_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            'section_vision_mission_bg_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            'section_services_bg_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            'section_projects_bg_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            'section_media_bg_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            'section_testimonials_bg_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            'section_partners_bg_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            'section_news_bg_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            'section_banner_sections_bg_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            'section_staff_bg_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            // Section background opacity
            'section_about_bg_opacity' => 'nullable|integer|min:0|max:100',
            'section_vision_mission_bg_opacity' => 'nullable|integer|min:0|max:100',
            'section_services_bg_opacity' => 'nullable|integer|min:0|max:100',
            'section_projects_bg_opacity' => 'nullable|integer|min:0|max:100',
            'section_media_bg_opacity' => 'nullable|integer|min:0|max:100',
            'section_testimonials_bg_opacity' => 'nullable|integer|min:0|max:100',
            'section_partners_bg_opacity' => 'nullable|integer|min:0|max:100',
            'section_news_bg_opacity' => 'nullable|integer|min:0|max:100',
            'section_banner_sections_bg_opacity' => 'nullable|integer|min:0|max:100',
            'section_staff_bg_opacity' => 'nullable|integer|min:0|max:100',
        ];
        $baseRules['hero_footer_bg_color'] = ['nullable', 'string', 'max:7', 'regex:/^#?[0-9A-Fa-f]{6}$/'];
        $baseRules['hero_footer_text_color'] = ['nullable', 'string', 'max:7', 'regex:/^#?[0-9A-Fa-f]{6}$/'];
        $baseRules['hero_footer_title_color'] = ['nullable', 'string', 'max:7', 'regex:/^#?[0-9A-Fa-f]{6}$/'];
        $baseRules['hero_circle_bg_color'] = ['nullable', 'string', 'max:7', 'regex:/^#?[0-9A-Fa-f]{6}$/'];
        $baseRules['hero_circle_icon_color'] = ['nullable', 'string', 'max:7', 'regex:/^#?[0-9A-Fa-f]{6}$/'];
        $baseRules['hero_social_icons_color'] = ['nullable', 'string', 'max:7', 'regex:/^#?[0-9A-Fa-f]{6}$/'];
        $baseRules['page_content_bg_color'] = ['nullable', 'string', 'max:7', 'regex:/^#?[0-9A-Fa-f]{6}$/'];
        $baseRules['page_content_text_color'] = ['nullable', 'string', 'max:7', 'regex:/^#?[0-9A-Fa-f]{6}$/'];
        $baseRules['page_content_title_color'] = ['nullable', 'string', 'max:7', 'regex:/^#?[0-9A-Fa-f]{6}$/'];
        $sectionKeys = ['about', 'vision_mission', 'banner_sections', 'services', 'projects', 'media', 'testimonials', 'partners', 'news', 'contact'];
        $sectionColorKeys = ['bg_color', 'text_color', 'title_color', 'icon_color', 'card_bg_color', 'card_title_color', 'hover_text_color', 'button_color'];
        foreach ($sectionKeys as $sk) {
            foreach ($sectionColorKeys as $ck) {
                $baseRules["section_{$sk}_{$ck}"] = ['nullable', 'string', 'max:7', 'regex:/^#?[0-9A-Fa-f]{6}$/'];
            }
        }
        $validator = Validator::make($request->all(), $baseRules);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Handle logo upload
        if ($request->hasFile('site_logo')) {
            $logo = $request->file('site_logo');
            $logoName = 'logo_' . time() . '.' . $logo->getClientOriginalExtension();
            $logoPath = $logo->storeAs('uploads', $logoName, 'public');
            // Store relative path only
            SiteSetting::setValue('site_logo', 'uploads/' . $logoName);
        } elseif ($request->has('site_logo_remove')) {
            // Remove logo if requested
            $oldLogo = SiteSetting::getValue('site_logo');
            if ($oldLogo) {
                $oldLogoPath = str_replace('storage/', '', $oldLogo);
                $oldLogoPath = ltrim($oldLogoPath, '/');
                if (Storage::disk('public')->exists($oldLogoPath)) {
                    Storage::disk('public')->delete($oldLogoPath);
                }
            }
            SiteSetting::setValue('site_logo', '');
        }

        // Handle icon file upload
        if ($request->hasFile('site_icon_file')) {
            $icon = $request->file('site_icon_file');
            $iconName = 'icon_' . time() . '.' . $icon->getClientOriginalExtension();
            $iconPath = $icon->storeAs('uploads', $iconName, 'public');
            // Store relative path only
            SiteSetting::setValue('site_icon_file', 'uploads/' . $iconName);
            // Clear Font Awesome icon if image is uploaded
            SiteSetting::setValue('site_icon', '');
        }

        // Handle license image upload
        if ($request->hasFile('license_image')) {
            $license = $request->file('license_image');
            $licenseName = 'license_' . time() . '.' . $license->getClientOriginalExtension();
            $licensePath = $license->storeAs('uploads', $licenseName, 'public');
            // Store relative path only
            SiteSetting::setValue('license_image', 'uploads/' . $licenseName);
        } elseif ($request->has('license_image_remove')) {
            // Remove license image if requested
            $oldLicense = SiteSetting::getValue('license_image');
            if ($oldLicense) {
                $oldLicensePath = str_replace('storage/', '', $oldLicense);
                $oldLicensePath = ltrim($oldLicensePath, '/');
                if (Storage::disk('public')->exists($oldLicensePath)) {
                    Storage::disk('public')->delete($oldLicensePath);
                }
            }
            SiteSetting::setValue('license_image', '');
        }

        // Handle hero background image upload
        if ($request->hasFile('hero_background_image')) {
            $heroBg = $request->file('hero_background_image');
            $heroBgName = 'hero_bg_' . time() . '.' . $heroBg->getClientOriginalExtension();
            $heroBgPath = $heroBg->storeAs('uploads', $heroBgName, 'public');
            // Store relative path only
            SiteSetting::setValue('hero_background_image', 'uploads/' . $heroBgName);
        } elseif ($request->has('hero_background_image_remove') || $request->has('reset_hero_background')) {
            // Remove hero background image if requested
            $oldHeroBg = SiteSetting::getValue('hero_background_image');
            if ($oldHeroBg) {
                $oldHeroBgPath = str_replace('storage/', '', $oldHeroBg);
                $oldHeroBgPath = ltrim($oldHeroBgPath, '/');
                if (Storage::disk('public')->exists($oldHeroBgPath)) {
                    Storage::disk('public')->delete($oldHeroBgPath);
                }
            }
            SiteSetting::setValue('hero_background_image', '');
            if ($request->has('reset_hero_background')) {
                SiteSetting::setValue('hero_background_opacity', '30');
            }
        }

        // Handle hero background opacity
        if ($request->has('hero_background_opacity')) {
            SiteSetting::setValue('hero_background_opacity', $request->input('hero_background_opacity'));
        }

        // Handle hero background video upload
        if ($request->hasFile('hero_background_video')) {
            $heroVideo = $request->file('hero_background_video');
            try {
                $dirCheck = ensure_uploads_directory();
                if (!$dirCheck['success']) {
                    \Log::error('Failed to ensure uploads directory: ' . $dirCheck['message']);
                } else {
                    $videoName = 'hero_video_' . time() . '_' . uniqid() . '.' . $heroVideo->getClientOriginalExtension();
                    $videoPath = Storage::disk('public')->putFileAs('uploads', $heroVideo, $videoName);
                    if ($videoPath) {
                        $videoPath = str_replace('\\', '/', $videoPath);
                        $videoPath = ltrim($videoPath, '/');
                        $videoPath = preg_replace('#^storage/#', '', $videoPath);
                        SiteSetting::setValue('hero_background_video', $videoPath);
                        \Log::info('Hero video uploaded successfully: ' . $videoPath);
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Hero video upload error: ' . $e->getMessage());
            }
        } elseif ($request->has('hero_background_video_remove')) {
            // Remove hero background video if requested
            $oldHeroVideo = SiteSetting::getValue('hero_background_video');
            if ($oldHeroVideo) {
                $oldHeroVideoPath = str_replace('storage/', '', $oldHeroVideo);
                $oldHeroVideoPath = ltrim($oldHeroVideoPath, '/');
                if (Storage::disk('public')->exists($oldHeroVideoPath)) {
                    Storage::disk('public')->delete($oldHeroVideoPath);
                }
            }
            SiteSetting::setValue('hero_background_video', '');
        }

        // Handle hero background video opacity
        if ($request->has('hero_background_video_opacity')) {
            SiteSetting::setValue('hero_background_video_opacity', $request->input('hero_background_video_opacity'));
        }

        // Handle hero template type
        if ($request->has('hero_template_type')) {
            SiteSetting::setValue('hero_template_type', $request->input('hero_template_type'));
        }

        // Handle hero video template settings
        SiteSetting::setValue('hero_video_title_background', $request->has('hero_video_title_background') ? '1' : '0');
        SiteSetting::setValue('hero_video_show_contact_button', $request->has('hero_video_show_contact_button') ? '1' : '0');

        SiteSetting::setValue('popup_video_enabled', $request->has('popup_video_enabled') ? '1' : '0');
        if ($request->has('popup_video_url')) {
            SiteSetting::setValue('popup_video_url', trim($request->input('popup_video_url', '')));
        }
        if ($request->has('popup_video_position')) {
            SiteSetting::setValue('popup_video_position', $request->input('popup_video_position') === 'left' ? 'left' : 'right');
        }
        if ($request->has('popup_video_size')) {
            $size = $request->input('popup_video_size');
            SiteSetting::setValue('popup_video_size', in_array($size, ['small', 'medium', 'large'], true) ? $size : 'medium');
        }
        if ($request->hasFile('popup_video_file')) {
            $popupVideo = $request->file('popup_video_file');
            $popupVideoName = 'popup_video_' . time() . '.' . $popupVideo->getClientOriginalExtension();
            $popupVideo->storeAs('uploads', $popupVideoName, 'public');
            SiteSetting::setValue('popup_video_file', 'uploads/' . $popupVideoName);
        } elseif ($request->has('popup_video_file_remove')) {
            $oldPopupVideo = SiteSetting::getValue('popup_video_file');
            if ($oldPopupVideo) {
                $oldPath = str_replace('storage/', '', $oldPopupVideo);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }
            SiteSetting::setValue('popup_video_file', '');
        }

        // Handle logo background settings
        if ($request->has('logo_background_type')) {
            SiteSetting::setValue('logo_background_type', $request->input('logo_background_type'));
        }
        if ($request->has('logo_background_color')) {
            SiteSetting::setValue('logo_background_color', $request->input('logo_background_color'));
        }
        if ($request->filled('site_logo_icon_size')) {
            SiteSetting::setValue('site_logo_icon_size', $request->input('site_logo_icon_size'));
        }
        if ($request->filled('site_hero_icon_size')) {
            SiteSetting::setValue('site_hero_icon_size', $request->input('site_hero_icon_size'));
        }
        if ($request->filled('hero_title_font_size')) {
            $size = (int) $request->input('hero_title_font_size');
            SiteSetting::setValue('hero_title_font_size', $size >= 18 && $size <= 120 ? (string) $size : '56');
        }

        // Handle hero slider images
        if ($request->has('hero_slider_images')) {
            $sliderImages = $request->file('hero_slider_images');
            $titles = $request->input('hero_slider_titles', []);
            $descriptions = $request->input('hero_slider_descriptions', []);
            $orders = $request->input('hero_slider_orders', []);

            foreach ($sliderImages as $index => $image) {
                if ($image && $image->isValid()) {
                    try {
                        $imageName = 'hero_slider_' . time() . '_' . $index . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                        $imagePath = Storage::disk('public')->putFileAs('uploads', $image, $imageName);
                        if ($imagePath) {
                            $imagePath = str_replace('\\', '/', $imagePath);
                            $imagePath = ltrim($imagePath, '/');
                            $imagePath = preg_replace('#^storage/#', '', $imagePath);
                            
                            HeroSliderImage::create([
                                'image' => $imagePath,
                                'title' => $titles[$index] ?? null,
                                'description' => $descriptions[$index] ?? null,
                                'order' => $orders[$index] ?? 0,
                                'is_active' => true,
                            ]);
                        }
                    } catch (\Exception $e) {
                        \Log::error('Hero slider image upload error: ' . $e->getMessage());
                    }
                }
            }
        }

        // Handle hero slider image deletion
        if ($request->has('hero_slider_delete')) {
            $deleteIds = $request->input('hero_slider_delete', []);
            foreach ($deleteIds as $id) {
                $sliderImage = HeroSliderImage::find($id);
                if ($sliderImage) {
                    // Delete file
                    $imagePath = str_replace('storage/', '', $sliderImage->image);
                    $imagePath = ltrim($imagePath, '/');
                    if (Storage::disk('public')->exists($imagePath)) {
                        Storage::disk('public')->delete($imagePath);
                    }
                    // Delete record
                    $sliderImage->delete();
                }
            }
        }

        // Handle section background images and opacity
        $sections = [
            'about', 'vision_mission', 'services', 'projects', 'media', 
            'testimonials', 'partners', 'news', 'banner_sections', 'staff', 'reports'
        ];

        foreach ($sections as $section) {
            $bgImageKey = 'section_' . $section . '_bg_image';
            $bgOpacityKey = 'section_' . $section . '_bg_opacity';
            $removeKey = $bgImageKey . '_remove';

            // Handle background image upload
            if ($request->hasFile($bgImageKey)) {
                $bgImage = $request->file($bgImageKey);
                
                // Validate file is actually uploaded (not temp path)
                if (!$bgImage->isValid()) {
                    \Log::error('Invalid file upload for ' . $bgImageKey);
                    continue;
                }
                
                $bgImageName = 'section_' . $section . '_bg_' . time() . '_' . uniqid() . '.' . $bgImage->getClientOriginalExtension();
                
                // Use helper function for safe upload
                $uploadResult = upload_image_safely($bgImage, 'section_' . $section . '_bg');
                
                \Log::info('Upload result for ' . $bgImageKey . ': ' . json_encode($uploadResult));
                
                if ($uploadResult['success'] && !empty($uploadResult['path'])) {
                    // Ensure path is clean and relative (e.g., 'uploads/filename.jpg')
                    $cleanPath = trim($uploadResult['path']);
                    
                    \Log::info('Clean path before validation: ' . $cleanPath);
                    
                    // Remove any absolute paths or temp paths
                    if (strpos($cleanPath, '/private/') !== false || 
                        strpos($cleanPath, '/tmp/') !== false || 
                        strpos($cleanPath, '/var/folders/') !== false ||
                        strpos($cleanPath, 'php') === 0 ||
                        (strpos($cleanPath, 'http://') === false && strpos($cleanPath, 'https://') === false && strpos($cleanPath, 'uploads/') !== 0 && strpos($cleanPath, '/') === 0)) {
                        \Log::error('Invalid temp path detected for ' . $bgImageKey . ': ' . $cleanPath);
                        continue;
                    }
                    
                    // Ensure path starts with 'uploads/'
                    if (strpos($cleanPath, 'uploads/') !== 0) {
                        $cleanPath = 'uploads/' . ltrim($cleanPath, '/');
                    }
                    
                    // Final validation: path must be 'uploads/filename.ext'
                    if (!preg_match('#^uploads/[a-zA-Z0-9_\-\.]+\.(jpg|jpeg|png|gif|svg|webp)$#i', $cleanPath)) {
                        \Log::error('Invalid path format for ' . $bgImageKey . ': ' . $cleanPath);
                        continue;
                    }
                    
                    // Verify file exists before saving
                    $fullPath = storage_path('app/public/' . $cleanPath);
                    if (!file_exists($fullPath)) {
                        \Log::error('File does not exist before saving: ' . $fullPath);
                        continue;
                    }
                    
                    // Store relative path only (e.g., 'uploads/filename.jpg')
                    SiteSetting::setValue($bgImageKey, $cleanPath);
                    
                    // Verify it was saved correctly
                    $savedValue = SiteSetting::getValue($bgImageKey);
                    if ($savedValue !== $cleanPath) {
                        \Log::error('Path mismatch after save! Expected: ' . $cleanPath . ', Got: ' . $savedValue);
                    } else {
                        \Log::info('Background image saved successfully for ' . $bgImageKey . ': ' . $cleanPath);
                    }
                } else {
                    \Log::error('Failed to upload background image for ' . $bgImageKey . ': ' . ($uploadResult['message'] ?? 'Unknown error') . ' | Result: ' . json_encode($uploadResult));
                }
            } elseif ($request->has($removeKey)) {
                // Remove background image if requested
                $oldBgImage = SiteSetting::getValue($bgImageKey);
                if ($oldBgImage) {
                    $oldBgImagePath = str_replace('storage/', '', $oldBgImage);
                    $oldBgImagePath = ltrim($oldBgImagePath, '/');
                    if (Storage::disk('public')->exists($oldBgImagePath)) {
                        Storage::disk('public')->delete($oldBgImagePath);
                    }
                }
                SiteSetting::setValue($bgImageKey, '');
                // Reset opacity to default
                SiteSetting::setValue($bgOpacityKey, '30');
            }

            // Handle background opacity
            if ($request->has($bgOpacityKey)) {
                SiteSetting::setValue($bgOpacityKey, $request->input($bgOpacityKey));
            }
        }

        // Handle floating buttons settings
        SiteSetting::setValue('floating_whatsapp_enabled', $request->has('floating_whatsapp_enabled') ? '1' : '0');
        SiteSetting::setValue('floating_donate_enabled', $request->has('floating_donate_enabled') ? '1' : '0');
        
        if ($request->has('floating_whatsapp_number')) {
            SiteSetting::setValue('floating_whatsapp_number', $request->input('floating_whatsapp_number'));
        }
        if ($request->has('floating_donate_link')) {
            SiteSetting::setValue('floating_donate_link', $request->input('floating_donate_link'));
        }
        if ($request->has('floating_donate_text')) {
            SiteSetting::setValue('floating_donate_text', $request->input('floating_donate_text'));
        }

        // Handle working days and hours
        if ($request->has('working_days')) {
            $workingDays = $request->input('working_days', []);
            SiteSetting::setValue('working_days', json_encode($workingDays));
        } else {
            SiteSetting::setValue('working_days', json_encode([]));
        }
        
        if ($request->has('working_hours_from')) {
            SiteSetting::setValue('working_hours_from', $request->input('working_hours_from'));
        }
        
        if ($request->has('working_hours_to')) {
            SiteSetting::setValue('working_hours_to', $request->input('working_hours_to'));
        }

        // Handle section visibility settings
        // Only update visibility if the field is present in the request (to preserve existing values when updating other settings)
        if ($request->has('section_about_visible')) {
            SiteSetting::setValue('section_about_visible', $request->input('section_about_visible') ? '1' : '0');
        }
        if ($request->has('section_vision_mission_visible')) {
            SiteSetting::setValue('section_vision_mission_visible', $request->input('section_vision_mission_visible') ? '1' : '0');
        }
        if ($request->has('section_services_visible')) {
            SiteSetting::setValue('section_services_visible', $request->input('section_services_visible') ? '1' : '0');
        }
        if ($request->has('section_media_visible')) {
            SiteSetting::setValue('section_media_visible', $request->input('section_media_visible') ? '1' : '0');
        }
        if ($request->has('section_projects_visible')) {
            SiteSetting::setValue('section_projects_visible', $request->input('section_projects_visible') ? '1' : '0');
        }
        if ($request->has('section_testimonials_visible')) {
            SiteSetting::setValue('section_testimonials_visible', $request->input('section_testimonials_visible') ? '1' : '0');
        }
        if ($request->has('section_partners_visible')) {
            SiteSetting::setValue('section_partners_visible', $request->input('section_partners_visible') ? '1' : '0');
        }
        if ($request->has('section_news_visible')) {
            SiteSetting::setValue('section_news_visible', $request->input('section_news_visible') ? '1' : '0');
        }
        if ($request->has('section_banner_sections_visible')) {
            SiteSetting::setValue('section_banner_sections_visible', $request->input('section_banner_sections_visible') ? '1' : '0');
        }
        if ($request->has('section_staff_visible')) {
            SiteSetting::setValue('section_staff_visible', $request->input('section_staff_visible') ? '1' : '0');
        }

        // Handle dashboard color settings
        $dashboardColorFields = [
            'dashboard_primary_color', 'dashboard_primary_dark', 'dashboard_secondary_color', 
            'dashboard_accent_color', 'dashboard_sidebar_bg', 'dashboard_content_bg',
            'dashboard_text_primary', 'dashboard_text_secondary', 'dashboard_border_color',
            'dashboard_bg_gradient'
        ];
        
        foreach ($dashboardColorFields as $field) {
            if ($request->has($field)) {
                SiteSetting::setValue($field, $request->input($field));
            }
        }

        // Letter paper layout (header, middle, footer)
        $letterZones = ['header', 'middle', 'footer'];
        foreach ($letterZones as $zone) {
            $typeKey = "letter_paper_{$zone}_type";
            $contentKey = "letter_paper_{$zone}_content";
            $imageKey = "letter_paper_{$zone}_image";
            $removeKey = "letter_paper_{$zone}_image_remove";
            if ($request->hasFile($imageKey)) {
                $file = $request->file($imageKey);
                $name = "letter_paper_{$zone}_" . time() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('uploads', $name, 'public');
                SiteSetting::setValue($contentKey, 'uploads/' . $name);
                SiteSetting::setValue($typeKey, 'image');
            } elseif ($request->has($removeKey)) {
                $old = SiteSetting::getValue($contentKey);
                if ($old && strpos($old, 'uploads/') === 0 && Storage::disk('public')->exists($old)) {
                    Storage::disk('public')->delete($old);
                }
                SiteSetting::setValue($contentKey, '');
                SiteSetting::setValue($typeKey, 'none');
            } elseif ($request->has($typeKey)) {
                $typeVal = $request->input($typeKey);
                SiteSetting::setValue($typeKey, $typeVal);
                if ($typeVal === 'html' && $request->has($contentKey)) {
                    SiteSetting::setValue($contentKey, $request->input($contentKey) ?? '');
                } elseif ($typeVal === 'none') {
                    SiteSetting::setValue($contentKey, '');
                }
            }
        }

        // Update text settings
        $excludedKeys = [
            '_token', 'save_settings', 'site_logo', 'site_icon_file', 'license_image', 'license_image_remove',
            'floating_whatsapp_enabled', 'floating_whatsapp_number', 'floating_donate_enabled', 'floating_donate_link', 'floating_donate_text',
            'section_about_visible', 'section_vision_mission_visible', 'section_services_visible', 'section_media_visible',
            'section_projects_visible', 'section_testimonials_visible', 'section_partners_visible', 'section_news_visible',
            'section_banner_sections_visible',
            'dashboard_primary_color', 'dashboard_primary_dark', 'dashboard_secondary_color', 
            'dashboard_accent_color', 'dashboard_sidebar_bg', 'dashboard_content_bg',
            'dashboard_text_primary', 'dashboard_text_secondary', 'dashboard_border_color',
            'dashboard_primary_color_text', 'dashboard_primary_dark_text', 'dashboard_secondary_color_text',
            'dashboard_accent_color_text', 'dashboard_text_primary_text', 'dashboard_text_secondary_text',
            'site_primary_color_text', 'site_primary_dark_text', 'site_secondary_color_text', 'site_accent_color_text',
            'working_days', 'working_hours_from', 'working_hours_to',
            // Exclude section background images - they are handled separately above
            'section_about_bg_image', 'section_vision_mission_bg_image', 'section_services_bg_image',
            'section_projects_bg_image', 'section_media_bg_image', 'section_testimonials_bg_image',
            'section_partners_bg_image', 'section_news_bg_image', 'section_banner_sections_bg_image', 'section_staff_bg_image', 'section_reports_bg_image',
            'section_about_bg_image_remove', 'section_vision_mission_bg_image_remove', 'section_services_bg_image_remove',
            'section_projects_bg_image_remove', 'section_media_bg_image_remove', 'section_testimonials_bg_image_remove',
            'section_partners_bg_image_remove', 'section_news_bg_image_remove', 'section_banner_sections_bg_image_remove', 'section_staff_bg_image_remove', 'section_reports_bg_image_remove',
            'section_about_bg_opacity', 'section_vision_mission_bg_opacity', 'section_services_bg_opacity',
            'section_projects_bg_opacity', 'section_media_bg_opacity', 'section_testimonials_bg_opacity',
            'section_partners_bg_opacity', 'section_news_bg_opacity', 'section_banner_sections_bg_opacity', 'section_staff_bg_opacity', 'section_reports_bg_opacity',
            'hero_background_image', 'hero_background_image_remove', 'reset_hero_background', 'hero_background_opacity',
            'hero_background_video', 'hero_background_video_remove', 'hero_background_video_opacity',
            'popup_video_file', 'popup_video_file_remove',
            'executive_director_name', 'executive_director_position', 'executive_director_image', 'executive_director_image_remove', 'executive_director_visible',
            'executive_director_email', 'executive_director_phone', 'executive_director_bio',
            'executive_director_facebook', 'executive_director_twitter', 'executive_director_instagram',
            'executive_director_linkedin', 'executive_director_whatsapp', 'executive_director_telegram',
            'hero_background_video', 'hero_background_video_remove', 'hero_background_video_opacity',
            'letter_paper_header_type', 'letter_paper_header_content', 'letter_paper_header_image', 'letter_paper_header_image_remove',
            'letter_paper_middle_type', 'letter_paper_middle_content', 'letter_paper_middle_image', 'letter_paper_middle_image_remove',
            'letter_paper_footer_type', 'letter_paper_footer_content', 'letter_paper_footer_image', 'letter_paper_footer_image_remove',
        ];
        
        foreach ($request->except($excludedKeys) as $key => $value) {
            if ($value !== null) {
                // Section colors: skip empty so we don't overwrite with blank; normalize hex without # to #RRGGBB
                if (preg_match('/^section_[a-z_]+_(bg_color|text_color|title_color|icon_color|card_bg_color|card_title_color|hover_text_color|button_color)$/', $key)) {
                    if ($value === '') continue;
                    if (is_string($value) && preg_match('/^[0-9A-Fa-f]{6}$/', $value)) $value = '#' . $value;
                }
                if (in_array($key, ['navbar_bg_color', 'navbar_text_color', 'hero_footer_bg_color', 'hero_footer_text_color', 'hero_footer_title_color', 'hero_circle_bg_color', 'hero_circle_icon_color', 'hero_social_icons_color', 'page_content_bg_color', 'page_content_text_color', 'page_content_title_color', 'article_bg_color', 'article_text_color', 'article_title_color', 'article_meta_color', 'article_button_color', 'article_button_hover_color'], true)) {
                    if ($value === '') continue;
                    if (is_string($value) && preg_match('/^[0-9A-Fa-f]{6}$/', $value)) $value = '#' . $value;
                }
                // Additional safety check: reject temp paths
                if (is_string($value) && (
                    strpos($value, '/private/') !== false || 
                    strpos($value, '/tmp/') !== false || 
                    strpos($value, '/var/folders/') !== false ||
                    (strpos($value, 'php') === 0 && strlen($value) < 20)
                )) {
                    \Log::warning('Rejected temp path in general settings save: ' . $key . ' => ' . $value);
                    continue; // Skip saving temp paths
                }
                SiteSetting::setValue($key, $value);
            }
        }

        $this->bumpFrontendCacheVersion();
        return back()->with('success_message', 'تم حفظ الإعدادات بنجاح!');
    }

    /**
     * حفظ إعدادات النظام فقط (شعار الجهة + ألوان لوحة التحكم + شكل ورقة الخطاب).
     * يُستخدم من صفحة /wesal/system-settings لضمان حفظ الألوان دون تعارض مع تحقق الصفحة الرئيسية.
     */
    public function updateSystemSettings(Request $request)
    {
        $request->validate([
            'organization_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            'organization_logo_remove' => 'nullable|boolean',
            'organization_type' => 'nullable|string|max:100',
            'dashboard_primary_color' => 'nullable|string|max:50',
            'dashboard_primary_dark' => 'nullable|string|max:50',
            'dashboard_secondary_color' => 'nullable|string|max:50',
            'dashboard_accent_color' => 'nullable|string|max:50',
            'dashboard_sidebar_bg' => 'nullable|string|max:255',
            'dashboard_content_bg' => 'nullable|string|max:255',
            'dashboard_card_bg' => 'nullable|string|max:255',
            'dashboard_text_primary' => 'nullable|string|max:50',
            'dashboard_text_secondary' => 'nullable|string|max:50',
            'dashboard_sidebar_text' => 'nullable|string|max:50',
            'dashboard_border_color' => 'nullable|string|max:255',
            'dashboard_bg_gradient' => 'nullable|string|max:500',
            'letter_paper_header_type' => 'nullable|string|in:none,html,image',
            'letter_paper_header_content' => 'nullable|string|max:15000',
            'letter_paper_header_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
            'letter_paper_header_image_remove' => 'nullable|boolean',
            'letter_paper_middle_type' => 'nullable|string|in:none,html,image',
            'letter_paper_middle_content' => 'nullable|string|max:15000',
            'letter_paper_middle_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
            'letter_paper_middle_image_remove' => 'nullable|boolean',
            'letter_paper_footer_type' => 'nullable|string|in:none,html,image',
            'letter_paper_footer_content' => 'nullable|string|max:15000',
            'letter_paper_footer_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
            'letter_paper_footer_image_remove' => 'nullable|boolean',
            'attendance_mode' => 'nullable|string|in:anywhere,ip_restricted,location_restricted',
            'attendance_allowed_ips' => 'nullable|array',
            'attendance_allowed_ips.*' => 'nullable|string|max:45',
            'attendance_location_lat' => 'nullable|numeric',
            'attendance_location_lng' => 'nullable|numeric',
            'attendance_location_radius_meters' => 'nullable|numeric|min:10|max:5000',
        ]);

        // شعار الجهة
        if ($request->hasFile('organization_logo')) {
            $file = $request->file('organization_logo');
            $name = 'organization_logo_' . time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('uploads', $name, 'public');
            SiteSetting::setValue('organization_logo', 'uploads/' . $name);
        }
        if ($request->has('organization_logo_remove')) {
            $old = SiteSetting::getValue('organization_logo');
            if ($old && strpos($old, 'uploads/') === 0 && Storage::disk('public')->exists($old)) {
                Storage::disk('public')->delete($old);
            }
            SiteSetting::setValue('organization_logo', '');
        }
        if ($request->has('organization_type')) {
            SiteSetting::setValue('organization_type', $request->input('organization_type'));
        }

        // ألوان لوحة التحكم — حفظ عند وجود الحقل (استخدام has لضمان حفظ خلفية القائمة الجانبية وغيرها)
        $dashboardColorFields = [
            'dashboard_primary_color', 'dashboard_primary_dark', 'dashboard_secondary_color',
            'dashboard_accent_color', 'dashboard_sidebar_bg', 'dashboard_content_bg',
            'dashboard_card_bg', 'dashboard_text_primary', 'dashboard_text_secondary',
            'dashboard_sidebar_text', 'dashboard_border_color', 'dashboard_bg_gradient',
        ];
        $hexOnlyFields = [
            'dashboard_primary_color', 'dashboard_primary_dark', 'dashboard_secondary_color',
            'dashboard_accent_color', 'dashboard_text_primary', 'dashboard_text_secondary',
            'dashboard_sidebar_text',
        ];
        foreach ($dashboardColorFields as $field) {
            if (!$request->has($field)) {
                continue;
            }
            $value = $request->input($field);
            if (in_array($field, $hexOnlyFields, true) && $value !== null && $value !== '') {
                $value = self::normalizeHexColor($value);
            }
            SiteSetting::setValue($field, $value !== null ? (string) $value : '');
        }

        // شكل ورقة الخطاب
        foreach (['header', 'middle', 'footer'] as $zone) {
            $typeKey = "letter_paper_{$zone}_type";
            $contentKey = "letter_paper_{$zone}_content";
            $imageKey = "letter_paper_{$zone}_image";
            $removeKey = "letter_paper_{$zone}_image_remove";
            if ($request->hasFile($imageKey)) {
                $file = $request->file($imageKey);
                $name = "letter_paper_{$zone}_" . time() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('uploads', $name, 'public');
                SiteSetting::setValue($contentKey, 'uploads/' . $name);
                SiteSetting::setValue($typeKey, 'image');
            } elseif ($request->has($removeKey)) {
                $old = SiteSetting::getValue($contentKey);
                if ($old && strpos($old, 'uploads/') === 0 && Storage::disk('public')->exists($old)) {
                    Storage::disk('public')->delete($old);
                }
                SiteSetting::setValue($contentKey, '');
                SiteSetting::setValue($typeKey, 'none');
            } elseif ($request->has($typeKey)) {
                $typeVal = $request->input($typeKey);
                SiteSetting::setValue($typeKey, $typeVal);
                if ($typeVal === 'html' && $request->has($contentKey)) {
                    SiteSetting::setValue($contentKey, $request->input($contentKey) ?? '');
                } elseif ($typeVal === 'none') {
                    SiteSetting::setValue($contentKey, '');
                }
            }
        }

        // إعدادات الحضور والانصراف
        if ($request->has('attendance_mode')) {
            SiteSetting::setValue('attendance_mode', $request->input('attendance_mode', 'anywhere'));
        }
        if ($request->has('attendance_allowed_ips')) {
            $ips = $request->input('attendance_allowed_ips', []);
            $ips = is_array($ips) ? array_values(array_filter(array_map('trim', $ips))) : [];
            SiteSetting::setValue('attendance_allowed_ips', implode("\n", $ips));
        }
        if ($request->has('attendance_location_lat')) {
            SiteSetting::setValue('attendance_location_lat', $request->input('attendance_location_lat') !== '' ? $request->input('attendance_location_lat') : '');
        }
        if ($request->has('attendance_location_lng')) {
            SiteSetting::setValue('attendance_location_lng', $request->input('attendance_location_lng') !== '' ? $request->input('attendance_location_lng') : '');
        }
        if ($request->has('attendance_location_radius_meters')) {
            $r = $request->input('attendance_location_radius_meters');
            SiteSetting::setValue('attendance_location_radius_meters', ($r !== null && $r !== '') ? $r : '100');
        }

        $this->bumpFrontendCacheVersion();
        return back()->with('success_message', 'تم حفظ إعدادات النظام بنجاح.');
    }

    /**
     * Reset hero background to default
     */
    public function resetHeroBackground(Request $request)
    {
        // Delete hero background image
        $oldHeroBg = SiteSetting::getValue('hero_background_image');
        if ($oldHeroBg) {
            $oldHeroBgPath = str_replace('storage/', '', $oldHeroBg);
            $oldHeroBgPath = ltrim($oldHeroBgPath, '/');
            if (Storage::disk('public')->exists($oldHeroBgPath)) {
                Storage::disk('public')->delete($oldHeroBgPath);
            }
        }
        SiteSetting::setValue('hero_background_image', '');
        SiteSetting::setValue('hero_background_opacity', '30');
        
        // Delete hero background video
        $oldHeroVideo = SiteSetting::getValue('hero_background_video');
        if ($oldHeroVideo) {
            $oldHeroVideoPath = str_replace('storage/', '', $oldHeroVideo);
            $oldHeroVideoPath = ltrim($oldHeroVideoPath, '/');
            if (Storage::disk('public')->exists($oldHeroVideoPath)) {
                Storage::disk('public')->delete($oldHeroVideoPath);
            }
        }
        SiteSetting::setValue('hero_background_video', '');
        SiteSetting::setValue('hero_background_video_opacity', '50');

        $this->bumpFrontendCacheVersion();
        return back()->with('success_message', 'تم إعادة إعدادات خلفية الهيرو إلى القيم الافتراضية بنجاح!');
    }

    /**
     * Reset section background to default
     */
    public function resetSectionBackground(Request $request, $section)
    {
        $bgImageKey = 'section_' . $section . '_bg_image';
        $bgOpacityKey = 'section_' . $section . '_bg_opacity';

        // Delete section background image
        $oldBgImage = SiteSetting::getValue($bgImageKey);
        if ($oldBgImage) {
            $oldBgImagePath = str_replace('storage/', '', $oldBgImage);
            $oldBgImagePath = ltrim($oldBgImagePath, '/');
            if (Storage::disk('public')->exists($oldBgImagePath)) {
                Storage::disk('public')->delete($oldBgImagePath);
            }
        }
        SiteSetting::setValue($bgImageKey, '');
        SiteSetting::setValue($bgOpacityKey, '30');

        $this->bumpFrontendCacheVersion();
        return back()->with('success_message', 'تم إعادة إعدادات خلفية القسم إلى القيم الافتراضية بنجاح!');
    }

    /**
     * Reset colors to default
     */
    public function resetColors(Request $request)
    {
        // Reset section colors to default (green theme) for each section
        $sectionKeys = ['about', 'vision_mission', 'banner_sections', 'services', 'projects', 'media', 'testimonials', 'partners', 'news', 'contact'];
        $defaults = ['bg_color' => '#FFFFFF', 'text_color' => '#0F3D2E', 'title_color' => '#5FB38E', 'icon_color' => '#5FB38E', 'card_bg_color' => '#FFFFFF', 'card_title_color' => '#5FB38E', 'hover_text_color' => '#5FB38E', 'button_color' => '#5FB38E'];
        foreach ($sectionKeys as $sk) {
            foreach ($defaults as $dk => $dv) {
                SiteSetting::setValue("section_{$sk}_{$dk}", $dv);
            }
        }

        // Reset navbar colors to default
        SiteSetting::setValue('navbar_bg_color', '#FFFFFF');
        SiteSetting::setValue('navbar_text_color', '#0F3D2E');
        SiteSetting::setValue('navbar_border_color', '#0F3D2E');

        // Reset hero & footer (one set) to default
        SiteSetting::setValue('hero_footer_bg_color', '#0F3D2E');
        SiteSetting::setValue('hero_footer_text_color', '#FFFFFF');
        SiteSetting::setValue('hero_footer_title_color', '#FFFFFF');
        SiteSetting::setValue('hero_circle_bg_color', '#5FB38E');
        SiteSetting::setValue('hero_circle_icon_color', '#FFFFFF');
        SiteSetting::setValue('hero_social_icons_color', '#FFFFFF');
        
        // Reset page content colors (standalone pages) to default
        SiteSetting::setValue('page_content_bg_color', '#FFFFFF');
        SiteSetting::setValue('page_content_text_color', '#0F3D2E');
        SiteSetting::setValue('page_content_title_color', '#5FB38E');

        // Reset article page colors (صفحة الخبر الداخلية): خلفية بيضاء، كل النصوص والأزرار أسود
        SiteSetting::setValue('article_bg_color', '#FFFFFF');
        SiteSetting::setValue('article_text_color', '#000000');
        SiteSetting::setValue('article_title_color', '#000000');
        SiteSetting::setValue('article_meta_color', '#000000');
        SiteSetting::setValue('article_button_color', '#000000');
        SiteSetting::setValue('article_button_hover_color', '#333333');
        
        // Reset dashboard colors to default (same as frontend)
        SiteSetting::setValue('dashboard_primary_color', '#5FB38E');
        SiteSetting::setValue('dashboard_primary_dark', '#1F6B4F');
        SiteSetting::setValue('dashboard_secondary_color', '#A8DCC3');
        SiteSetting::setValue('dashboard_accent_color', '#5FB38E');
        SiteSetting::setValue('dashboard_sidebar_bg', 'rgba(15, 61, 46, 0.95)');
        SiteSetting::setValue('dashboard_content_bg', 'rgba(255, 255, 255, 0.05)');
        SiteSetting::setValue('dashboard_card_bg', 'rgba(255, 255, 255, 0.08)');
        SiteSetting::setValue('dashboard_text_primary', '#FFFFFF');
        SiteSetting::setValue('dashboard_text_secondary', '#FFFFFF');
        SiteSetting::setValue('dashboard_sidebar_text', '#FFFFFF');
        SiteSetting::setValue('dashboard_border_color', 'rgba(255, 255, 255, 0.1)');
        SiteSetting::setValue('dashboard_bg_gradient', 'linear-gradient(180deg, #0F3D2E 0%, #1F6B4F 30%, #5FB38E 60%, #A8DCC3 85%, #FFFFFF 100%)');

        $this->bumpFrontendCacheVersion();
        return back()->with('success_message', 'تم إعادة ضبط ألوان الموقع إلى الإعدادات الافتراضية بنجاح!');
    }

    /**
     * إعادة ألوان لوحة التحكم (إعدادات النظام) إلى الافتراضي
     */
    public function resetDashboardColors(Request $request)
    {
        SiteSetting::setValue('dashboard_primary_color', '#5FB38E');
        SiteSetting::setValue('dashboard_primary_dark', '#1F6B4F');
        SiteSetting::setValue('dashboard_secondary_color', '#A8DCC3');
        SiteSetting::setValue('dashboard_accent_color', '#5FB38E');
        SiteSetting::setValue('dashboard_sidebar_bg', 'rgba(15, 61, 46, 0.95)');
        SiteSetting::setValue('dashboard_content_bg', 'rgba(255, 255, 255, 0.05)');
        SiteSetting::setValue('dashboard_card_bg', 'rgba(255, 255, 255, 0.08)');
        SiteSetting::setValue('dashboard_text_primary', '#FFFFFF');
        SiteSetting::setValue('dashboard_text_secondary', '#FFFFFF');
        SiteSetting::setValue('dashboard_sidebar_text', '#FFFFFF');
        SiteSetting::setValue('dashboard_border_color', 'rgba(255, 255, 255, 0.1)');
        SiteSetting::setValue('dashboard_bg_gradient', 'linear-gradient(180deg, #0F3D2E 0%, #1F6B4F 30%, #5FB38E 60%, #A8DCC3 85%, #FFFFFF 100%)');

        $this->bumpFrontendCacheVersion();
        return back()->with('success_message', 'تم استعادة الألوان الافتراضية للوحة التحكم بنجاح.');
    }

    /**
     * إعادة شكل ورقة الخطاب (رأس / وسط / تذييل) إلى الافتراضي — لا شيء
     */
    public function resetLetterPaper(Request $request)
    {
        foreach (['header', 'middle', 'footer'] as $zone) {
            SiteSetting::setValue("letter_paper_{$zone}_type", 'none');
            SiteSetting::setValue("letter_paper_{$zone}_content", '');
        }

        $this->bumpFrontendCacheVersion();
        return back()->with('success_message', 'تم استعادة شكل ورقة الخطاب إلى الإعداد الافتراضي (بدون رأس أو تذييل أو ختم مائي).');
    }

    /**
     * تطبيع لون هيكس للصيغة #RRGGBB (معالجة RTL مثل FFFFFF# أو #FFFFFF المحفوظ معكوساً)
     */
    private static function normalizeHexColor(?string $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }
        $value = trim($value);
        // إزالة # من أي مكان وإعادة وضعها في البداية
        $hex = preg_replace('/#/u', '', $value);
        if (preg_match('/^[0-9A-Fa-f]{6}$/', $hex)) {
            return '#' . $hex;
        }
        return $value;
    }

    /**
     * تحديث نسخة الكاش ومسح كاش العرض لضمان ظهور التحديثات على الاستضافة
     */
    private function bumpFrontendCacheVersion(): void
    {
        SiteSetting::setValue('settings_updated_at', (string) time());
        Artisan::call('view:clear');
        \Illuminate\Support\Facades\Cache::flush();
    }

    /**
     * Generate colors for a section using AI-like harmonious palette
     * Returns JSON with bg_color, text_color, title_color, icon_color, card_bg_color, card_title_color
     */
    public function generateSectionColors(Request $request, string $section)
    {
        $allowed = ['about', 'vision_mission', 'banner_sections', 'services', 'projects', 'media', 'testimonials', 'partners', 'news', 'contact'];
        if (!in_array($section, $allowed, true)) {
            return response()->json(['error' => 'قسم غير صالح'], 400);
        }

        $palettes = [
            ['bg' => '#FFFFFF', 'text' => '#1a1a2e', 'title' => '#16213e', 'icon' => '#0f3460', 'card_bg' => '#e8e8e8', 'card_title' => '#0f3460'],
            ['bg' => '#f8f9fa', 'text' => '#2d3436', 'title' => '#5FB38E', 'icon' => '#1F6B4F', 'card_bg' => '#FFFFFF', 'card_title' => '#0F3D2E'],
            ['bg' => '#fff5eb', 'text' => '#2c1810', 'title' => '#c45c26', 'icon' => '#a64b00', 'card_bg' => '#FFFFFF', 'card_title' => '#8b4513'],
            ['bg' => '#f0f4f8', 'text' => '#1e3a5f', 'title' => '#2563eb', 'icon' => '#1d4ed8', 'card_bg' => '#FFFFFF', 'card_title' => '#1e40af'],
            ['bg' => '#fdf2f8', 'text' => '#4c0519', 'title' => '#be185d', 'icon' => '#9d174d', 'card_bg' => '#FFFFFF', 'card_title' => '#831843'],
            ['bg' => '#f0fdf4', 'text' => '#14532d', 'title' => '#15803d', 'icon' => '#166534', 'card_bg' => '#FFFFFF', 'card_title' => '#14532d'],
        ];
        $p = $palettes[array_rand($palettes)];

        $hover = $p['card_title'] ?? $p['title'];
        $colors = [
            'section_' . $section . '_bg_color' => $p['bg'],
            'section_' . $section . '_text_color' => $p['text'],
            'section_' . $section . '_title_color' => $p['title'],
            'section_' . $section . '_icon_color' => $p['icon'],
            'section_' . $section . '_card_bg_color' => $p['card_bg'],
            'section_' . $section . '_card_title_color' => $p['card_title'],
            'section_' . $section . '_hover_text_color' => $hover,
            'section_' . $section . '_button_color' => $p['title'] ?? $p['icon'],
        ];
        return response()->json($colors);
    }

    /**
     * Generate full site colors from 3 base colors (AI-like harmonious palette).
     * Returns JSON with navbar, hero/footer, hero circle/social, and all section colors.
     */
    public function generateSiteColors(Request $request)
    {
        $color1 = $request->input('color1', '#5FB38E');
        $color2 = $request->input('color2', '#1F6B4F');
        $color3 = $request->input('color3', '#0F3D2E');

        $normalize = function ($hex) {
            $hex = preg_replace('/[^0-9A-Fa-f]/', '', $hex);
            if (strlen($hex) === 6) {
                return '#' . $hex;
            }
            return '#5FB38E';
        };
        $color1 = $normalize($color1);
        $color2 = $normalize($color2);
        $color3 = $normalize($color3);

        $baseColors = [$color1, $color2, $color3];

        // Navbar: light bg, dark text from color1
        $navbarBg = '#FFFFFF';
        $navbarText = $this->adjustBrightness($color1, -45);

        // Hero & Footer: dark bg from color3 (or darkest of the three), white text
        $heroFooterBg = $this->adjustBrightness($color3, -10);
        $heroFooterText = '#FFFFFF';
        $heroFooterTitle = '#FFFFFF';
        $heroCircleBg = $color1;
        $heroCircleIcon = '#FFFFFF';
        $heroSocialIcons = '#FFFFFF';

        $out = [
            'navbar_bg_color' => $navbarBg,
            'navbar_text_color' => $navbarText,
            'hero_footer_bg_color' => $heroFooterBg,
            'hero_footer_text_color' => $heroFooterText,
            'hero_footer_title_color' => $heroFooterTitle,
            'hero_circle_bg_color' => $heroCircleBg,
            'hero_circle_icon_color' => $heroCircleIcon,
            'hero_social_icons_color' => $heroSocialIcons,
        ];

        $sectionKeys = ['about', 'vision_mission', 'banner_sections', 'services', 'projects', 'media', 'testimonials', 'partners', 'news', 'contact'];
        $sectionColorKeys = ['bg_color', 'text_color', 'title_color', 'icon_color', 'card_bg_color', 'card_title_color', 'hover_text_color', 'button_color'];

        foreach ($sectionKeys as $i => $sk) {
            $base = $baseColors[$i % 3];
            $bg = in_array($i % 3, [0]) ? '#FFFFFF' : ($i % 3 === 1 ? $this->adjustBrightness($base, 85) : $this->adjustBrightness($base, 90));
            $text = $this->adjustBrightness($base, -50);
            $title = $base;
            $icon = $base;
            $cardBg = '#FFFFFF';
            $cardTitle = $this->adjustBrightness($base, -25);
            $hoverText = $this->adjustBrightness($base, -15);
            $buttonColor = $base;

            $out["section_{$sk}_bg_color"] = $bg;
            $out["section_{$sk}_text_color"] = $text;
            $out["section_{$sk}_title_color"] = $title;
            $out["section_{$sk}_icon_color"] = $icon;
            $out["section_{$sk}_card_bg_color"] = $cardBg;
            $out["section_{$sk}_card_title_color"] = $cardTitle;
            $out["section_{$sk}_hover_text_color"] = $hoverText;
            $out["section_{$sk}_button_color"] = $buttonColor;
        }

        return response()->json($out);
    }

    /**
     * Extract colors from uploaded brand image or PDF
     */
    public function extractColorsFromImage(Request $request)
    {
        $request->validate([
            'brand_file' => 'required|file|mimes:jpeg,png,jpg,gif,svg,pdf|max:20480',
        ]);

        try {
            $file = $request->file('brand_file');
            $filePath = $file->getRealPath();
            $mimeType = $file->getMimeType();
            $extension = strtolower($file->getClientOriginalExtension());
            
            $allColors = [];
            
            // Handle PDF files
            if ($mimeType === 'application/pdf' || $extension === 'pdf') {
                $allColors = $this->extractColorsFromPDF($filePath);
            } 
            // Handle image files
            else {
                $allColors = $this->extractColorsFromImageFile($filePath);
            }
            
            if (empty($allColors)) {
                // Provide helpful error message
                $errorMessage = 'لم يتم العثور على ألوان في الملف. ';
                if ($mimeType === 'application/pdf' || $extension === 'pdf') {
                    $errorMessage .= 'تأكد من أن ملف PDF يحتوي على صور أو ألوان. إذا كان PDF نصي فقط، يرجى رفع صورة للشعار بدلاً من ذلك.';
                    if (!extension_loaded('imagick')) {
                        $errorMessage .= ' (ملاحظة: ImageMagick غير مثبت، مما قد يحد من استخراج الألوان من PDF)';
                    }
                } else {
                    $errorMessage .= 'تأكد من أن الصورة تحتوي على ألوان واضحة وليست بيضاء أو سوداء بالكامل.';
                }
                
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 400);
            }

            // Sort colors by frequency
            arsort($allColors);
            
            // Get top colors
            $topColorsArray = array_keys($allColors);
            $topColors = array_slice($topColorsArray, 0, 20);
            
            // Convert to RGB and filter similar colors
            $uniqueColors = [];
            foreach ($topColors as $hex) {
                if (empty($hex) || !is_string($hex)) {
                    continue;
                }
                
                try {
                    $rgb = $this->hexToRgb($hex);
                    if (empty($rgb) || !is_array($rgb) || !isset($rgb['r']) || !isset($rgb['g']) || !isset($rgb['b'])) {
                        continue;
                    }
                    
                    $hsl = $this->rgbToHsl($rgb);
                    
                    // Skip very light or very dark colors
                    $brightness = ($rgb['r'] + $rgb['g'] + $rgb['b']) / 3;
                    if ($brightness > 240 || $brightness < 15) {
                        continue;
                    }
                    
                    // Check if color is similar to existing colors
                    $isUnique = true;
                    foreach ($uniqueColors as $existingHex) {
                        try {
                            $existingRgb = $this->hexToRgb($existingHex);
                            $existingHsl = $this->rgbToHsl($existingRgb);
                            
                            // Check hue difference (colors are similar if hue difference is small)
                            $hueDiff = abs($hsl['h'] - $existingHsl['h']);
                            if ($hueDiff < 30 || $hueDiff > 330) {
                                $isUnique = false;
                                break;
                            }
                        } catch (\Exception $e) {
                            // Skip this comparison if there's an error
                            continue;
                        }
                    }
                    
                    if ($isUnique) {
                        $uniqueColors[] = $hex;
                        if (count($uniqueColors) >= 4) {
                            break;
                        }
                    }
                } catch (\Exception $e) {
                    \Log::debug('Error processing color ' . $hex . ': ' . $e->getMessage());
                    continue;
                }
            }

            // Ensure we have at least 4 colors
            while (count($uniqueColors) < 4 && count($topColors) > count($uniqueColors)) {
                $found = false;
                foreach ($topColors as $hex) {
                    if (!in_array($hex, $uniqueColors) && !empty($hex)) {
                        try {
                            $rgb = $this->hexToRgb($hex);
                            if (!empty($rgb) && is_array($rgb) && isset($rgb['r']) && isset($rgb['g']) && isset($rgb['b'])) {
                                $brightness = ($rgb['r'] + $rgb['g'] + $rgb['b']) / 3;
                                if ($brightness > 240 || $brightness < 15) {
                                    continue;
                                }
                                $uniqueColors[] = $hex;
                                $found = true;
                                break;
                            }
                        } catch (\Exception $e) {
                            continue;
                        }
                    }
                }
                if (!$found) {
                    break; // No more valid colors to add
                }
            }

            // Assign colors: primary, secondary, tertiary, accent
            // Ensure we have at least one color
            if (empty($uniqueColors)) {
                // If no colors found, generate default colors based on a neutral color
                $primary = '#5FB38E';
                $secondary = $this->adjustBrightness($primary, -30);
                $tertiary = $this->adjustBrightness($primary, 15);
                $accent = $this->adjustBrightness($primary, 20);
            } else {
                $primary = isset($uniqueColors[0]) ? $uniqueColors[0] : '#5FB38E';
                $secondary = isset($uniqueColors[1]) ? $uniqueColors[1] : $this->adjustBrightness($primary, -30);
                $tertiary = isset($uniqueColors[2]) ? $uniqueColors[2] : $this->adjustBrightness($primary, 15);
                $accent = isset($uniqueColors[3]) ? $uniqueColors[3] : $this->adjustBrightness($primary, 20);
            }

            return response()->json([
                'success' => true,
                'colors' => [
                    'primary' => $primary,
                    'secondary' => $secondary,
                    'tertiary' => $tertiary,
                    'accent' => $accent
                ],
                'message' => 'تم استخراج الألوان بنجاح من ' . ($mimeType === 'application/pdf' ? 'ملف PDF' : 'الصورة')
            ]);

        } catch (\Exception $e) {
            \Log::error('Error extracting colors: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء استخراج الألوان: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Extract colors from PDF file
     */
    private function extractColorsFromPDF($pdfPath)
    {
        $colors = [];
        
        try {
            // Try using PDFParser library to extract images from PDF
            if (class_exists('\Smalot\PdfParser\Parser')) {
                try {
                    $parser = new \Smalot\PdfParser\Parser();
                    $pdf = $parser->parseFile($pdfPath);
                    
                    // Get all pages
                    $pages = $pdf->getPages();
                    
                    // Extract images from each page
                    foreach ($pages as $pageIndex => $page) {
                        if ($pageIndex > 2) break; // Limit to first 3 pages for performance
                        
                        // Get images from page
                        $objects = $page->get('XObject');
                        if ($objects) {
                            foreach ($objects as $key => $object) {
                                if (is_object($object)) {
                                    $subtype = $object->get('Subtype');
                                    if ($subtype && strtolower($subtype->getContent()) === 'image') {
                                        // Try to get image data
                                        try {
                                            $imageData = $object->getContent();
                                            if ($imageData) {
                                                // Try to save as temporary image file
                                                $tempImage = tempnam(sys_get_temp_dir(), 'pdf_img_') . '.png';
                                                
                                                // Check image format
                                                $filter = $object->get('Filter');
                                                $imageFormat = 'png';
                                                
                                                if ($filter) {
                                                    $filterContent = is_object($filter) ? $filter->getContent() : $filter;
                                                    if (stripos($filterContent, 'DCTDecode') !== false || stripos($filterContent, 'JPXDecode') !== false) {
                                                        $imageFormat = 'jpg';
                                                        $tempImage = tempnam(sys_get_temp_dir(), 'pdf_img_') . '.jpg';
                                                    }
                                                }
                                                
                                                // Decode and save image
                                                $decodedData = $object->getContent(true);
                                                if ($decodedData && strlen($decodedData) > 100) {
                                                    @file_put_contents($tempImage, $decodedData);
                                                    
                                                    if (file_exists($tempImage)) {
                                                        $imageColors = $this->extractColorsFromImageFile($tempImage);
                                                        @unlink($tempImage);
                                                        
                                                        if (!empty($imageColors)) {
                                                            $colors = array_merge($colors, $imageColors);
                                                        }
                                                    }
                                                }
                                            }
                                        } catch (\Exception $e) {
                                            \Log::debug('Failed to extract image from PDF page: ' . $e->getMessage());
                                        }
                                    }
                                }
                            }
                        }
                    }
                    
                    if (!empty($colors)) {
                        \Log::info('Successfully extracted colors from PDF using PDFParser');
                        return $colors;
                    }
                } catch (\Exception $e) {
                    \Log::warning('Failed to parse PDF with PDFParser: ' . $e->getMessage());
                }
            }
            
            // First, try to convert PDF to image using ImageMagick (most reliable method)
            if (extension_loaded('imagick')) {
                try {
                    $imagick = new \Imagick();
                    $imagick->setResolution(200, 200); // Higher resolution for better color detection
                    $imagick->readImage($pdfPath . '[0]'); // First page only
                    $imagick->setImageFormat('png');
                    $imagick->setImageCompressionQuality(100);
                    
                    // Get image blob
                    $imageData = $imagick->getImageBlob();
                    
                    // Save to temp file
                    $tempFile = tempnam(sys_get_temp_dir(), 'pdf_img_') . '.png';
                    file_put_contents($tempFile, $imageData);
                    
                    // Extract colors from the converted image
                    $imageColors = $this->extractColorsFromImageFile($tempFile);
                    
                    // Clean up
                    if (file_exists($tempFile)) {
                        unlink($tempFile);
                    }
                    $imagick->clear();
                    $imagick->destroy();
                    
                    if (!empty($imageColors)) {
                        return $imageColors;
                    }
                } catch (\Exception $e) {
                    \Log::warning('Failed to extract PDF as image with Imagick: ' . $e->getMessage());
                    // Continue to try other methods
                }
            }
            
            // Fallback: Try using command-line tools if available
            if (empty($colors)) {
                // Try using pdftoppm or convert command
                $tempDir = sys_get_temp_dir();
                $tempPrefix = 'pdf_extract_' . uniqid();
                $outputPath = $tempDir . '/' . $tempPrefix . '.png';
                
                // Try pdftoppm (from poppler-utils)
                $command = "pdftoppm -png -f 1 -l 1 -scale-to-x 1200 -scale-to-y 1200 \"$pdfPath\" \"$tempDir/$tempPrefix\" 2>&1";
                @exec($command, $output, $returnCode);
                
                if ($returnCode === 0) {
                    $pngFile = $tempDir . '/' . $tempPrefix . '-1.png';
                    if (file_exists($pngFile)) {
                        $imageColors = $this->extractColorsFromImageFile($pngFile);
                        @unlink($pngFile);
                        if (!empty($imageColors)) {
                            \Log::info('Successfully extracted colors from PDF using pdftoppm');
                            return $imageColors;
                        }
                    }
                }
                
                // Try ImageMagick convert command (if imagick extension not available)
                $command = "convert -density 300 \"$pdfPath[0]\" -quality 100 -flatten \"$outputPath\" 2>&1";
                @exec($command, $output, $returnCode);
                
                if ($returnCode === 0 && file_exists($outputPath)) {
                    $imageColors = $this->extractColorsFromImageFile($outputPath);
                    @unlink($outputPath);
                    if (!empty($imageColors)) {
                        \Log::info('Successfully extracted colors from PDF using convert command');
                        return $imageColors;
                    }
                }
                
                // Try ghostscript (gs command) as another option
                $gsOutputPath = $tempDir . '/' . $tempPrefix . '_gs.png';
                $command = "gs -dNOPAUSE -dBATCH -sDEVICE=png16m -r300 -dFirstPage=1 -dLastPage=1 -sOutputFile=\"$gsOutputPath\" \"$pdfPath\" 2>&1";
                @exec($command, $output, $returnCode);
                
                if ($returnCode === 0 && file_exists($gsOutputPath)) {
                    $imageColors = $this->extractColorsFromImageFile($gsOutputPath);
                    @unlink($gsOutputPath);
                    if (!empty($imageColors)) {
                        \Log::info('Successfully extracted colors from PDF using ghostscript');
                        return $imageColors;
                    }
                }
            }
            
            // Last resort: Try to read PDF content and extract color definitions
            // Also try to extract embedded images from PDF
            if (empty($colors)) {
                $pdfContent = @file_get_contents($pdfPath);
                if ($pdfContent && strlen($pdfContent) > 0) {
                    // More comprehensive regex patterns for PDF color extraction
                    $patterns = [
                        // RGB arrays: [r g b] or [r g b rg]
                        '/\[([0-9.]+)\s+([0-9.]+)\s+([0-9.]+)\s*(?:rg|RGB)?\]/i',
                        // Color operators: rg, RG (space-separated)
                        '/rg\s+([0-9.]+)\s+([0-9.]+)\s+([0-9.]+)/i',
                        '/RG\s+([0-9.]+)\s+([0-9.]+)\s+([0-9.]+)/i',
                        // DeviceRGB colors
                        '/DeviceRGB\s+([0-9.]+)\s+([0-9.]+)\s+([0-9.]+)/i',
                        // Hex colors in PDF
                        '/#([0-9a-fA-F]{6})/i',
                        // Color in stream objects
                        '/\/ColorSpace\s*\/DeviceRGB[^[]*\[([0-9.]+)\s+([0-9.]+)\s+([0-9.]+)\]/i',
                    ];
                    
                    foreach ($patterns as $pattern) {
                        preg_match_all($pattern, $pdfContent, $matches, PREG_SET_ORDER);
                        foreach ($matches as $match) {
                            if (count($match) >= 4) {
                                // RGB values
                                $r = (float)$match[1];
                                $g = (float)$match[2];
                                $b = (float)$match[3];
                                
                                // Normalize (PDF uses 0-1 range)
                                if ($r <= 1 && $g <= 1 && $b <= 1 && $r >= 0 && $g >= 0 && $b >= 0) {
                                    $r = (int)($r * 255);
                                    $g = (int)($g * 255);
                                    $b = (int)($b * 255);
                                } else {
                                    $r = (int)$r;
                                    $g = (int)$g;
                                    $b = (int)$b;
                                }
                                
                                $r = max(0, min(255, $r));
                                $g = max(0, min(255, $g));
                                $b = max(0, min(255, $b));
                                
                                $hex = sprintf('#%02x%02x%02x', $r, $g, $b);
                                if (!isset($colors[$hex])) {
                                    $colors[$hex] = 0;
                                }
                                $colors[$hex]++;
                            } elseif (count($match) >= 2 && strpos($match[0], '#') === 0) {
                                // Hex color
                                $hex = '#' . $match[1];
                                if (!isset($colors[$hex])) {
                                    $colors[$hex] = 0;
                                }
                                $colors[$hex]++;
                            }
                        }
                    }
                    
                    // Try to extract embedded images from PDF
                    // Look for image streams (JPXDecode, DCTDecode, etc.)
                    if (empty($colors)) {
                        // Extract image data from PDF streams
                        preg_match_all('/stream\s*(.*?)\s*endstream/is', $pdfContent, $streamMatches);
                        foreach ($streamMatches[1] as $streamData) {
                            // Try to decode and extract colors from image data
                            // This is a simplified approach - full PDF parsing would require a library
                            if (strpos($streamData, '/DCTDecode') !== false || strpos($streamData, '/JPXDecode') !== false) {
                                // This is likely a JPEG image embedded in PDF
                                // Try to extract it
                                $imageStart = strpos($streamData, "\xFF\xD8"); // JPEG start marker
                                if ($imageStart !== false) {
                                    $jpegData = substr($streamData, $imageStart);
                                    $tempJpeg = tempnam(sys_get_temp_dir(), 'pdf_jpeg_') . '.jpg';
                                    @file_put_contents($tempJpeg, $jpegData);
                                    
                                    if (file_exists($tempJpeg)) {
                                        $imageColors = $this->extractColorsFromImageFile($tempJpeg);
                                        @unlink($tempJpeg);
                                        if (!empty($imageColors)) {
                                            $colors = array_merge($colors, $imageColors);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            
            // If still no colors, provide helpful message
            if (empty($colors)) {
                \Log::warning('Could not extract colors from PDF. ImageMagick or PDF conversion tools may be needed.');
            }
            
        } catch (\Exception $e) {
            \Log::error('Error extracting colors from PDF: ' . $e->getMessage());
        }
        
        return $colors;
    }

    /**
     * Extract colors from image file
     */
    private function extractColorsFromImageFile($imagePath)
    {
        $colors = [];
        
        if (!file_exists($imagePath)) {
            return $colors;
        }
        
        // Get image info
        $imageInfo = @getimagesize($imagePath);
        if (!$imageInfo) {
            return $colors;
        }

        $width = $imageInfo[0];
        $height = $imageInfo[1];
        $type = $imageInfo[2];

        // Create image resource based on type
        $img = null;
        switch ($type) {
            case IMAGETYPE_JPEG:
                $img = @imagecreatefromjpeg($imagePath);
                break;
            case IMAGETYPE_PNG:
                $img = @imagecreatefrompng($imagePath);
                // Handle PNG transparency
                if ($img) {
                    imagealphablending($img, false);
                    imagesavealpha($img, true);
                }
                break;
            case IMAGETYPE_GIF:
                $img = @imagecreatefromgif($imagePath);
                break;
            default:
                return $colors;
        }

        if (!$img) {
            return $colors;
        }

        // Sample colors from image (sample every 5th pixel for better coverage)
        $sampleRate = 5;
        $totalPixels = 0;
        
        for ($x = 0; $x < $width; $x += $sampleRate) {
            for ($y = 0; $y < $height; $y += $sampleRate) {
                $rgb = imagecolorat($img, $x, $y);
                
                // Handle transparency in PNG
                if ($type == IMAGETYPE_PNG) {
                    $alpha = ($rgb >> 24) & 0x7F;
                    if ($alpha > 100) { // Skip very transparent pixels
                        continue;
                    }
                }
                
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;
                
                // Skip very light or very dark colors (likely background/transparency)
                $brightness = ($r + $g + $b) / 3;
                if ($brightness > 240 || $brightness < 15) {
                    continue;
                }
                
                $hex = sprintf('#%02x%02x%02x', $r, $g, $b);
                if (!isset($colors[$hex])) {
                    $colors[$hex] = 0;
                }
                $colors[$hex]++;
                $totalPixels++;
            }
        }

        imagedestroy($img);
        
        // If we didn't get enough colors, try a more aggressive sampling
        if (count($colors) < 5 && $totalPixels > 0) {
            // Retry with smaller sample rate
            $img = null;
            switch ($type) {
                case IMAGETYPE_JPEG:
                    $img = @imagecreatefromjpeg($imagePath);
                    break;
                case IMAGETYPE_PNG:
                    $img = @imagecreatefrompng($imagePath);
                    if ($img) {
                        imagealphablending($img, false);
                        imagesavealpha($img, true);
                    }
                    break;
                case IMAGETYPE_GIF:
                    $img = @imagecreatefromgif($imagePath);
                    break;
            }
            
            if ($img) {
                $sampleRate = 3; // More aggressive sampling
                for ($x = 0; $x < $width; $x += $sampleRate) {
                    for ($y = 0; $y < $height; $y += $sampleRate) {
                        $rgb = imagecolorat($img, $x, $y);
                        $r = ($rgb >> 16) & 0xFF;
                        $g = ($rgb >> 8) & 0xFF;
                        $b = $rgb & 0xFF;
                        
                        $brightness = ($r + $g + $b) / 3;
                        if ($brightness > 240 || $brightness < 15) {
                            continue;
                        }
                        
                        $hex = sprintf('#%02x%02x%02x', $r, $g, $b);
                        if (!isset($colors[$hex])) {
                            $colors[$hex] = 0;
                        }
                        $colors[$hex]++;
                    }
                }
                imagedestroy($img);
            }
        }
        
        return $colors;
    }

    /**
     * Generate automatic color scheme based on brand colors using AI/color theory
     */
    public function generateAutoColorScheme(Request $request)
    {
        $request->validate([
            'brand_primary_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'brand_secondary_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'brand_tertiary_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'brand_accent_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        $primaryColor = $request->input('brand_primary_color');
        $secondaryColor = $request->input('brand_secondary_color');
        $tertiaryColor = $request->input('brand_tertiary_color');
        $accentColor = $request->input('brand_accent_color');

        // Generate multiple color scheme options (6 options)
        $colorOptions = [];
        for ($i = 0; $i < 6; $i++) {
            $colorOptions[] = $this->generateColorPalette($primaryColor, $secondaryColor, $tertiaryColor, $accentColor, $i);
        }

        return response()->json([
            'success' => true,
            'colorOptions' => $colorOptions,
            'message' => 'تم توليد ' . count($colorOptions) . ' خيارات للون الهوية بنجاح'
        ]);
    }

    /**
     * Generate a harmonious color palette based on color theory
     * @param string $primaryColor Primary brand color
     * @param string|null $secondaryColor Secondary brand color (optional)
     * @param string|null $tertiaryColor Tertiary brand color (optional)
     * @param string|null $accentColor Accent brand color (optional)
     * @param int $variant Variant number (0-5) for different color schemes
     */
    private function generateColorPalette($primaryColor, $secondaryColor = null, $tertiaryColor = null, $accentColor = null, $variant = 0)
    {
        // Convert hex to RGB
        $primaryRgb = $this->hexToRgb($primaryColor);
        $primaryHsl = $this->rgbToHsl($primaryRgb);

        // Different variants based on color theory
        $variants = [
            // Variant 0: Analogous (similar colors)
            ['h' => 0, 's' => 0, 'l' => -25],
            // Variant 1: Complementary (opposite colors)
            ['h' => 180, 's' => 10, 'l' => -20],
            // Variant 2: Triadic (three evenly spaced colors)
            ['h' => 120, 's' => 5, 'l' => -15],
            // Variant 3: Split Complementary
            ['h' => 150, 's' => -5, 'l' => -30],
            // Variant 4: Monochromatic (same hue, different saturation/lightness)
            ['h' => 0, 's' => -15, 'l' => -35],
            // Variant 5: Tetradic
            ['h' => 90, 's' => 15, 'l' => -22],
        ];

        $variantConfig = $variants[$variant % 6];

        // Generate secondary color if not provided
        if (!$secondaryColor) {
            $secondaryHsl = [
                'h' => ($primaryHsl['h'] + $variantConfig['h']) % 360,
                's' => max(10, min(100, $primaryHsl['s'] + $variantConfig['s'])),
                'l' => max(10, min(90, $primaryHsl['l'] + $variantConfig['l']))
            ];
            $secondaryColor = $this->hslToHex($secondaryHsl);
        }

        $secondaryRgb = $this->hexToRgb($secondaryColor);
        $secondaryHsl = $this->rgbToHsl($secondaryRgb);

        // Use tertiary color if provided, otherwise generate based on variant
        if (!$tertiaryColor) {
            $tertiaryHsl = [
                'h' => ($primaryHsl['h'] + 60 + ($variant * 10)) % 360,
                's' => max(20, $primaryHsl['s'] - 10 - ($variant * 2)),
                'l' => min(85, $primaryHsl['l'] + 20 + ($variant * 3))
            ];
            $tertiaryColor = $this->hslToHex($tertiaryHsl);
        }

        // Use accent color if provided, otherwise generate based on variant
        if (!$accentColor) {
            $accentHsl = [
                'h' => ($primaryHsl['h'] + 30 + ($variant * 20)) % 360,
                's' => min(100, $primaryHsl['s'] + 5 + ($variant * 2)),
                'l' => min(90, $primaryHsl['l'] + 15 + ($variant * 3))
            ];
            $accentColor = $this->hslToHex($accentHsl);
        }

        // Generate lighter version for secondary color
        $lightSecondaryHsl = [
            'h' => $secondaryHsl['h'],
            's' => max(20, $secondaryHsl['s'] - 20 - ($variant * 2)),
            'l' => min(90, $secondaryHsl['l'] + 30 + ($variant * 3))
        ];
        $lightSecondaryColor = $this->hslToHex($lightSecondaryHsl);

        // Generate variant-specific colors for different elements
        // Each variant uses different color combinations based on variant number
        
        // Base colors that vary by variant
        $variantPrimary = $primaryColor;
        $variantSecondary = $secondaryColor ?: $this->adjustBrightness($primaryColor, -30);
        $variantAccent = $accentColor ?: $this->adjustBrightness($primaryColor, 15);
        
        // Adjust base colors based on variant for more diversity
        // Each variant gets significantly different colors
        if ($variant == 0) {
            // Analogous - keep original colors but adjust slightly
            $variantPrimary = $primaryColor;
            $variantSecondary = $secondaryColor ?: $this->adjustBrightness($primaryColor, -25);
            $variantAccent = $accentColor ?: $this->adjustBrightness($primaryColor, 15);
        } elseif ($variant == 1) {
            // Complementary - use opposite colors
            $variantPrimary = $primaryColor;
            $variantSecondary = $this->hslToHex(['h' => ($primaryHsl['h'] + 180) % 360, 's' => min(100, $primaryHsl['s'] + 10), 'l' => max(10, $primaryHsl['l'] - 30)]);
            $variantAccent = $this->hslToHex(['h' => ($primaryHsl['h'] + 150) % 360, 's' => min(100, $primaryHsl['s'] + 5), 'l' => min(90, $primaryHsl['l'] + 10)]);
        } elseif ($variant == 2) {
            // Triadic - use three evenly spaced colors
            $variantPrimary = $primaryColor;
            $variantSecondary = $this->hslToHex(['h' => ($primaryHsl['h'] + 120) % 360, 's' => min(100, $primaryHsl['s'] + 5), 'l' => max(10, $primaryHsl['l'] - 25)]);
            $variantAccent = $this->hslToHex(['h' => ($primaryHsl['h'] + 240) % 360, 's' => min(100, $primaryHsl['s'] + 5), 'l' => min(90, $primaryHsl['l'] + 15)]);
        } elseif ($variant == 3) {
            // Split Complementary - darker theme
            $variantPrimary = $primaryColor;
            $variantSecondary = $this->hslToHex(['h' => ($primaryHsl['h'] + 150) % 360, 's' => max(10, $primaryHsl['s'] - 5), 'l' => max(10, $primaryHsl['l'] - 30)]);
            $variantAccent = $this->hslToHex(['h' => ($primaryHsl['h'] + 210) % 360, 's' => min(100, $primaryHsl['s'] + 5), 'l' => min(90, $primaryHsl['l'] + 20)]);
        } elseif ($variant == 4) {
            // Monochromatic - same hue, different saturation/lightness
            $variantPrimary = $primaryColor;
            $variantSecondary = $this->hslToHex(['h' => $primaryHsl['h'], 's' => max(10, $primaryHsl['s'] - 15), 'l' => max(10, $primaryHsl['l'] - 35)]);
            $variantAccent = $this->hslToHex(['h' => $primaryHsl['h'], 's' => min(100, $primaryHsl['s'] + 10), 'l' => min(90, $primaryHsl['l'] + 25)]);
        } elseif ($variant == 5) {
            // Tetradic - high contrast
            $variantPrimary = $primaryColor;
            $variantSecondary = $this->hslToHex(['h' => ($primaryHsl['h'] + 90) % 360, 's' => min(100, $primaryHsl['s'] + 15), 'l' => max(10, $primaryHsl['l'] - 22)]);
            $variantAccent = $this->hslToHex(['h' => ($primaryHsl['h'] + 270) % 360, 's' => min(100, $primaryHsl['s'] + 10), 'l' => min(90, $primaryHsl['l'] + 30)]);
        }
        
        // Determine text colors based on background brightness
        $primaryBrightness = $this->getBrightness($primaryRgb);
        $textColor = $primaryBrightness > 128 ? $this->adjustBrightness($variantPrimary, -60) : '#FFFFFF';
        $textSecondaryColor = $primaryBrightness > 128 ? $this->adjustBrightness($variantPrimary, -40) : '#E0E0E0';

        // Card colors - vary by variant
        $cardBgColors = ['#FFFFFF', '#FFFFFF', '#F8F9FA', '#FFFFFF', '#FAFAFA', '#FFFFFF'];
        $cardBgColor = $cardBgColors[$variant % 6];
        $cardBorderColors = [-30, -25, -20, -35, -15, -40];
        $cardBorderColor = $this->adjustBrightness($variantPrimary, $cardBorderColors[$variant % 6]);
        $cardTitleColor = $variantPrimary;

        // Navbar colors - vary by variant
        $navbarBgColors = ['#FFFFFF', $this->adjustBrightness($variantPrimary, -35), '#FFFFFF', '#FFFFFF', '#FAFAFA', '#FFFFFF'];
        $navbarBgColor = $navbarBgColors[$variant % 6];
        $navbarTextColors = [-40, '#FFFFFF', -35, -45, -30, -50];
        if (is_string($navbarTextColors[$variant % 6])) {
            $navbarTextColor = $navbarTextColors[$variant % 6];
        } else {
            $navbarTextColor = $this->adjustBrightness($variantPrimary, $navbarTextColors[$variant % 6]);
        }
        $navbarBorderColors = [-20, -20, -15, -25, -10, -30];
        $navbarBorderColor = $this->adjustBrightness($variantPrimary, $navbarBorderColors[$variant % 6]);

        // Footer colors - vary by variant
        $footerBgColors = [0, -40, -30, -50, -25, -45];
        $footerBgColor = $this->adjustBrightness($variantSecondary, $footerBgColors[$variant % 6]);
        $footerTextColor = '#FFFFFF';
        $footerTextSecondaryColor = 'rgba(255, 255, 255, 0.8)';
        $footerLinkColor = 'rgba(255, 255, 255, 0.8)';
        $footerLinkHoverColor = '#FFFFFF';
        $footerBorderColor = $this->adjustBrightness($variantPrimary, -10);
        $footerTitleColor = '#FFFFFF';
        $footerIconColor = $variantPrimary;

        // News article page colors - vary by variant
        $articleBgColors = ['#FFFFFF', '#F5F5F5', '#FFFFFF', '#FFFFFF', '#FFFFFF', '#FFFFFF'];
        $articleBgColor = $articleBgColors[$variant % 6];
        $articleTextColors = [-50, -45, -40, -55, -45, -60];
        $articleTextColor = $this->adjustBrightness($variantPrimary, $articleTextColors[$variant % 6]);
        $articleTitleColor = $variantPrimary;
        $articleMetaColors = [-30, -25, -20, -35, -25, -40];
        $articleMetaColor = $this->adjustBrightness($variantPrimary, $articleMetaColors[$variant % 6]);
        $articleBorderColors = [-20, -15, -10, -25, -15, -30];
        $articleBorderColor = $this->adjustBrightness($variantPrimary, $articleBorderColors[$variant % 6]);
        $articleCardBgColors = ['#F8F9FA', '#FFFFFF', '#FAFAFA', '#F0F0F0', '#FFFFFF', '#FFFFFF'];
        $articleCardBgColor = $articleCardBgColors[$variant % 6];
        $articleCardBorderColor = $this->adjustBrightness($variantPrimary, -25);
        $articleButtonColor = $variantPrimary;
        $articleButtonHoverColor = $variantSecondary;

        // Additional professional colors
        $buttonPrimaryColor = $primaryColor;
        $buttonSecondaryColor = $secondaryColor;
        $linkColor = $primaryColor;
        $linkHoverColor = $secondaryColor;
        $borderColor = $this->adjustBrightness($primaryColor, -20);
        $shadowColor = $primaryColor; // For shadows and glows

        return [
            'site_primary_color' => $variantPrimary, // Use variant-specific primary
            'site_primary_dark' => $variantSecondary, // Use variant-specific secondary
            'site_secondary_color' => $lightSecondaryColor,
            'site_accent_color' => $variantAccent, // Use variant-specific accent
            'site_text_primary_color' => $textColor,
            'site_text_secondary_color' => $textSecondaryColor,
            'site_icon_color' => $variantPrimary, // Use variant-specific primary
            'site_card_bg_color' => $cardBgColor,
            'site_card_border_color' => $cardBorderColor,
            'site_card_title_color' => $cardTitleColor,
            'site_hero_title_color' => $variantPrimary, // Use variant-specific primary
            'navbar_bg_color' => $navbarBgColor,
            'navbar_text_color' => $navbarTextColor,
            'navbar_border_color' => $navbarBorderColor,
            // Footer colors
            'footer_bg_color' => $footerBgColor,
            'footer_text_color' => $footerTextColor,
            'footer_text_secondary_color' => $footerTextSecondaryColor,
            'footer_link_color' => $footerLinkColor,
            'footer_link_hover_color' => $footerLinkHoverColor,
            'footer_border_color' => $footerBorderColor,
            'footer_title_color' => $footerTitleColor,
            'footer_icon_color' => $footerIconColor,
            // News article colors
            'article_bg_color' => $articleBgColor,
            'article_text_color' => $articleTextColor,
            'article_title_color' => $articleTitleColor,
            'article_meta_color' => $articleMetaColor,
            'article_border_color' => $articleBorderColor,
            'article_card_bg_color' => $articleCardBgColor,
            'article_card_border_color' => $articleCardBorderColor,
            'article_button_color' => $articleButtonColor,
            'article_button_hover_color' => $articleButtonHoverColor,
            // Additional colors
            'button_primary_color' => $variantPrimary, // Use variant-specific primary
            'button_secondary_color' => $variantSecondary, // Use variant-specific secondary
            'link_color' => $variantPrimary, // Use variant-specific primary
            'link_hover_color' => $variantSecondary, // Use variant-specific secondary
            'border_color' => $this->adjustBrightness($variantPrimary, -20),
            'shadow_color' => $variantPrimary, // Use variant-specific primary
        ];
    }

    /**
     * Convert hex to RGB
     */
    private function hexToRgb($hex)
    {
        $hex = str_replace('#', '', $hex);
        return [
            'r' => hexdec(substr($hex, 0, 2)),
            'g' => hexdec(substr($hex, 2, 2)),
            'b' => hexdec(substr($hex, 4, 2))
        ];
    }

    /**
     * Convert RGB to HSL
     */
    private function rgbToHsl($rgb)
    {
        $r = $rgb['r'] / 255;
        $g = $rgb['g'] / 255;
        $b = $rgb['b'] / 255;

        $max = max($r, $g, $b);
        $min = min($r, $g, $b);
        $delta = $max - $min;

        $l = ($max + $min) / 2;

        if ($delta == 0) {
            $h = $s = 0;
        } else {
            $s = $l > 0.5 ? $delta / (2 - $max - $min) : $delta / ($max + $min);

            switch ($max) {
                case $r:
                    $h = (($g - $b) / $delta + ($g < $b ? 6 : 0)) / 6;
                    break;
                case $g:
                    $h = (($b - $r) / $delta + 2) / 6;
                    break;
                case $b:
                    $h = (($r - $g) / $delta + 4) / 6;
                    break;
            }
        }

        return [
            'h' => round($h * 360),
            's' => round($s * 100),
            'l' => round($l * 100)
        ];
    }

    /**
     * Convert HSL to Hex
     */
    private function hslToHex($hsl)
    {
        $h = $hsl['h'] / 360;
        $s = $hsl['s'] / 100;
        $l = $hsl['l'] / 100;

        if ($s == 0) {
            $r = $g = $b = $l;
        } else {
            $hue2rgb = function($p, $q, $t) {
                if ($t < 0) $t += 1;
                if ($t > 1) $t -= 1;
                if ($t < 1/6) return $p + ($q - $p) * 6 * $t;
                if ($t < 1/2) return $q;
                if ($t < 2/3) return $p + ($q - $p) * (2/3 - $t) * 6;
                return $p;
            };

            $q = $l < 0.5 ? $l * (1 + $s) : $l + $s - $l * $s;
            $p = 2 * $l - $q;

            $r = $hue2rgb($p, $q, $h + 1/3);
            $g = $hue2rgb($p, $q, $h);
            $b = $hue2rgb($p, $q, $h - 1/3);
        }

        return '#' . str_pad(dechex(round($r * 255)), 2, '0', STR_PAD_LEFT) .
                   str_pad(dechex(round($g * 255)), 2, '0', STR_PAD_LEFT) .
                   str_pad(dechex(round($b * 255)), 2, '0', STR_PAD_LEFT);
    }

    /**
     * Get brightness of a color (0-255)
     */
    private function getBrightness($rgb)
    {
        return ($rgb['r'] * 299 + $rgb['g'] * 587 + $rgb['b'] * 114) / 1000;
    }

    /**
     * Adjust brightness of a hex color
     */
    private function adjustBrightness($hex, $percent)
    {
        $rgb = $this->hexToRgb($hex);
        $rgb['r'] = max(0, min(255, $rgb['r'] + ($rgb['r'] * $percent / 100)));
        $rgb['g'] = max(0, min(255, $rgb['g'] + ($rgb['g'] * $percent / 100)));
        $rgb['b'] = max(0, min(255, $rgb['b'] + ($rgb['b'] * $percent / 100)));

        return '#' . str_pad(dechex(round($rgb['r'])), 2, '0', STR_PAD_LEFT) .
                   str_pad(dechex(round($rgb['g'])), 2, '0', STR_PAD_LEFT) .
                   str_pad(dechex(round($rgb['b'])), 2, '0', STR_PAD_LEFT);
    }
}
