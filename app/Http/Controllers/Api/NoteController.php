<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNoteRequest;
use App\Http\Requests\UpdateNoteRequest;
use App\Http\Resources\NoteResource;
use App\Models\Note;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

/** Handle REST operations for notes. */
class NoteController extends Controller
{
    /** Return a validated, paginated list of notes. */
    public function index(Request $request): AnonymousResourceCollection
    {
        $validated = $request->validate([
            'per_page' => ['sometimes', 'integer', 'between:1,100'],
        ]);

        $notes = Note::query()
            ->latest('updated_at')
            ->paginate($validated['per_page'] ?? 12)
            ->withQueryString();

        return NoteResource::collection($notes);
    }

    /** Store a newly validated note. */
    public function store(StoreNoteRequest $request): NoteResource
    {
        return new NoteResource(Note::query()->create($request->validated()));
    }

    /** Return one note selected through validated route binding. */
    public function show(Note $note): NoteResource
    {
        return new NoteResource($note);
    }

    /** Update a note with validated input. */
    public function update(UpdateNoteRequest $request, Note $note): NoteResource
    {
        $note->update($request->validated());

        return new NoteResource($note->refresh());
    }

    /** Delete a note selected through validated route binding. */
    public function destroy(Note $note): JsonResponse
    {
        $note->delete();

        return response()->json(status: Response::HTTP_NO_CONTENT);
    }
}
