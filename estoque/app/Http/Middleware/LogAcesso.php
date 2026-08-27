<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Acesso;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LogAcesso
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Registra apenas se o usuário estiver autenticado e não for requisição AJAX
        if (Auth::check() && !$request->ajax()) {
            $ip = $request->ip();
            $acesso = Acesso::where('ip_address', $ip)->first();

            if ($acesso) {
                $acesso->increment('total_acessos');
                $acesso->update([
                    'user_id'       => Auth::id(),
                    'user_agent'    => $request->userAgent(),
                    'ultimo_acesso' => now(),
                ]);
            } else {
                Acesso::create([
                    'ip_address'    => $ip,
                    'user_id'       => Auth::id(),
                    'user_agent'    => $request->userAgent(),
                    'total_acessos' => 1,
                    'ultimo_acesso' => now(),
                ]);
            }
        }

        return $response;
    }
}
