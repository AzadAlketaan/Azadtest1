<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTemporaryTableForNotesDateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('temporary_table_for_notes_date', function (Blueprint $table) {
            $table->integer('note_id')->unsigned();
            $table->integer('users_id')->unsigned()->nullable();
            $table->integer('status_id')->unsigned()->nullable();
        });

        Artisan::call('db:seed', array('--class' => 'MigrateDataFromNotesForNewKeys'));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('temporary_table_for_notes_date');
    }
}
