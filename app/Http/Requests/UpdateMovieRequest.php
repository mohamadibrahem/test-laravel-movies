<?php

namespace App\Http\Requests;

use App\Models\Movie;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateMovieRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('movie_edit');
    }

    public function rules()
    {
        return [
            'title' => [
                'string',
                'required',
            ],
            'description' => [
                'required',
            ],
            'rate' => [
                'string',
                'min:1',
                'max:10',
                'required',
            ],
            'image' => [
                'required',
            ],
            'category_id' => [
                'required',
                'integer',
            ],
        ];
    }
}
