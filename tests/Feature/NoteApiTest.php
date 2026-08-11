<?php

namespace Tests\Feature;

use App\Models\Note;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Verify every notes API operation and its validation behavior. */
class NoteApiTest extends TestCase
{
    use RefreshDatabase;

    /** Verify that notes are returned. */
    public function test_it_lists_notes(): void
    {
        Note::factory()->count(3)->create();
        $this->getJson('/api/notes')->assertOk()->assertJsonCount(3, 'data');
    }

    /** Verify creation and input validation. */
    public function test_it_creates_and_validates_a_note(): void
    {
        $this->postJson('/api/notes', [])->assertUnprocessable()->assertJsonValidationErrors('title');
        $this->postJson('/api/notes', ['title' => 'План', 'content' => 'Сделать сервис'])
            ->assertCreated()->assertJsonPath('data.title', 'План');
        $this->assertDatabaseHas('notes', ['title' => 'План']);
    }

    /** Verify retrieval of one note. */
    public function test_it_shows_a_note(): void
    {
        $note = Note::factory()->create();
        $this->getJson("/api/notes/{$note->id}")->assertOk()->assertJsonPath('data.id', $note->id);
        $this->getJson('/api/notes/999999')->assertNotFound();
    }

    /** Verify update validation and persistence. */
    public function test_it_updates_and_validates_a_note(): void
    {
        $note = Note::factory()->create();
        $this->putJson("/api/notes/{$note->id}", ['title' => ''])->assertUnprocessable();
        $this->putJson("/api/notes/{$note->id}", ['title' => 'Обновлено', 'content' => null])
            ->assertOk()->assertJsonPath('data.title', 'Обновлено');
    }

    /** Verify deletion and route identifier validation. */
    public function test_it_deletes_a_note(): void
    {
        $note = Note::factory()->create();
        $this->deleteJson("/api/notes/{$note->id}")->assertNoContent();
        $this->assertDatabaseMissing('notes', ['id' => $note->id]);
        $this->deleteJson('/api/notes/not-a-number')->assertNotFound();
    }

    /** Verify list query validation. */
    public function test_it_validates_list_parameters(): void
    {
        $this->getJson('/api/notes?per_page=101')->assertUnprocessable()->assertJsonValidationErrors('per_page');
    }
}
