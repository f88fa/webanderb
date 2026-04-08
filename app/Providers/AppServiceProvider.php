<?php

namespace App\Providers;

use App\Models\InternalMessage;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $helpers = app_path('Helpers/helpers.php');
        if (is_file($helpers)) {
            require_once $helpers;
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // مزامنة الصلاحيات تلقائياً من config عند كل تشغيل للتطبيق
        try {
            \App\Services\PermissionsRegistry::sync();
        } catch (\Throwable $e) {
            // تجاهل أثناء التنفيذ الأولي (مثلاً قبل تشغيل migrations)
        }

        View::composer('wesal.index', function ($view) {
            $unreadMessagesCount = 0;
            $newTasksCount = 0;
            $newBeneficiaryRequestsCount = 0;
            if (Auth::check()) {
                $userId = Auth::id();
                $unreadMessagesCount = InternalMessage::whereHas('recipients', function ($q) use ($userId) {
                    $q->where('user_id', $userId)->whereNull('read_at');
                })->count();
                $newTasksCount = Task::whereHas('assignees', function ($q) use ($userId) {
                    $q->where('task_assignees.user_id', $userId)->whereNull('task_assignees.seen_at');
                })->count();
                $newBeneficiaryRequestsCount = \App\Models\Beneficiary\RegistrationRequest::pending()->count();
            }
            $view->with('unreadMessagesCount', $unreadMessagesCount);
            $view->with('newTasksCount', $newTasksCount);
            $view->with('newBeneficiaryRequestsCount', $newBeneficiaryRequestsCount);
            $view->with('notificationsTotal', $unreadMessagesCount + $newTasksCount + $newBeneficiaryRequestsCount);
        });
    }
}
