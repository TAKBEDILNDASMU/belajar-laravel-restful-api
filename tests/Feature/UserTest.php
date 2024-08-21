<?php

namespace Tests\Feature;

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
}
