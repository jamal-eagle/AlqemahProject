<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Actions_log; // استدعاء موديل ActionLog لتسجيل الأنشطة
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class LogUserActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            // تسجيل النشاط لكل المستخدمين
            Actions_log::create([
                'user_id' => $user->id,  // ID الخاص بالمستخدم
                'action' => $request->method() . ' ' . $request->path(), // نوع العملية والمسار
                'description' => 'قام المستخدم بطلب ' . $request->path(), // وصف للعملية
            ]);
        }

        return $next($request);
    }
}
