<?php

namespace App\Http\Controllers\API\Firearms;

use App\Criteria\NotableTypeCriteria;
use App\Http\Controllers\Controller;
use App\Models\Firearm;
use App\Models\Note;
use App\Repositories\Interfaces\FirearmRepository;
use App\Repositories\Interfaces\NoteRepository;
use App\Transformers\NoteTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Prettus\Repository\Exceptions\RepositoryException;

class NoteController extends Controller
{
    /**
     * @var NoteRepository
     */
    protected NoteRepository $repository;

    /**
     * @param  NoteRepository  $repository
     */
    public function __construct(NoteRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  int  $firearm
     * @param  Request  $request
     *
     * @return JsonResponse
     * @throws RepositoryException
     */
    public function index(int $firearm, Request $request): JsonResponse
    {
        $notes = $this->repository->pushCriteria(new NotableTypeCriteria(Firearm::class, $firearm))
                                  ->paginate();

        return fractal($notes, NoteTransformer::class)->respond();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  $firearm
     * @param  Request  $request
     * @param  FirearmRepository  $firearm_repository
     *
     * @return JsonResponse
     */
    public function store($firearm, Request $request, FirearmRepository $firearm_repository): JsonResponse
    {
        $firearm = $firearm_repository->find($firearm);

        // #TODO Add AmmunitionNoteRequest
        $note = $firearm->notes()->create([
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
