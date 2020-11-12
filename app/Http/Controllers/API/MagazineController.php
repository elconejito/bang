<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Magazine;
use App\Models\Picture;
use App\Repositories\Interfaces\MagazineRepository;
use App\Transformers\MagazineTransformer;
use Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MagazineController extends Controller
{
    /**
     * @var MagazineRepository
     */
    private $magazineRepository;

    /**
     * MagazineController constructor.
     *
     * @param MagazineRepository $magazine_repository
     */
    public function __construct(MagazineRepository $magazine_repository)
    {
        $this->magazineRepository = $magazine_repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        $magazines = $this->magazineRepository->with(['calibers', 'firearms'])->all();

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
    public function store(Request $request)
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
        $magazine = $this->magazineRepository->create($data);

        $magazine->calibers()->sync($request->input('calibers', []));

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
    public function show($magazine_id)
    {
        $magazine = $this->magazineRepository->find($magazine_id);

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
    public function update(Request $request, $magazine_id)
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
        $magazine = $this->magazineRepository->find($magazine_id);

        $magazine->update($data);

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
