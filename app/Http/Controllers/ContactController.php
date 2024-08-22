<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactCreateRequest;
use App\Http\Requests\UpdateContactRequest;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    public function create(ContactCreateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = Auth::user();

        $contact = new Contact();
        $contact->name = $data['name'];
        $contact->phone = $data['phone'];
        $contact->user_id = $user->id;
        if (isset($data['email'])) {
            $contact->email = $data['email'];
        }

        $contact->save();
        return (new ContactResource($contact))->response()->setStatusCode(201);
    }

    public function get(int $contactId): ContactResource
    {
        $user = Auth::user();
        $contact = Contact::where('id', $contactId)->where('user_id', $user->id)->first();

        if (!$contact) {
            throw new HttpResponseException(
                response()->json([
                    'errors' => [
                        "message" => "Contact not found"
                    ]
                ])->setStatusCode(404)
            );
        }

        return new ContactResource($contact);
    }

    public function update(int $contactId, UpdateContactRequest $request): ContactResource
    {
        $data = $request->validated();
        $user = Auth::user();
        $contact = Contact::where('id', $contactId)->where('user_id', $user->id)->first();

        if (!$contact) {
            throw new HttpResponseException(
                response()->json([
                    'errors' => [
                        "message" => "Contact not found"
                    ]
                ])->setStatusCode(404)
            );
        }

        if (isset($data['name'])) {
            $contact->name = $data['name'];
        }

        if (isset($data['phone'])) {
            $contact->phone = $data['phone'];
        }

        if (isset($data['email'])) {
            $contact->email = $data['email'];
        }

        return new ContactResource($contact);
    }

    public function delete(int $contactId): JsonResponse
    {
        $user = Auth::user();
        $contact = Contact::where('id', $contactId)->where('user_id', $user->id)->first();

        if (!$contact) {
            throw new HttpResponseException(
                response()->json([
                    'errors' => [
                        "message" => "Contact not found"
                    ]
                ])->setStatusCode(404)
            );
        }

        $contact->delete();
        return response()->json([
            "data" => true
        ])->setStatusCode(200);
    }
}
