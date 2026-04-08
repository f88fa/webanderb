<?php

namespace App\Http\Controllers;

use App\Models\AdminLetter;
use Illuminate\Http\Request;

class AdminCommunicationsController extends Controller
{
    /**
     * الخطابات الصادرة
     */
    public function outgoing(Request $request)
    {
        $letters = AdminLetter::outgoing()
            ->with('creator')
            ->orderByDesc('letter_date')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('wesal.index', [
            'page' => 'communications',
            'settings' => \App\Models\SiteSetting::getAllAsArray(),
            'formType' => 'communications-outgoing',
            'letters' => $letters,
            'direction' => 'outgoing',
        ]);
    }

    /**
     * نموذج خطاب صادر جديد
     */
    public function createOutgoing()
    {
        return view('wesal.index', [
            'page' => 'communications',
            'settings' => \App\Models\SiteSetting::getAllAsArray(),
            'formType' => 'communications-letter-form',
            'direction' => 'outgoing',
            'letter' => null,
        ]);
    }

    /**
     * حفظ خطاب صادر
     */
    public function storeOutgoing(Request $request)
    {
        $valid = $request->validate([
            'letter_no' => 'nullable|string|max:100',
            'subject' => 'required|string|max:255',
            'letter_date' => 'nullable|date',
            'to_party' => 'nullable|string|max:255',
            'from_party' => 'nullable|string|max:255',
            'body' => 'nullable|string',
            'reference_no' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);
        $valid['direction'] = 'outgoing';
        $valid['created_by'] = auth()->id();
        AdminLetter::create($valid);
        return redirect()->route('wesal.communications.outgoing')->with('success', 'تم تسجيل الخطاب الصادر بنجاح.');
    }

    /**
     * الخطابات الواردة
     */
    public function incoming(Request $request)
    {
        $letters = AdminLetter::incoming()
            ->with('creator')
            ->orderByDesc('letter_date')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('wesal.index', [
            'page' => 'communications',
            'settings' => \App\Models\SiteSetting::getAllAsArray(),
            'formType' => 'communications-incoming',
            'letters' => $letters,
            'direction' => 'incoming',
        ]);
    }

    /**
     * نموذج تسجيل خطاب وارد جديد
     */
    public function createIncoming()
    {
        return view('wesal.index', [
            'page' => 'communications',
            'settings' => \App\Models\SiteSetting::getAllAsArray(),
            'formType' => 'communications-letter-form',
            'direction' => 'incoming',
            'letter' => null,
        ]);
    }

    /**
     * حفظ خطاب وارد
     */
    public function storeIncoming(Request $request)
    {
        $valid = $request->validate([
            'letter_no' => 'nullable|string|max:100',
            'subject' => 'required|string|max:255',
            'letter_date' => 'nullable|date',
            'from_party' => 'nullable|string|max:255',
            'to_party' => 'nullable|string|max:255',
            'body' => 'nullable|string',
            'reference_no' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);
        $valid['direction'] = 'incoming';
        $valid['created_by'] = auth()->id();
        AdminLetter::create($valid);
        return redirect()->route('wesal.communications.incoming')->with('success', 'تم تسجيل الخطاب الوارد بنجاح.');
    }

    /**
     * عرض تفاصيل خطاب
     */
    public function show(AdminLetter $letter)
    {
        $letter->load('creator');
        return view('wesal.index', [
            'page' => 'communications',
            'settings' => \App\Models\SiteSetting::getAllAsArray(),
            'formType' => 'communications-letter-show',
            'letter' => $letter,
        ]);
    }

    /**
     * طباعة خطاب بتصميم الورقة من إعدادات النظام (رأس، وسط، تذييل)
     */
    public function print(AdminLetter $letter)
    {
        $settings = \App\Models\SiteSetting::getAllAsArray();
        return view('wesal.pages.communications.letter-paper', [
            'letter' => $letter,
            'settings' => $settings,
        ]);
    }
}
