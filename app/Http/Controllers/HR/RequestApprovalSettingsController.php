<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\HR\RequestApprovalSequence;
use Illuminate\Http\Request;

class RequestApprovalSettingsController extends Controller
{
    public function store(Request $request)
    {
        $raw = $request->input('sequences', []);
        if (!is_array($raw)) {
            $raw = [];
        }

        $sequences = [];
        foreach ($raw as $row) {
            if (!is_array($row) || empty($row['request_type'])) {
                continue;
            }
            $step = (int) ($row['step'] ?? 0);
            $approverType = $row['approver_type'] ?? '';

            if ($step === 1) {
                $sequences[] = [
                    'request_type' => $row['request_type'],
                    'step' => 1,
                    'approver_type' => RequestApprovalSequence::APPROVER_DIRECT_MANAGER,
                    'role_name' => null,
                    'employee_id' => null,
                ];
                continue;
            }

            if ($approverType === RequestApprovalSequence::APPROVER_ROLE && !empty(trim((string) ($row['role_name'] ?? '')))) {
                $sequences[] = [
                    'request_type' => $row['request_type'],
                    'step' => $step,
                    'approver_type' => RequestApprovalSequence::APPROVER_ROLE,
                    'role_name' => trim($row['role_name']),
                    'employee_id' => null,
                ];
            } elseif ($approverType === RequestApprovalSequence::APPROVER_EMPLOYEE && !empty($row['employee_id'])) {
                $sequences[] = [
                    'request_type' => $row['request_type'],
                    'step' => $step,
                    'approver_type' => RequestApprovalSequence::APPROVER_EMPLOYEE,
                    'role_name' => null,
                    'employee_id' => (int) $row['employee_id'],
                ];
            }
        }

        $request->merge(['sequences' => $sequences]);
        $validated = $request->validate([
            'sequences' => 'array',
            'sequences.*.request_type' => 'required|in:leave,permission,financial,beneficiary_support,general',
            'sequences.*.step' => 'required|integer|min:1|max:6',
            'sequences.*.approver_type' => 'required|in:direct_manager,role,employee',
            'sequences.*.role_name' => 'nullable|string|max:100',
            'sequences.*.employee_id' => 'nullable|exists:hr_employees,id',
        ]);

        $byType = collect($validated['sequences'] ?? [])->groupBy('request_type');

        foreach (array_keys(RequestApprovalSequence::TYPES) as $type) {
            RequestApprovalSequence::where('request_type', $type)->delete();
            $rows = $byType->get($type, collect());
            foreach ($rows->sortBy('step')->values() as $row) {
                RequestApprovalSequence::create([
                    'request_type' => $row['request_type'],
                    'step' => $row['step'],
                    'approver_type' => $row['approver_type'],
                    'role_name' => $row['approver_type'] === RequestApprovalSequence::APPROVER_ROLE ? ($row['role_name'] ?? null) : null,
                    'employee_id' => $row['approver_type'] === RequestApprovalSequence::APPROVER_EMPLOYEE ? ($row['employee_id'] ?? null) : null,
                ]);
            }
        }

        return redirect()->route('wesal.hr.show', ['section' => 'request-settings'])
            ->with('success', 'تم حفظ تسلسل الموافقات. الدور الأخير المعيّن هو من يحدد القبول النهائي أو الرفض.');
    }
}
