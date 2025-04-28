<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('flights', function (Blueprint $table) {
            $table->id();
            $table->string('from');
            $table->string('to');
            $table->dateTime('flight_date');
            $table->dateTime('arrival_date');
            $table->string('aircraft');
            $table->integer('econom_free_seats')->nullable();
            $table->integer('business_free_seats')->nullable();
            $table->decimal('econom_price', 8, 2)->nullable();
            $table->decimal('business_price', 8, 2)->nullable();
            $table->string('flight_number');
            $table->integer('free_seats')->nullable();
            $table->text('booked_seats')->nullable();
            $table->foreignId('airport_id')->constrained('airports')->onDelete('set null');
            $table->timestamps();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('flights');
    }
};
