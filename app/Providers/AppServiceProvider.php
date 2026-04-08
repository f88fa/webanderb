<?php

namespace App\Providers;

use App\Models\InternalMessage;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

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
        Schema::defaultStringLength(191);

        // تعديل بسيط هنا: نتأكد أن التطبيق لا يحاول المزامنة أثناء تشغيل الأوامر البرمجية (مثل الاختبارات أو الميغريشن)
        if (!app()->runningInConsole()) {
            try {
                \App\Services\PermissionsRegistry::sync();
            } catch (\Throwable $e) {
                // تجاهل أثناء التنفيذ الأولي
            }
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