<?php

use Illuminate\Database\Seeder;
use App\Models\Contest;
use App\Models\Problem;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ContestProblemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = app(Faker\Generator::class);
        $contests=Contest::all()->pluck('id')->toArray();
        $problems=Problem::all()->pluck('id')->toArray();
        $users=User::all()->pluck('id')->toArray();
        $i=1;
        while($i<2000){
            $pid=$faker->randomElement($problems);
            $cid=$faker->randomElement($contests);
            $uid=$faker->randomElement($users);
            DB::insert('insert into contest_problem (contest_id,problem_id) values (?, ?)', [$cid, $pid]);
            DB::insert('insert into contest_user (contest_id,user_id) values (?, ?)', [$cid, $uid]);
            $i++;
        }

    }
}
