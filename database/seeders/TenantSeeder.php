<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use App\Models\Tenant;

class TenantSeeder extends Seeder
{
    public function run()
    {
        $tenants = [
            [
                'name'        => 'Company One',
                'subdomain'   => 'company1',
                'db_host'     => '127.0.0.1',
                'db_name'     => 'tenant_company1',
                'db_user'     => 'root',
                'db_password' => 'secret',
            ],
            [
                'name'        => 'Company Two',
                'subdomain'   => 'company2',
                'db_host'     => '127.0.0.1',
                'db_name'     => 'tenant_company2',
                'db_user'     => 'root',
                'db_password' => 'secret',
            ],
        ];

        foreach ($tenants as $tenantData) {
            $tenant = Tenant::firstOrCreate(
                ['subdomain' => $tenantData['subdomain']],
                $tenantData
            );

            $dbName = $tenant->db_name;
            DB::statement("CREATE DATABASE IF NOT EXISTS `$dbName`");

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
        }
    }
}
