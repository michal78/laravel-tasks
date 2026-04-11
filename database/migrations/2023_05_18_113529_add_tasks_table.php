<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Michal78\Tasks\Models\Task;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->morphs('taskable');
            $table->string('name');
            $table->string('type')->default(Task::TYPE_SERVICE);
            $table->string('target');
            $table->string('method')->nullable();
            $table->json('payload')->nullable();
            $table->timestamp('run_at')->index();
            $table->string('status')->default(Task::STATUS_PENDING)->index();
            $table->text('error_message')->nullable();
            $table->timestamp('last_ran_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
