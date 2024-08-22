<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{

    use RefreshDatabase;
    public function testRegisterSuccess(): void
    {
        $this->post('/api/users', [
            'name' => 'Patrick',
            'email' => 'patrick@bikinibottom.com',
            'password' => 'patrickstar123',
            'age' => 20
        ])->assertStatus(201)
            ->assertJson([
                "data" => [
                    'name' => 'Patrick',
                    'email' => 'patrick@bikinibottom.com',
                    'age' => 20
                ]
            ]);
    }

    public function testRegisterFailed(): void
    {
        $this->post('/api/users', [
            'name' => '',
            'email' => '',
            'password' => '',
        ])->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "name" => ["The name field is required."],
                    "email" => ["The email field is required."],
                    "password" => ["The password field is required."],
                ]
            ]);
    }

    public function testRegisterEmailAlreadyExist(): void
    {
        $this->testRegisterSuccess();

        $this->post('/api/users', [
            'name' => 'Patrick',
            'email' => 'patrick@bikinibottom.com',
            'password' => 'patrickstar123',
            'age' => 20
        ])->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "email" => ["The email has already been taken."],
                ]
            ]);
    }

    public function testLoginSuccess()
    {
        $this->seed([UserSeeder::class]);

        $this->post('/api/users/login', [
            'email' => "test@test.com",
            'password' => 'test',
        ])->assertStatus(200)
            ->assertJson([
                "data" => [
                    'name' => 'test',
                    'email' => 'test@test.com',
                    'age' => 20
                ]
            ]);

        $user = User::where('name', 'test')->first();
        self::assertNotNull($user->token);
    }
    public function testLoginUsernameNotFound()
    {
        $this->post('/api/users/login', [
            'email' => "test@test.com",
            'password' => 'test',
        ])->assertStatus(401)
            ->assertJson([
                "errors" => [
                    'message' => 'Email or password wrong'
                ]
            ]);
    }
    public function testLoginPasswordWrong()
    {
        $this->seed([UserSeeder::class]);

        $this->post('/api/users/login', [
            'email' => "test@test.com",
            'password' => 'wrong',
        ])->assertStatus(401)
            ->assertJson([
                "errors" => [
                    'message' => 'Email or password wrong'
                ]
            ]);
    }
}
