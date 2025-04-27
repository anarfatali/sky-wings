<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('flights', function (Blueprint $table) {
            $table->id();
            $table->string('from')->nullable();
            $table->string('to')->nullable();
            $table->dateTime('flight_date')->nullable();
            $table->dateTime('arrival_date')->nullable();
            $table->string('aircraft')->nullable();
            $table->integer('econom_free_seats')->nullable();
            $table->integer('business_free_seats')->nullable();
            $table->decimal('econom_price', 8, 2)->nullable();
            $table->decimal('business_price', 8, 2)->nullable();
            $table->string('flight_number')->unique();
            $table->integer('free_seats')->nullable();
            $table->text('booked_seats')->nullable();
            $table->foreignId('airport_id')->nullable()->constrained('airports')->onDelete('set null');
            $table->timestamps();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('flights');
    }
};
