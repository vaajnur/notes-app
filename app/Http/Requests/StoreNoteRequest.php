<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/** Validate input used to create a note. */
class StoreNoteRequest extends FormRequest
{
    /** Determine whether the request is authorized. */
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, list<string>> */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string', 'max:10000'],
        ];
    }
}
