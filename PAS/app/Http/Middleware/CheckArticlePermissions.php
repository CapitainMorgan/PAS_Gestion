<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckArticlePermissions
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user(); // Récupérer l'utilisateur actuel

        switch ($request->method()) {
            case 'GET':
                if (!$user->can('view_article')) { 
                    return response()->json(['error' => 'Unauthorized'], 403);
                }
                break;

            case 'POST':
                if (!$user->can('create_article')) {
                    return response()->json(['error' => 'Unauthorized'], 403);
                }
                break;

            case 'PUT':
            case 'PATCH':
                if (!$user->can('update_article')) {
                    return response()->json(['error' => 'Unauthorized'], 403);
                }
                break;

            case 'DELETE':
                if (!$user->can('delete_article')) {
                    return response()->json(['error' => 'Unauthorized'], 403);
                }
                break;
        }

        return $next($request); // Continuer si l'utilisateur a les permissions
    }
}
