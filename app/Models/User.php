<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'email',
        'password',
        'branch_id',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    protected $appends = ['avatar_url'];

    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return Storage::url($this->avatar);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->username);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user')
            ->withTimestamps();
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function hasRole($roleName)
    {
        if (is_array($roleName)) {
            return $this->roles()->whereIn('name', $roleName)->exists();
        }
        return $this->roles()->where('name', $roleName)->exists();
    }

    public function isBranchRestricted()
    {
        if ($this->hasRole('Admin') || $this->hasRole('Super Admin')) {
            return false;
        }
        return true;
    }

    public function canManageBranch($branchId)
    {
        if (!$this->isBranchRestricted()) {
            return true;
        }
        return $this->branch_id === $branchId;
    }

    public function assignRole($role)
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->firstOrFail();
        }
        $this->roles()->sync($role, false);
    }

    public function removeRole($role)
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->firstOrFail();
        }
        $this->roles()->detach($role);
    }

    public function syncRoles($roles)
    {
        if (is_string($roles)) {
            $roles = [$roles];
        }

        $roleIds = collect($roles)->map(function ($role) {
            if (is_string($role)) {
                return Role::where('name', $role)->firstOrFail()->id;
            }
            return $role->id;
        });

        $this->roles()->sync($roleIds);
    }

    public function getAllPermissions()
    {
        return $this->roles->map->permissions->flatten()->unique('id');
    }

    public function deleteAvatar()
    {
        if ($this->avatar) {
            Storage::delete($this->avatar);
            $this->update(['avatar' => null]);
        }
    }

    public function canManageProduct(Product $product = null)
    {
        if ($this->hasRole(['Admin', 'Super Admin'])) {
            return true;
        }

        if ($this->hasRole('Branch Manager')) {
            return !$product || $product->branch_id === $this->branch_id;
        }

        if ($this->hasRole('Stock Manager')) {
            return !$product || $product->branch_id === $this->branch_id;
        }

        return false;
    }

    public function canCreateProduct()
    {
        return $this->hasRole(['Admin', 'Super Admin', 'Branch Manager']);
    }

    public function canEditProduct()
    {
        return $this->hasRole(['Admin', 'Super Admin', 'Branch Manager']);
    }

    public function canDeleteProduct()
    {
        return $this->hasRole(['Admin', 'Super Admin', 'Branch Manager']);
    }

    public function canManageTool(Tool $tool = null)
    {
        if ($this->hasRole(['Admin', 'Super Admin'])) {
            return true;
        }

        if ($this->hasRole('Branch Manager')) {
            return !$tool || $tool->branch_id === $this->branch_id;
        }

        if ($this->hasRole('Stock Manager')) {
            return !$tool || $tool->branch_id === $this->branch_id;
        }

        return false;
    }

    public function canCreateTool()
    {
        return $this->hasRole(['Admin', 'Super Admin', 'Branch Manager', 'Stock Manager']);
    }

    public function canEditTool()
    {
        return $this->hasRole(['Admin', 'Super Admin', 'Branch Manager']);
    }

    public function canDeleteTool()
    {
        return $this->hasRole(['Admin', 'Super Admin', 'Branch Manager']);
    }

    public function canManageInventory()
    {
        return $this->hasRole(['Admin', 'Super Admin', 'Branch Manager']);
    }
    public function canEditInventory()
    {
        return $this->hasRole(['Admin', 'Super Admin', 'Branch Manager']);
    }

    public function canDeleteInventory()
    {
        return $this->hasRole(['Admin', 'Super Admin', 'Branch Manager']);
    }

    public function canManageBranches()
    {
        return $this->hasRole(['Admin', 'Super Admin']);
    }

    public function canManageVendors()
    {
        return $this->hasRole(['Admin', 'Super Admin']);
    }

    public function canManageCustomers()
    {
        return $this->hasRole(['Admin', 'Super Admin']);
    }

    public function canManageCategories()
    {
        return $this->hasRole(['Admin', 'Super Admin']);
    }

    public function canManageReports()
    {
        return $this->hasRole(['Admin', 'Super Admin', 'Branch Manager']);
    }

    public function canExportProducts()
    {
        // Implement your permission logic here
        return true; // or based on user roles/permissions
    }

    public function canImportProducts()
    {
        // Implement your permission logic here
        return true; // or based on user roles/permissions
    }

    public function canDeleteUsers()
    {
        return $this->hasRole(['Super Admin']);
    }

    public function canEditProfile()
    {
        return $this->hasRole(['Admin', 'Super Admin']);
    }
}
