<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class HttpBasicAuth
{
    public function handle(Request $request, Closure $next)
    {
        $authorization = $request->header('Authorization');

        if ($authorization && $this->isValidCredentials($authorization)) {
            return $next($request);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    private function isValidCredentials($authorizationHeader)
    {
        // Extraer las credenciales del header de autorización
        $credentials = base64_decode(str_replace('Basic ', '', $authorizationHeader));
        list($username, $password) = explode(':', $credentials);

        // Verificar las credenciales (puedes ajustar esto según tus necesidades)
        return $username === 'admin@admin.com' && $password === 'Clave123+';
    }
}
