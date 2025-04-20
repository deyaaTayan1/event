<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
  
    public function run()
    {
        $users = User::all();

        // foreach($users as $user){
        //     Event::factory()->create([
        //         'user_id' => $user->id
        //     ]);
        // } 

        for( $i=0 ; $i<200 ; $i++ ){
            $user = $users->random();
            Event::factory()->create([
                'user_id' => $user->id ,
            ]);
        }
    }
}
