<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ManageContactRequest;
use App\Http\Requests\Admin\UpdateContactRequest;
use App\Models\Contact;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ContactController extends Controller
{
    public function index(ManageContactRequest $request): View
    {
        return view('admin.contact.index', [
            'contact' => Contact::query()->first() ?? new Contact,
        ]);
    }

    public function update(UpdateContactRequest $request): RedirectResponse
    {
        $contact = Contact::query()->first() ?? new Contact;
        $contact->fill($request->validated())->save();

        return redirect()
            ->route('admin.kontak')
            ->with('success', 'Kontak program studi berhasil diperbarui.');
    }
}
