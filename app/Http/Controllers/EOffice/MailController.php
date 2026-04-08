<?php

namespace App\Http\Controllers\EOffice;

use App\Http\Controllers\Controller;
use App\Models\InternalMessage;
use App\Models\InternalMessageAttachment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MailController extends Controller
{
    public function inbox(Request $request)
    {
        $messages = InternalMessage::with(['fromUser', 'attachments', 'recipients'])
            ->whereHas('recipients', function ($q) {
                $q->where('user_id', auth()->id());
            })
            ->orderByDesc('internal_messages.created_at')
            ->paginate(15);

        return view('wesal.index', [
            'page' => 'e-office',
            'settings' => \App\Models\SiteSetting::getAllAsArray(),
            'formType' => 'mail-inbox',
            'messages' => $messages,
        ]);
    }

    public function sent(Request $request)
    {
        $messages = InternalMessage::with(['recipients.user', 'attachments'])
            ->where('from_user_id', auth()->id())
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('wesal.index', [
            'page' => 'e-office',
            'settings' => \App\Models\SiteSetting::getAllAsArray(),
            'formType' => 'mail-sent',
            'messages' => $messages,
        ]);
    }

    public function compose(Request $request)
    {
        $users = User::orderBy('name')->get(['id', 'name', 'email']);
        $replyToMessage = null;
        if ($request->filled('reply_to')) {
            $replyToMessage = InternalMessage::with(['fromUser', 'recipients'])
                ->find($request->reply_to);
            if ($replyToMessage) {
                $isRecipient = $replyToMessage->recipients()->where('user_id', auth()->id())->exists();
                $isSender = $replyToMessage->from_user_id === auth()->id();
                if (!$isSender && !$isRecipient) {
                    $replyToMessage = null;
                }
            }
        }

        return view('wesal.index', [
            'page' => 'e-office',
            'settings' => \App\Models\SiteSetting::getAllAsArray(),
            'formType' => 'mail-compose',
            'users' => $users,
            'replyToMessage' => $replyToMessage,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'to' => 'required|array',
            'to.*' => 'exists:users,id',
            'cc' => 'nullable|array',
            'cc.*' => 'exists:users,id',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'reply_to' => 'nullable|exists:internal_messages,id',
            'attachments.*' => 'nullable|file|max:10240',
        ]);

        $to = array_filter(array_unique((array) $request->to));
        if (empty($to)) {
            return back()->withErrors(['to' => 'يجب اختيار مستلم واحد على الأقل'])->withInput();
        }

        $parentId = null;
        if ($request->filled('reply_to')) {
            $parent = InternalMessage::find($request->reply_to);
            if ($parent) {
                $isRecipient = $parent->recipients()->where('user_id', auth()->id())->exists();
                $isSender = $parent->from_user_id === auth()->id();
                if ($isSender || $isRecipient) {
                    $parentId = $parent->id;
                }
            }
        }

        DB::beginTransaction();
        try {
            $body = is_string($request->body) ? strip_tags($request->body, '<p><br><strong><b><em><i><u><s><a><ul><ol><li><span><div>') : '';
            $message = InternalMessage::create([
                'from_user_id' => auth()->id(),
                'parent_id' => $parentId,
                'subject' => $request->subject,
                'body' => $body,
            ]);

            foreach ($to as $userId) {
                $message->recipients()->create(['user_id' => $userId, 'type' => 'to']);
            }
            $cc = array_filter(array_unique((array) ($request->cc ?? [])));
            foreach ($cc as $userId) {
                if (!in_array($userId, $to)) {
                    $message->recipients()->create(['user_id' => $userId, 'type' => 'cc']);
                }
            }

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('internal_mail/' . $message->id, 'public');
                    $message->attachments()->create([
                        'path' => $path,
                        'original_name' => $file->getClientOriginalName(),
                    ]);
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'حدث خطأ أثناء الإرسال'])->withInput();
        }

        return redirect()->route('wesal.e-office.mail.sent')->with('success', 'تم إرسال الرسالة بنجاح');
    }

    public function show(InternalMessage $internal_message)
    {
        $userId = auth()->id();
        $isRecipient = $internal_message->recipients()->where('user_id', $userId)->exists();
        $isSender = $internal_message->from_user_id === $userId;
        if (!$isSender && !$isRecipient) {
            abort(404);
        }
        $recipient = $internal_message->recipients()->where('user_id', $userId)->first();
        if ($recipient && !$recipient->read_at) {
            $recipient->update(['read_at' => now()]);
        }
        $isSent = $internal_message->from_user_id === auth()->id();

        $root = $internal_message;
        while ($root->parent_id) {
            $parent = InternalMessage::find($root->parent_id);
            if (!$parent) {
                break;
            }
            $root = $parent;
        }
        $replies = $root->replies()->with('fromUser')->orderBy('created_at')->get();

        return view('wesal.index', [
            'page' => 'e-office',
            'settings' => \App\Models\SiteSetting::getAllAsArray(),
            'formType' => 'mail-show',
            'message' => $internal_message->load(['fromUser', 'recipients.user', 'attachments']),
            'isSent' => $isSent,
            'replies' => $replies,
        ]);
    }
}
