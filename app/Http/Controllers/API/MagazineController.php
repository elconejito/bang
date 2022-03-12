<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Magazine;
use App\Models\Picture;
use App\Repositories\Interfaces\MagazineRepository;
use App\Transformers\MagazineTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MagazineController extends Controller
{
    /**
     * @var MagazineRepository
     */
    private MagazineRepository $repository;

    /**
     * MagazineController constructor.
     *
     * @param MagazineRepository $magazine_repository
     */
    public function __construct(MagazineRepository $magazine_repository)
    {
        $this->repository = $magazine_repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $magazines = $this->repository->with(['calibers', 'firearms'])->all();

        return fractal($magazines, MagazineTransformer::class)
            ->respond();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $data = array_merge(
            $request->only([
                'label',
                'manufacturer',
                'model_name',
                'capacity',
                'serial_number',
                'id_marking',
            ]),
            [
                'user_id' => Auth::id(),
            ]
        );
        $magazine = $this->repository->create($data);

        $magazine->calibers()->sync($request->input('calibers', []));
        $magazine->firearms()->sync($request->input('firearms', []));

        return fractal()->item($magazine, MagazineTransformer::class)
                        ->respond();
    }

    /**
     * Display the specified resource.
     *
     * @param $magazine_id
     *
     * @return JsonResponse
     */
    public function show($magazine_id): JsonResponse
    {
        $magazine = $this->repository->find($magazine_id);

        return fractal()->item($magazine, MagazineTransformer::class)
                        ->respond();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Magazine $magazine
     *
     * @return JsonResponse
     */
    public function update(Request $request, Magazine $magazine): JsonResponse
    {
        $data = array_merge(
            $request->only([
                'label',
                'manufacturer',
                'model_name',
                'capacity',
                'serial_number',
                'id_marking',
            ]),
            [
                'user_id' => Auth::id(),
            ]
        );

        $magazine = $this->repository->update($data, $magazine->id);

        $magazine->calibers()->sync($request->input('calibers', []));

        return fractal()->item($magazine, MagazineTransformer::class)
                        ->respond();
    }

    public function addPhoto(Request $request, Magazine $magazine)
    {
        // #TODO check for valid file
        // save the original photo
        $path = $request->file->store('public/images');
        $filename = str_replace('public/images/', '', $path);

        $picture = Picture::create([
            'name' => $filename,
            'filename' => $filename
        ]);

        // save the resized images
        $picture->resize();

        // attach to the magazine
        $magazine->pictures()->attach($picture);

        return response()->json([
            'message' => 'Successfully added Picture to Magazine',
            'data' => [
                'path' => $path,
            ],
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Magazine $magazine
     *
     * @return void
     */
    public function destroy(Magazine $magazine)
    {
        //
    }
}
