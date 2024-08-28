<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SearchContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::query()->where('name', '=', 'test')->first();
        for ($i = 1; $i <= 10; $i++) {
            $contact = new Contact();
            $contact->name = "test" . $i;
            $contact->phone = "088-" . $i;
            $contact->email = "test" . $i . "@gmail.com";
            $contact->user_id = $user->id;
            $contact->save();
        }

        for ($i = 1; $i <= 10; $i++) {
            $contact = new Contact();
            $contact->name = "newTest" . $i;
            $contact->phone = "089-" . $i;
            $contact->email = "newTest" . $i . "@gmail.com";
            $contact->user_id = $user->id;
            $contact->save();
        }
    }
}
