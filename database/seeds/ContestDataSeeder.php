<?php

use Illuminate\Database\Seeder;
use App\Models\Contest;
use App\Models\Problem;
use App\Models\User;
use App\Models\Topic;
use App\Models\Status;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ContestDataSeeder extends Seeder
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

        $num=array();
        $i=1;
        while($i<100){
            $pid=$faker->randomElement($problems);
            $cid=$faker->randomElement($contests);
            $uid=$faker->randomElement($users);
            if(array_key_exists($cid,$num)===FALSE){
                $num[$cid]=1;
            }else{
                $num[$cid]++;
            }
            DB::insert('insert  IGNORE into contest_problem (contest_id,problem_id,keychar) values (?, ?,?)', [$cid, $pid,$num[$cid]]);
            DB::insert('insert  IGNORE into contest_user (contest_id,user_id) values (?, ?)', [$cid, $uid]);
            $i++;
        }
    }
}
