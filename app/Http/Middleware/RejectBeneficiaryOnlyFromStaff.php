<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RejectBeneficiaryOnlyFromStaff
{
    /**
     * يمنع حسابات المستفيدين (بدون صلاحيات الموظفين/الإدارة) من الوصول إلى ويسال ولوحة تحكم الموقع.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if ($user && $user->mustUseBeneficiaryPortalExclusively()) {
            return redirect()->route('beneficiary-portal.dashboard')
                ->with('error', 'حسابك مخصص لبوابة المستفيدين فقط. لا يمكن الوصول إلى لوحة النظام أو لوحة الموقع.');
        }

        return $next($request);
    }
}
