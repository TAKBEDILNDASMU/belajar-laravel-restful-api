<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\User;
use Database\Seeders\ContactSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ContactTest extends TestCase
{

    use RefreshDatabase;
    public function testCreateContactSuccess()
    {
        $this->seed([UserSeeder::class]);

        $this->post('/api/contacts', [
            "name" => "test",
            "phone" => "test",
            "email" => "test@test.com"
        ], headers: [
            'Authorization' => 'test'
        ])->assertStatus(201)
            ->assertJson([
                "data" => [
                    "name" => "test",
                    "phone" => "test",
                    "email" => "test@test.com"
                ]
            ]);
    }

    public function testCreateContactWithoutEmail()
    {
        $this->seed([UserSeeder::class]);

        $this->post('/api/contacts', [
            "name" => "test",
            "phone" => "test",
        ], headers: [
            'Authorization' => 'test'
        ])->assertStatus(201)
            ->assertJson([
                "data" => [
                    "name" => "test",
                    "phone" => "test",
                ]
            ]);
    }

    public function testCreateContactFailed()
    {
        $this->seed([UserSeeder::class]);

        $this->post('/api/contacts', [
            "name" => "",
            "phone" => "",
        ], headers: [
            'Authorization' => 'test'
        ])->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "name" => [
                        "The name field is required."
                    ],
                    "phone" => [
                        "The phone field is required."
                    ],
                ]
            ]);
    }

    public function testCreateContactUnauthorized()
    {
        $this->seed([UserSeeder::class]);

        $this->post('/api/contacts', [
            "name" => "",
            "phone" => "",
        ])->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "message" => "Unauthorized"
                ]
            ]);
    }

    public function testGetContactSuccess()
    {
        $this->seed([UserSeeder::class]);
        $this->seed([ContactSeeder::class]);

        $user = User::where('name', 'test')->first();
        $contact = Contact::where('user_id', $user->id)->first();

        $this->get('/api/contacts/' . $contact->id, headers: [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                "data" => [
                    "name" => "test",
                    "phone" => "test",
                    "email" => "test@test.com",
                ]
            ]);
    }

    public function testGetContactNotFound()
    {
        $this->seed([UserSeeder::class]);
        $this->seed([ContactSeeder::class]);

        $user = User::where('name', 'test')->first();
        $contact = Contact::where('user_id', $user->id)->first();

        $this->get('/api/contacts/' . $contact->id + 1, headers: [
            'Authorization' => 'test'
        ])->assertStatus(404)
            ->assertJson([
                "errors" => [
                    "message" => "Contact not found"
                ]
            ]);
    }

    public function testUpdateContactName()
    {
        $this->seed([UserSeeder::class]);
        $this->seed([ContactSeeder::class]);

        $user = User::where('name', 'test')->first();
        $contact = Contact::where('user_id', $user->id)->first();

        $this->put('/api/contacts/' . $contact->id, data: [
            "name" => "testNew"
        ], headers: [
            'Authorization' => "test"
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'name' => "testNew",
                    'phone' => "test",
                    "email" => "test@test.com"
                ]
            ]);
    }

    public function testUpdateContactPhone()
    {
        $this->seed([UserSeeder::class]);
        $this->seed([ContactSeeder::class]);

        $user = User::where('name', 'test')->first();
        $contact = Contact::where('user_id', $user->id)->first();

        $this->put('/api/contacts/' . $contact->id, data: [
            "phone" => "testNew"
        ], headers: [
            'Authorization' => "test"
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'name' => "test",
                    'phone' => "testNew",
                    "email" => "test@test.com"
                ]
            ]);
    }

    public function testUpdateContactEmail()
    {
        $this->seed([UserSeeder::class]);
        $this->seed([ContactSeeder::class]);

        $user = User::where('name', 'test')->first();
        $contact = Contact::where('user_id', $user->id)->first();

        $this->put('/api/contacts/' . $contact->id, data: [
            "email" => "testNew@test.com"
        ], headers: [
            'Authorization' => "test"
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'name' => "test",
                    'phone' => "test",
                    "email" => "testNew@test.com"
                ]
            ]);
    }

    public function testUpdateFailed()
    {
        $this->seed([UserSeeder::class]);
        $this->seed([ContactSeeder::class]);

        $user = User::where('name', 'test')->first();
        $contact = Contact::where('user_id', $user->id)->first();

        $this->put('/api/contacts/' . $contact->id, data: [
            "name" => "testNewtestNewtestNewtestNewtestNewtestNewtestNewtestNewtestNewtestNewtestNewtestNewtestNewtestNewtestNewtestNewtestNewtestNewtestNewtestNewtestNewtestNewtestNewtestNewtestNewtestNewtestNewtestNewtestNewtestNewtestNewtestNewtestNewtestNewtestNewtestNewtestNewtestNewtestNewtestNewtestNewtestNewtestNewtestNewtestNewtestNewtestNewtestNew"
        ], headers: [
            'Authorization' => "test"
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'name' => [
                        "The name field must not be greater than 100 characters."
                    ]
                ]
            ]);
    }

    public function testDeleteSuccess()
    {
        $this->seed([UserSeeder::class]);
        $this->seed([ContactSeeder::class]);

        $user = User::where('name', 'test')->first();
        $contact = Contact::where('user_id', $user->id)->first();

        $this->delete('/api/contacts/' . $contact->id, headers: [
            "Authorization" => 'test'
        ])->assertStatus(200)
            ->assertJson([
                'data' => true
            ]);
    }

    public function testDeleteFailed()
    {
        $this->seed([UserSeeder::class]);
        $this->seed([ContactSeeder::class]);

        $user = User::where('name', 'test')->first();
        $contact = Contact::where('user_id', $user->id)->first();

        $this->delete('/api/contacts/' . $contact->id, headers: [
            "Authorization" => 'salah'
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => "Unauthorized",
                ]
            ]);
    }
}
