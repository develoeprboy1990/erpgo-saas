<?php

namespace App\Http\Middleware;

use Closure;

use App\Models\Tenant;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Config;

use Illuminate\Support\Facades\abort;

class CheckTenant

{

    public function handle($request, Closure $next)

    {

        $host = $request->getHost();

        $subdomain = explode('.', $host)[0];



        $tenant = Tenant::where('subdomain', $subdomain)->first();



        if (!$tenant) {

            abort(404, 'Tenant not found');

        }
$request->merge(['tenant' => $tenant]);



// Set the database connection for the tenant

\Config::set('database.connections.tenant', [

    'driver' => 'mysql',

    'host' => $tenant->db_host,

    'database' => $tenant->db_name,

    'username' => $tenant->db_user,

    'password' => $tenant->db_password,

    'charset' => 'utf8',

    'collation' => 'utf8_general_ci',

    'prefix' => '',

]);

\Config::set('database.default', 'tenant');

return $next($request);


    }

}