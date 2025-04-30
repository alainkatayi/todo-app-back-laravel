<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('task', function (Blueprint $table) {
            $table->id();
            $table ->string('title');
            $table -> string('description');
            $table -> boolean('is_completed') -> default(false);
            $table -> date('start_date');
            $table -> date('end_date');
            $table -> enum('priority', ['low', 'medium', 'high']);
            $table -> foreignId('user_id') ->contrained('users') -> onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task');
    }
};
