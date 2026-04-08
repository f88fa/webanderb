<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use App\Models\AboutUs;
use App\Models\AboutStat;
use App\Models\AboutFeature;
use App\Models\VisionMission;
use App\Models\Service;
use App\Models\Partner;
use App\Models\MenuItem;
use App\Models\BoardMember;
use App\Models\PolicyCategory;
use App\Models\Project;
use App\Models\Testimonial;
use App\Models\MediaVideo;
use App\Models\MediaSlide;
use App\Models\BannerSection;
use App\Models\Staff;
use App\Models\News;
use App\Models\Report;
use App\Models\SectionOrder;
use App\Models\HeroSliderImage;

/**
 * FrontendController
 * Migrated from Plain PHP: frontend.php
 * Handles public-facing frontend pages
 */
class FrontendController extends Controller
{
    /**
     * Show frontend homepage
     * Replaces: frontend.php data fetching and rendering
     */
    public function index(\Illuminate\Http\Request $request)
    {
        // Get settings - replaces: $conn->query("SELECT setting_key, setting_value FROM site_settings")
        $settings = SiteSetting::getAllAsArray();
        
        // Get about us - replaces: SELECT * FROM about_us ORDER BY id DESC LIMIT 1
        $about = AboutUs::getLatest();
        
        // Get about stats
        $aboutStats = AboutStat::getAllOrdered();
        
        // Get about features
        $aboutFeatures = AboutFeature::getAllOrdered();
        
        // Get vision and mission
        $visionMission = VisionMission::getLatest();
        
        // Get active services
        $services = Service::getActiveOrdered();
        
        // Get active partners
        $partners = Partner::getActiveOrdered();
        
        // Get active projects
        $projects = Project::getActiveOrdered();
        
        // Get active testimonials
        $testimonials = Testimonial::getActiveOrdered();
        
        // Get active reports
        $reports = Report::getActiveOrdered();
        
        // Get active media videos and slides
        $mediaVideos = MediaVideo::getActiveOrdered();
        $mediaSlides = MediaSlide::getActiveOrdered();
        
        // Get active banner sections
        $bannerSections = BannerSection::getActiveOrdered();
        
        // Get menu items - exclude "الرئيسية" menu item
        $menuItems = MenuItem::getRootItemsExcludingHome()->load('activeChildren');
        
        // Get active news - replaces: SELECT * FROM news WHERE status = 'active' ORDER BY created_at DESC LIMIT 6
        $news = News::getActive(6);
        
        // Get section order
        $sectionOrder = SectionOrder::getVisibleOrdered();
        
        // Get hero slider images
        $heroSliderImages = HeroSliderImage::getActiveOrdered();
        
        $response = response()->view('frontend.index', [
            'settings' => $settings,
            'about' => $about,
            'aboutStats' => $aboutStats,
            'aboutFeatures' => $aboutFeatures,
            'visionMission' => $visionMission,
            'services' => $services,
            'partners' => $partners,
            'projects' => $projects,
            'testimonials' => $testimonials,
            'reports' => $reports,
            'mediaVideos' => $mediaVideos,
            'mediaSlides' => $mediaSlides,
            'bannerSections' => $bannerSections,
            'sectionOrder' => $sectionOrder,
            'menuItems' => $menuItems,
            'news' => $news,
            'heroSliderImages' => $heroSliderImages,
        ]);
        $cacheVersion = $settings['settings_updated_at'] ?? '0';
        $response->setEtag($cacheVersion);
        $response->header('Cache-Control', 'public, max-age=0, must-revalidate');
        if ($response->isNotModified($request)) {
            return $response;
        }
        return $response;
    }

    /**
     * Show page for menu item with type 'page'
     */
    public function page($slug)
    {
        $page = MenuItem::getPageBySlug($slug);
        
        if (!$page) {
            abort(404);
        }

        $settings = SiteSetting::getAllAsArray();
        $menuItems = MenuItem::getRootItemsExcludingHome()->load('activeChildren');

        return view('frontend.page', [
            'page' => $page,
            'settings' => $settings,
            'menuItems' => $menuItems,
        ]);
    }

    /**
     * Show board members page
     */
    public function boardMembers()
    {
        $settings = SiteSetting::getAllAsArray();
        $menuItems = MenuItem::getRootItemsExcludingHome()->load('activeChildren');
        $boardMembers = BoardMember::getActiveOrdered();

        return view('frontend.board-members', [
            'settings' => $settings,
            'menuItems' => $menuItems,
            'boardMembers' => $boardMembers,
        ]);
    }

    /**
     * Show staff page
     */
    public function staff()
    {
        $settings = SiteSetting::getAllAsArray();
        $menuItems = MenuItem::getRootItemsExcludingHome()->load('activeChildren');
        $staff = Staff::getActiveOrdered();

        return view('frontend.staff', [
            'settings' => $settings,
            'menuItems' => $menuItems,
            'staff' => $staff,
        ]);
    }

    /**
     * Show single news article
     */
    public function newsArticle($id)
    {
        $news = \App\Models\News::where('id', $id)
            ->where('status', 'active')
            ->firstOrFail();
        
        // Get related news (same category or latest)
        $relatedNews = \App\Models\News::where('status', 'active')
            ->where('id', '!=', $id)
            ->orderBy('created_at', 'desc')
            ->limit(4)
            ->get();
        
        $settings = SiteSetting::getAllAsArray();
        $menuItems = MenuItem::getRootItemsExcludingHome()->load('activeChildren');

        return view('frontend.news-article', [
            'news' => $news,
            'relatedNews' => $relatedNews,
            'settings' => $settings,
            'menuItems' => $menuItems,
        ]);
    }

    /**
     * Show project article page
     */
    public function projectArticle($id)
    {
        $project = \App\Models\Project::where('id', $id)
            ->where('is_active', true)
            ->firstOrFail();
        
        // Get related projects
        $relatedProjects = \App\Models\Project::where('is_active', true)
            ->where('id', '!=', $id)
            ->orderBy('order')
            ->limit(4)
            ->get();
        
        $settings = SiteSetting::getAllAsArray();
        $menuItems = MenuItem::getRootItemsExcludingHome()->load('activeChildren');

        return view('frontend.project-article', [
            'project' => $project,
            'relatedProjects' => $relatedProjects,
            'settings' => $settings,
            'menuItems' => $menuItems,
        ]);
    }

    /**
     * Show policies page
     */
    public function policies()
    {
        $settings = SiteSetting::getAllAsArray();
        $menuItems = MenuItem::getRootItemsExcludingHome()->load('activeChildren');
        $categories = PolicyCategory::getActiveWithPolicies();

        return view('frontend.policies', [
            'settings' => $settings,
            'menuItems' => $menuItems,
            'categories' => $categories,
        ]);
    }

    /**
     * Show reports page
     */
    public function reports()
    {
        $settings = SiteSetting::getAllAsArray();
        $menuItems = MenuItem::getRootItemsExcludingHome()->load('activeChildren');
        $reports = Report::getActiveOrdered();

        return view('frontend.reports', [
            'settings' => $settings,
            'menuItems' => $menuItems,
            'reports' => $reports,
        ]);
    }

    /**
     * Show executive director page
     */
    public function executiveDirector()
    {
        $settings = SiteSetting::getAllAsArray();
        $menuItems = MenuItem::getRootItemsExcludingHome()->load('activeChildren');
        
        // Debug logging (commented out - uncomment if needed)
        // \Log::info('Executive Director Page - Settings Retrieved:', [
        //     'executive_director_name' => $settings['executive_director_name'] ?? 'NOT FOUND',
        //     'executive_director_email' => $settings['executive_director_email'] ?? 'NOT FOUND',
        //     'executive_director_phone' => $settings['executive_director_phone'] ?? 'NOT FOUND',
        //     'executive_director_image' => $settings['executive_director_image'] ?? 'NOT FOUND',
        //     'total_settings_count' => count($settings),
        // ]);

        return view('frontend.executive-director', [
            'settings' => $settings,
            'menuItems' => $menuItems,
        ]);
    }
}
