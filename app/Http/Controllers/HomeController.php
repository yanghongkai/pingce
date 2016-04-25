<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\News;

class HomeController extends Controller
{
    //不需要验证是否登录
    //当用户已经登录，/显示的是logout
    //当用户未登录，/显示的是login
    public function home(Request $request){
        $news=News::where('active',1)->orderBy('updated_at','desc')->get();
    	if($request->session()->has('name')){
    		return view('indexOut',['news'=>$news]);
    	}else{//未登录
    		return view('index',['news'=>$news]);
    	}
    }
    /*
    //用户退出
    public function postLogout(Request $request){
    	//清楚所有的session
    	//删除所有的sesion
    	$request->session()->flush();
    	return redirect('/');
    }
    */
    //新闻
    public function accNews($id){
        $news=News::where('id',$id)->first();
        return view('news',['news'=>$news]);
    }
}
