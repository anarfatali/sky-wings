<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('flights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('departure_airport_id')->constrained('airports')->onDelete('set null');
            $table->foreignId('arrival_airport_id')->constrained('airports')->onDelete('set null');
            $table->dateTime('flight_date');
            $table->dateTime('arrival_date');
            $table->string('aircraft');
            $table->integer('total_seats');
            $table->integer('econom_free_seats');
            $table->integer('business_free_seats');
            $table->text('booked_seats')->nullable();
            $table->decimal('econom_price', 8, 2);
            $table->decimal('business_price', 8, 2);
            $table->string('flight_number');
            $table->timestamps();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('flights');
    }
};
