<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Storage;

use App\Paper;
use App\User;
use App\UserPaper;
use App\ScorerPaper;
use \Exception;
use App\Resource;
use App\Role;

class PaperController extends Controller
{
     //
    public function __construct(){
    	$this->middleware('namese');
    }

    //测试删除功能
    public function delete(){
        $content='paper/39f34415c5e075d550146b5d6315cbb4.xml';
        Storage::delete($content);
    }

    public function paperNew(Request $request){
        $paperName=$request->input('paperName');
        if(empty($paperName)){
            //如果试卷名为空，则保存失败
            file_put_contents('dataTest.txt', 'paperName='.$paperName.'\r\n',FILE_APPEND);
            return response()->json(['success'=>false]);
        }
        $category=$request->input('subject');
        //file_put_contents('dataTest.txt', 'category='.$category.'\r\n',FILE_APPEND);
        if(empty($category)){
            //如果类别为空，则保存失败

            return response()->json(['success'=>false]);
        }
        $paper_exist=Paper::where('name',$paperName)->where('active',1)->first();
        if(!empty($paper_exist)){
            //如果非空，说明该记录已经存在
            //file_put_contents('dataTest.txt', '已存在'.'\r\n',FILE_APPEND);
            return response()->json(['success'=>false]);
        }

        //file_put_contents('dataTest.txt', $paperName.'\r\n',FILE_APPEND);
        $introduction=$request->input('introduction');
        //file_put_contents('dataTest.txt', $introduction.'\r\n',FILE_APPEND);
        $publisher=$request->session()->get('name');
        //file_put_contents('dataTest.txt', $publisher.'\r\n',FILE_APPEND);
        $content="";
        $answer="";
        
        if($request->session()->has('paper')){
            $content=$request->session()->get('paper');
            //file_put_contents('dataTest.txt', 'content='.$content.'\r\n',FILE_APPEND);
        }
        if($request->session()->has('answer')){
            $answer=$request->session()->get('answer');
            
        }

        if(empty($content) || empty($answer)){
            //如果试卷或答案为空，则保存失败
            //file_put_contents('dataTest.txt', 'answer='.$answer.'\r\n',FILE_APPEND);
            return response()->json(['success'=>false]);
        }

        
        try{
            $paper=new Paper;
            $paper->name=$paperName;
            $paper->introduction=$introduction;
            $paper->content=$content;
            $paper->answer=$answer;
            $paper->publisher=$publisher;
            $paper->category=$category;
            $paper->save();
        }catch(Exception $e){
            //需要删除已经上传的试题和答案
            if(!empty($content)){
                //上传了试题文件
                Storage::delete($content);
            }
            if(!empty($answer)){
                //上传了试题文件
                Storage::delete($answer);
            }
            $request->session()->forget('paper');
            $request->session()->forget('answer');
            return response()->json(['success'=>false]);

        }
         //存入之后，需要将session清空(有没有执行成功都需要执行)
        $request->session()->forget('paper');
        $request->session()->forget('answer');
       
        return response()->json(['success'=>true]);

    }

    public function paperEditTrue(Request $request){
        $id=$request->input('id');
        $introduction=$request->input('introduction');
        $paper=Paper::where('id',$id)->first();
        $content=$request->session()->get('paperEdit');
        $answer=$request->session()->get('answerEdit');
        
        
        $paper->content=$content;
        $paper->answer=$answer;
        $paper->introduction=$introduction;

        try{
            $paper->save();
        }catch(Exception $e){
            return response()->json(['success'=>false]);
        }
        $request->session()->forget('paperEdit');
        $request->session()->forget('answerEdit');
        return response()->json(['success'=>true]);
        
    }

    public function getUploadPaper(Request $request){
    	$postUrl='/uploadPaper';
    	$csrf_field=csrf_field();
    	$html=<<<CREATE
    	<form action="$postUrl" method="POST" enctype="multipart/form-data">
    	$csrf_field
    	<input type="file" name="file" ><br/><br/>
    	<input type="submit" value="提交"/>
    	</form>
CREATE;
	return $html;
    }

    

    public function postUploadPaper(Request $request){
    	//判断请求中是否包含name=uploadPaper的上传文件
    	if(!$request->hasFile('uploadPaper')){
    		//exit('上传文件为空!');
            return response()->json(['success'=>false]);
    	}
    	$file=$request->file('uploadPaper');
    	//判断文件上传过程中是否出错
    	if(!$file->isValid()){
    		//exit('文件上传出错!');
            return response()->json(['success'=>false]);
    	}
    	$newFileName=md5(time().rand(0,10000)).'.'.$file->getClientOriginalExtension();
    	//$newFileName=$file->getClientOriginalName();
    	$savePath='paper/'.$newFileName;
    	$bytes=Storage::put($savePath,file_get_contents($file->getRealPath()));
    	if(!Storage::exists($savePath)){
    		//exit('保存文件失败！');
            return response()->json(['success'=>false]);
    	}
        //将提交的文件的路径名保存到session中
        $request->session()->put('paper',$savePath);
        $request->session()->save();//写入session后，需要马上保存
        return response()->json(['success'=>true]);
    	
    	
    }

    //编辑页面中上传文件 试题文件session paperEdit
    public function postUploadPaperEdt(Request $request){
        //判断请求中是否包含name=uploadPaper的上传文件
        if(!$request->hasFile('uploadPaper')){
            //exit('上传文件为空!');
            return response()->json(['success'=>false]);
        }
        $file=$request->file('uploadPaper');
        //判断文件上传过程中是否出错
        if(!$file->isValid()){
            //exit('文件上传出错!');
            return response()->json(['success'=>false]);
        }
        $newFileName=md5(time().rand(0,10000)).'.'.$file->getClientOriginalExtension();
        //$newFileName=$file->getClientOriginalName();
        $savePath='paper/'.$newFileName;
        $bytes=Storage::put($savePath,file_get_contents($file->getRealPath()));
        if(!Storage::exists($savePath)){
            //exit('保存文件失败！');
            return response()->json(['success'=>false]);
        }


        //将提交的文件的路径名保存到session中
        $request->session()->put('paperEdit',$savePath);
        $request->session()->save();//写入session后，需要马上保存

        //判断试题文件和答案文件是否匹配
        if($request->session()->has('answerEdit')){
            $content_path=$request->session()->get('paperEdit');
            $content=simplexml_load_file('./content/'.$content_path);
            $content_name=(string)$content['name'];
            $answer_path=$request->session()->get('answerEdit');
            $answer=simplexml_load_file('./content/'.$answer_path);
            $answer_name=(string)$answer['papername'];
            //file_put_contents('./dataTest.txt', $content_name.'--'.$answer_name.'\r\n',FILE_APPEND);
            if($content_name != $answer_name){
                //清空之前的session
                $request->session()->forget('answer');
                //Storage::delete($answer);//不能清空，如果修改不成功还要保存原来的
                return response()->json(['success'=>false]);
            }
        }else{
            return response()->json(['success'=>false]);
        }

        return response()->json(['success'=>true]);

    }

    //上传资源
    
    public function postUploadResource(Request $request){
        //file_put_contents('./dataTest.txt', 'hello'.'\r\n',FILE_APPEND);
        //判断请求中是否包含name=uploadPaper的上传文件
        if(!$request->hasFile('uploadResource')){
            //exit('上传文件为空!');
            return response()->json(['success'=>false]);
        }
        $file=$request->file('uploadResource');
        //判断文件上传过程中是否出错
        if(!$file->isValid()){
            //exit('文件上传出错!');
            return response()->json(['success'=>false]);
        }
        $newFileName=md5(time().rand(0,10000)).'.'.$file->getClientOriginalExtension();
        //$newFileName=$file->getClientOriginalName();
        $savePath='res/'.$newFileName;
        $bytes=Storage::put($savePath,file_get_contents($file->getRealPath()));
        if(!Storage::exists($savePath)){
            //exit('保存文件失败！');
            return response()->json(['success'=>false]);
        }
        //将提交的文件的路径名保存到session中
        $request->session()->put('resource',$savePath);
        $request->session()->save();//写入session后，需要马上保存
        return response()->json(['success'=>true]);
    }
    

    //新建资源
    
    public function postResourceNew(Request $request){
        $title=$request->input('resName');
        if($request->session()->has('resource')){
            $content=$request->session()->get('resource');
        }
        $publisher=$request->session()->get('name');
        try{
            $res=new Resource;
            $res->title=$title;
            $res->content=$content;
            $res->publisher=$publisher;
            $res->save();
        }catch(Exception $e){
            return response()->json(['success'=>false]);
        }
        
        return response()->json(['success'=>true]);
        //return redirect('/manageResource');

    }

    //新建用户
    public function postUserNew(Request $request){
        $name=$request->input('userName');
        $newPassword=$request->input('newPassword');
        $conPassword=$request->input('conPassword');
        $role_name=$request->input('userStatus');
        if(empty($name) || empty($newPassword) || empty($conPassword) || empty($role_name)){
            return response()->json(['success'=>false]);
        }
        if($newPassword != $conPassword){
            return response()->json(['success'=>false]);
        }
        $role=Role::where('name',$role_name)->first();
        $role_id=$role->id;
        try{
            $user=new User;
            $user->name=$name;
            $user->password=$newPassword;
            $user->role_id=$role_id;
            $user->save();
        }catch(Exception $e){
            return response()->json(['success'=>false]);
        }
        return response()->json(['success'=>true]);
    }





    //上传试卷答案处理
    public function postUploadAnswer(Request $request){
        //判断请求中是否包含name=uploadAnswer的上传文件
        if(!$request->hasFile('uploadAnswer')){
            //exit('上传文件为空!');
            return response()->json(['success'=>false]);
        }
        $file=$request->file('uploadAnswer');
        //判断文件上传过程中是否出错
        if(!$file->isValid()){
            //exit('文件上传出错!');
            return response()->json(['success'=>false]);
        }
        $newFileName=md5(time().rand(0,10000)).'.'.$file->getClientOriginalExtension();
        //$newFileName=$file->getClientOriginalName();
        $savePath='answer/'.$newFileName;
        $bytes=Storage::put($savePath,file_get_contents($file->getRealPath()));
        if(!Storage::exists($savePath)){
            //exit('保存文件失败！');
            return response()->json(['success'=>false]);
        }
        
        
        

        //将提交的文件的路径名保存到session中
        $request->session()->put('answer',$savePath);
        $request->session()->save();//写入session后，需要马上保存

        //判断试题文件和答案文件是否匹配
        if($request->session()->has('paper')){
            $content_path=$request->session()->get('paper');
            $content=simplexml_load_file('./content/'.$content_path);
            $content_name=(string)$content['name'];
            $answer_path=$request->session()->get('answer');
            $answer=simplexml_load_file('./content/'.$answer_path);
            $answer_name=(string)$answer['papername'];
            file_put_contents('./dataTest.txt', $content_name.'--'.$answer_name.'\r\n',FILE_APPEND);
            if($content_name != $answer_name){
                //清空之前的session
                $request->session()->forget('answer');
                //Storage::delete($answer);
                return response()->json(['success'=>false]);
            }
        }else{
            return response()->json(['success'=>false]);
        }
        return response()->json(['success'=>true]);
    }

    //编辑页面中上传答案处理
    public function postUploadAnswerEdt(Request $request){

        
         //判断请求中是否包含name=uploadAnswer的上传文件
        if(!$request->hasFile('uploadAnswer')){
            //exit('上传文件为空!');
            return response()->json(['success'=>false]);
        }
        $file=$request->file('uploadAnswer');

        
        //判断文件上传过程中是否出错
        if(!$file->isValid()){
            //exit('文件上传出错!');
            return response()->json(['success'=>false]);
        }
        $newFileName=md5(time().rand(0,10000)).'.'.$file->getClientOriginalExtension();
        //$newFileName=$file->getClientOriginalName();
        $savePath='answer/'.$newFileName;
        $bytes=Storage::put($savePath,file_get_contents($file->getRealPath()));
        if(!Storage::exists($savePath)){
            //exit('保存文件失败！');
            return response()->json(['success'=>false]);
        }
        
        //将提交的文件的路径名保存到session中
        $request->session()->put('answerEdit',$savePath);
        $request->session()->save();//写入session后，需要马上保存

        
        //判断试题文件和答案文件是否匹配
        if($request->session()->has('paperEdit')){
            $content_path=$request->session()->get('paperEdit');
            $content=simplexml_load_file('./content/'.$content_path);
            $content_name=(string)$content['name'];
            $answer_path=$request->session()->get('answerEdit');
            $answer=simplexml_load_file('./content/'.$answer_path);
            $answer_name=(string)$answer['papername'];
            //file_put_contents('./dataTest.txt', $content_name.'--'.$answer_name.'\r\n',FILE_APPEND);
            if($content_name != $answer_name){
                //清空之前的session
                $request->session()->forget('answer');
                //Storage::delete($answer);//不能清空，如果修改不成功还要保存原来的
                return response()->json(['success'=>false]);
            }
        }else{
            return response()->json(['success'=>false]);
        }

        return response()->json(['success'=>true]);
        


    }
    //
    public function uploadUserAnswer(Request $request){
        $paperId=$request->input('id');
        
        //判断请求中是否包含name=user_answer的上传文件
        if(!$request->hasFile('user_answer')){
            //exit('上传文件为空!');
            return response()->json(['success'=>false]);
        }
        $file=$request->file('user_answer');
        //判断文件上传过程中是否出错
        if(!$file->isValid()){
            //exit('文件上传出错!');
            return response()->json(['success'=>false]);
        }
        $newFileName=md5(time().rand(0,10000)).'.'.$file->getClientOriginalExtension();
        //$newFileName=$file->getClientOriginalName();
        $savePath='user/'.$newFileName;
        $bytes=Storage::put($savePath,file_get_contents($file->getRealPath()));
        if(!Storage::exists($savePath)){
            //exit('保存文件失败！');
            return response()->json(['success'=>false]);
        }
        //当前时间
        $time=date("Y-m-d H:i:s",time());

        //判断试题文件和答案文件是否匹配
        $paper=Paper::where('id',$paperId)->first();
        $content_path=$paper->content;
        $content=simplexml_load_file('./content/'.$content_path);
        $content_name=(string)$content['name'];
        $answer=simplexml_load_file('./content/'.$savePath);
        $answer_name=(string)$answer['papername'];
        //file_put_contents('./dataTest.txt', $content_name.'--'.$answer_name.'\r\n',FILE_APPEND);
        if($content_name != $answer_name){
                return response()->json(['success'=>false]);
        }


        try{
            $userAnswer=$savePath;
            $name=$request->session()->get('name');
            $user=User::where('name',$name)->first();
            //attach方法插入没有时间戳
            //$user->papers()->attach($paperId,['userAnswer'=>$userAnswer]);
            $userPaper=new UserPaper;
            $userPaper->user_id=$user->id;
            $userPaper->paper_id=$paperId;
            $userPaper->userAnswer=$userAnswer;
            $userPaper->status="未评阅";
            $userPaper->time=$time;
            $userPaper->save();
        }catch(Exception $e){
            //插入数据失败，删除已经上传的用户答案
            Storage::delete($savePath);
            return response()->json(['success'=>false]);
        }
        //上传成功之后，用一个虚拟教师给他打分
        //经测试，可以得到$user_paper_id
        $user_paper_id=$userPaper->id;
        $paper_content_path=$paper->content;
        $paper_answer_path=$paper->answer;
        $user_answer_path=$userAnswer;
        $vteacher=User::where('name','vteacher')->first();
        $scorer_id=$vteacher->id;
        $flag=$this->createScorerPaperItem($paper_content_path,$paper_answer_path,$user_answer_path,$scorer_id,$user_paper_id);
        if($flag){
            file_put_contents('./dataTest.txt', 'flag='.$flag.'\r\n',FILE_APPEND);
        }else{
            return response()->json(['success'=>false]);
        }
        //file_put_contents('./dataTest.txt', 'user_paper_id='.$user_paper_id.'\r\n',FILE_APPEND);

        return response()->json(['success'=>true]);
    }

    //新建一个阅卷记录 $scorer为vteacher创建的虚拟教师
    public function createScorerPaperItem($paper_content_path,$paper_answer_path,$user_answer_path,$scorer_id,$user_paper_id){
        //试题
        $paper_content=simplexml_load_file('./content/'.$paper_content_path);
        //获得所有试题的question
        $arr_que=$paper_content->xpath('/paper//question');
        //dd($arr_que);

        //标准答案
        $paper_answer=simplexml_load_file('./content/'.$paper_answer_path);
        //dd($paper_answer);
        $arr_paper_answer=$paper_answer->xpath('/paperanswer//question');
        //dd($arr_paper_answer);

        //用户答案
        $user_answer=simplexml_load_file('./content/'.$user_answer_path);
        $arr_user_answer=$user_answer->xpath('/paperanswer//question');
        //dd($arr_user_answer);

        //客观题得分
        $object_grade=0.0;
        //默认生成一个只包含选择题的xml
        //新建一个detail_xml对象（保存教师的批改详情，选择题自动加入）
        $str='<?xml version="1.0" encoding="UTF-8"?>';
        $str.='<paperanswer></paperanswer>';
        $detail_xml=simplexml_load_string($str);
        

        //计算客观题分值
        for($i=0;$i<count($arr_paper_answer);$i++){
            if((string)$arr_paper_answer[$i]['type']=='select'){
                $paper_ans='';
                $user_ans='';
                $user_que_score='';//用户这道选择题的得分
                $paper_ans_texts=$arr_paper_answer[$i]->xpath('.//text');
                    foreach ($paper_ans_texts as $paper_ans_text){
                        $paper_ans_item=(string)$paper_ans_text;
                        $paper_ans.=$paper_ans_item;
                        //echo $paper_ans.' ';
                        //echo $paper_ans_text.' ';
                }
                $user_ans_texts=$arr_user_answer[$i]->xpath('.//text');
                    foreach ($user_ans_texts as $user_ans_text){
                        $user_ans_item=(string)$user_ans_text;
                        $user_ans.=$user_ans_item;
                }
                $score=(float)$arr_que[$i]['score'];
                if(empty($score)){
                    //如果没有分值默认为1
                    $score=1;
                }
                $id=$arr_que[$i]['id'];
                //echo $id.'--';
                //echo $paper_ans.'--';
                //echo $user_ans.'--';
                //echo $score.'--';
                if($paper_ans==$user_ans){
                    $user_que_score=$score;
                }else{
                    $user_que_score=0;
                }
                //echo $user_que_score;
                $object_grade+=$user_que_score;
                //新建节点
                $question=$detail_xml->addChild('question');
                $question->addAttribute('id',$id);
                $question->addAttribute('type','select');
                $question->addChild('text',$user_que_score);
                $question->addChild('comment','');//选择题不要备注， 

            }
            //echo '--------------------------------------<br/>';

        }
        //dd($detail_xml);
        //dd($object_grade);

        $count_que=count($arr_que);
        //echo $count_que."<br/>";
        //评论
        $comment="";

        //新建一条记录
        try{
            $scorer_paper=new ScorerPaper;
            $scorer_paper->user_id=$scorer_id;
            $scorer_paper->user_paper_id=$user_paper_id;
            //vteacher只是一个虚拟教师，并不修改试卷的评阅状态
            /*
            //只要新建记录，就将试卷的状态改为评阅中
            $user_paper=UserPaper::where('id',$user_paper_id)->first();
            $user_paper->status='评阅中';
            $user_paper->save();
            */

            //新建一个detail_xml对象
            $str_det_xml=$detail_xml->asXML();
            //dd($str_det_xml);
            //file_put_contents('./dataTest.txt', $str_det_xml.'\n',FILE_APPEND);
            //echo $str_det_xml.'<br/>';
            $scorer_paper->detail_xml=$str_det_xml;
            //题目总数
            $scorer_paper->count=$count_que;
            //客观题分数
            $scorer_paper->object_grade=$object_grade;
            $scorer_paper->save();

        }catch(Exception $e){
            return false;
        }
        return true;
        



    }



    //下载
    public function downloadPaper($id){
        $paper=Paper::where('id',$id)->first();
        $content=$paper->content;
        $name=$paper->name;
        $pathToFile=realpath(public_path('content')).'/'.$content;
        return response()->download($pathToFile,$name);

    }
    //下载内容，后面参数为下载路径
    public function downloadByPath(Request $request){
        $path=$request->input('path');
        $pathToFile=realpath(public_path('content')).'/'.$path;
        return response()->download($pathToFile);
    }

    //跳转阅卷页面
    public function paperScore($id,Request $request){
        $user_paper_id=$id;
        $user_paper=UserPaper::where('id',$user_paper_id)->first();
        //上传者姓名
        $stu_id=$user_paper->user_id;
        $stu=User::where('id',$stu_id)->first();
        $stu_name=$stu->name;
        //提交时间
        $user_paper_time=$user_paper->created_at;
        //用户提交的答案
        $user_answer_path=$user_paper->userAnswer;
        //dd($user_paper);

        //标准答案
        $paper_id=$user_paper->paper_id;
        $std_paper=Paper::where('id',$paper_id)->first();
        //试卷题目
        $paper_name=$std_paper->name;
        //echo $paper_name.'<br/>';
        //试卷类型
        $paper_category=$std_paper->category;
        //试题的内容和参考答案
        $paper_content_path=$std_paper->content;
        $paper_answer_path=$std_paper->answer;

        //dd($std_paper);

        //试题
        $paper_content=simplexml_load_file('./content/'.$paper_content_path);
        $arr_questions=$paper_content->xpath('/paper//questions');
        //echo count($arr_questions);//以语文试卷为例，8道题
        $arr_ques_head_text=array();
        $arr_ques_title=array();
        $arr_ques_text=array();
        $arr_ques_count=array();
        $arr_ques_score=array();
        foreach($arr_questions as $arr_question){
            //dd($arr_question);
            $questions_head_text=$arr_question->headtext;
            $questions_title=(string)$arr_question->title;
            $questions_text=(string)$arr_question->text;
            $arr_ques_head_text[]=(string)$questions_head_text;
            $arr_ques_title[]=$questions_title;
            $arr_ques_text[]=$questions_text;
            $arr_que_bel=$arr_question->xpath('./question');
            //dd($arr_que_bel);
            $ques_count=count($arr_que_bel);
            $arr_ques_count[]=$ques_count;
            //大题分值
            $arr_ques_score[]=$arr_question['score'];



        }
        //dd($arr_ques_title);
        //dd($arr_ques_head_text);
        //dd($arr_ques_count);


        //获得所有试题的question
        $arr_que=$paper_content->xpath('/paper//question');
        //dd($arr_que);
        //echo count($arr_que);
        //试卷id以试题的为准
        $std_que_ids=array();
        foreach($arr_que as $que){
            $std_que_ids[]=(string)$que['id'];
        }
        //dd($std_que_ids);
        
        /*
        foreach($arr_que as $que){
            if((string)$que['type']=='select'){
                $sel_options=$que->select->xpath('.//option');
                foreach($sel_options as $sel_option){
                    //echo $sel_option.'<br/>';
                    //echo $sel_option['value'].'<br/>';
                    //dd($sel_option);
                }
                //dd($sel_options);
            }
            if((string)$que['type']=='shortanswer'){
                //echo $que->text->asXML().'<br/>';
            }
        }
        */


        //echo $arr_que[0]['id'].'<br/>';
        //标准答案
        $paper_answer=simplexml_load_file('./content/'.$paper_answer_path);
        //dd($paper_answer);
        $arr_paper_answer=$paper_answer->xpath('/paperanswer//question');
        //dd($arr_paper_answer);

        //test
        /*
        foreach($arr_paper_answer as $paper_answer){
            $ans_texts=$paper_answer->xpath('.//text');
            foreach ($ans_texts as $ans_text){
                //echo $ans_text.'<br/>';
            }
        }
        */

        //test

        //用户答案
        $user_answer=simplexml_load_file('./content/'.$user_answer_path);
        $arr_user_answer=$user_answer->xpath('/paperanswer//question');
        //dd($arr_user_answer);
        /*
        for($i=0,$k=0;$i<count($arr_ques_title);$i++){
            //questions
            //echo 'i='.$i.'<br/>';
            //echo 'head_text='.$arr_ques_head_text[$i].'<br/>';
            //echo 'title='.$arr_ques_title[$i].'<br/>';
            //echo 'text='.$arr_ques_text[$i].'<br/>';
            
            for($j=0; $j<$arr_ques_count[$i] ;$j++,$k++){
                //question
                //echo 'j='.$j.'<br/>';
                //echo 'k='.$k.'<br/>';
                $que_id=(string)$arr_que[$k]['id'];
                $score=(string)$arr_que[$k]['score'];
                $paper_answer=(string)$arr_paper_answer[$k]->text;
                $user_answer=(string)$arr_paper_answer[$k]->text;
                //echo 'que_id='.$que_id.'<br/>';
                //echo 'paper_answer='.$paper_answer.'<br/>';
                //echo 'user_answer='.$user_answer.'<br/>';

            }
            
        }
        */

        //客观题得分
        $object_grade=0.0;
        //默认生成一个只包含选择题的xml
        //新建一个detail_xml对象（保存教师的批改详情，选择题自动加入）
        $str='<?xml version="1.0" encoding="UTF-8"?>';
        $str.='<paperanswer></paperanswer>';
        $detail_xml=simplexml_load_string($str);
        

        //计算客观题分值
        for($i=0;$i<count($arr_paper_answer);$i++){
            if((string)$arr_paper_answer[$i]['type']=='select'){
                $paper_ans='';
                $user_ans='';
                $user_que_score='';//用户这道选择题的得分
                $paper_ans_texts=$arr_paper_answer[$i]->xpath('.//text');
                    foreach ($paper_ans_texts as $paper_ans_text){
                        $paper_ans_item=(string)$paper_ans_text;
                        $paper_ans.=$paper_ans_item;
                        //echo $paper_ans.' ';
                        //echo $paper_ans_text.' ';
                }
                $user_ans_texts=$arr_user_answer[$i]->xpath('.//text');
                    foreach ($user_ans_texts as $user_ans_text){
                        $user_ans_item=(string)$user_ans_text;
                        $user_ans.=$user_ans_item;
                }
                $score=(float)$arr_que[$i]['score'];
                if(empty($score)){
                    //如果没有分值默认为1
                    $score=1;
                }
                $id=$arr_que[$i]['id'];
                //echo $id.'--';
                //echo $paper_ans.'--';
                //echo $user_ans.'--';
                //echo $score.'--';
                if($paper_ans==$user_ans){
                    $user_que_score=$score;
                }else{
                    $user_que_score=0;
                }
                //echo $user_que_score;
                $object_grade+=$user_que_score;
                //新建节点
                $question=$detail_xml->addChild('question');
                $question->addAttribute('id',$id);
                $question->addAttribute('type','select');
                $question->addChild('text',$user_que_score);
                $question->addChild('comment','');//选择题不要备注， 

            }
            //echo '--------------------------------------<br/>';

        }
        //dd($detail_xml);
        //dd($object_grade);

        //先在数据库中插入一条记录教师用户试卷记录，用于保存教师已经改过的题scorer_paper
        //用户id
        $scorer_name=$request->session()->get('name');
        //echo $scorer_name;
        $scorer=User::where('name',$scorer_name)->first();
        $scorer_id=$scorer->id;
        //echo $scorer_id;
        $count_que=count($arr_que);
        //echo $count_que."<br/>";
        //评论
        $comment="";

        $scorer_paper=ScorerPaper::where('user_id',$scorer_id)->where('user_paper_id',$user_paper_id)->first();
        //dd($scorer_paper);
        if(empty($scorer_paper)){
            //新建一条记录
            $scorer_paper=new ScorerPaper;
            $scorer_paper->user_id=$scorer_id;
            $scorer_paper->user_paper_id=$user_paper_id;
            //只要新建记录，就将试卷的状态改为评阅中
            $user_paper=UserPaper::where('id',$user_paper_id)->first();
            $user_paper->status='评阅中';
            $user_paper->save();

            //新建一个detail_xml对象
            $str_det_xml=$detail_xml->asXML();
            //dd($str_det_xml);
            //file_put_contents('./dataTest.txt', $str_det_xml.'\n',FILE_APPEND);
            //echo $str_det_xml.'<br/>';
            $scorer_paper->detail_xml=$str_det_xml;
            //题目总数
            $scorer_paper->count=$count_que;
            //客观题分数
            $scorer_paper->object_grade=$object_grade;
            $scorer_paper->save();

            //如果为空也要传递$tea_save_anws数组
            $detail_xml=simplexml_load_string($str_det_xml);
            $tea_save_anws=array();
            $tea_save_coms=array();
            for($i=0;$i<count($std_que_ids);$i++){
                $que_id=$std_que_ids[$i];
                $ans_save=$detail_xml->xpath("//paperanswer/question[@id='$que_id']");
                if(count($ans_save)>0){
                    $tea_save_anws[]=(float)$ans_save[0]->text;
                    $tea_save_coms[]=$ans_save[0]->comment;
                }else{
                    $tea_save_anws[]='';
                    $tea_save_coms[]='';
                }
            }
        }else{
            //如果不空的话，获取用户上次保存的批改结果
            $str_det_xml=$scorer_paper->detail_xml;
            $detail_xml=simplexml_load_string($str_det_xml);
            $tea_save_anws=array();
            $tea_save_coms=array();

            for($i=0;$i<count($std_que_ids);$i++){
                $que_id=$std_que_ids[$i];
                $ans_save=$detail_xml->xpath("//paperanswer/question[@id='$que_id']");
                if(count($ans_save)>0){
                    $tea_save_anws[]=(float)$ans_save[0]->text;
                    $tea_save_coms[]=$ans_save[0]->comment;
                }else{
                    $tea_save_anws[]='';
                    $tea_save_coms[]='';

                }
            }
            //如果保存有用户的选择题答案，从数据库中读出用户的选择题答案
            if($scorer_paper->submit==1){//说明教师提交了批改结果
                $object_grade=$scorer_paper->object_grade;
            }
            
        }
        $comment=$scorer_paper->comment;
        //dd($tea_save_anws);
        //dd($user_paper_id);
        //dd($scorer_id);
        //echo $user_paper_id.'--'.$scorer_id.'<br/>';
        //dd($tea_save_coms);

        

        
        



         



        
        return view('paperScore',['paper_name'=>$paper_name,'stu_name'=>$stu_name,'user_paper_time'=>$user_paper_time,'user_paper_id'=>$user_paper_id,
                                   'scorer_id'=>$scorer_id,'arr_ques_head_text'=>$arr_ques_head_text,'arr_ques_title'=>$arr_ques_title,'arr_ques_text'=>$arr_ques_text,
                                    'arr_ques_count'=>$arr_ques_count,'arr_que'=>$arr_que,'arr_paper_answer'=>$arr_paper_answer,
                                    'arr_user_answer'=>$arr_user_answer,'arr_ques_score'=>$arr_ques_score,'comment'=>$comment,
                                    'tea_save_anws'=>$tea_save_anws,'tea_save_coms'=>$tea_save_coms
                                    ]);
        
        
       
    }


    //删除试卷，软删除，acitve=0
    public function paperDelete($id){
        $paper=Paper::where('id',$id)->first();
        $paper->active=0;
        $paper->save();
        return redirect('/userManage');
    }
    //保存批改的每道题的结果
    public function answerSave(Request $request){

       
        $scorer_id=$request->input('scorer_id');
        //file_put_contents('./dataTest.txt', $scorer_id.'\n',FILE_APPEND);
        $user_paper_id=$request->input('user_paper_id');
        $question_id=$request->input('question_id');
        //试题备注queText
        $que_text=$request->input('queText');
        //file_put_contents('./dataTest.txt', $que_text.'\r\n',FILE_APPEND);
        //用户试题得分
        $question_scorer=$request->input('paperName_scorer');
        //0,'',empty()都是true
        if(empty($question_scorer) && $question_scorer===''){
            //如果某道题为给出分值，则保存不成功
            return response()->json(['success'=>false,'id'=>$question_id]);
        }
        //如果试卷得分非数值也要报错
        $pattern='/^[0-9]{1,}$/';
        $num=preg_match($pattern, $question_scorer,$matches);
        if($num<1){
            return response()->json(['success'=>false,'id'=>$question_id]);
        }
        //试题分值
        $question_score=$request->input('question_score');
        //判断某道题的分值是否存在
        if(!empty($question_score) && $question_score>0){
            //如果分值不存在，则不进行判断
            //如果某道题得分超过了该题的分值，错误
            if($question_scorer>$question_score){
                return response()->json(['success'=>false,'id'=>$question_id]);
            }

        }
        
        //file_put_contents('./dataTest.txt', $question_scorer.'\n',FILE_APPEND);
        //file_put_contents('./dataTest.txt', $scorer_id.'--'.$user_paper_id.'--'.$question_id.'--'.$question_scorer.'\n',FILE_APPEND);
        //从scorer_paper表中找出该老师批改的该试卷的以前保留的详情信息
        $scorer_paper=ScorerPaper::where('user_id',$scorer_id)->where('user_paper_id',$user_paper_id)->first();
        $str_det_xml=$scorer_paper->detail_xml;
        $detail_xml=simplexml_load_string($str_det_xml);
        //先确定<question id="">存在吗
        $question_exist=$detail_xml->xpath("/paperanswer/question[@id='$question_id']");
        if(count($question_exist)>0){
            //存在的话，修改分值
            $question_exist[0]->text=$question_scorer;
            $question_exist[0]->comment=$que_text;

        }else{
            //新建节点
            $question=$detail_xml->addChild('question');
            $question->addAttribute('id',$question_id);
            $question->addChild('text',$question_scorer);
            $question->addChild('comment',$que_text); 
        }
        
        $str_new_det=$detail_xml->asXML();
        //file_put_contents('./dataTest.txt', $str_new_det.'\n',FILE_APPEND);
        $scorer_paper->detail_xml=$str_new_det;
        
        $scorer_paper->save();
        //user_paper状态变为评阅中
        $user_paper=UserPaper::where('id',$user_paper_id)->first();
        if($user_paper->status!='已评阅'){
            //已经提交了试卷批改结果，再一次修改某一道题，状态也保持是“已评阅”
            $user_paper->status="评阅中";
        }
        
        $user_paper->save();
        return response()->json(['success'=>true,'id'=>$question_id]);



    }

    //保存试卷的备注信息
    public function commentSave(Request $request){
        $comment=$request->input('comment');
        $scorer_id=$request->input('scorer_id');
        $user_paper_id=$request->input('user_paper_id');
        $scorer_paper=ScorerPaper::where('user_id',$scorer_id)->where('user_paper_id',$user_paper_id)->first();
        $scorer_paper->comment=$comment;
        try{
            $scorer_paper->save();
        }catch(Exception $e){
            return response()->json(['success'=>false]);
        }
        return response()->json(['success'=>true]);

    }

    //保存试卷的总成绩
    public function gradeSave(Request $request){
        $scorer_id=$request->input('scorer_id');
        $user_paper_id=$request->input('user_paper_id');
        $scorer_paper=ScorerPaper::where('user_id',$scorer_id)->where('user_paper_id',$user_paper_id)->first();
        $str_det_xml=$scorer_paper->detail_xml;
        $detail_xml=simplexml_load_string($str_det_xml);
        //计算总分
        $total_grade=0.0;
        $question_anws=$detail_xml->xpath('//question');
        //file_put_contents('./dataTest.txt', count($question_anws).'\r\n',FILE_APPEND);
        if(count($question_anws)<$scorer_paper->count){
            return response()->json(['success'=>false]);
        }
        if(count($question_anws)==$scorer_paper->count){
            for($i=0;$i<count($question_anws);$i++){
                $question_anw=$question_anws[$i];
                $que_scorer=(float)$question_anw->text;
                //echo $que_scorer.'<br/>';
                //dd($que_scorer);
                $total_grade+=$que_scorer;
            }
        }
        //echo $total_grade.'<br/>';
        //客观题得分
        $object_grade=0.0;
        $question_sels=$detail_xml->xpath("//question[@type='select']");
        for($i=0;$i<count($question_sels);$i++){
            $que_sel_score=(float)$question_sels[$i]->text;
            $object_grade+=$que_sel_score;
        }
        //$object_grade=$scorer_paper->object_grade;
        $scorer_paper->object_grade=$object_grade;
        //主观题得分
        $subject_grade=$total_grade-$object_grade;
        $scorer_paper->subject_grade=$subject_grade;
        $scorer_paper->grade=$total_grade;
        //确定提交了总成绩，结束这份试卷的批阅
        $scorer_paper->submit=1;
        $scorer_paper->save();
        //user_paper状态变为已评阅
        $user_paper=UserPaper::where('id',$user_paper_id)->first();
        $user_paper->status="已评阅";
        $user_paper->save();
        return response()->json(['success'=>true]);
        //return redirect('/userScorer');

    }




}
