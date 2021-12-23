<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRosterEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roster_events', function (Blueprint $table) {
            $table->id();
            $table->dateTime('date');
            $table->string('activity');
            $table->string('from');
            $table->string('to');
            $table->string('arrival_time');
            $table->string('departure_time');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('roster_events');
    }
}
