<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateForeignKeysInNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notes', function (Blueprint $table) {

            $table->foreignId('users_id')->after('applies_to_date')->nullable()->constrained('users')
                ->onDelete('SET NULL')->onUpdate('CASCADE');

            $table->foreignId('status_id')->after('users_id')->nullable()->constrained('status')
                ->onDelete('SET NULL')->onUpdate('CASCADE');
        });

        Artisan::call('db:seed', array('--class' => 'MigrateDataToNotesForNewKeys'));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notes', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['status_id']);
        });
    }
}
