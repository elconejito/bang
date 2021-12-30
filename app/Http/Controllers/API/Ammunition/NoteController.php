<?php

namespace App\Http\Controllers\API\Ammunition;

use App\Http\Controllers\Controller;
use App\Models\Ammunition;
use App\Models\Note;
use App\Repositories\Interfaces\AmmunitionRepository;
use App\Repositories\Interfaces\NoteRepository;
use App\Transformers\NoteTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NoteController extends Controller
{
    /**
     * @var NoteRepository
     */
    protected $repository;

    /**
     * @param  NoteRepository  $repository
     */
    public function __construct(NoteRepository $repository)
    {
        Log::debug(__METHOD__.':'.__LINE__, []);
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param $ammunition
     * @param  Request  $request
     * @param  AmmunitionRepository  $ammunition_repository
     *
     * @return JsonResponse
     */
    public function index($ammunition, Request $request, AmmunitionRepository $ammunition_repository): JsonResponse
    {
        $ammunition = $ammunition_repository->find($ammunition);

        return fractal($ammunition->notes, NoteTransformer::class)->respond();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  $ammunition
     * @param  Request  $request
     * @param  AmmunitionRepository  $ammunition_repository
     *
     * @return JsonResponse
     */
    public function store($ammunition, Request $request, AmmunitionRepository $ammunition_repository): JsonResponse
    {
        $ammunition = $ammunition_repository->find($ammunition);

        // #TODO Add AmmunitionNoteRequest
        $note = $ammunition->notes()->create([
            'user_id' => auth()->user()->id,
            'note'    => $request->get('note'),
        ]);

        return fractal()->item($note, NoteTransformer::class)->respond();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Note $note)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Note $note)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Note $note)
    {
        //
    }
}
