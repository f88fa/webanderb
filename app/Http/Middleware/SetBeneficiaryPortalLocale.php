<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetBeneficiaryPortalLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = (string) config('app.beneficiary_portal_locale', 'ar');
        if (is_string($locale) && $locale !== '') {
            App::setLocale($locale);
        }

        return $next($request);
    }
}
