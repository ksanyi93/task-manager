<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('lenght')->default(0);
            $table->boolean('finished')->default(false);
            $table->string('assignes')->default('');
            $table->enum('priority', ['alacsony', 'normal', 'magas'])->default('normal');
            $table->date('schedule_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};