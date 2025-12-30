<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

/**
 * DEMO SKELETON: User Model Unit Tests
 * 
 * Tests for the User model relationships, attributes, and basic functionality.
 * Since business logic has been removed for demo purposes, tests focus on
 * model structure, relationships, and accessor methods.
 */
class UserTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test that user can be created with fillable attributes
     */
    public function test_user_can_be_created()
    {
        $user = User::create([
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => bcrypt('password'),
            'mobile' => $this->faker->phoneNumber,
            'status' => 'active',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => $user->email,
            'name' => $user->name,
        ]);
    }

    /**
     * Test that user email must be unique
     */
    public function test_user_email_must_be_unique()
    {
        $email = $this->faker->unique()->safeEmail;
        
        User::create([
            'name' => $this->faker->name,
            'email' => $email,
            'password' => bcrypt('password'),
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);
        
        User::create([
            'name' => $this->faker->name,
            'email' => $email,
            'password' => bcrypt('password'),
        ]);
    }

    /**
     * Test that password is hidden from array/JSON
     */
    public function test_password_is_hidden()
    {
        $user = User::create([
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => bcrypt('password'),
        ]);

        $userArray = $user->toArray();
        
        $this->assertArrayNotHasKey('password', $userArray);
        $this->assertArrayNotHasKey('remember_token', $userArray);
    }

    /**
     * Test that sites, idns, and regions are cast to arrays
     */
    public function test_array_casts_work_correctly()
    {
        $user = User::create([
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => bcrypt('password'),
            'sites' => [1, 2, 3],
            'idns' => [1, 2],
            'regions' => ['region1', 'region2'],
        ]);

        $this->assertIsArray($user->sites);
        $this->assertIsArray($user->idns);
        $this->assertIsArray($user->regions);
        $this->assertEquals([1, 2, 3], $user->sites);
        $this->assertEquals([1, 2], $user->idns);
        $this->assertEquals(['region1', 'region2'], $user->regions);
    }

    /**
     * Test that array casts handle null values
     */
    public function test_array_casts_handle_null_values()
    {
        $user = User::create([
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => bcrypt('password'),
            'sites' => null,
            'idns' => null,
            'regions' => null,
        ]);

        $this->assertNull($user->sites);
        $this->assertNull($user->idns);
        $this->assertNull($user->regions);
    }

    /**
     * Test profile image URL accessor when image is null
     */
    public function test_profile_image_url_accessor_with_null_image()
    {
        $user = User::create([
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => bcrypt('password'),
            'image' => null,
        ]);

        $this->assertStringContainsString('avatar.png', $user->profile_image_url);
    }

    /**
     * Test profile image URL accessor when image exists
     */
    public function test_profile_image_url_accessor_with_image()
    {
        $user = User::create([
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => bcrypt('password'),
            'image' => 'test-image.jpg',
        ]);

        $this->assertStringContainsString('test-image.jpg', $user->profile_image_url);
        $this->assertStringContainsString('profile/', $user->profile_image_url);
    }

    /**
     * Test mobile with code accessor
     */
    public function test_mobile_with_code_accessor()
    {
        $user = User::create([
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => bcrypt('password'),
            'mobile' => '1234567890',
            'calling_code' => '+1',
        ]);

        $expected = '1' . '1234567890';
        $this->assertEquals($expected, $user->mobile_with_code);
    }

    /**
     * Test mobile with code accessor when calling code is null
     */
    public function test_mobile_with_code_accessor_with_null_calling_code()
    {
        $user = User::create([
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => bcrypt('password'),
            'mobile' => '1234567890',
            'calling_code' => null,
        ]);

        $this->assertEquals('1234567890', $user->mobile_with_code);
    }

    /**
     * Test formatted mobile accessor
     */
    public function test_formatted_mobile_accessor()
    {
        $user = User::create([
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => bcrypt('password'),
            'mobile' => '1234567890',
            'calling_code' => '+1',
        ]);

        $expected = '+1-1234567890';
        $this->assertEquals($expected, $user->formatted_mobile);
    }

    /**
     * Test formatted mobile accessor when calling code is null
     */
    public function test_formatted_mobile_accessor_with_null_calling_code()
    {
        $user = User::create([
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => bcrypt('password'),
            'mobile' => '1234567890',
            'calling_code' => null,
        ]);

        $this->assertEquals('1234567890', $user->formatted_mobile);
    }

    /**
     * Test user has messages relationship
     */
    public function test_user_has_messages_relationship()
    {
        $user = User::create([
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => bcrypt('password'),
        ]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $user->messages());
    }

    /**
     * Test user has role relationship
     */
    public function test_user_has_role_relationship()
    {
        $user = User::create([
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => bcrypt('password'),
        ]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasOne::class, $user->role());
    }

    /**
     * Test user has staff relationship
     */
    public function test_user_has_staff_relationship()
    {
        $user = User::create([
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => bcrypt('password'),
        ]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasOne::class, $user->is_staff());
    }

    /**
     * Test user has todo items relationship
     */
    public function test_user_has_todo_items_relationship()
    {
        $user = User::create([
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => bcrypt('password'),
        ]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $user->todoItems());
    }

    /**
     * Test user has orders relationship
     */
    public function test_user_has_orders_relationship()
    {
        $user = User::create([
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => bcrypt('password'),
        ]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $user->orders());
    }

    /**
     * Test user uses Notifiable trait
     */
    public function test_user_uses_notifiable_trait()
    {
        $user = new User();
        $traits = class_uses_recursive(get_class($user));
        
        $this->assertContains(\Illuminate\Notifications\Notifiable::class, $traits);
    }

    /**
     * Test user uses EntrustUserTrait
     */
    public function test_user_uses_entrust_trait()
    {
        $user = new User();
        $traits = class_uses_recursive(get_class($user));
        
        $this->assertContains(\Trebol\Entrust\Traits\EntrustUserTrait::class, $traits);
    }

    /**
     * Test user fillable attributes
     */
    public function test_user_fillable_attributes()
    {
        $user = new User();
        
        $expectedFillable = [
            'name', 'email', 'password', 'image', 'mobile', 'status', 'sites', 'idns', 'regions'
        ];
        
        $this->assertEquals($expectedFillable, $user->getFillable());
    }

    /**
     * Test user hidden attributes
     */
    public function test_user_hidden_attributes()
    {
        $user = new User();
        
        $this->assertContains('password', $user->getHidden());
        $this->assertContains('remember_token', $user->getHidden());
    }

    /**
     * Test user appended attributes
     */
    public function test_user_appended_attributes()
    {
        $user = new User();
        
        $this->assertContains('profile_image_url', $user->getAppends());
        $this->assertContains('mobile_with_code', $user->getAppends());
        $this->assertContains('formatted_mobile', $user->getAppends());
    }

    /**
     * Test user can be updated
     */
    public function test_user_can_be_updated()
    {
        $user = User::create([
            'name' => 'Original Name',
            'email' => $this->faker->unique()->safeEmail,
            'password' => bcrypt('password'),
        ]);

        $user->update([
            'name' => 'Updated Name',
            'status' => 'inactive',
        ]);

        $this->assertEquals('Updated Name', $user->fresh()->name);
        $this->assertEquals('inactive', $user->fresh()->status);
    }

    /**
     * Test user can be deleted
     */
    public function test_user_can_be_deleted()
    {
        $user = User::create([
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => bcrypt('password'),
        ]);

        $userId = $user->id;
        $user->delete();

        $this->assertDatabaseMissing('users', [
            'id' => $userId,
        ]);
    }
}
