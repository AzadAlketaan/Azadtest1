<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Notes;
use App\Models\User;
use App\Models\Status;

class MigrateDataFromNotesForNewKeys extends Seeder
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
        $NotesCount = Notes::count();
        $statusIds = Status::pluck('id')->toArray();
        $usersIds = User::pluck('id')->toArray();
        
        while ($skip < $NotesCount)
        {
            $notes = Notes::select('id', 'users_id', 'status_id')->skip($skip)->take($limit)->get();
            $notesArray= array();
            
            foreach ($notes as $note)
            {
                array_push($notesArray,
                    array('note_id' => $note->id,
                        'status_id' =>  in_array($note->status_id, $statusIds) ? $note->status_id : Null,
                        'users_id' => in_array($note->users_id, $usersIds) ? $note->users_id : Null
                    )
                );
            }

            DB::table('temporary_table_for_notes_date')->insert($notesArray);

            $skip += $limit; 
        }
    }
}