<?php

namespace App\Modules\Role\Repositories;

use App\Modules\Role\Models\Role;

class RoleRepository{

    public function getRoleByName($name)
    {
        return Role::where('name', $name)->first();
    }
}