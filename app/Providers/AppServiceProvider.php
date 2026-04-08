/**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        // 1. هذا السطر هو "مفتاح الحل": إذا كان التطبيق يعمل في الكونسول (مثل الميغريشن في GitHub)، توقف هنا فوراً.
        if (app()->runningInConsole()) {
            return;
        }

        // 2. الكود بالأسفل لن يراه الروبوت ولن يسبب له أي أخطاء، وسيعمل فقط للمستخدمين الحقيقيين.
        try {
            \App\Services\PermissionsRegistry::sync();
        } catch (\Throwable $e) {
            // تجاهل أثناء التنفيذ الأولي
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
            
            $view->with([
                'unreadMessagesCount' => $unreadMessagesCount,
                'newTasksCount' => $newTasksCount,
                'newBeneficiaryRequestsCount' => $newBeneficiaryRequestsCount,
                'notificationsTotal' => $unreadMessagesCount + $newTasksCount + $newBeneficiaryRequestsCount
            ]);
        });
    }