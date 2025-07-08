<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tenant;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class TenantController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string',
            'subdomain'   => 'required|string|unique:tenants,subdomain',
            'db_user'     => 'required|string',
            'db_password' => 'required|string',
        ]);

        $dbName = 'tenant_' . $request->subdomain;

        $tenant = Tenant::create([
            'name'        => $request->name,
            'subdomain'   => $request->subdomain,
            'db_host'     => '127.0.0.1',
            'db_name'     => $dbName,
            'db_user'     => $request->db_user,
            'db_password' => $request->db_password,
        ]);

        DB::statement("CREATE DATABASE `$dbName`");

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

        Artisan::call('migrate', [
            '--database' => 'tenant',
            '--path'     => '/database/migrations/tenant',
            '--force'    => true,
        ]);

        return response()->json(['message' => 'Tenant created successfully!']);
    }
}
