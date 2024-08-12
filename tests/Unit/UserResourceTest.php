<?php

use App\Filament\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use function Pest\Laravel\{actingAs, assertDatabaseHas, assertDatabaseMissing, get, post, put, delete};
use Filament\Resources\Pages\Page;

/* beforeEach(function () {
    // Set up the required permissions and roles
    // This assumes that the setup permissions and roles code is part of a trait or helper function
    // Example: $this->setupPermissionsAndRoles();

    // Create a user for authentication
    $this->authUser = User::factory()->create([
        'email' => 'abod@admin.com',
        'password' => Hash::make('password'),
    ]);

    // Assign a role to the user
    $role = \Spatie\Permission\Models\Role::first(); // Use the first role created
    $this->authUser->assignRole($role);

    // Log in the user
    actingAs($this->authUser);
}); */

// Test listing users

/* it('can render page', function () {
    $this->get(UserResource::getUrl('index'))->assertSuccessful();
}); */
/* it('can list users', function () {
    $response = get(route('filament.admin.resources.users.index'));

    $response->assertStatus(200);
    $response->assertSee($this->authUser->name);
});

// Test creating a user
it('can create a user', function () {
    $userData = [
        'name' => 'John Doe',
        'last_name' => 'Doe',
        'email' => 'john.doe@example.com',
        'phone' => '701234567',
        'gender' => 'male',
        'password' => 'securepassword',
        'password_confirmation' => 'securepassword',
    ];

    $response = post(route('filament.admin.resources.users.create'), $userData);

    $response->assertStatus(302); // Redirect after successful creation
    assertDatabaseHas('users', [
        'email' => 'john.doe@example.com',
    ]);
});

// Test editing a user
it('can edit a user', function () {
    $user = User::factory()->create();
    $updatedData = [
        'name' => 'Updated Name',
        'email' => 'updated.email@example.com',
        'new_password' => 'newsecurepassword',
        'new_password_confirmation' => 'newsecurepassword',
    ];

    $response = put(route('filament.admin.resources.users.edit', $user->id), $updatedData);

    $response->assertStatus(302); // Redirect after successful update
    assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'Updated Name',
        'email' => 'updated.email@example.com',
    ]);
    // Check that the password has been updated
    $this->assertTrue(Hash::check('newsecurepassword', User::find($user->id)->password));
}); */

// Test deleting a user
/* it('can delete a user', function () {
    $user = User::factory()->create();

    $response = delete(route('filament.admin.resources.users.delete', $user->id));

    $response->assertStatus(302); // Redirect after successful deletion
    assertDatabaseMissing('users', [
        'id' => $user->id,
    ]);
}); */