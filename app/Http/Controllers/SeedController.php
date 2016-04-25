<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Role;
use App\User;

class SeedController extends Controller
{
    //
    //插入一条roles表记录
    public function getInsertRole(){
    	
		$role=new Role;
		$role->name='math';
		$role->save();

    }
    //插入users表的一条记录
    public function getInsertUser(Request $request){
        $name=$request->input('name');
        //默认密码为123456
        $password='123456';
        //角色
        $role_id=$request->input('role_id');
    	$user=new User(['name'=>$name,'password'=>$password]);
    	$role=Role::find($role_id);
    	$user=$role->users()->save($user);
    }
    //测试时间格式
    public function getTime(){
        //当前时间
        $time=date("Y-m-d H:i:s",time());
        
    }
}
