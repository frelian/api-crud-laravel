<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\Contact as ContactResource;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContactController extends BaseController
{
    public function __construct()
    {
        $this->fields = [
            'name'  => 'required',
            'phone' => 'required',
            'email' => 'required',
            'birthday' => 'required',
        ];
    }

    public function index()
    {
        $contacts = Contact::all();
        return $this->handleResponse( ContactResource::collection($contacts), 'Contacts have been retrieved!');
    }

    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, $this->fields);
        if($validator->fails()){
            return $this->handleError($validator->errors());
        }
        $task = Contact::create($input);
        return $this->handleResponse(new ContactResource($task), 'Contact created!');
    }

    public function show($id)
    {
        $contact = Contact::find($id);
        if (is_null($contact)) {
            return $this->handleError('Contact not found!');
        }
        return $this->handleResponse(new ContactResource($contact), 'Contact retrieved.');
    }

    public function update(Request $request, Contact $contact)
    {
        $input = $request->all();

        $validator = Validator::make($input, $this->fields);

        if($validator->fails()){
            return $this->handleError($validator->errors());
        }

        $contact->name = $input['name'];
        $contact->phone = $input['phone'];
        $contact->email = $input['email'];
        $contact->birthday = $input['birthday'];

        if ( isset($input['state']) ) {
            $contact->state = $input['state'];
        }

        if ( isset($input['notes']) ) {
            $contact->notes = $input['notes'];
        }

        $contact->save();

        return $this->handleResponse(new ContactResource($contact), 'Contact successfully updated!');
    }

    public function destroy(Request $request)
    {
        $id = $request->contact;
        $contact = Contact::find($id);

        if ( $contact ) {

            $response = $contact->delete();

            if ( $response ) {

                return $this->handleResponse([], 'Contact deleted succesfully');
            }

            return response()->json(['message' => 'Error to update post'], 500);
        }

        return response()->json(['message' => 'Error, not found'], 404);

    }
}
