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
        file_put_contents('./dataTest.txt', $content_name.'--'.$answer_name.'\r\n',FILE_APPEND);
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
        

        return response()->json(['success'=>true]);
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
        //选择题
        //$selectSections=$shortSections=$paper_content->xpath("/paper/section[@name='选择题']");
        //$selectSections=$paper_content->xpath("/paper//section[contains(@name,'选择') or contains(@name,'单选')]");
        //第一道题是选择题
        $selectSections=$paper_content->xpath("/paper/section[1]");
        //dd($selectSections);
        $selectSection=$selectSections[0];
        //dd($selectSection);
        //选择题的总分值
        try{
            $score_total_sel=$selectSection['score'];
        }catch(Exception $e){
            //如果没有score这个属性的话，把分值设置为0.0
            $score_total_sel=0.0;
        }
        
        //$select_num选择题的数目
        $select_num=0;
        $arr_sel_que=$selectSection->xpath(".//question");
        $select_num=count($arr_sel_que);
        //分值数组
        $std_sele_score=array();
        foreach ($arr_sel_que as $arr_que){
            try{
                $score_temp=(string)$arr_que['score'];
                if(empty($score_temp)){
                    $score_temp=1;//如果没有分值，则默认为1分
                }
            }catch(Exception $e){
                //实际上执行不到这一步，是为了代码的健壮性
                $score_temp=1;
            }
            
            $std_sele_score[]=$score_temp;
        }
        //dd($std_sele_score);


        //标准答案
        $paper_answer=simplexml_load_file('./content/'.$paper_answer_path);
        //dd($paper_answer);
        $std_total_answer=$paper_answer->children();

        //dd($std_total_answer);
        //对答案需要进行预处理<text>氓之蚩蚩</text><text>抱布贸丝</text>这种情况应该合并为一个
        foreach($std_total_answer as $question_answer){
            
            $texts=$question_answer->children();
            //$ans=(string)$question_answer->text;
            
            if(count($texts)>1){
                //dd($question_answer);
                //如果是填空题则多个答案连接在一起
                if((string)$question_answer['type']=='select'){
                    $ans_text="";
                    foreach($texts as $text){

                        $ans_text.=(string)$text;
                    }
                    //将多个text合并为一个
                    //删除text节点
                    unset($question_answer->text);
                    //dd($question_answer);
                    //新增text节点
                    $question_answer->addChild('text',$ans_text);
                    //$question_answer->text=$ans_text;
                    //dd($question_answer);

                }else{
                    //fillbank等类型多个答案之间则以空格隔开
                    $ans_text="";
                    foreach($texts as $text){

                        $ans_text.=(string)$text;
                        $ans_text.=' ';
                    }
                    unset($question_answer->text);
                    $question_answer->addChild('text',$ans_text);

                }
                
            }
               
        }
        //dd($std_total_answer);
        //预处理结束


        //选择题答案
        $std_sele_answer=array();
        for($i=0;$i<$select_num;$i++){
            $std_sele_answer[]=(string)$std_total_answer[$i]->text;
        }
        //将选择题答案数组转换成字符串
        $str_std_sele_answer=implode(' ', $std_sele_answer);
        //echo $str_std_sele_answer;

        //用户答案
        $user_answer=simplexml_load_file('./content/'.$user_answer_path);
        //dd($user_answer); 
        $user_total_answer=$user_answer->children();

        //对答案需要进行预处理<text>氓之蚩蚩</text><text>抱布贸丝</text>这种情况应该合并为一个
        foreach($user_total_answer as $question_answer){
            
            $texts=$question_answer->children();
            if(count($texts)>1){
                //dd($question_answer);
                //如果是填空题则多个答案连接在一起
                if((string)$question_answer['type']=='select'){
                    $ans_text="";
                    foreach($texts as $text){

                        $ans_text.=(string)$text;
                    }
                    unset($question_answer->text);
                    $question_answer->addChild('text',$ans_text);

                }else{
                    //fillbank等类型多个答案之间则以空格隔开
                    $ans_text="";
                    foreach($texts as $text){

                        $ans_text.=(string)$text;
                        $ans_text.=' ';
                    }
                    unset($question_answer->text);
                    $question_answer->addChild('text',$ans_text);

                }
                
            }
               
        }
        //dd($user_total_answer);
        //预处理结束



        //用户选择题答案
        $user_sele_answer=array();
        for($i=0;$i<$select_num;$i++){
            $user_sele_answer[]=(string)$user_total_answer[$i]->text;
        }
        //用户的选择题答案
        $str_user_sele_answer=implode(' ', $user_sele_answer);
        //选择题分值
        $select_grade=0.0;
        //用户选择题对了几道
        $cor_sele_num=0;
        for($i=0;$i<$select_num;$i++){
            if($std_sele_answer[$i]==$user_sele_answer[$i]){
                $select_grade+=$std_sele_score[$i];
                $cor_sele_num++;
            }
        }
        //echo $select_grade;
        //echo $cor_sele_num.'<br/>';
        //用户选择题的分
        /*
        $select_grade=(float)$score_total_sel/($select_num*1.0)*$cor_sele_num*1.0;
        $select_grade=round($select_grade*1.0,1);
        */
        if($score_total_sel<=0){
            //如果选择题没有设置分值，就以‘--’处理
            $select_grade='--';
            $score_total_sel='--';
        }
        
        //地理和历史的试卷格式是一样的
        //以后几个学科可能需要单独处理
        if($paper_category=='geography' || $paper_category=='history'){
            //简答题试题
            //简答题
            //$shortSections=$paper_content->xpath("/paper/section[contains(@name,'综合')]");
            //除选择题之外的题作为一道题处理
            $shortSections=$paper_content->xpath("/paper/section[position()>1]");
            //dd($shortSections);
            $shortSection=$shortSections[0];
            //dd($shortSection);
            $questions_arr=$shortSection->children();

            //dd($questions_arr);
            //从当前路径开始查找
            $question_arr=$shortSection->xpath(".//question");
            //dd($question_arr);
            
            //试题简答题数组
            $std_short_answers=array();
            //分值数组
            $std_scores=array();
            //试题的id数组
            $std_que_ids=array();
            //标记数组 0->该题分值 1->该小题所在大题的分值
            $questions_flag=array();
            foreach ($question_arr as $question){
            if((string)$question['type']=='fillblank'){
                //echo $question['id'].'--'.$question['score'].'--'.$question->blank.'<br/>';
                $std_short_answers[]=$question->blank->asXML();
                try{
                    //当不存在score这个属性时，并不会报错，而是为空字符串
                    $score=(string)$question['score'];
                    $flag=0;//表示该小题有分值
                    if(empty($score)){
                        //如果该小题没有分值，则用大题的分值代替
                        $que_par_arr=$question->xpath('parent::questions');
                        $que_par=$que_par_arr[0];
                        //dd($que_par);
                        $score=(string)$que_par['score'];
                        $flag=1;//表示该小题的分值是大题的分值
                        
                    }
                    $std_scores[]=$score;
                }catch(Exception $e){
                    $std_scores[]="";
                }
                
                $std_que_ids[]=(string)$question['id'];
                $questions_flag[]=$flag;

                }

            if((string)$question['type']=='shortanswer'){
                //echo $question['id'].'--'.$question['score'].'--'.$question->text.'<br/>';

                $std_short_answers[]=$question->text->asXML();
                //$std_scores[]=(string)$question['score'];
                try{
                    //当不存在score这个属性时，并不会报错，而是为空字符串
                    $score=(string)$question['score'];
                    $flag=0;//表示该小题有分值
                    if(empty($score)){
                        //如果该小题没有分值，则用大题的分值代替
                        $que_par_arr=$question->xpath('parent::questions');
                        $que_par=$que_par_arr[0];
                        //dd($que_par);
                        $score=(string)$que_par['score'];
                        $flag=1;//表示该小题的分值是大题的分值
                        
                    }
                    $std_scores[]=$score;
                }catch(Exception $e){
                    $std_scores[]="";
                }
                $std_que_ids[]=(string)$question['id'];
                $questions_flag[]=$flag;
            }

            if((string)$question['type']=='select'){
                //echo $question['id'].'--'.$question['score'].'--'.$question->text.'<br/>';
                $std_short_answers[]=$question->text->asXML();
                //$std_scores[]=(string)$question['score'];
                try{
                    //当不存在score这个属性时，并不会报错，而是为空字符串
                    $score=(string)$question['score'];
                    $flag=0;//表示该小题有分值
                    if(empty($score)){
                        //如果该小题没有分值，则用大题的分值代替
                        $que_par_arr=$question->xpath('parent::questions');
                        $que_par=$que_par_arr[0];
                        //dd($que_par);
                        $score=(string)$que_par['score'];
                        $flag=1;//表示该小题的分值是大题的分值
                        
                    }
                    $std_scores[]=$score;
                }catch(Exception $e){
                    $std_scores[]="";
                }
                $std_que_ids[]=(string)$question['id'];
                $questions_flag[]=$flag;
            }

        }

    }
        //处理语文试卷
        if($paper_category=='chinese'){
            //简答题试题
            //除选择题之外的题作为一道题处理
            $shortSections=$paper_content->xpath("/paper/section[position()>1]");
            //dd($shortSections);
            //试题简答题数组
            $std_short_answers=array();
            //分值数组
            $std_scores=array();
            //试题的id数组
            $std_que_ids=array();
            //标记数组 0->该题分值 1->该小题所在大题的分值
            $questions_flag=array();
            //语文和数学会有多个section
            for($i=0;$i<count($shortSections);$i++){
                $shortSection=$shortSections[$i];
                //dd($shortSection);
                $questions_arr=$shortSection->children();

                //dd($questions_arr);
                //从当前路径开始查找
                $question_arr=$shortSection->xpath(".//question");
                //dd($question_arr);
                
               
                foreach ($question_arr as $question){
                    if((string)$question['type']=='fillblank'){
                        //echo $question['id'].'--'.$question['score'].'--'.$question->blank.'<br/>';
                        //填空题可能有多个

                        $std_short_answers[]=$question->blank->asXML();
                        try{
                            //当不存在score这个属性时，并不会报错，而是为空字符串
                            $score=(string)$question['score'];
                            $flag=0;//表示该小题有分值
                            if(empty($score)){
                                //如果该小题没有分值，则用大题的分值代替
                                $que_par_arr=$question->xpath('parent::questions');
                                $que_par=$que_par_arr[0];
                                //dd($que_par);
                                $score=(string)$que_par['score'];
                                $flag=1;//表示该小题的分值是大题的分值
                                
                            }
                            $std_scores[]=$score;
                        }catch(Exception $e){
                            $std_scores[]="";
                        }
                        
                        $std_que_ids[]=(string)$question['id'];
                        $questions_flag[]=$flag;

                    }

                    if((string)$question['type']=='shortanswer'){
                        $std_short_answers[]=$question->text->asXML();
                        //$std_scores[]=(string)$question['score'];
                        try{
                            //当不存在score这个属性时，并不会报错，而是为空字符串
                            $score=(string)$question['score'];
                            $flag=0;//表示该小题有分值
                            if(empty($score)){
                                //如果该小题没有分值，则用大题的分值代替
                                $que_par_arr=$question->xpath('parent::questions');
                                $que_par=$que_par_arr[0];
                                //dd($que_par);
                                $score=(string)$que_par['score'];
                                $flag=1;//表示该小题的分值是大题的分值
                                
                            }
                            $std_scores[]=$score;
                            
                        }catch(Exception $e){
                            //并不会执行到这里，当不存在score这个属性时，并不会报错，而是为空字符串
                            $std_scores[]="";
                        }
                        $std_que_ids[]=(string)$question['id'];
                        $questions_flag[]=$flag;
                    }

                    if((string)$question['type']=='select'){
                        
                        //$std_short_answers[]=(string)$question->text;
                        $std_short_answers[]=$question->text->asXML();
                        //$std_scores[]=(string)$question['score'];
                        try{
                            //当不存在score这个属性时，并不会报错，而是为空字符串
                            $score=(string)$question['score'];
                            $flag=0;//表示该小题有分值
                            if(empty($score)){
                                //如果该小题没有分值，则用大题的分值代替
                                $que_par_arr=$question->xpath('parent::questions');
                                $que_par=$que_par_arr[0];
                                //dd($que_par);
                                $score=(string)$que_par['score'];
                                $flag=1;//表示该小题的分值是大题的分值
                                
                            }
                            $std_scores[]=$score;
                        }catch(Exception $e){
                            $std_scores[]="";
                        }
                        $std_que_ids[]=(string)$question['id'];
                        $questions_flag[]=$flag;
                    }

                    if((string)$question['type']=='punctuation'){
                        //$std_short_answers[]=(string)$question->passage;
                        $std_short_answers[]=$question->passage->asXML();
                        try{
                            //当不存在score这个属性时，并不会报错，而是为空字符串
                            $score=(string)$question['score'];
                            $flag=0;//表示该小题有分值
                            if(empty($score)){
                                //如果该小题没有分值，则用大题的分值代替
                                $que_par_arr=$question->xpath('parent::questions');
                                $que_par=$que_par_arr[0];
                                //dd($que_par);
                                $score=(string)$que_par['score'];
                                $flag=1;//表示该小题的分值是大题的分值
                                
                            }
                            $std_scores[]=$score;
                        }catch(Exception $e){
                            $std_scores[]="";
                        }
                        $std_que_ids[]=(string)$question['id'];
                        $questions_flag[]=$flag;
                    }

                    //作文
                    if((string)$question['type']=='composition'){
                        $std_short_answers[]=$question->text->asXML();
                        try{
                            //当不存在score这个属性时，并不会报错，而是为空字符串
                            $score=(string)$question['score'];
                            $flag=0;//表示该小题有分值
                            if(empty($score)){
                                //如果该小题没有分值，则用大题的分值代替
                                $que_par_arr=$question->xpath('parent::questions');
                                $que_par=$que_par_arr[0];
                                //dd($que_par);
                                $score=(string)$que_par['score'];
                                $flag=1;//表示该小题的分值是大题的分值
                                
                            }
                            $std_scores[]=$score;
                        }catch(Exception $e){
                            $std_scores[]="";
                        }
                        $std_que_ids[]=(string)$question['id'];
                        $questions_flag[]=$flag;
                    }


                }//foreach

            }
            
        }
        //语文试卷处理结束


        //处理数学试卷
        if($paper_category=='math'){
            //简答题试题
            //除选择题之外的题作为一道题处理
            $shortSections=$paper_content->xpath("/paper/section[position()>1]");
            //dd($shortSections);
            //试题简答题数组
            $std_short_answers=array();
            //分值数组
            $std_scores=array();
            //试题的id数组
            $std_que_ids=array();
            //标记数组 0->该题分值 1->该小题所在大题的分值
            $questions_flag=array();
            //语文和数学会有多个section
            for($i=0;$i<count($shortSections);$i++){
                $shortSection=$shortSections[$i];
                //dd($shortSection);
                $questions_arr=$shortSection->children();

                //dd($questions_arr);
                //从当前路径开始查找
                $question_arr=$shortSection->xpath(".//question");
                //dd($question_arr);
                
               
                foreach ($question_arr as $question){
                    if((string)$question['type']=='fillblank'){
                        //echo $question['id'].'--'.$question['score'].'--'.$question->blank.'<br/>';
                        //填空题可能有多个

                        $std_short_answers[]=$question->blank->asXML();
                        try{
                            //当不存在score这个属性时，并不会报错，而是为空字符串
                            $score=(string)$question['score'];
                            $flag=0;//表示该小题有分值
                            if(empty($score)){
                                //如果该小题没有分值，则用大题的分值代替
                                $que_par_arr=$question->xpath('parent::questions');
                                $que_par=$que_par_arr[0];
                                //dd($que_par);
                                $score=(string)$que_par['score'];
                                $flag=1;//表示该小题的分值是大题的分值
                                
                            }
                            $std_scores[]=$score;
                        }catch(Exception $e){
                            $std_scores[]="";
                        }
                        
                        $std_que_ids[]=(string)$question['id'];
                        $questions_flag[]=$flag;

                    }

                    if((string)$question['type']=='shortanswer'){
                        $std_short_answers[]=$question->text->asXML();
                        //$std_scores[]=(string)$question['score'];
                        try{
                            //当不存在score这个属性时，并不会报错，而是为空字符串
                            $score=(string)$question['score'];
                            $flag=0;//表示该小题有分值
                            if(empty($score)){
                                //如果该小题没有分值，则用大题的分值代替
                                $que_par_arr=$question->xpath('parent::questions');
                                $que_par=$que_par_arr[0];
                                //dd($que_par);
                                $score=(string)$que_par['score'];
                                $flag=1;//表示该小题的分值是大题的分值
                                
                            }
                            $std_scores[]=$score;
                            
                        }catch(Exception $e){
                            //并不会执行到这里，当不存在score这个属性时，并不会报错，而是为空字符串
                            $std_scores[]="";
                        }
                        $std_que_ids[]=(string)$question['id'];
                        $questions_flag[]=$flag;
                    }

                    if((string)$question['type']=='select'){
                        //echo $question['id'].'--'.$question['score'].'--'.$question->text.'<br/>';
                        $std_short_answers[]=$question->text->asXML();
                        //$std_scores[]=(string)$question['score'];
                        try{
                            //当不存在score这个属性时，并不会报错，而是为空字符串
                            $score=(string)$question['score'];
                            $flag=0;//表示该小题有分值
                            if(empty($score)){
                                //如果该小题没有分值，则用大题的分值代替
                                $que_par_arr=$question->xpath('parent::questions');
                                $que_par=$que_par_arr[0];
                                //dd($que_par);
                                $score=(string)$que_par['score'];
                                $flag=1;//表示该小题的分值是大题的分值
                                
                            }
                            $std_scores[]=$score;
                        }catch(Exception $e){
                            $std_scores[]="";
                        }
                        $std_que_ids[]=(string)$question['id'];
                        $questions_flag[]=$flag;
                    }

                    if((string)$question['type']=='punctuation'){
                        $std_short_answers[]=$question->passage->asXML();
                        try{
                            //当不存在score这个属性时，并不会报错，而是为空字符串
                            $score=(string)$question['score'];
                            $flag=0;//表示该小题有分值
                            if(empty($score)){
                                //如果该小题没有分值，则用大题的分值代替
                                $que_par_arr=$question->xpath('parent::questions');
                                $que_par=$que_par_arr[0];
                                //dd($que_par);
                                $score=(string)$que_par['score'];
                                $flag=1;//表示该小题的分值是大题的分值
                                
                            }
                            $std_scores[]=$score;
                        }catch(Exception $e){
                            $std_scores[]="";
                        }
                        $std_que_ids[]=(string)$question['id'];
                        $questions_flag[]=$flag;
                    }

                    //作文
                    if((string)$question['type']=='composition'){
                        $std_short_answers[]=$question->text->asXML();
                        try{
                            //当不存在score这个属性时，并不会报错，而是为空字符串
                            $score=(string)$question['score'];
                            $flag=0;//表示该小题有分值
                            if(empty($score)){
                                //如果该小题没有分值，则用大题的分值代替
                                $que_par_arr=$question->xpath('parent::questions');
                                $que_par=$que_par_arr[0];
                                //dd($que_par);
                                $score=(string)$que_par['score'];
                                $flag=1;//表示该小题的分值是大题的分值
                                
                            }
                            $std_scores[]=$score;
                        }catch(Exception $e){
                            $std_scores[]="";
                        }
                        $std_que_ids[]=(string)$question['id'];
                        $questions_flag[]=$flag;
                    }


                }//foreach
            }
            
        }
        //数学试卷处理结束

        
        

        
        //dd($std_que_ids);
        //dd($std_short_answers);
        //dd($std_scores);
        //dd($questions_flag);//标记该题的分值是大题的分值还是该小题的分值
        //简答题答案
        //必须得知道选择题是几道题，因为答案都是统一的question，而简答题中又有选择题
        /*
        for($i=$select_num;$i<count($std_total_answer);$i++){
            //试卷简答题试题
            echo $std_total_answer[$i]['id'].'  ';
            echo $std_scores[$i-$select_num].'  ';
            echo '试题:'.$std_short_answers[$i-$select_num].'<br/>';
            echo '参考答案   '.$std_total_answer[$i]->text.'<br/>';
            echo '用户答案  '.$user_total_answer[$i]->text.'<br/>';
        }
        */

        //先在数据库中插入一条记录教师用户试卷记录，用于保存教师已经改过的题scorer_paper
        //用户id
        $scorer_name=$request->session()->get('name');
        //echo $scorer_name;
        $scorer=User::where('name',$scorer_name)->first();
        $scorer_id=$scorer->id;
        //echo $scorer_id;
        //整个选择题算一道题
        $count_que=count($std_que_ids)+1;
        //echo $count_que."<br/>";
        //评论
        $comment="";
     

        $scorer_paper=ScorerPaper::where('user_id',$scorer_id)->where('user_paper_id',$user_paper_id)->first();
        //dd($scorer_paper);
        //dd($scorer_paper);
        if(empty($scorer_paper)){
            //新建一条记录
            $scorer_paper=new ScorerPaper;
            $scorer_paper->user_id=$scorer_id;
            $scorer_paper->user_paper_id=$user_paper_id;
            //新建一个detail_xml对象
            $str='<?xml version="1.0" encoding="UTF-8"?>';
            $str.='<paperanswer></paperanswer>';
            $detail_xml=simplexml_load_string($str);
            $str_det_xml=$detail_xml->asXML();
            //file_put_contents('./dataTest.txt', $str_det_xml.'\n',FILE_APPEND);
            //echo $str_det_xml.'<br/>';
            $scorer_paper->detail_xml=$str_det_xml;
            //题目总数
            $scorer_paper->count=$count_que;
            //客观题分数
            $scorer_paper->object_grade=$select_grade;
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
                $select_grade=$scorer_paper->object_grade;
            }
            
        }
        $comment=$scorer_paper->comment;

        //dd($select_num);//选择题数目
        //dd($cor_sele_num);//选择题正确数目
        //dd($select_grade);//选择题分数
        //dd($str_std_sele_answer);//选择题标准答案
        //dd($str_user_sele_answer);//用户选择题答案
        //dd($std_short_answers);//简答题试题
        //dd($score_total_sel);//选择题得分
        //dd($paper_name);//试卷名称
        //dd($stu_name);//上传者姓名
        //dd($user_paper_time);//用户提交试卷时间
        //dd($scorer_id);//教师id
        //dd($user_paper_id);//用户提交试卷id
        //dd($tea_save_anws);//教师上次批改的结果
        //dd($std_scores);//分值数组
        //dd($std_total_answer);//参考答案
        //dd($user_total_answer);//用户提交的答案

       
        return view('paperScore',['select_num'=>$select_num,'cor_sele_num'=>$cor_sele_num,'select_grade'=>$select_grade,
                                'str_std_sele_answer'=>$str_std_sele_answer,'str_user_sele_answer'=>$str_user_sele_answer,
                                'std_short_answers'=>$std_short_answers,'score_total_sel'=>$score_total_sel,
                                'paper_name'=>$paper_name,'stu_name'=>$stu_name,'user_paper_time'=>$user_paper_time,
                                'scorer_id'=>$scorer_id,'user_paper_id'=>$user_paper_id,'tea_save_anws'=>$tea_save_anws,'tea_save_coms'=>$tea_save_coms,
                                'comment'=>$comment,'questions_flag'=>$questions_flag,
                                 'std_scores'=>$std_scores,'std_total_answer'=>$std_total_answer,'user_total_answer'=>$user_total_answer]);
       
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
        if($question_id=="select"){
            
            $object_grade=(float)$question_scorer;
            //file_put_contents('./dataTest.txt', $object_grade.'\n',FILE_APPEND);
            $scorer_paper->object_grade=$object_grade;
            
        }
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
        $object_grade=$scorer_paper->object_grade;
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
