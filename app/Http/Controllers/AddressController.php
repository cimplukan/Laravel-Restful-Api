<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressCreateRequest;
use App\Http\Requests\AddressUpdateRequest;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    private function get_contact(User $user, int $id_contact)
    {
        $contact = Contact::where('id', $id_contact)->where('user_id', $user->id)->first();
        if (!$contact) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => [
                        'Not found.'
                    ]
                ]
            ])->setStatusCode(404));
        }

        return $contact;
    }
    private function get_address(Contact $contact, int $id_address)
    {
        $address = Address::where('id', $id_address)->where('contact_id', $contact->id)->first();
        if (!$address) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => [
                        'Not found.'
                    ]
                ]
            ])->setStatusCode(404));
        }
        return $address;
    }

    public function create(int $id_contact, AddressCreateRequest $request): JsonResponse
    {
        $user = Auth::user();
        $contact = $this->get_contact($user, $id_contact);

        $data = $request->validated();
        $address = new Address($data);
        $address->contact_id = $contact->id;
        $address->save();

        return (new AddressResource($address))->response()->setStatusCode(201);
    }

    public function get(int $id_contact, int $id_address): AddressResource
    {
        $user = Auth::user();
        $contact = $this->get_contact($user, $id_contact);
        $address = $this->get_address($contact, $id_address);

        return new AddressResource($address);
    }

    public function update(int $id_contact, int $id_address, AddressUpdateRequest $request): AddressResource
    {
        $user = Auth::user();
        $contact = $this->get_contact($user, $id_contact);
        $address = $this->get_address($contact, $id_address);

        $data = $request->validated();
        $address->fill($data);
        $address->save();

        return new AddressResource($address);
    }

    public function delete(int $id_contact, int $id_address): JsonResponse
    {
        $user = Auth::user();
        $contact = $this->get_contact($user, $id_contact);
        $address = $this->get_address($contact, $id_address);

        $address->delete();

        return response()->json([
            'data' => true
        ])->setStatusCode(200);
    }
}
