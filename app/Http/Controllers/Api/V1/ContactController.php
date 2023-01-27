<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\Contact as ContactResource;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContactController extends BaseController
{
    public function index()
    {
        $contacts = Contact::all();
        return $this->handleResponse( ContactResource::collection($contacts), 'Contacts have been retrieved!');
    }


    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'required',
            'details' => 'required'
        ]);
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
            return $this->handleError('Task not found!');
        }
        return $this->handleResponse(new ContactResource($contact), 'Contact retrieved.');
    }


    public function update(Request $request, Contact $contact)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required',
            'details' => 'required'
        ]);

        if($validator->fails()){
            return $this->handleError($validator->errors());
        }

        $contact->name = $input['name'];
        $contact->details = $input['details'];
        $contact->save();

        return $this->handleResponse(new ContactResource($contact), 'Contact successfully updated!');
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();
        return $this->handleResponse([], 'Task deleted!');
    }
}
