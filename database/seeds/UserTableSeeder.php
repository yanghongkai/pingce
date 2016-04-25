<?php

use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //填充初始数据
        DB::table('users')->insert([
        		['name'=>'student','password'=>'123456','role'=>1],
        		['name'=>'teacher','password'=>'123456','role'=>2],
        		['name'=>'administrator','password'=>'123456','role'=>3]
        	]);
    }
}
