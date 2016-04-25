<?php

namespace App\Http\Middleware;

use Closure;
use App\User;


class EnterMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

       
        //echo "hello"."<br/>";
        //得到用户输入的用户名和密码
        $name=$request->input('name');
        //echo $name."<br/>";
        
        $password=$request->input('password');
        $user=User::where('name',$name)->first();
        //dd($user);
        //echo "hello"."<br/>";
       
        if(!empty($user)){
            if($password == $user->password){
                //登录成功
                $request->session()->put('name',$name);
                
                if($user->role_id==1){
                    //考生
                    $request->session()->put('role',1);
                    $request->session()->save();//写入session后，需要马上保存
                    return redirect('/userEvaluate');

                }else if($user->role_id == 2){
                    //阅卷人
                    $request->session()->put('role',2);
                    $request->session()->save();
                    return redirect('/userScorer');
                    
                }else if($user->role_id == 3){
                    //管理员
                    $request->session()->put('role',3);
                    $request->session()->save();
                    return redirect('/userManage');
 
                }else if($user->role_id==4){
                    //考生
                    $request->session()->put('role',4);
                    $request->session()->save();//写入session后，需要马上保存
                    //$sessions = $request->session()->all();
                    //dd($sessions);

                    //Session::put('role', 4); 
                    //Session::save();
                    
                    
                    return redirect('/userEvaluate');
                }else if($user->role_id == 5){
                    //考生
                    $request->session()->put('role',5);
                    $request->session()->save();//写入session后，需要马上保存
                    return redirect('/userEvaluate');
                }else if($user->role_id == 6){
                    //考生
                    $request->session()->put('role',6);
                    $request->session()->save();//写入session后，需要马上保存
                    return redirect('/userEvaluate');
                }else if($user->role_id == 7){
                    //考生
                    $request->session()->put('role',7);
                    $request->session()->save();//写入session后，需要马上保存
                    return redirect('/userEvaluate');
                }
            } else{
                //登录失败
               
                return redirect('/')->withInput()->withErrors('用户名密码不正确');
            }


        }else{
            //没查找到该用户
                     
            return redirect('/')->withInput()->withErrors('用户名密码不正确');
        }
        
        


        return $next($request);

    
    }

}
