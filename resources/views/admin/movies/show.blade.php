@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.movie.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.movies.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.movie.fields.id') }}
                        </th>
                        <td>
                            {{ $movie->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.movie.fields.title') }}
                        </th>
                        <td>
                            {{ $movie->title }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.movie.fields.description') }}
                        </th>
                        <td>
                            {{ $movie->description }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.movie.fields.rate') }}
                        </th>
                        <td>
                            {{ $movie->rate }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.movie.fields.image') }}
                        </th>
                        <td>
                            @if($movie->image)
                                <a href="{{ $movie->image->getUrl() }}" target="_blank" style="display: inline-block">
                                    <img src="{{ $movie->image->getUrl('thumb') }}">
                                </a>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.movie.fields.category') }}
                        </th>
                        <td>
                            {{ $movie->category->title ?? '' }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.movies.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection