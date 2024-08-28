<?php

namespace Tests\Feature;

use App\Models\Contact;
use Database\Seeders\ContactSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class AddressTest extends TestCase
{
    use RefreshDatabase;

    private function getContactId(): int
    {
        $contact = Contact::where('name', 'test')->first();
        return $contact->id;
    }

    public function testCreateAddressSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contactId = $this->getContactId();

        $this->post('/api/contacts/' . $contactId . '/addresses', [
            "street" => "test",
            "village" => "test",
            "district" => "test",
            "city" => "test",
            "province" => "test",
            "state" => "test",
            "postal_code" => "1111"
        ], [
            'Authorization' => 'test'
        ])->assertStatus(201)
            ->assertJson([
                'data' => [
                    "street" => "test",
                    "village" => "test",
                    "district" => "test",
                    "city" => "test",
                    "province" => "test",
                    "state" => "test",
                    "postal_code" => "1111"
                ]
            ]);
    }

    public function testCreateAddressOnlyStateAndPostalCode()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contactId = $this->getContactId();

        $this->post('/api/contacts/' . $contactId . '/addresses', [
            "state" => "test",
            "postal_code" => "111"
        ], [
            'Authorization' => 'test'
        ])->assertStatus(201)
            ->assertJson([
                'data' => [
                    "state" => "test",
                    "postal_code" => "111"
                ]
            ]);
    }

    public function testCreateFailed()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contactId = $this->getContactId();

        $this->post('/api/contacts/' . $contactId . '/addresses', [
            "state" => "test",
            "postal_code" => "heydude"
        ], [
            'Authorization' => 'test'
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    "postal_code" => [
                        "The postal code field must be an integer."
                    ]
                ]
            ]);
    }

    public function testContactIsNotAnId()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contactId = $this->getContactId();

        $this->post('/api/contacts/haha/addresses', [
            "state" => "test",
            "postal_code" => "111"
        ], [
            'Authorization' => 'test'
        ])->assertStatus(404)
            ->assertJson([
                "errors" => [
                    "message" => "Contact is not found"
                ]
            ]);
    }
}
