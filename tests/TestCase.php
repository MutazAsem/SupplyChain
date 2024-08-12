<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    protected $authUser;

    protected  function setUp(): void
    {
        parent::setUp();

        // كتابة الصلاحيات (العمليات)
        $permissions = [
            'view_address',
            'view_any_address',
            'create_address',
            'update_address',
            'restore_address',
            'restore_any_address',
            'replicate_address',
            'reorder_address',
            'delete_address',
            'delete_any_address',
            'force_delete_address',
            'force_delete_any_address',
            'view_category',
            'view_any_category',
            'create_category',
            'update_category',
            'restore_category',
            'restore_any_category',
            'replicate_category',
            'reorder_category',
            'delete_category',
            'delete_any_category',
            'force_delete_category',
            'force_delete_any_category',
            'view_delivery::detail',
            'view_any_delivery::detail',
            'create_delivery::detail',
            'update_delivery::detail',
            'restore_delivery::detail',
            'restore_any_delivery::detail',
            'replicate_delivery::detail',
            'reorder_delivery::detail',
            'delete_delivery::detail',
            'delete_any_delivery::detail',
            'force_delete_delivery::detail',
            'force_delete_any_delivery::detail',
            'view_farm',
            'view_any_farm',
            'create_farm',
            'update_farm',
            'restore_farm',
            'restore_any_farm',
            'replicate_farm',
            'reorder_farm',
            'delete_farm',
            'delete_any_farm',
            'force_delete_farm',
            'force_delete_any_farm',
            'view_order',
            'view_any_order',
            'create_order',
            'update_order',
            'restore_order',
            'restore_any_order',
            'replicate_order',
            'reorder_order',
            'delete_order',
            'delete_any_order',
            'force_delete_order',
            'force_delete_any_order',
            'view_product',
            'view_any_product',
            'create_product',
            'update_product',
            'restore_product',
            'restore_any_product',
            'replicate_product',
            'reorder_product',
            'delete_product',
            'delete_any_product',
            'force_delete_product',
            'force_delete_any_product',
        ];
        /*
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        } */

        // (role)كتابة الصلاحيات
        /*   $roles = [
            'super_admin',
            'farmer',
            'supplier',
            'delivery',
            'content_manager',
            'inspector'
        ];

        foreach ($roles as $role) {
            Role::create(['name' => $role]);
        } */

        // إنشاء دور وأذونات
        $role = Role::first(); // استخدام أول دور تم إنشاؤه
        $permission = Permission::pluck('id', 'id')->all();
        $role->syncPermissions($permission);



        // Create a user for authentication
        $this->authUser = User::factory()->create();

        $this->authUser->assignRole($role);

        // Log in the user before each test
        $this->actingAs($this->authUser);
    }
}
