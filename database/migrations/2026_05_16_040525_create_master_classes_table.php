<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_classes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('creativity_type_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('master_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('title');
            $table->text('description');

            $table->date('class_date');
            $table->time('start_time');

            $table->integer('group_size');

            $table->decimal('price', 10, 2);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_classes');
    }
};
