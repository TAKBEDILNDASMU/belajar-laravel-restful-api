<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\User;
use Database\Seeders\ContactSeeder;
use Database\Seeders\SearchContactSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
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
        $this->seed([UserSeeder::class, ContactSeeder::class]);

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
        $this->seed([UserSeeder::class, ContactSeeder::class]);

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
        $this->seed([UserSeeder::class, ContactSeeder::class]);

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
        $this->seed([UserSeeder::class, ContactSeeder::class]);

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
        $this->seed([UserSeeder::class, ContactSeeder::class]);

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
        $this->seed([UserSeeder::class, ContactSeeder::class]);

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
        $this->seed([UserSeeder::class, ContactSeeder::class]);

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
        $this->seed([UserSeeder::class, ContactSeeder::class]);

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

    public function testSearchWithName()
    {
        $this->seed([UserSeeder::class, SearchContactSeeder::class]);

        $response = $this->get('/api/contacts?name=test', headers: [
            "Authorization" => 'test'
        ])->assertStatus(200)->json();

        self::assertEquals(count($response['data']), 10);
        self::assertEquals($response['meta']['current_page'], 1);
        self::assertEquals($response['meta']['last_page'], 2);
        self::assertEquals($response['meta']['total'], 20);
    }

    public function testSearchWithPhone()
    {
        $this->seed([UserSeeder::class, SearchContactSeeder::class]);

        $response = $this->get('/api/contacts?phone=088', headers: [
            "Authorization" => 'test'
        ])->assertStatus(200)->json();

        self::assertEquals(count($response['data']), 10);
        self::assertEquals($response['meta']['current_page'], 1);
        self::assertEquals($response['meta']['last_page'], 1);
        self::assertEquals($response['meta']['total'], 10);
    }

    public function testSearchWithEmail()
    {
        $this->seed([UserSeeder::class, SearchContactSeeder::class]);

        $response = $this->get('/api/contacts?email=test', headers: [
            "Authorization" => 'test'
        ])->assertStatus(200)->json();

        self::assertEquals(count($response['data']), 10);
        self::assertEquals($response['meta']['current_page'], 1);
        self::assertEquals($response['meta']['last_page'], 2);
        self::assertEquals($response['meta']['total'], 20);
    }

    public function testSearchNotFound()
    {
        $this->seed([UserSeeder::class, SearchContactSeeder::class]);

        $response = $this->get('/api/contacts?name=tidakada', headers: [
            "Authorization" => 'test'
        ])->assertStatus(200)->json();

        self::assertEquals(count($response['data']), 0);
        self::assertEquals($response['meta']['current_page'], 1);
        self::assertEquals($response['meta']['last_page'], 1);
        self::assertEquals($response['meta']['total'], 0);
    }

    public function testWithPage()
    {
        $this->seed([UserSeeder::class, SearchContactSeeder::class]);

        $response = $this->get('/api/contacts?page=2&size=5', headers: [
            "Authorization" => 'test'
        ])->assertStatus(200)->json();

        self::assertEquals(count($response['data']), 5);
        self::assertEquals($response['meta']['current_page'], 2);
        self::assertEquals($response['meta']['last_page'], 4);
        self::assertEquals($response['meta']['total'], 20);
    }

    public function testWithOnlySize()
    {
        $this->seed([UserSeeder::class, SearchContactSeeder::class]);

        $response = $this->get('/api/contacts?size=5', headers: [
            "Authorization" => 'test'
        ])->assertStatus(200)->json();

        self::assertEquals(count($response['data']), 5);
        self::assertEquals($response['meta']['current_page'], 1);
        self::assertEquals($response['meta']['last_page'], 4);
        self::assertEquals($response['meta']['total'], 20);
    }

    public function testWithPageFailed()
    {
        $this->seed([UserSeeder::class, SearchContactSeeder::class]);

        $this->get('/api/contacts?size=woy', headers: [
            "Authorization" => 'test'
        ])->assertStatus(400)->assertJson([
            "errors" => [
                "size" => [
                    "The size field must be an integer."
                ],
            ]
        ]);
    }
}
