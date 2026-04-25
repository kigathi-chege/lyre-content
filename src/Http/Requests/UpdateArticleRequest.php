<?php

namespace Lyre\Content\Http\Requests;

use Lyre\Request;

class UpdateArticleRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "title" => "string",
            "subtitle" => "string",
            "content" => "string",
            "unpublished" => "boolean",
            "published_at" => "date",
            "files" => "nullable|array",
            "files.*" => "integer",
        ];
    }
}
