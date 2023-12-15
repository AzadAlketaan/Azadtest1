<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MigrateDataToNotesForNewKeys extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);
        set_time_limit(0);

        $limit = 1000;
        $skip = 0;
        $TemporaryCount = DB::table('temporary_table_for_notes_date')->count();

        while ($skip < $TemporaryCount)
        {
            $temporaryData = DB::table('temporary_table_for_notes_date')->skip($skip)->take($limit)->get();
        
            foreach ($temporaryData as $data)
            {
                DB::table('notes')->updateOrInsert(
                    ['id' => $data->note_id],
                    ['status_id' => $data->status_id, 'users_id' => $data->users_id]
                );
            }
            $skip += $limit;
        }
    }
}