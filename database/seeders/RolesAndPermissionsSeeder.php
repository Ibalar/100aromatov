<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $guard = config('moonshine.auth.guard');
        $permissionModel = config('permission.models.permission');

        $abilities = [
            'viewAny',
            'view',
            'create',
            'update',
            'delete',
            'massDelete',
            'restore',
            'forceDelete',
        ];

        $resources = [
            'UserResource',
            'RoleResource',
            'PermissionResource',
            'CategoryResource',
            'BrandResource',
            'ProductResource',
            'ProductVariantResource',
            'ProductImageResource',
            'AttributeResource',
            'AttributeValueResource',
            'FilterPageResource',
            'WishlistResource',
            'SettingResource',
            'OrderResource',
            'PageResource',
            'ReviewResource',
            'SliderResource',
        ];

        $allPermissionNames = [];
        foreach ($resources as $resource) {
            foreach ($abilities as $ability) {
                $allPermissionNames[] = "{$resource}.{$ability}";
            }
        }

        foreach ($allPermissionNames as $permissionName) {
            $permissionModel::query()->firstOrCreate([
                'name' => $permissionName,
                'guard_name' => $guard,
            ]);
        }

        $adminRole = Role::query()->updateOrCreate(
            ['id' => User::SUPER_ADMIN_ROLE_ID],
            ['name' => 'admin', 'guard_name' => $guard]
        );

        $managerRole = Role::query()->updateOrCreate(
            ['name' => 'site_manager', 'guard_name' => $guard],
            ['guard_name' => $guard]
        );

        $adminRole->syncPermissions($allPermissionNames);

        $managerPermissions = [];
        $managerResources = [
            'CategoryResource',
            'BrandResource',
            'ProductResource',
            'ProductVariantResource',
            'ProductImageResource',
            'AttributeResource',
            'AttributeValueResource',
            'FilterPageResource',
            'WishlistResource',
            'OrderResource',
            'PageResource',
            'ReviewResource',
            'SliderResource',
        ];

        foreach ($managerResources as $resource) {
            foreach (['viewAny', 'view', 'create', 'update', 'delete', 'massDelete'] as $ability) {
                $managerPermissions[] = "{$resource}.{$ability}";
            }
        }

        foreach (['viewAny', 'view', 'update'] as $ability) {
            $managerPermissions[] = "SettingResource.{$ability}";
        }

        $managerRole->syncPermissions($managerPermissions);

        $adminEmail = (string) env('MOONSHINE_ADMIN_EMAIL', 'admin@example.com');
        $adminName = (string) env('MOONSHINE_ADMIN_NAME', 'Administrator');
        $adminPassword = (string) env('MOONSHINE_ADMIN_PASSWORD', 'admin12345');

        $adminUser = User::query()->firstOrCreate(
            ['email' => $adminEmail],
            [
                'name' => $adminName,
                'password' => $adminPassword,
            ]
        );

        $adminUser->assignRole($adminRole);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
