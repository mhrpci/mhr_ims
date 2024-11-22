<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    // The table associated with the model (if it's not the default 'roles')
    protected $table = 'roles';

    // Mass assignable attributes
    protected $fillable = ['name'];

    /**
     * The users that belong to the role.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * The branch that this role manages (only applicable for Stock Manager role).
     */
    public function stock_manager_branch()
    {
        return $this->hasOne(Branch::class, 'stock_manager_role_id');
    }
}
