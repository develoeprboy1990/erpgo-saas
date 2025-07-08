<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    // This model represents a tenant with subdomain and database configuration.
    // You can add accessors or mutators here if needed.
    protected $fillable = ['id', 'name', 'subdomain', 'db_host', 'db_name', 'db_user', 'db_password', 'created_at', 'updated_at'];
}
