<?php

namespace App\Http\Controllers\Wesal;

use App\Http\Controllers\Controller;
use App\Services\GoogleCalendarService;
use App\Models\UserGoogleCalendarToken;
use Illuminate\Http\Request;

class CalendarConnectController extends Controller
{
    public function redirect(GoogleCalendarService $googleCalendar)
    {
        $client = $googleCalendar->getOAuthClient();
        $authUrl = $client->createAuthUrl();
        return redirect()->away($authUrl);
    }

    public function callback(Request $request, GoogleCalendarService $googleCalendar)
    {
        if (!$request->has('code')) {
            return redirect()->route('wesal.page', 'home')
                ->with('error', 'لم يتم الربط. يرجى المحاولة مرة أخرى.');
        }
        $success = $googleCalendar->exchangeCodeAndStore(auth()->user(), $request->input('code'));
        if ($success) {
            return redirect()->route('wesal.calendar.connect')
                ->with('success', 'تم ربط تقويم Google بنجاح. ستصل المهام والاجتماعات تلقائياً إلى تقويمك.');
        }
        return redirect()->route('wesal.calendar.connect')
            ->with('error', 'فشل الربط. يرجى المحاولة مرة أخرى.');
    }

    public function disconnect()
    {
        UserGoogleCalendarToken::where('user_id', auth()->id())->delete();
        CalendarSyncEvent::where('user_id', auth()->id())->delete();
        return redirect()->route('wesal.calendar.connect')
            ->with('success', 'تم فك ربط التقويم.');
    }

    /** صفحة ربط التقويم (تعرض حالة الربط وزر الربط/فك الربط) */
    public function show()
    {
        $token = UserGoogleCalendarToken::findForUser(auth()->id());
        return view('wesal.pages.calendar-connect', [
            'connected' => $token !== null,
        ]);
    }
}
