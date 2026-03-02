<?php

namespace App\Support;

use App\Models\ManagedUser;
use App\Models\Permission;
use App\Models\RolePermission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class RolePermissionAccess
{
    public function allows(string $moduleName, string $action): bool
    {
        $roleId = $this->resolveRoleId();

        if (! $roleId) {
            return true;
        }

        $variants = $this->moduleNameVariants($moduleName);
        $permission = Permission::query()
            ->whereIn(\DB::raw('LOWER(module_name)'), $variants)
            ->first();

        if (! $permission) {
            return false;
        }

        $mapping = RolePermission::where('role_id', $roleId)
            ->where('permission_id', $permission->id)
            ->first();

        if (! $mapping) {
            return false;
        }

        return match ($action) {
            'view' => (bool) $mapping->can_view,
            'add' => (bool) $mapping->can_add,
            'edit' => (bool) $mapping->can_edit,
            'delete' => (bool) $mapping->can_delete,
            default => false,
        };
    }

    private function resolveRoleId(): ?int
    {
        $authUser = Auth::user();
        if ($authUser && isset($authUser->role_id)) {
            return (int) $authUser->role_id;
        }

        $managedUserId = Session::get('managed_user_id');
        if ($managedUserId) {
            $managedUser = ManagedUser::find($managedUserId);
            if ($managedUser) {
                return (int) $managedUser->role_id;
            }
        }

        $sessionRoleId = Session::get('role_id');
        if ($sessionRoleId) {
            return (int) $sessionRoleId;
        }

        return null;
    }

    private function moduleNameVariants(string $moduleName): array
    {
        $base = strtolower(trim($moduleName));
        if ($base === '') {
            return [''];
        }

        $variants = [
            $base,
            str_replace('_', '-', $base),
            str_replace(' ', '-', $base),
            str_replace('-', ' ', $base),
            str_replace('_', ' ', $base),
            str_replace('-', '_', $base),
            str_replace(' ', '_', $base),
        ];

        return array_values(array_unique($variants));
    }
}
