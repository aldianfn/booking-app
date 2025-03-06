<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'password' => Hash::make($password = '12341234')
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => $password
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'token'
        ]);
    }

    public function test_user_cannot_login_with_invalid_credentials()
    {
        $user = User::factory()->create([
            'password' => Hash::make($password = '12341234')
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'wrongpassword'
        ]);

        $response->assertStatus(401);
        $response->assertJson([
            'message' => 'Invalid credentials',
        ]);
    }

    public function test_user_can_access_protected_route_with_valid_token()
    {
        $user = User::factory()->create();
        $token = $user->createToken('testToken')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->getJson(('api/user'));

        $response->assertStatus(200);
    }

    public function test_user_cannot_access_protected_route_with_invalid_token()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer invalid_token'
        ])->getJson(('api/user'));

        $response->assertStatus(401);
    }
}
