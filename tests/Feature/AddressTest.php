<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Contact;
use Database\Seeders\AddressSeeder;
use Database\Seeders\ContactSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AddressTest extends TestCase
{
    use RefreshDatabase;

    private function getContactId(): int
    {
        $contact = Contact::where('name', 'test')->first();
        return $contact->id;
    }

    private function getAddressId(): int
    {
        $address = Address::where('state', 'state-1')->first();
        return $address->id;
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

    public function testGetAddressListSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $contactId = $this->getContactId();

        $response = $this->get('/api/contacts/' . $contactId . '/addresses', headers: [
            'Authorization' => 'test'
        ])->assertStatus(200)->json();

        self::assertCount(5, $response['data']);
    }

    public function testGetAddressListContactNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $contactId = $this->getContactId();

        $response = $this->get('/api/contacts/' . $contactId + 1 . '/addresses', headers: [
            'Authorization' => 'test'
        ])->assertStatus(404)->json();

        self::assertEquals([
            'errors' => [
                'message' => 'Contact is not found'
            ]
        ], $response);
    }

    public function testUpdateAddressSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $contactId = $this->getContactId();
        $addressId = $this->getAddressId();

        $response = $this->put('/api/contacts/' . $contactId . '/addresses/' . $addressId, data: [
            "street" => "newStreet",
            "village" => "newVillage",
            "district" => "newDistrict",
            "city" => "newCity",
            "province" => "newProvince",
            "state" => "newState",
            "postal_code" => "222"
        ], headers: [
            'Authorization' => 'test'
        ])->assertStatus(200)->json();

        self::assertEquals([
            "data" => [
                "street" => "newStreet",
                "village" => "newVillage",
                "district" => "newDistrict",
                "city" => "newCity",
                "province" => "newProvince",
                "state" => "newState",
                "postal_code" => "222"
            ]
        ], $response);
    }

    public function testUpdateAddressFailed()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $contactId = $this->getContactId();
        $addressId = $this->getAddressId();

        $response = $this->put('/api/contacts/' . $contactId . '/addresses/' . $addressId, data: [
            "street" => "newStreet",
            "village" => "newVillage",
            "district" => "newDistrict",
            "city" => "newCity",
            "province" => "newProvince",
            "state" => "newState",
            "postal_code" => "notanumber"
        ], headers: [
            'Authorization' => 'test'
        ])->assertStatus(400)->json();

        self::assertEquals([
            "errors" => [
                "postal_code" => [
                    "The postal code field must be an integer."
                ]
            ]
        ], $response);
    }

    public function testUpdateNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $contactId = $this->getContactId();
        $addressId = $this->getAddressId();

        $response = $this->put('/api/contacts/' . $contactId . '/addresses/' . $addressId + 999, data: [
            "street" => "newStreet",
            "village" => "newVillage",
            "district" => "newDistrict",
            "city" => "newCity",
            "province" => "newProvince",
            "state" => "newState",
            "postal_code" => "111"
        ], headers: [
            'Authorization' => 'test'
        ])->assertStatus(404)->json();

        self::assertEquals([
            'errors' => [
                'message' => "Address is not found"
            ]
        ], $response);
    }

    public function testUpdateAddressIdNotaNumber()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $contactId = $this->getContactId();
        $addressId = $this->getAddressId();

        $response = $this->put('/api/contacts/' . $contactId . '/addresses/haha', data: [
            "street" => "newStreet",
            "village" => "newVillage",
            "district" => "newDistrict",
            "city" => "newCity",
            "province" => "newProvince",
            "state" => "newState",
            "postal_code" => "111"
        ], headers: [
            'Authorization' => 'test'
        ])->assertStatus(404)->json();

        self::assertEquals([
            "errors" => [
                "message" => "Address is not found"
            ]
        ], $response);
    }

    public function testGetAddressSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $contactId = $this->getContactId();
        $addressId = $this->getAddressId();

        $response = $this->get('/api/contacts/' . $contactId . '/addresses/' . $addressId, headers: [
            'Authorization' => 'test'
        ])->assertStatus(200)->json();

        self::assertEquals([
            "data" => [
                "street" => "street-1",
                "village" => "village-1",
                "district" => "district-1",
                "city" => "city-1",
                "province" => "province-1",
                "state" => "state-1",
                "postal_code" => "111"
            ]
        ], $response);
    }

    public function testGetAddressNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $contactId = $this->getContactId();
        $addressId = $this->getAddressId();

        $response = $this->get('/api/contacts/' . $contactId . '/addresses/' . $addressId + 999, headers: [
            'Authorization' => 'test'
        ])->assertStatus(404)->json();

        self::assertEquals([
            "errors" => [
                "message" => "Address is not found"
            ]
        ], $response);
    }

    public function testGetAddressIdisNotaNumber()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $contactId = $this->getContactId();
        $addressId = $this->getAddressId();

        $response = $this->get('/api/contacts/' . $contactId . '/addresses/haha', headers: [
            'Authorization' => 'test'
        ])->assertStatus(404)->json();

        self::assertEquals([
            "errors" => [
                "message" => "Address is not found"
            ]
        ], $response);
    }

    public function testGetAddressContactNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $contactId = $this->getContactId();
        $addressId = $this->getAddressId();

        $response = $this->get('/api/contacts/' . $contactId + 1 . '/addresses/' . $addressId, headers: [
            'Authorization' => 'test'
        ])->assertStatus(404)->json();

        self::assertEquals([
            "errors" => [
                "message" => "Contact is not found"
            ]
        ], $response);
    }

    public function testGetAddressContactIsNotaNumber()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $contactId = $this->getContactId();
        $addressId = $this->getAddressId();

        $response = $this->get('/api/contacts/haha/addresses/' . $addressId, headers: [
            'Authorization' => 'test'
        ])->assertStatus(404)->json();

        self::assertEquals([
            "errors" => [
                "message" => "Contact is not found"
            ]
        ], $response);
    }

    public function testDeleteAddressSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $contactId = $this->getContactId();
        $addressId = $this->getAddressId();

        $response = $this->delete('/api/contacts/' . $contactId . '/addresses/' . $addressId, headers: [
            'Authorization' => 'test'
        ])->assertStatus(200)->json();

        self::assertEquals([
            "data" => true
        ], $response);
    }

    public function testDeleteAddresNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $contactId = $this->getContactId();
        $addressId = $this->getAddressId();

        $response = $this->delete('/api/contacts/' . $contactId . '/addresses/' . $addressId + 999, headers: [
            'Authorization' => 'test'
        ])->assertStatus(404)->json();

        self::assertEquals([
            "errors" => [
                "message" => "Address is not found"
            ]
        ], $response);
    }

    public function testDeleteAddressIsNotaNumber()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $contactId = $this->getContactId();
        $addressId = $this->getAddressId();

        $response = $this->delete('/api/contacts/' . $contactId . '/addresses/hahah', headers: [
            'Authorization' => 'test'
        ])->assertStatus(404)->json();

        self::assertEquals([
            "errors" => [
                "message" => "Address is not found"
            ]
        ], $response);
    }

    public function testDeleteAddressContactNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $contactId = $this->getContactId();
        $addressId = $this->getAddressId();

        $response = $this->delete('/api/contacts/' . $contactId + 1 . '/addresses/' . $addressId, headers: [
            'Authorization' => 'test'
        ])->assertStatus(404)->json();

        self::assertEquals([
            "errors" => [
                "message" => "Contact is not found"
            ]
        ], $response);
    }

    public function testDeleteAddressContactIsNotaNumber()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $contactId = $this->getContactId();
        $addressId = $this->getAddressId();

        $response = $this->delete('/api/contacts/haha/addresses/' . $addressId, headers: [
            'Authorization' => 'test'
        ])->assertStatus(404)->json();

        self::assertEquals([
            "errors" => [
                "message" => "Contact is not found"
            ]
        ], $response);
    }
}
