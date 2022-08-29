<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyMovieRequest;
use App\Http\Requests\StoreMovieRequest;
use App\Http\Requests\UpdateMovieRequest;
use App\Models\Category;
use App\Models\Movie;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;

class MovieController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('movie_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $movies = Movie::with(['category', 'media'])->get();

        return view('admin.movies.index', compact('movies'));
    }

    public function create()
    {
        abort_if(Gate::denies('movie_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $categories = Category::pluck('title', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.movies.create', compact('categories'));
    }

    public function store(StoreMovieRequest $request)
    {
        $movie = Movie::create($request->all());

        if ($request->input('image', false)) {
            $movie->addMedia(storage_path('tmp/uploads/' . basename($request->input('image'))))->toMediaCollection('image');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $movie->id]);
        }

        return redirect()->route('admin.movies.index');
    }

    public function edit(Movie $movie)
    {
        abort_if(Gate::denies('movie_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $categories = Category::pluck('title', 'id')->prepend(trans('global.pleaseSelect'), '');

        $movie->load('category');

        return view('admin.movies.edit', compact('categories', 'movie'));
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

        return redirect()->route('admin.movies.index');
    }

    public function show(Movie $movie)
    {
        abort_if(Gate::denies('movie_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $movie->load('category');

        return view('admin.movies.show', compact('movie'));
    }

    public function destroy(Movie $movie)
    {
        abort_if(Gate::denies('movie_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $movie->delete();

        return back();
    }

    public function massDestroy(MassDestroyMovieRequest $request)
    {
        Movie::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('movie_create') && Gate::denies('movie_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new Movie();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}
