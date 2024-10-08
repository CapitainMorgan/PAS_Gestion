<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckDepotPermissions
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user(); // Récupérer l'utilisateur actuel

        switch ($request->method()) {
            case 'GET':
                if (!$user->can('view_depot')) { 
                    return response()->json(['error' => 'Unauthorized'], 403);
                }
                break;

            case 'POST':
                if (!$user->can('create_depot')) {
                    return response()->json(['error' => 'Unauthorized'], 403);
                }
                break;

            case 'PUT':
            case 'PATCH':
                if (!$user->can('update_depot')) {
                    return response()->json(['error' => 'Unauthorized'], 403);
                }
                break;

            case 'DELETE':
                if (!$user->can('delete_depot')) {
                    return response()->json(['error' => 'Unauthorized'], 403);
                }
                break;
        }

        return $next($request); // Continuer si l'utilisateur a les permissions
    }
}
