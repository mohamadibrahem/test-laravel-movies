<?php

namespace App\Http\Controllers\Api\V1\App;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\StoreMovieRequest;
use App\Http\Requests\UpdateMovieRequest;
use App\Http\Resources\Admin\MovieResource;
use App\Models\Movie;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MovieApiController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('movie_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new MovieResource(Movie::with(['category'])->get());
    }

    public function store(StoreMovieRequest $request)
    {
        $movie = Movie::create($request->all());

        if ($request->input('image', false)) {
            $movie->addMedia(storage_path('tmp/uploads/' . basename($request->input('image'))))->toMediaCollection('image');
        }

        return (new MovieResource($movie))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Movie $movie)
    {
        abort_if(Gate::denies('movie_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new MovieResource($movie->load(['category']));
    }

    public function update(UpdateMovieRequest $request, Movie $movie)
    {
        $movie->update($request->all());

        if ($request->input('image', false)) {
            if (!$movie->image || $request->input('image') !== $movie->image->file_name) {
                if ($movie->image) {
                    $movie->image->delete();
                }
                $movie->addMedia(storage_path('tmp/uploads/' . basename($request->input('image'))))->toMediaCollection('image');
            }
        } elseif ($movie->image) {
            $movie->image->delete();
        }

        return (new MovieResource($movie))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Movie $movie)
    {
        abort_if(Gate::denies('movie_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $movie->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
