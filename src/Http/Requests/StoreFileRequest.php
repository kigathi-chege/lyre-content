<?php

namespace Lyre\Content\Http\Requests;

use Lyre\Request;

class StoreFileRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'mimes:pdf,doc,docx,csv,xls,xlsx,ppt,pptx,zip,jpg,jpeg,png,gif,svg,webp,mp4,mp3,txt', 'max:2048'],
            'name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'file.required' => 'Please select a file to upload',
        ];
    }
}
