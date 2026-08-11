<?php

namespace Tests\Feature;

use App\Models\Note;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/** Test successful and erroneous behavior of all notes API endpoints. */
class NoteApiTest extends TestCase
{
    use DatabaseTransactions {
        beginDatabaseTransaction as startDatabaseTransaction;
    }

    /** Ensure the in-memory schema exists before starting its transaction. */
    public function beginDatabaseTransaction(): void
    {
        if (! Schema::hasTable('notes')) {
            $this->artisan('migrate', ['--force' => true]);
        }

        $this->startDatabaseTransaction();
    }

    /** Verify that the index endpoint returns notes with status 200. */
    public function test_index_returns_notes(): void
    {
        Note::factory()->count(3)->create();

        $this->getJson('/api/notes')
            ->assertOk()
            ->assertJsonCount(3, 'data');
    }

    /** Verify validation of index query parameters. */
    public function test_index_rejects_invalid_page_size(): void
    {
        $this->getJson('/api/notes?per_page=101')
            ->assertUnprocessable()
            ->assertJsonValidationErrors('per_page');
    }

    /** Verify that a valid note is created with status 201. */
    public function test_store_creates_a_note(): void
    {
        $payload = ['title' => 'План', 'content' => 'Сделать сервис'];

        $this->postJson('/api/notes', $payload)
            ->assertCreated()
            ->assertJsonPath('data.title', 'План');
        $this->assertDatabaseHas('notes', $payload);
    }

    /** Verify required field validation during creation. */
    public function test_store_rejects_missing_title(): void
    {
        $this->postJson('/api/notes', ['content' => 'Без заголовка'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('title');
    }

    /** Verify maximum length validation during creation. */
    public function test_store_rejects_content_that_is_too_long(): void
    {
        $this->postJson('/api/notes', [
            'title' => 'Заметка',
            'content' => str_repeat('а', 10001),
        ])->assertUnprocessable()->assertJsonValidationErrors('content');
    }

    /** Verify that one existing note is returned with status 200. */
    public function test_show_returns_a_note(): void
    {
        $note = Note::factory()->create();

        $this->getJson("/api/notes/{$note->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $note->id);
    }

    /** Verify status 404 when viewing a missing note. */
    public function test_show_returns_not_found_for_missing_note(): void
    {
        $this->getJson('/api/notes/999999')->assertNotFound();
    }

    /** Verify that valid data updates a note with status 200. */
    public function test_update_changes_a_note(): void
    {
        $note = Note::factory()->create();

        $this->putJson("/api/notes/{$note->id}", [
            'title' => 'Обновлено',
            'content' => null,
        ])->assertOk()->assertJsonPath('data.title', 'Обновлено');

        $this->assertDatabaseHas('notes', ['id' => $note->id, 'title' => 'Обновлено']);
    }

    /** Verify status 422 for invalid update data. */
    public function test_update_rejects_invalid_data(): void
    {
        $note = Note::factory()->create();

        $this->putJson("/api/notes/{$note->id}", ['title' => ''])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('title');
    }

    /** Verify status 404 when updating a missing note. */
    public function test_update_returns_not_found_for_missing_note(): void
    {
        $this->putJson('/api/notes/999999', ['title' => 'Тест', 'content' => null])
            ->assertNotFound();
    }

    /** Verify that an existing note is deleted with status 204. */
    public function test_destroy_deletes_a_note(): void
    {
        $note = Note::factory()->create();

        $this->deleteJson("/api/notes/{$note->id}")->assertNoContent();
        $this->assertDatabaseMissing('notes', ['id' => $note->id]);
    }

    /** Verify status 404 when deleting a missing note. */
    public function test_destroy_returns_not_found_for_missing_note(): void
    {
        $this->deleteJson('/api/notes/999999')->assertNotFound();
    }
}
