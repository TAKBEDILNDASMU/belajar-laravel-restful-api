<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
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

    public function testGetSuccess()
    {
        $this->seed([UserSeeder::class]);

        $this->get('/api/users/current', [
            'Authorization' => "test"
        ])->assertStatus(200)
            ->assertJson([
                "data" => [
                    'name' => 'test',
                    'email' => 'test@test.com',
                    'age' => 20
                ]
            ]);
    }

    public function testGetUnauthorized()
    {
        $this->seed([UserSeeder::class]);

        $this->get('/api/users/current')->assertStatus(401)
            ->assertJson([
                "errors" => [
                    'message' => 'Unauthorized',
                ]
            ]);
    }

    public function testGetInvalidToken()
    {
        $this->seed([UserSeeder::class]);

        $this->get('/api/users/current', [
            'Authorization' => "Salah"
        ])->assertStatus(401)
            ->assertJson([
                "errors" => [
                    'message' => 'Unauthorized',
                ]
            ]);
    }

    public function testUpdatePasswordSuccess()
    {
        $this->seed([UserSeeder::class]);

        $this->patch('/api/users/current', [
            'password' => 'testNew',
        ], [
            'Authorization' => "test"
        ])->assertStatus(200)
            ->assertJson([
                "data" => [
                    'name' => 'test',
                    'email' => 'test@test.com',
                    'age' => 20
                ]
            ]);

        $user = User::where('email', 'test@test.com')->first();
        self::assertTrue(Hash::check("testNew", $user->password));
    }

    public function testUpdateNameSuccess()
    {
        $this->seed([UserSeeder::class]);

        $this->patch('/api/users/current', [
            'name' => 'testNew',
        ], [
            'Authorization' => "test"
        ])->assertStatus(200)
            ->assertJson([
                "data" => [
                    'name' => 'testNew',
                    'email' => 'test@test.com',
                    'age' => 20
                ]
            ]);
    }

    public function testUpdateAgeSuccess()
    {
        $this->seed([UserSeeder::class]);

        $this->patch('/api/users/current', [
            'age' => '25',
        ], [
            'Authorization' => "test"
        ])->assertStatus(200)
            ->assertJson([
                "data" => [
                    'name' => 'test',
                    'email' => 'test@test.com',
                    'age' => 25
                ]
            ]);
    }

    public function testUpdateFailed()
    {
        $this->seed([UserSeeder::class]);

        $this->patch('/api/users/current', [
            'name' => 'EkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEkoEko',
        ], [
            'Authorization' => "test"
        ])->assertStatus(400)
            ->assertJson([
                "errors" => [
                    'name' => [
                        "The name field must not be greater than 100 characters."
                    ]
                ]
            ]);
    }

    public function testLogoutSuccess()
    {
        $this->seed([UserSeeder::class]);

        $this->delete('/api/users/logout', headers: [
            'Authorization' => "test"
        ])->assertStatus(200)
            ->assertJson([
                "data" => true
            ]);
    }

    public function testLogoutSalah()
    {
        $this->seed([UserSeeder::class]);

        $this->delete('/api/users/logout', headers: [
            'Authorization' => "salah"
        ])->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "message" => "Unauthorized"
                ]
            ]);
    }
}
