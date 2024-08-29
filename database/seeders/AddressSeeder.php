<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Contact;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $contact = Contact::where("name", "test")->first();
        for ($i = 1; $i <= 5; $i++) {
            $address = new Address();
            $address->street = "street-" . $i;
            $address->village = "village-" . $i;
            $address->district = "district-" . $i;
            $address->city = "city-" . $i;
            $address->province = "province-" . $i;
            $address->state = "state-" . $i;
            $address->postal_code = "111";
            $address->contact_id = $contact->id;
            $address->save();
        }
    }
}
