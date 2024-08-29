<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressCreateRequest;
use App\Http\Requests\AddressUpdateRequest;
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
                        'message' => 'Contact is not found'
                    ]
                ])->setStatusCode(404)
            );
        }

        return $contact;
    }

    private function getAddress(int $contactId, int $addressId): Address
    {
        $address = Address::where('contact_id', $contactId)->where('id', $addressId)->first();
        if (!$address) {
            throw new HttpResponseException(
                response()->json([
                    'errors' => [
                        'message' => "Address is not found"
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

    public function list(Request $request, int $contactId): JsonResponse
    {
        $user = Auth::user();
        $contact = $this->getContact($contactId, $user->id);

        $addresses = Address::where('contact_id', $contact->id)->get();

        return (AddressResource::collection($addresses))->response()->setStatusCode(200);
    }

    public function update(AddressUpdateRequest $request, int $contactId, int $addressId): AddressResource
    {
        $user = Auth::user();
        $this->getContact($contactId, $user->id);
        $address = $this->getAddress($contactId, $addressId);

        $data = $request->validated();
        $address->fill($data);
        $address->save();

        return new AddressResource($address);
    }

    public function get(Request $request, int $contactId, int $addressId): AddressResource
    {
        $user = Auth::user();
        $this->getContact($contactId, $user->id);
        $address = $this->getAddress($contactId, $addressId);

        return new AddressResource($address);
    }

    public function delete(Request $request, int $contactId, int $addressId): JsonResponse
    {
        $user = Auth::user();
        $this->getContact($contactId, $user->id);
        $address = $this->getAddress($contactId, $addressId);

        $address->delete();
        return response()->json([
            "data" => true
        ])->setStatusCode(200);
    }
}
