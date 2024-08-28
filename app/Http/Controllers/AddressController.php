<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressCreateRequest;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use App\Models\Contact;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AddressController extends Controller
{

    private function getContact(int $contactId, int $userId): Contact
    {
        $contact = Contact::where('user_id', $userId)->where('id', $contactId)->first();
        if (!$contact) {
            throw new HttpResponseException(
                response()->json([
                    "errors" => [
                        'message' => 'Contact not found'
                    ]
                ])->setStatusCode(404)
            );
        }

        return $contact;
    }

    private function getAddress(int $contactId, int $addressId): Address
    {
        $address = Address::where('contact_id', $contactId)->where('id', $addressId);
        if (!$address) {
            throw new HttpResponseException(
                response()->json([
                    'errors' => [
                        'message' => "Address not found"
                    ]
                ])->setStatusCode(404)
            );
        }

        return $address;
    }

    public function create(AddressCreateRequest $request, int $contactId): JsonResponse
    {
        $user = Auth::user();
        $contact = $this->getContact($contactId, $user->id);
        $data = $request->validated();

        $address = new Address($data);
        $address->contact_id = $contact->id;
        $address->save();

        return (new AddressResource($address))->response()->setStatusCode(201);
    }
}
