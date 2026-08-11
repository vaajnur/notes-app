<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Create the notes table. */
    public function up(): void
    {
        Schema::create('notes', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->text('content')->nullable();
            $table->timestamps();
        });
    }

    /** Drop the notes table. */
    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};
