<?php

namespace App\Http\Middleware;

use App\Models\Permission;
use App\Models\Role;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user(); // Получаем текущего пользователя

        if ($user->is_admin){

            return $next($request); // Доступ разрешён

        } elseif($user->is_moderator){

            // Получаем имя текущего маршрута
            $routeName = $request->route()->getName();

            // Преобразуем формат имени маршрута для соответствия с базой
            $normalizedRouteName = str_replace('.', '_', $routeName);

            // Получаем разрешения для всех ролей пользователя
            $permissions = $user->roles->flatMap(function ($role) {
                return $role->permissions->pluck('title');
            })->unique();

//            dd($permissions);

            // Проверяем, входит ли преобразованное имя маршрута в разрешения
            if ($permissions->contains($normalizedRouteName)) {
                return $next($request); // Доступ разрешён
            }
        }

        return response(['message' => 'forbidden'], \Illuminate\Http\Response::HTTP_FORBIDDEN);

    }
}
