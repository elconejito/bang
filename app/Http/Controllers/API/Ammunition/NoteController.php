<?php

namespace App\Http\Controllers\API\Ammunition;

use App\Http\Controllers\Controller;
use App\Models\Ammunition;
use App\Models\Note;
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
     * @return \Illuminate\Http\Response
     */
    public function index(Ammunition $ammunition, Request $request)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Ammunition  $ammunition
     * @param  Request  $request
     *
     * @return JsonResponse
     */
    public function store(Ammunition $ammunition, Request $request): JsonResponse
    {
        Log::debug(__METHOD__.':'.__LINE__, [$ammunition, $request->all()]);
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
