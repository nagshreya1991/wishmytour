<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Modules\Admin\app\Models\AdminActivityLog;

class LogAdminActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        // Log admin activity here
        $admin = auth()->user(); // Get the authenticated admin user
        $action = $this->getActionName($request);
        $description = $this->getDescription($request, $action);

        // Log login and logout actions with different messages
        if ($action === 'login') {
            $this->logLoginActivity($admin);
        } elseif ($action === 'logout') {
            $this->logLogoutActivity($admin);
        } elseif ($action && in_array($action, ['store', 'update', 'destroy'])) {
            $itemType = $request->route('item_type'); // Extract item type from route parameters
            $itemId = $request->route('id'); // Extract item ID from route parameters
            AdminActivityLog::create([
                'admin_id' => $admin->id,
                'action' => $action,
                'description' => "{$admin->name} {$description} {$itemType}: {$itemId}",
            ]);
        }

        return $response;
    }

    /**
     * Get human-readable action name based on the route name.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function getActionName(Request $request)
    {
        $routeName = $request->route()->getName();

        // Extract the action type from the route name
        // You may need to adjust this logic based on your route naming conventions
        return substr($routeName, strrpos($routeName, '.') + 1);
    }

    /**
     * Get description based on the action (only for store, update, and destroy actions).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $action
     * @return string|null
     */
    protected function getDescription(Request $request, $action)
    {
        // Define descriptions for specific actions
        switch ($action) {
            case 'store':
                return 'added a new ';
            case 'update':
                return 'edited an ';
            case 'destroy':
                return 'deleted an ';
            default:
                return null; // Return null for actions that don't require logging
        }
    }

    /**
     * Log login activity.
     *
     * @param  \App\Models\Admin  $admin
     * @return void
     */
    protected function logLoginActivity($admin)
    {
        AdminActivityLog::create([
            'admin_id' => $admin->id,
            'action' => 'login',
            'description' => "{$admin->name} logged in.",
        ]);
    }

    /**
     * Log logout activity.
     *
     * @param  \App\Models\Admin  $admin
     * @return void
     */
    protected function logLogoutActivity($admin)
    {
        AdminActivityLog::create([
            'admin_id' => $admin->id,
            'action' => 'logout',
            'description' => "{$admin->name} logged out.",
        ]);
    }
}
