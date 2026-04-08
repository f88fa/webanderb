<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use Illuminate\Http\Request;

/**
 * DashboardController
 * Migrated from Plain PHP: index.php
 * Handles dashboard routing and page switching
 */
class DashboardController extends Controller
{
    /**
     * Show dashboard with page switching
     * Replaces: index.php switch($page) logic
     */
    public function index(Request $request)
    {
        $page = $request->get('page', 'home');
        
        // Get settings for all pages
        $settings = SiteSetting::getAllAsArray();
        
        // Load page-specific data
        $viewData = ['page' => $page, 'settings' => $settings];
        
        // Add page-specific data
        switch($page) {
            case 'home':
                // إحصائيات الموقع
                $viewData['stats'] = [
                    'news' => \App\Models\News::count(),
                    'active_news' => \App\Models\News::where('status', 'active')->count(),
                    'services' => \App\Models\Service::count(),
                    'active_services' => \App\Models\Service::where('is_active', true)->count(),
                    'partners' => \App\Models\Partner::count(),
                    'active_partners' => \App\Models\Partner::where('is_active', true)->count(),
                    'board_members' => \App\Models\BoardMember::count(),
                    'active_board_members' => \App\Models\BoardMember::where('is_active', true)->count(),
                    'policies_categories' => \App\Models\PolicyCategory::count(),
                    'active_policies_categories' => \App\Models\PolicyCategory::where('is_active', true)->count(),
                    'policies' => \App\Models\Policy::count(),
                    'active_policies' => \App\Models\Policy::where('is_active', true)->count(),
                    'menu_items' => \App\Models\MenuItem::count(),
                    'active_menu_items' => \App\Models\MenuItem::where('is_active', true)->count(),
                    'projects' => \App\Models\Project::count(),
                    'active_projects' => \App\Models\Project::where('is_active', true)->count(),
                    'testimonials' => \App\Models\Testimonial::count(),
                    'active_testimonials' => \App\Models\Testimonial::where('is_active', true)->count(),
                    'banner_sections' => \App\Models\BannerSection::count(),
                    'active_banner_sections' => \App\Models\BannerSection::where('is_active', true)->count(),
                    'staff' => \App\Models\Staff::count(),
                    'active_staff' => \App\Models\Staff::where('is_active', true)->count(),
                ];
                break;
            case 'settings':
                $viewData['settings'] = $settings;
                $viewData['heroSliderImages'] = \App\Models\HeroSliderImage::getAllOrdered();
                break;
            case 'about':
                $viewData['about'] = \App\Models\AboutUs::getLatest();
                $viewData['stats'] = \App\Models\AboutStat::getAllOrdered();
                $viewData['features'] = \App\Models\AboutFeature::getAllOrdered();
                break;
            case 'vision-mission':
                $viewData['visionMission'] = \App\Models\VisionMission::getLatest();
                break;
            case 'services':
                $viewData['services'] = \App\Models\Service::getAllOrdered();
                $viewData['editService'] = $request->has('edit') 
                    ? \App\Models\Service::find($request->get('edit')) 
                    : null;
                break;
            case 'partners':
                $viewData['partners'] = \App\Models\Partner::getAllOrdered();
                $viewData['editPartner'] = $request->has('edit') 
                    ? \App\Models\Partner::find($request->get('edit')) 
                    : null;
                break;
            case 'menu':
                $viewData['menuItems'] = \App\Models\MenuItem::getAllOrdered();
                $viewData['editMenuItem'] = $request->has('edit') 
                    ? \App\Models\MenuItem::find($request->get('edit')) 
                    : null;
                break;
            case 'board-members':
                $viewData['boardMembers'] = \App\Models\BoardMember::getAllOrdered();
                $viewData['editBoardMember'] = $request->has('edit') 
                    ? \App\Models\BoardMember::find($request->get('edit')) 
                    : null;
                break;
            case 'executive-director':
                // No additional data needed, settings are already loaded
                break;
            case 'policies':
                $viewData['categories'] = \App\Models\PolicyCategory::getAllOrdered();
                $viewData['policies'] = \App\Models\Policy::getAllOrdered();
                $viewData['editCategory'] = $request->has('edit_category') 
                    ? \App\Models\PolicyCategory::find($request->get('edit_category')) 
                    : null;
                $viewData['editPolicy'] = $request->has('edit_policy') 
                    ? \App\Models\Policy::find($request->get('edit_policy')) 
                    : null;
                break;
            case 'projects':
                $viewData['projects'] = \App\Models\Project::getAllOrdered();
                $viewData['editProject'] = $request->has('edit') 
                    ? \App\Models\Project::find($request->get('edit')) 
                    : null;
                break;
            case 'testimonials':
                $viewData['testimonials'] = \App\Models\Testimonial::getAllOrdered();
                $viewData['editTestimonial'] = $request->has('edit') 
                    ? \App\Models\Testimonial::find($request->get('edit')) 
                    : null;
                break;
            case 'media':
                $viewData['videos'] = \App\Models\MediaVideo::getAllOrdered();
                $viewData['slides'] = \App\Models\MediaSlide::getAllOrdered();
                $viewData['editVideo'] = $request->has('edit_video') 
                    ? \App\Models\MediaVideo::find($request->get('edit_video')) 
                    : null;
                $viewData['editSlide'] = $request->has('edit_slide') 
                    ? \App\Models\MediaSlide::find($request->get('edit_slide')) 
                    : null;
                break;
            case 'banner-sections':
                $viewData['banners'] = \App\Models\BannerSection::getAllOrdered();
                $viewData['editBanner'] = $request->has('edit') 
                    ? \App\Models\BannerSection::find($request->get('edit')) 
                    : null;
                break;
            case 'staff':
                $viewData['staff'] = \App\Models\Staff::getAllOrdered();
                $viewData['editStaff'] = $request->has('edit') 
                    ? \App\Models\Staff::find($request->get('edit')) 
                    : null;
                break;
            case 'files':
                $viewData['files'] = \App\Models\File::getAllOrdered();
                $viewData['editFile'] = $request->has('edit') 
                    ? \App\Models\File::find($request->get('edit')) 
                    : null;
                break;
            case 'reports':
                $viewData['reports'] = \App\Models\Report::getAllOrdered();
                $viewData['editReport'] = $request->has('edit') 
                    ? \App\Models\Report::find($request->get('edit')) 
                    : null;
                break;
            case 'section-order':
            case 'section_order':
                $viewData['sections'] = \App\Models\SectionOrder::getAllOrdered();
                break;
            case 'news':
                $viewData['news'] = \App\Models\News::getAllOrdered();
                $viewData['editNews'] = $request->has('edit') 
                    ? \App\Models\News::find($request->get('edit')) 
                    : null;
                break;
        }
        
        return view('dashboard.index', $viewData);
    }
}
