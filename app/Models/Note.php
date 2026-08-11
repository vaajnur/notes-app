<?php

namespace App\Models;

use Database\Factories\NoteFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/** A user-created note. */
class Note extends Model
{
    /** @use HasFactory<NoteFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = ['title', 'content'];
}
