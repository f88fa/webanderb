<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\BannerSectionController;
use App\Http\Controllers\BoardMemberController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\PolicyController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SectionOrderController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\TestimonialController;
use App\Http\Controllers\VisionMissionController;
use App\Http\Controllers\Wesal\UserController as WesalUserController;
use App\Http\Controllers\WesalController;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Migrated from Plain PHP project:
| - index.php -> DashboardController
| - frontend.php -> FrontendController
| - pages/settings.php -> SettingsController
| - pages/about.php -> AboutController
| - pages/news.php -> NewsController
|
*/

// Storage route for shared hosting compatibility (serves files from storage/app/public)
// This route handles /storage/* requests when symlink doesn't work on shared hosting
Route::get('/storage/{path}', function ($path) {
    try {
        // Remove any leading slashes
        $path = ltrim($path, '/');

        // Security: prevent directory traversal
        if (strpos($path, '..') !== false || strpos($path, './') !== false) {
            abort(404);
        }

        // Get file path
        $filePath = storage_path('app/public/'.$path);
        $storagePath = storage_path('app/public');

        // Normalize paths
        $realFilePath = realpath($filePath);
        $realStoragePath = realpath($storagePath);

        // Security check: ensure file is within storage/app/public
        if (! $realFilePath || ! $realStoragePath) {
            abort(404);
        }

        if (strpos($realFilePath, $realStoragePath) !== 0) {
            abort(404);
        }

        // Check if file exists
        if (! file_exists($realFilePath) || ! is_file($realFilePath)) {
            abort(404);
        }

        // Get file content and mime type
        $fileContent = file_get_contents($realFilePath);
        $mimeType = mime_content_type($realFilePath);

        // Fallback for common image types if mime_content_type fails
        if (! $mimeType) {
            $extension = strtolower(pathinfo($realFilePath, PATHINFO_EXTENSION));
            $mimeTypes = [
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                'svg' => 'image/svg+xml',
                'pdf' => 'application/pdf',
            ];
            $mimeType = $mimeTypes[$extension] ?? 'application/octet-stream';
        }

        return Response::make($fileContent, 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="'.basename($path).'"',
            'Cache-Control' => 'public, max-age=31536000',
        ]);
    } catch (\Exception $e) {
        abort(404);
    }
})->where('path', '.*')->name('storage.serve');

// Frontend routes (public)
Route::get('/', [FrontendController::class, 'index'])->name('frontend');

// Page routes (for menu items with type 'page')
Route::get('/page/{slug}', [FrontendController::class, 'page'])->name('frontend.page');

// Board Members page
Route::get('/board-members', [FrontendController::class, 'boardMembers'])->name('frontend.board-members');

// Staff page
Route::get('/staff', [FrontendController::class, 'staff'])->name('frontend.staff');

// Reports page
Route::get('/reports', [FrontendController::class, 'reports'])->name('frontend.reports');

// Executive Director page
Route::get('/executive-director', [FrontendController::class, 'executiveDirector'])->name('frontend.executive-director');

// News article page
Route::get('/news/{id}', [FrontendController::class, 'newsArticle'])->name('frontend.news.article');

// Project article page
Route::get('/projects/{id}', [FrontendController::class, 'projectArticle'])->name('frontend.projects.article');

// Policies page
Route::get('/policies', [FrontendController::class, 'policies'])->name('frontend.policies');

// Authentication routes (أسماء فريدة لـ route:cache — لا تكرار لـ login/register)
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

// بوابة المستفيدين (عامة)
Route::prefix('beneficiary-portal')->name('beneficiary-portal.')->middleware(\App\Http\Middleware\SetBeneficiaryPortalLocale::class)->group(function () {
    Route::get('/', [\App\Http\Controllers\Beneficiary\BeneficiaryPortalController::class, 'index'])->name('index');
    Route::get('/login', [\App\Http\Controllers\Beneficiary\BeneficiaryPortalController::class, 'showLogin'])->name('login');
    Route::get('/register', [\App\Http\Controllers\Beneficiary\BeneficiaryPortalController::class, 'showRegister'])->name('register');
    Route::post('/register', [\App\Http\Controllers\Beneficiary\BeneficiaryPortalController::class, 'register'])->name('register.store');
    Route::get('/dashboard', [\App\Http\Controllers\Beneficiary\BeneficiaryPortalController::class, 'dashboard'])->name('dashboard')->middleware('auth');
});

// Wesal ERB system routes - Protected by auth + permission middleware (nocache لظهور ألوان لوحة التحكم فوراً بعد التعديل)
Route::prefix('wesal')->name('wesal')->middleware(['auth', 'staff.not-beneficiary-only', 'wesal.permission', 'wesal.nocache'])->group(function () {
    Route::get('/', [WesalController::class, 'index'])->name('');
    Route::post('users', [WesalUserController::class, 'store'])->name('.users.store');
    Route::put('users/{user}', [WesalUserController::class, 'update'])->name('.users.update');
    Route::post('users/{user}/roles', [WesalUserController::class, 'updateRoles'])->name('.users.roles');

    // الموارد البشرية — يجب قبل /{page}
    Route::prefix('hr')->name('.hr')->group(function () {
        Route::get('employees/{employee}/edit', [\App\Http\Controllers\HR\HrController::class, 'editEmployee'])->name('.employees.edit');
        Route::post('employees', [\App\Http\Controllers\HR\EmployeeController::class, 'store'])->name('.employees.store');
        Route::put('employees/{employee}', [\App\Http\Controllers\HR\EmployeeController::class, 'update'])->name('.employees.update');
        Route::delete('employees/{employee}', [\App\Http\Controllers\HR\EmployeeController::class, 'destroy'])->name('.employees.destroy');
        Route::post('departments', [\App\Http\Controllers\HR\DepartmentController::class, 'store'])->name('.departments.store');
        Route::put('departments/{department}', [\App\Http\Controllers\HR\DepartmentController::class, 'update'])->name('.departments.update');
        Route::delete('departments/{department}', [\App\Http\Controllers\HR\DepartmentController::class, 'destroy'])->name('.departments.destroy');
        Route::post('attendance/check-in', [\App\Http\Controllers\HR\AttendanceController::class, 'checkIn'])->name('.attendance.check-in');
        Route::post('attendance/check-out', [\App\Http\Controllers\HR\AttendanceController::class, 'checkOut'])->name('.attendance.check-out');
        Route::get('attendance/log/export', [\App\Http\Controllers\HR\HrController::class, 'attendanceLogExport'])->name('.attendance.log.export');
        Route::post('leave-types', [\App\Http\Controllers\HR\LeaveTypeController::class, 'store'])->name('.leave-types.store');
        Route::put('leave-types/{leave_type}', [\App\Http\Controllers\HR\LeaveTypeController::class, 'update'])->name('.leave-types.update');
        Route::delete('leave-types/{leave_type}', [\App\Http\Controllers\HR\LeaveTypeController::class, 'destroy'])->name('.leave-types.destroy');
        Route::get('leave/approvals', [\App\Http\Controllers\HR\HrController::class, 'show'])->defaults('section', 'leave')->defaults('sub', 'approvals')->name('.leave.approvals');
        Route::get('leave/{leaveRequest}', [\App\Http\Controllers\HR\LeaveController::class, 'show'])->name('.leave.show');
        Route::post('leave/request', [\App\Http\Controllers\HR\LeaveController::class, 'storeRequest'])->name('.leave.request');
        Route::post('leave/{leaveRequest}/approve', [\App\Http\Controllers\HR\LeaveController::class, 'approve'])->name('.leave.approve');
        Route::post('leave/{leaveRequest}/reject', [\App\Http\Controllers\HR\LeaveController::class, 'reject'])->name('.leave.reject');
        Route::post('shifts', [\App\Http\Controllers\HR\ShiftController::class, 'store'])->name('.shifts.store');
        Route::put('shifts/{shift}', [\App\Http\Controllers\HR\ShiftController::class, 'update'])->name('.shifts.update');
        Route::delete('shifts/{shift}', [\App\Http\Controllers\HR\ShiftController::class, 'destroy'])->name('.shifts.destroy');
        Route::post('payroll/run', [\App\Http\Controllers\HR\PayrollController::class, 'storeRun'])->name('.payroll.run');
        Route::post('allowance-deduction', [\App\Http\Controllers\HR\AllowanceDeductionController::class, 'store'])->name('.allowance-deduction.store');
        Route::delete('allowance-deduction/{allowanceDeduction}', [\App\Http\Controllers\HR\AllowanceDeductionController::class, 'destroy'])->name('.allowance-deduction.destroy');
        Route::post('advances', [\App\Http\Controllers\HR\AdvanceController::class, 'store'])->name('.advances.store');
        Route::post('advances/{advance}/approve', [\App\Http\Controllers\HR\AdvanceController::class, 'approve'])->name('.advances.approve');
        Route::post('contracts', [\App\Http\Controllers\HR\ContractController::class, 'store'])->name('.contracts.store');
        Route::delete('contracts/{contract}', [\App\Http\Controllers\HR\ContractController::class, 'destroy'])->name('.contracts.destroy');
        Route::post('decisions', [\App\Http\Controllers\HR\DecisionController::class, 'store'])->name('.decisions.store');
        Route::delete('decisions/{decision}', [\App\Http\Controllers\HR\DecisionController::class, 'destroy'])->name('.decisions.destroy');
        Route::post('letters', [\App\Http\Controllers\HR\LetterController::class, 'store'])->name('.letters.store');
        Route::delete('letters/{letter}', [\App\Http\Controllers\HR\LetterController::class, 'destroy'])->name('.letters.destroy');
        Route::post('request-settings', [\App\Http\Controllers\HR\RequestApprovalSettingsController::class, 'store'])->name('.request-settings.store');
        Route::get('{section?}/{sub?}', [\App\Http\Controllers\HR\HrController::class, 'show'])->name('.show')->defaults('section', null);
    });

    // المستفيدين — يجب قبل /{page}
    Route::prefix('beneficiaries')->name('.beneficiaries')->group(function () {
        Route::get('forms', [\App\Http\Controllers\Beneficiary\BeneficiaryFormController::class, 'index'])->name('.forms.index');
        Route::get('forms/create', [\App\Http\Controllers\Beneficiary\BeneficiaryFormController::class, 'create'])->name('.forms.create');
        Route::post('forms', [\App\Http\Controllers\Beneficiary\BeneficiaryFormController::class, 'store'])->name('.forms.store');
        Route::get('forms/{form}/edit', [\App\Http\Controllers\Beneficiary\BeneficiaryFormController::class, 'edit'])->name('.forms.edit');
        Route::put('forms/{form}', [\App\Http\Controllers\Beneficiary\BeneficiaryFormController::class, 'update'])->name('.forms.update');
        Route::delete('forms/{form}', [\App\Http\Controllers\Beneficiary\BeneficiaryFormController::class, 'destroy'])->name('.forms.destroy');
        Route::post('forms/{form}/fields', [\App\Http\Controllers\Beneficiary\BeneficiaryFormController::class, 'storeField'])->name('.forms.fields.store');
        Route::put('form-fields/{field}', [\App\Http\Controllers\Beneficiary\BeneficiaryFormController::class, 'updateField'])->name('.forms.fields.update');
        Route::delete('form-fields/{field}', [\App\Http\Controllers\Beneficiary\BeneficiaryFormController::class, 'destroyField'])->name('.forms.fields.destroy');
        Route::post('forms-settings', [\App\Http\Controllers\Beneficiary\BeneficiaryFormController::class, 'updateFormSettings'])->name('.forms.settings');
        Route::post('beneficiaries', [\App\Http\Controllers\Beneficiary\BeneficiaryController::class, 'store'])->name('.beneficiaries.store');
        Route::put('beneficiaries/{beneficiary}', [\App\Http\Controllers\Beneficiary\BeneficiaryController::class, 'update'])->name('.beneficiaries.update');
        Route::post('beneficiaries/{beneficiary}/archive', [\App\Http\Controllers\Beneficiary\BeneficiaryController::class, 'archive'])->name('.beneficiaries.archive');
        Route::post('beneficiaries/{beneficiary}/unarchive', [\App\Http\Controllers\Beneficiary\BeneficiaryController::class, 'unarchive'])->name('.beneficiaries.unarchive');
        Route::delete('beneficiaries/{beneficiary}', [\App\Http\Controllers\Beneficiary\BeneficiaryController::class, 'destroy'])->name('.beneficiaries.destroy');
        Route::post('requests', [\App\Http\Controllers\Beneficiary\BeneficiaryRequestController::class, 'store'])->name('.requests.store');
        Route::post('requests/{beneficiary_request}/study', [\App\Http\Controllers\Beneficiary\BeneficiaryRequestController::class, 'moveToStudy'])->name('.requests.study');
        Route::post('requests/{beneficiary_request}/approve', [\App\Http\Controllers\Beneficiary\BeneficiaryRequestController::class, 'approve'])->name('.requests.approve');
        Route::post('requests/{beneficiary_request}/reject', [\App\Http\Controllers\Beneficiary\BeneficiaryRequestController::class, 'reject'])->name('.requests.reject');
        Route::post('service-types', [\App\Http\Controllers\Beneficiary\ServiceTypeController::class, 'store'])->name('.service-types.store');
        Route::delete('service-types/{serviceType}', [\App\Http\Controllers\Beneficiary\ServiceTypeController::class, 'destroy'])->name('.service-types.destroy');
        Route::post('beneficiary-services', [\App\Http\Controllers\Beneficiary\BeneficiaryServiceController::class, 'store'])->name('.beneficiary-services.store');
        Route::post('medical-records', [\App\Http\Controllers\Beneficiary\MedicalRecordController::class, 'store'])->name('.medical-records.store');
        Route::post('assessments', [\App\Http\Controllers\Beneficiary\AssessmentController::class, 'store'])->name('.assessments.store');
        Route::post('documents', [\App\Http\Controllers\Beneficiary\BeneficiaryDocumentController::class, 'store'])->name('.documents.store');
        Route::delete('documents/{beneficiary_document}', [\App\Http\Controllers\Beneficiary\BeneficiaryDocumentController::class, 'destroy'])->name('.documents.destroy');
        Route::post('programs', [\App\Http\Controllers\Beneficiary\ProgramController::class, 'store'])->name('.programs.store');
        Route::delete('programs/{program}', [\App\Http\Controllers\Beneficiary\ProgramController::class, 'destroy'])->name('.programs.destroy');
        Route::get('registration-requests/{registration_request}', [\App\Http\Controllers\Beneficiary\RegistrationRequestController::class, 'show'])->name('.registration-requests.show');
        Route::post('registration-requests/{registration_request}/approve', [\App\Http\Controllers\Beneficiary\RegistrationRequestController::class, 'approve'])->name('.registration-requests.approve');
        Route::post('registration-requests/{registration_request}/reject', [\App\Http\Controllers\Beneficiary\RegistrationRequestController::class, 'reject'])->name('.registration-requests.reject');
        Route::get('{section?}/{sub?}', [\App\Http\Controllers\BeneficiariesController::class, 'show'])->name('.show')->defaults('section', null);
    });

    // البرامج والمشاريع
    Route::prefix('programs-projects')->name('.programs-projects')->group(function () {
        Route::post('projects', [\App\Http\Controllers\ProgramsProjects\ProjectController::class, 'store'])->name('.projects.store');
        Route::put('projects/{project}', [\App\Http\Controllers\ProgramsProjects\ProjectController::class, 'update'])->name('.projects.update');
        Route::post('projects/{project}/archive', [\App\Http\Controllers\ProgramsProjects\ProjectController::class, 'archive'])->name('.projects.archive');
        Route::post('projects/{project}/unarchive', [\App\Http\Controllers\ProgramsProjects\ProjectController::class, 'unarchive'])->name('.projects.unarchive');
        Route::delete('projects/{project}', [\App\Http\Controllers\ProgramsProjects\ProjectController::class, 'destroy'])->name('.projects.destroy');
        Route::post('donors', [\App\Http\Controllers\ProgramsProjects\DonorController::class, 'store'])->name('.donors.store');
        Route::put('donors/{donor}', [\App\Http\Controllers\ProgramsProjects\DonorController::class, 'update'])->name('.donors.update');
        Route::delete('donors/{donor}', [\App\Http\Controllers\ProgramsProjects\DonorController::class, 'destroy'])->name('.donors.destroy');
        Route::post('stages', [\App\Http\Controllers\ProgramsProjects\StageController::class, 'store'])->name('.stages.store');
        Route::put('stages/{stage}', [\App\Http\Controllers\ProgramsProjects\StageController::class, 'update'])->name('.stages.update');
        Route::delete('stages/{stage}', [\App\Http\Controllers\ProgramsProjects\StageController::class, 'destroy'])->name('.stages.destroy');
        Route::post('stage-updates', [\App\Http\Controllers\ProgramsProjects\StageUpdateController::class, 'store'])->name('.stage-updates.store');
        Route::delete('stage-updates/{stageUpdate}', [\App\Http\Controllers\ProgramsProjects\StageUpdateController::class, 'destroy'])->name('.stage-updates.destroy');
        Route::post('project-tasks', [\App\Http\Controllers\ProgramsProjects\ProjectTaskController::class, 'store'])->name('.project-tasks.store');
        Route::put('project-tasks/{project_task}', [\App\Http\Controllers\ProgramsProjects\ProjectTaskController::class, 'update'])->name('.project-tasks.update');
        Route::delete('project-tasks/{project_task}', [\App\Http\Controllers\ProgramsProjects\ProjectTaskController::class, 'destroy'])->name('.project-tasks.destroy');
        Route::post('agreements', [\App\Http\Controllers\ProgramsProjects\AgreementController::class, 'store'])->name('.agreements.store');
        Route::delete('agreements/{agreement}', [\App\Http\Controllers\ProgramsProjects\AgreementController::class, 'destroy'])->name('.agreements.destroy');
        Route::post('grants', [\App\Http\Controllers\ProgramsProjects\GrantController::class, 'store'])->name('.grants.store');
        Route::delete('grants/{grant}', [\App\Http\Controllers\ProgramsProjects\GrantController::class, 'destroy'])->name('.grants.destroy');
        Route::post('expenses', [\App\Http\Controllers\ProgramsProjects\ExpenseController::class, 'store'])->name('.expenses.store');
        Route::delete('expenses/{expense}', [\App\Http\Controllers\ProgramsProjects\ExpenseController::class, 'destroy'])->name('.expenses.destroy');
        Route::post('project-documents', [\App\Http\Controllers\ProgramsProjects\ProjectDocumentController::class, 'store'])->name('.project-documents.store');
        Route::delete('project-documents/{project_document}', [\App\Http\Controllers\ProgramsProjects\ProjectDocumentController::class, 'destroy'])->name('.project-documents.destroy');
        Route::get('{section?}/{sub?}', [\App\Http\Controllers\ProgramsProjectsController::class, 'show'])->name('.show')->defaults('section', null);
    });

    // الاجتماعات
    Route::prefix('meetings')->name('.meetings')->group(function () {
        Route::post('meeting-types', [\App\Http\Controllers\Meetings\MeetingTypeController::class, 'store'])->name('.meeting-types.store');
        Route::put('meeting-types/{meeting_type}', [\App\Http\Controllers\Meetings\MeetingTypeController::class, 'update'])->name('.meeting-types.update');
        Route::delete('meeting-types/{meeting_type}', [\App\Http\Controllers\Meetings\MeetingTypeController::class, 'destroy'])->name('.meeting-types.destroy');
        Route::post('board-members', [\App\Http\Controllers\Meetings\BoardMemberController::class, 'store'])->name('.board-members.store');
        Route::put('board-members/{board_member}', [\App\Http\Controllers\Meetings\BoardMemberController::class, 'update'])->name('.board-members.update');
        Route::delete('board-members/{board_member}', [\App\Http\Controllers\Meetings\BoardMemberController::class, 'destroy'])->name('.board-members.destroy');
        Route::post('board-meetings', [\App\Http\Controllers\Meetings\BoardMeetingController::class, 'store'])->name('.board-meetings.store');
        Route::put('board-meetings/{board_meeting}', [\App\Http\Controllers\Meetings\BoardMeetingController::class, 'update'])->name('.board-meetings.update');
        Route::delete('board-meetings/{board_meeting}', [\App\Http\Controllers\Meetings\BoardMeetingController::class, 'destroy'])->name('.board-meetings.destroy');
        Route::post('staff-meetings', [\App\Http\Controllers\Meetings\StaffMeetingController::class, 'store'])->name('.staff-meetings.store');
        Route::put('staff-meetings/{staff_meeting}', [\App\Http\Controllers\Meetings\StaffMeetingController::class, 'update'])->name('.staff-meetings.update');
        Route::delete('staff-meetings/{staff_meeting}', [\App\Http\Controllers\Meetings\StaffMeetingController::class, 'destroy'])->name('.staff-meetings.destroy');
        Route::post('board-decisions', [\App\Http\Controllers\Meetings\BoardDecisionController::class, 'store'])->name('.board-decisions.store');
        Route::put('board-decisions/{board_decision}', [\App\Http\Controllers\Meetings\BoardDecisionController::class, 'update'])->name('.board-decisions.update');
        Route::delete('board-decisions/{board_decision}', [\App\Http\Controllers\Meetings\BoardDecisionController::class, 'destroy'])->name('.board-decisions.destroy');
        Route::get('{section?}/{sub?}', [\App\Http\Controllers\MeetingsController::class, 'show'])->name('.show')->defaults('section', null);
    });

    Route::post('roles', [\App\Http\Controllers\Wesal\RolesManagementController::class, 'store'])->name('.roles.store');
    Route::post('roles/permissions', [\App\Http\Controllers\Wesal\RolesManagementController::class, 'updatePermissions'])->name('.roles.update-permissions');
    Route::delete('roles/{role}', [\App\Http\Controllers\Wesal\RolesManagementController::class, 'destroy'])->name('.roles.destroy');

    // مسار سري: إعادة النظام إلى الافتراضي (ديمو) — لا يظهر أي زر، فقط بكتابة المسار
    Route::get('bbaacckk', [\App\Http\Controllers\Wesal\ResetToDefaultController::class, '__invoke'])->name('.reset-to-default');

    Route::get('letter-paper-preview', [WesalController::class, 'letterPaperPreview'])->name('.letter-paper-preview');

    // حفظ إعدادات النظام من واجهة Wesal (نفس الـ Controller لضمان عمل الحفظ عند فتح الصفحة من /wesal/system-settings)
    Route::post('system-settings', [SettingsController::class, 'updateSystemSettings'])->name('.system-settings.update');

    // حفظ إعدادات الموقع من وصال (حتى لا يحوّل المستخدم إلى لوحة التحكم المستقلة)
    Route::post('settings/update', [SettingsController::class, 'update'])->name('.settings.update');
    Route::post('settings/reset-hero-background', [SettingsController::class, 'resetHeroBackground'])->name('.settings.reset-hero-background');
    Route::post('settings/reset-section-background/{section}', [SettingsController::class, 'resetSectionBackground'])->name('.settings.reset-section-background');
    Route::post('settings/reset-colors', [SettingsController::class, 'resetColors'])->name('.settings.reset-colors');
    Route::post('settings/reset-dashboard-colors', [SettingsController::class, 'resetDashboardColors'])->name('.settings.reset-dashboard-colors');
    Route::post('settings/reset-letter-paper', [SettingsController::class, 'resetLetterPaper'])->name('.settings.reset-letter-paper');
    Route::post('settings/generate-section-colors/{section}', [SettingsController::class, 'generateSectionColors'])->name('.settings.generate-section-colors');
    Route::post('settings/generate-site-colors', [SettingsController::class, 'generateSiteColors'])->name('.settings.generate-site-colors');
    Route::post('settings/auto-color-scheme', [SettingsController::class, 'generateAutoColorScheme'])->name('.settings.auto-color-scheme');
    Route::post('settings/extract-colors', [SettingsController::class, 'extractColorsFromImage'])->name('.settings.extract-colors');

    Route::get('/{page}', [WesalController::class, 'index'])->where('page', 'home|e-office|finance|hr|programs-projects|meetings|users|roles-permissions|settings|system-settings|about|news|vision-mission|services|partners|menu|board-members|executive-director|policies|projects|testimonials|media|banner-sections|staff|files|reports|section-order|section_order')->name('.page');

    // المكتب الإلكتروني — البريد + المهام
    Route::prefix('e-office')->name('.e-office')->group(function () {
        Route::prefix('mail')->name('.mail')->controller(\App\Http\Controllers\EOffice\MailController::class)->group(function () {
            Route::get('inbox', 'inbox')->name('.inbox');
            Route::get('sent', 'sent')->name('.sent');
            Route::get('compose', 'compose')->name('.compose');
            Route::post('compose', 'store')->name('.store');
            Route::get('{internal_message}', 'show')->name('.show');
        });
        Route::prefix('tasks')->name('.tasks')->controller(\App\Http\Controllers\EOffice\TaskController::class)->group(function () {
            Route::get('/', 'index')->name('.index');
            Route::get('create', 'create')->name('.create');
            Route::post('/', 'store')->name('.store');
            Route::get('{task}', 'show')->name('.show');
            Route::post('{task}/updates', 'addUpdate')->name('.updates.store');
            Route::post('{task}/close', 'close')->name('.close');
        });
    });

    // الطلبات الإدارية (ذاتية: إجازة، طلب عام، طلب مالي، حضور وانصراف — خاصة بالمستخدم فقط)
    Route::prefix('requests')->name('.requests')->controller(\App\Http\Controllers\HR\AdministrativeRequestsController::class)->group(function () {
        Route::get('leave/{leaveRequest}', 'showLeave')->name('.leave.show');
        Route::get('{section?}', 'show')->name('.show')->defaults('section', 'leave');
        Route::post('leave', 'storeLeave')->name('.leave.store');
        Route::post('general', 'storeGeneral')->name('.general.store');
        Route::post('financial', 'storeFinancial')->name('.financial.store');
        Route::post('attendance/check-in', 'attendanceCheckIn')->name('.attendance.check-in');
        Route::post('attendance/check-out', 'attendanceCheckOut')->name('.attendance.check-out');
    });

    // الاتصالات الإدارية - الصادر والوارد (الخطابات)
    Route::prefix('communications')->name('.communications')->controller(\App\Http\Controllers\AdminCommunicationsController::class)->group(function () {
        Route::get('outgoing', 'outgoing')->name('.outgoing');
        Route::get('outgoing/create', 'createOutgoing')->name('.outgoing.create');
        Route::post('outgoing', 'storeOutgoing')->name('.outgoing.store');
        Route::get('incoming', 'incoming')->name('.incoming');
        Route::get('incoming/create', 'createIncoming')->name('.incoming.create');
        Route::post('incoming', 'storeIncoming')->name('.incoming.store');
        Route::get('{letter}/print', 'print')->name('.print');
        Route::get('{letter}', 'show')->name('.show');
    });

    // Finance routes
    Route::prefix('finance')->name('.finance')->group(function () {
        // Dev Routes (only if FINANCE_DEV_ROUTES=true)
        if (env('FINANCE_DEV_ROUTES', false)) {
            Route::get('dev/diag', [\App\Http\Controllers\Finance\DevDiagnosticController::class, 'diagnostic'])->name('.dev.diag');
            Route::post('dev/bootstrap-accounts', [\App\Http\Controllers\Finance\DevBootstrapController::class, 'bootstrap'])->name('.dev.bootstrap');
        }

        // Chart Accounts
        // Routes الخاصة (قبل resource route لتجنب التعارض)
        Route::get('chart-accounts/tree', [\App\Http\Controllers\Finance\ChartAccountController::class, 'tree'])->name('.chart-accounts.tree');
        Route::get('chart-accounts/tree-from-level', [\App\Http\Controllers\Finance\ChartAccountController::class, 'treeFromLevel'])->name('.chart-accounts.tree-from-level');
        Route::get('chart-accounts/trial-balance', [\App\Http\Controllers\Finance\ChartAccountController::class, 'trialBalance'])->name('.chart-accounts.trial-balance');
        Route::get('chart-accounts/trial-balance/export', [\App\Http\Controllers\Finance\ChartAccountController::class, 'trialBalanceExport'])->name('.chart-accounts.trial-balance.export');
        Route::get('chart-accounts/{chartAccount}/details', [\App\Http\Controllers\Finance\ChartAccountController::class, 'getAccountDetails'])->name('.chart-accounts.details');

        // Resource routes (يجب أن تكون بعد routes الخاصة)
        Route::resource('chart-accounts', \App\Http\Controllers\Finance\ChartAccountController::class)->names([
            'index' => '.chart-accounts.index',
            'create' => '.chart-accounts.create',
            'store' => '.chart-accounts.store',
            'show' => '.chart-accounts.show',
            'edit' => '.chart-accounts.edit',
            'update' => '.chart-accounts.update',
            'destroy' => '.chart-accounts.destroy',
        ]);

        // Ledger
        Route::get('chart-accounts/{chartAccount}/ledger', [\App\Http\Controllers\Finance\LedgerController::class, 'index'])->name('.chart-accounts.ledger');
        Route::get('chart-accounts/{chartAccount}/ledger/export', [\App\Http\Controllers\Finance\LedgerController::class, 'exportExcel'])->name('.chart-accounts.ledger.export');

        // Fiscal Years
        Route::resource('fiscal-years', \App\Http\Controllers\Finance\FiscalYearController::class)->names([
            'index' => '.fiscal-years.index',
            'store' => '.fiscal-years.store',
            'show' => '.fiscal-years.show',
        ]);
        Route::post('fiscal-years/{fiscalYear}/close', [\App\Http\Controllers\Finance\FiscalYearController::class, 'close'])->name('.fiscal-years.close');

        // Accounting Periods
        Route::get('periods', [\App\Http\Controllers\Finance\PeriodController::class, 'index'])->name('.periods.index');
        Route::post('periods/{period}/close-posting', [\App\Http\Controllers\Finance\PeriodController::class, 'closePosting'])->name('.periods.close-posting');
        Route::post('periods/{period}/open-posting', [\App\Http\Controllers\Finance\PeriodController::class, 'openPosting'])->name('.periods.open-posting');
        Route::post('periods/{period}/close-adjustments', [\App\Http\Controllers\Finance\PeriodController::class, 'closeAdjustments'])->name('.periods.close-adjustments');
        Route::post('periods/{period}/open-adjustments', [\App\Http\Controllers\Finance\PeriodController::class, 'openAdjustments'])->name('.periods.open-adjustments');

        // Journal Entries
        Route::get('journal-entries/select-period', [\App\Http\Controllers\Finance\JournalEntryController::class, 'selectPeriod'])->name('.journal-entries.select-period');
        Route::resource('journal-entries', \App\Http\Controllers\Finance\JournalEntryController::class)->names([
            'index' => '.journal-entries.index',
            'create' => '.journal-entries.create',
            'store' => '.journal-entries.store',
            'show' => '.journal-entries.show',
        ]);
        Route::post('journal-entries/{journalEntry}/post', [\App\Http\Controllers\Finance\JournalEntryController::class, 'post'])->name('.journal-entries.post');
        Route::post('journal-entries/{journalEntry}/reverse', [\App\Http\Controllers\Finance\JournalEntryController::class, 'reverse'])->name('.journal-entries.reverse');
        Route::get('journal-entries/{journalEntry}/print', [\App\Http\Controllers\Finance\JournalEntryController::class, 'print'])->name('.journal-entries.print');

        // Vouchers
        Route::get('receipt-voucher/create', [\App\Http\Controllers\Finance\JournalEntryController::class, 'createReceiptVoucher'])->name('.receipt-voucher.create');
        Route::get('payment-voucher/create', [\App\Http\Controllers\Finance\JournalEntryController::class, 'createPaymentVoucher'])->name('.payment-voucher.create');

        // طلبات الصرف
        Route::get('payment-requests', [\App\Http\Controllers\Finance\PaymentRequestController::class, 'index'])->name('.payment-requests.index');
        Route::get('payment-requests/create', [\App\Http\Controllers\Finance\PaymentRequestController::class, 'create'])->name('.payment-requests.create');
        Route::post('payment-requests', [\App\Http\Controllers\Finance\PaymentRequestController::class, 'store'])->name('.payment-requests.store');
        Route::get('payment-requests/{paymentRequest}/beneficiaries', [\App\Http\Controllers\Finance\PaymentRequestController::class, 'showBeneficiariesReport'])->name('.payment-requests.beneficiaries');
        Route::post('payment-requests/{paymentRequest}/approve', [\App\Http\Controllers\Finance\PaymentRequestController::class, 'approve'])->name('.payment-requests.approve');
        Route::post('payment-requests/{paymentRequest}/reject', [\App\Http\Controllers\Finance\PaymentRequestController::class, 'reject'])->name('.payment-requests.reject');

        // التقارير المالية
        Route::get('reports', [\App\Http\Controllers\Finance\FinancialReportsController::class, 'index'])->name('.reports.index');
        Route::get('reports/income-statement', [\App\Http\Controllers\Finance\FinancialReportsController::class, 'incomeStatement'])->name('.reports.income-statement');
        Route::get('reports/income-statement/export', [\App\Http\Controllers\Finance\FinancialReportsController::class, 'incomeStatementExport'])->name('.reports.income-statement.export');
        Route::get('reports/balance-sheet', [\App\Http\Controllers\Finance\FinancialReportsController::class, 'balanceSheet'])->name('.reports.balance-sheet');
        Route::get('reports/balance-sheet/export', [\App\Http\Controllers\Finance\FinancialReportsController::class, 'balanceSheetExport'])->name('.reports.balance-sheet.export');
        Route::get('reports/statement-activities-by-function', [\App\Http\Controllers\Finance\FinancialReportsController::class, 'statementOfActivitiesByFunction'])->name('.reports.statement-activities-by-function');
        Route::get('reports/financial-movement', [\App\Http\Controllers\Finance\FinancialReportsController::class, 'financialMovement'])->name('.reports.financial-movement');
        Route::get('reports/financial-movement/export', [\App\Http\Controllers\Finance\FinancialReportsController::class, 'financialMovementExport'])->name('.reports.financial-movement.export');
        Route::get('reports/cash-flow', [\App\Http\Controllers\Finance\FinancialReportsController::class, 'cashFlow'])->name('.reports.cash-flow');
        Route::get('reports/cash-flow/export', [\App\Http\Controllers\Finance\FinancialReportsController::class, 'cashFlowExport'])->name('.reports.cash-flow.export');
        Route::get('reports/general-ledger', [\App\Http\Controllers\Finance\FinancialReportsController::class, 'generalLedger'])->name('.reports.general-ledger');
        Route::get('reports/net-assets-changes', [\App\Http\Controllers\Finance\FinancialReportsController::class, 'netAssetsChanges'])->name('.reports.net-assets-changes');
        Route::get('reports/net-assets-changes/export', [\App\Http\Controllers\Finance\FinancialReportsController::class, 'netAssetsChangesExport'])->name('.reports.net-assets-changes.export');

        // مراكز التكلفة
        Route::resource('cost-centers', \App\Http\Controllers\Finance\CostCenterController::class)->names([
            'index' => '.cost-centers.index',
            'store' => '.cost-centers.store',
            'update' => '.cost-centers.update',
            'destroy' => '.cost-centers.destroy',
        ])->only(['index', 'store', 'update', 'destroy']);

        // أصناف الأموال (الأوقاف)
        Route::resource('funds', \App\Http\Controllers\Finance\FundController::class)->names([
            'index' => '.funds.index',
            'edit' => '.funds.edit',
            'store' => '.funds.store',
            'update' => '.funds.update',
            'destroy' => '.funds.destroy',
        ])->only(['index', 'edit', 'store', 'update', 'destroy']);
    });
});

// Dashboard routes (admin panel) - Protected by auth middleware
Route::prefix('dashboard')->name('dashboard')->middleware(['auth', 'staff.not-beneficiary-only'])->group(function () {
    // Main dashboard with page switching (أسماء فريدة لـ route:cache — لا يُكرّر name(''))
    Route::get('/', [DashboardController::class, 'index'])->name('');
    Route::get('/{page}', [DashboardController::class, 'index'])->where('page', 'home|settings|about|news|vision-mission|services|partners|menu|board-members|executive-director|policies|projects|testimonials|media|banner-sections|staff|files|reports|section-order|section_order')->name('.page');

    // Settings routes
    Route::post('/settings', [SettingsController::class, 'update'])->name('.settings.update');
    Route::post('/settings/system', [SettingsController::class, 'updateSystemSettings'])->name('.settings.update-system');
    Route::post('/settings/reset-hero-background', [SettingsController::class, 'resetHeroBackground'])->name('.settings.reset-hero-background');
    Route::post('/settings/reset-section-background/{section}', [SettingsController::class, 'resetSectionBackground'])->name('.settings.reset-section-background');
    Route::post('/settings/reset-colors', [SettingsController::class, 'resetColors'])->name('.settings.reset-colors');
    Route::post('/settings/reset-dashboard-colors', [SettingsController::class, 'resetDashboardColors'])->name('.settings.reset-dashboard-colors');
    Route::post('/settings/reset-letter-paper', [SettingsController::class, 'resetLetterPaper'])->name('.settings.reset-letter-paper');
    Route::post('/settings/generate-section-colors/{section}', [SettingsController::class, 'generateSectionColors'])->name('.settings.generate-section-colors');
    Route::post('/settings/generate-site-colors', [SettingsController::class, 'generateSiteColors'])->name('.settings.generate-site-colors');
    Route::post('/settings/auto-color-scheme', [SettingsController::class, 'generateAutoColorScheme'])->name('.settings.auto-color-scheme');
    Route::post('/settings/extract-colors', [SettingsController::class, 'extractColorsFromImage'])->name('.settings.extract-colors');

    // About routes
    Route::post('/about', [AboutController::class, 'store'])->name('.about.store');

    // Vision & Mission routes
    Route::post('/vision-mission', [VisionMissionController::class, 'store'])->name('.vision-mission.store');

    // Services routes (RESTful)
    Route::post('/services', [ServiceController::class, 'store'])->name('.services.store');
    Route::put('/services/{id}', [ServiceController::class, 'update'])->name('.services.update');
    Route::delete('/services/{id}', [ServiceController::class, 'destroy'])->name('.services.destroy');

    // Partners routes (RESTful)
    Route::post('/partners', [PartnerController::class, 'store'])->name('.partners.store');
    Route::put('/partners/{id}', [PartnerController::class, 'update'])->name('.partners.update');
    Route::delete('/partners/{id}', [PartnerController::class, 'destroy'])->name('.partners.destroy');

    // Menu routes (RESTful)
    Route::post('/menu', [MenuController::class, 'store'])->name('.menu.store');
    Route::put('/menu/{id}', [MenuController::class, 'update'])->name('.menu.update');
    Route::delete('/menu/{id}', [MenuController::class, 'destroy'])->name('.menu.destroy');

    // Board Members routes (RESTful)
    Route::post('/board-members', [BoardMemberController::class, 'store'])->name('.board-members.store');
    Route::put('/board-members/{id}', [BoardMemberController::class, 'update'])->name('.board-members.update');
    Route::delete('/board-members/{id}', [BoardMemberController::class, 'destroy'])->name('.board-members.destroy');

    // Policies routes (RESTful)
    Route::post('/policies/category', [PolicyController::class, 'storeCategory'])->name('.policies.category.store');
    Route::put('/policies/category/{id}', [PolicyController::class, 'updateCategory'])->name('.policies.category.update');
    Route::delete('/policies/category/{id}', [PolicyController::class, 'destroyCategory'])->name('.policies.category.destroy');
    Route::post('/policies/policy', [PolicyController::class, 'storePolicy'])->name('.policies.policy.store');
    Route::put('/policies/policy/{id}', [PolicyController::class, 'updatePolicy'])->name('.policies.policy.update');
    Route::delete('/policies/policy/{id}', [PolicyController::class, 'destroyPolicy'])->name('.policies.policy.destroy');

    // Projects routes (RESTful)
    Route::post('/projects', [ProjectController::class, 'store'])->name('.projects.store');
    Route::put('/projects/{id}', [ProjectController::class, 'update'])->name('.projects.update');
    Route::delete('/projects/{id}', [ProjectController::class, 'destroy'])->name('.projects.destroy');

    // Testimonials routes (RESTful)
    Route::post('/testimonials', [TestimonialController::class, 'store'])->name('.testimonials.store');
    Route::put('/testimonials/{id}', [TestimonialController::class, 'update'])->name('.testimonials.update');
    Route::delete('/testimonials/{id}', [TestimonialController::class, 'destroy'])->name('.testimonials.destroy');

    // Media routes (RESTful)
    Route::post('/media/videos', [MediaController::class, 'storeVideo'])->name('.media.videos.store');
    Route::put('/media/videos/{id}', [MediaController::class, 'updateVideo'])->name('.media.videos.update');
    Route::delete('/media/videos/{id}', [MediaController::class, 'destroyVideo'])->name('.media.videos.destroy');
    Route::post('/media/slides', [MediaController::class, 'storeSlide'])->name('.media.slides.store');
    Route::put('/media/slides/{id}', [MediaController::class, 'updateSlide'])->name('.media.slides.update');
    Route::delete('/media/slides/{id}', [MediaController::class, 'destroySlide'])->name('.media.slides.destroy');

    // Banner Sections routes (RESTful)
    Route::post('/banner-sections', [BannerSectionController::class, 'store'])->name('.banner-sections.store');
    Route::put('/banner-sections/{id}', [BannerSectionController::class, 'update'])->name('.banner-sections.update');
    Route::delete('/banner-sections/{id}', [BannerSectionController::class, 'destroy'])->name('.banner-sections.destroy');

    // Staff routes (RESTful)
    Route::post('/staff', [StaffController::class, 'store'])->name('.staff.store');
    Route::put('/staff/{id}', [StaffController::class, 'update'])->name('.staff.update');
    Route::delete('/staff/{id}', [StaffController::class, 'destroy'])->name('.staff.destroy');

    // Files routes (RESTful)
    Route::post('/files', [FileController::class, 'store'])->name('.files.store');
    Route::put('/files/{id}', [FileController::class, 'update'])->name('.files.update');
    Route::delete('/files/{id}', [FileController::class, 'destroy'])->name('.files.destroy');

    // Reports routes (RESTful)
    Route::post('/reports', [ReportController::class, 'store'])->name('.reports.store');
    Route::put('/reports/{id}', [ReportController::class, 'update'])->name('.reports.update');
    Route::delete('/reports/{id}', [ReportController::class, 'destroy'])->name('.reports.destroy');

    // Section Order routes
    Route::post('/section-order/update', [SectionOrderController::class, 'update'])->name('.section-order.update');
    Route::post('/section-order/reset', [SectionOrderController::class, 'reset'])->name('.section-order.reset');

    // News routes (RESTful)
    Route::post('/news', [NewsController::class, 'store'])->name('.news.store');
    Route::put('/news/{id}', [NewsController::class, 'update'])->name('.news.update');
    Route::delete('/news/{id}', [NewsController::class, 'destroy'])->name('.news.destroy');
});
