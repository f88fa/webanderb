<?php

namespace App\Http\Controllers;

use App\Models\InternalMessage;
use App\Models\SiteSetting;
use App\Models\Task;
use App\Models\User;
use App\Models\HR\Employee;
use App\Models\HR\LeaveRequest;
use App\Models\HR\AdministrativeRequest;
use Illuminate\Http\Request;

/**
 * WesalController - نظام ERB على مسار /wesal
 * يحتوي على: الرئيسية، مستخدمين النظام، وجميع صفحات الموقع الإلكتروني
 */
class WesalController extends Controller
{
    /**
     * عرض نظام Wesal مع تبديل الصفحات
     */
    public function index(Request $request, ?string $page = null)
    {
        $page = $page ?? $request->get('page', 'home');

        $settings = SiteSetting::getAllAsArray();
        $viewData = ['page' => $page, 'settings' => $settings];

        switch ($page) {
            case 'home':
                $userId = auth()->id();
                $viewData['userName'] = auth()->user()->name ?? 'مستخدم';
                $viewData['inbox_count'] = InternalMessage::whereHas('recipients', fn ($q) => $q->where('user_id', $userId))->count();
                $viewData['sent_count'] = InternalMessage::where('from_user_id', $userId)->count();
                $viewData['tasks_open'] = Task::concernedByUser($userId)->where('status', 'open')->count();
                $viewData['tasks_closed'] = Task::concernedByUser($userId)->where('status', 'closed')->count();
                $employee = $userId ? Employee::where('user_id', $userId)->first() : null;
                $viewData['employee'] = $employee;
                $viewData['leave_pending'] = $employee ? LeaveRequest::where('employee_id', $employee->id)->where('status', 'pending')->count() : 0;
                $viewData['leave_total'] = $employee ? LeaveRequest::where('employee_id', $employee->id)->count() : 0;
                $viewData['requests_pending'] = $employee ? AdministrativeRequest::where('employee_id', $employee->id)->where('status', AdministrativeRequest::STATUS_PENDING)->count() : 0;
                $viewData['requests_total'] = $employee ? AdministrativeRequest::where('employee_id', $employee->id)->count() : 0;
                break;
            case 'e-office':
                $userId = auth()->id();
                $viewData['eoffice_inbox_count'] = InternalMessage::whereHas('recipients', fn ($q) => $q->where('user_id', $userId))->count();
                $viewData['eoffice_sent_count'] = InternalMessage::where('from_user_id', $userId)->count();
                $viewData['eoffice_tasks_open'] = Task::concernedByUser($userId)->where('status', 'open')->count();
                $viewData['eoffice_tasks_closed'] = Task::concernedByUser($userId)->where('status', 'closed')->count();
                break;
            case 'finance':
                // صفحة المالية الرئيسية
                $viewData['chartAccountsCount'] = \App\Models\ChartAccount::count();
                $viewData['fiscalYearsCount'] = \App\Models\FiscalYear::count();
                $viewData['journalEntriesCount'] = \App\Models\JournalEntry::count();
                $viewData['openPeriodsCount'] = \App\Models\AccountingPeriod::open()->count();
                break;
            case 'users':
                $viewData['users'] = User::with('roles')->orderBy('created_at', 'desc')->get();
                $viewData['roles'] = \Spatie\Permission\Models\Role::where('guard_name', 'web')->orderBy('name')->get();
                $viewData['permissions'] = \Spatie\Permission\Models\Permission::where('guard_name', 'web')->orderBy('name')->get();
                break;
            case 'roles-permissions':
                \App\Services\PermissionsRegistry::sync();
                $viewData['roles'] = \Spatie\Permission\Models\Role::where('guard_name', config('wesal_permissions.guard', 'web'))
                    ->with('permissions')
                    ->orderBy('name')
                    ->get();
                $viewData['groupedPermissions'] = \App\Services\PermissionsRegistry::getGroupedPermissions();
                break;
            case 'settings':
                $viewData['settings'] = $settings;
                $viewData['heroSliderImages'] = \App\Models\HeroSliderImage::getAllOrdered();
                break;
            case 'system-settings':
                $viewData['settings'] = $settings;
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
                $viewData['editService'] = $request->has('edit') ? \App\Models\Service::find($request->get('edit')) : null;
                break;
            case 'partners':
                $viewData['partners'] = \App\Models\Partner::getAllOrdered();
                $viewData['editPartner'] = $request->has('edit') ? \App\Models\Partner::find($request->get('edit')) : null;
                break;
            case 'menu':
                $viewData['menuItems'] = \App\Models\MenuItem::getAllOrdered();
                $viewData['editMenuItem'] = $request->has('edit') ? \App\Models\MenuItem::find($request->get('edit')) : null;
                break;
            case 'board-members':
                $viewData['boardMembers'] = \App\Models\BoardMember::getAllOrdered();
                $viewData['editBoardMember'] = $request->has('edit') ? \App\Models\BoardMember::find($request->get('edit')) : null;
                break;
            case 'executive-director':
                break;
            case 'policies':
                $viewData['categories'] = \App\Models\PolicyCategory::getAllOrdered();
                $viewData['policies'] = \App\Models\Policy::getAllOrdered();
                $viewData['editCategory'] = $request->has('edit_category') ? \App\Models\PolicyCategory::find($request->get('edit_category')) : null;
                $viewData['editPolicy'] = $request->has('edit_policy') ? \App\Models\Policy::find($request->get('edit_policy')) : null;
                break;
            case 'projects':
                $viewData['projects'] = \App\Models\Project::getAllOrdered();
                $viewData['editProject'] = $request->has('edit') ? \App\Models\Project::find($request->get('edit')) : null;
                break;
            case 'testimonials':
                $viewData['testimonials'] = \App\Models\Testimonial::getAllOrdered();
                $viewData['editTestimonial'] = $request->has('edit') ? \App\Models\Testimonial::find($request->get('edit')) : null;
                break;
            case 'media':
                $viewData['videos'] = \App\Models\MediaVideo::getAllOrdered();
                $viewData['slides'] = \App\Models\MediaSlide::getAllOrdered();
                $viewData['editVideo'] = $request->has('edit_video') ? \App\Models\MediaVideo::find($request->get('edit_video')) : null;
                $viewData['editSlide'] = $request->has('edit_slide') ? \App\Models\MediaSlide::find($request->get('edit_slide')) : null;
                break;
            case 'banner-sections':
                $viewData['banners'] = \App\Models\BannerSection::getAllOrdered();
                $viewData['editBanner'] = $request->has('edit') ? \App\Models\BannerSection::find($request->get('edit')) : null;
                break;
            case 'staff':
                $viewData['staff'] = \App\Models\Staff::getAllOrdered();
                $viewData['editStaff'] = $request->has('edit') ? \App\Models\Staff::find($request->get('edit')) : null;
                break;
            case 'files':
                $viewData['files'] = \App\Models\File::getAllOrdered();
                $viewData['editFile'] = $request->has('edit') ? \App\Models\File::find($request->get('edit')) : null;
                break;
            case 'reports':
                $viewData['reports'] = \App\Models\Report::getAllOrdered();
                $viewData['editReport'] = $request->has('edit') ? \App\Models\Report::find($request->get('edit')) : null;
                break;
            case 'section-order':
            case 'section_order':
                $viewData['sections'] = \App\Models\SectionOrder::getAllOrdered();
                break;
            case 'news':
                $viewData['news'] = \App\Models\News::getAllOrdered();
                $viewData['editNews'] = $request->has('edit') ? \App\Models\News::find($request->get('edit')) : null;
                break;
        }

        return response()
            ->view('wesal.index', $viewData)
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    /**
     * معاينة ورقة الخطاب (رأس / وسط / تذييل) من إعدادات النظام — بدون خطاب حقيقي
     */
    public function letterPaperPreview()
    {
        $settings = SiteSetting::getAllAsArray();
        return view('wesal.pages.communications.letter-paper', [
            'letter' => null,
            'settings' => $settings,
            'preview' => true,
        ]);
    }
}
