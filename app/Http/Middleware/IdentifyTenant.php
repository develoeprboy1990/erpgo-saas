<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;

class IdentifyTenant
{
    public function handle($request, Closure $next)
    {
        $host = $request->getHost();
        $subdomain = explode('.', $host)[0];

        $tenant = Tenant::where('subdomain', $subdomain)->first();

        if (!$tenant) {
            abort(404, 'Tenant not found.');
        }

        config([
            'database.connections.tenant' => [
                'driver'    => 'mysql',
                'host'      => $tenant->db_host,
                'database'  => $tenant->db_name,
                'username'  => $tenant->db_user,
                'password'  => $tenant->db_password,
                'charset'   => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
            ],
        ]);

        DB::setDefaultConnection('tenant');
        app()->instance('tenant', $tenant);

        return $next($request);
    }
}
