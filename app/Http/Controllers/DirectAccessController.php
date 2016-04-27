<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\User;
use App\Role;
use App\Paper;
use App\UserPaper;
use App\ScorerPaper;
use App\News;
use App\Resource;

class DirectAccessController extends Controller
{
    //
    public function __construct(){
    	$this->middleware('namese');
    }
    //访问资源下载页
    public function accDownload(Request $request){
        $res=Resource::where('active',1)->orderBy('created_at','desc')->get();
    	return view('download',['res'=>$res]);
    }
    //判断该用户是否做过某个试卷
    public function judgeSubmit($paper_id,$user_id){
        $user_paper=UserPaper::where('user_id',$user_id)->where('paper_id',$paper_id)->first();
        if(empty($user_paper)){
            //不存在
            return '未上传';
        }else{
            return '已上传';
        }
    }
    //访问试卷评测
    public function accEvaluate(Request $request){
        //test
        /*
        if($request->session()->has('name')){
            $name=$request->session()->get('name');
            $role=$request->session()->get('role');
            echo $name."--".$role."<br/>";
        }
        */
        //test

        
        $name=$request->session()->get('name');
        $stu=User::where('name',$name)->first();
        $stu_id=$stu->id;
        $role_id=$stu->role_id;
        //管理员，教师，所有的试卷都可以做role_id>=1and role_id<=3
        if($role_id>=4 && $role_id<=7){
            $role_name=$stu->role->name;
             //不能包含已删除的试卷id
            $papers=Paper::where('active',1)->where('category',$role_name)->orderBy('created_at','desc')->get();
        }else if($role_id==1){//考生有做四科试卷的权限
            $papers=Paper::where('active',1)->orderBy('created_at','desc')->get();
        }else{
            return redirect('/');
        }
        
        

       
        if(count($papers)>0){
            foreach($papers as $paper){
                $paper_id=$paper->id;
                $arr_history[]=$this->judgeSubmit($paper_id,$stu_id);
            }
        }else{
            $arr_history=array();
        }
        //dd($arr_history);
        //dd($papers);
        //$paper=$papers[0];
        //dd($paper);
    	return view('evaluate',['papers'=>$papers,'arr_history'=>$arr_history]);
    }
    //访问用户中心
    public function accUser(Request $request){
    	
    	//$name=$request->session()->get('name');
        //$sessions = $request->session()->all();
        //dd($sessions);
    	$role=$request->session()->get('role');
    	if($role==1 || $role==4 || $role==5 || $role==6 || $role==7){
    		return redirect('/userEvaluate');
    		
    	}else if($role==2){
    		//return view('userScorer');
            return redirect('/userScorer');

    	}else if($role ==3){
    		
            return redirect('/userManage');
    	}
    	//return view('evaluate');
    }

    //计算选择题答案,输入试题路径，参考答案路径,用户答案路径，
    public function getSelectGrade($paper_content_path,$paper_answer_path,$user_answer_path){

         //试题
        $paper_content=simplexml_load_file('./content/'.$paper_content_path);
        //选择题
        //$selectSections=$shortSections=$paper_content->xpath("/paper//section[contains(@name,'选择') or contains(@name,'单选')]");
        
        //第一道题是选择题
        $selectSections=$paper_content->xpath("/paper/section[1]");
        $selectSection=$selectSections[0];
        //dd($selectSection);
        //选择题的总分值
       //选择题的总分值
        try{
            $score_total_sel=$selectSection['score'];
        }catch(Exception $e){
            //如果没有score这个属性的话，把分值设置为0.0
            $score_total_sel=0.0;
        }
        //echo $score_total_sel.'<br/>';

        //$select_num选择题的数目
        $select_num=0;
        foreach ($selectSection->question as $single_select){
            //echo $single_select['id'].'<br/>';
            $select_num++;
        }
        //echo $select_num.'<br/>';
        foreach ($selectSection->questions as $multi_selects){
                foreach ($multi_selects->question as $multi_select){
                    //echo $multi_select['id'].'<br/>';
                    $select_num++;
                }
        }


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
                $cor_sele_num++;
            }
        }
        //echo $cor_sele_num.'<br/>';
        //用户选择题的分
        $select_grade=(float)$score_total_sel/($select_num*1.0)*$cor_sele_num*1.0;
        $select_grade=round($select_grade*1.0,1);
        //echo $select_grade.'<br/>';
         if($score_total_sel<=0){
            //如果选择题没有设置分值，就以‘--’处理
            $select_grade='--';
            $score_total_sel='--';
        }
        
        return $select_grade;
    }

    public function accUserEvaluate(Request $request){

        $stu_name=$request->session()->get('name');
        $stu=User::where('name',$stu_name)->first();
        $stu_id=$stu->id;
        
        //找到被删除的试卷id,$del_papers被删除试卷的id
        $del_papers=Paper::where('active',0)->lists('id');
        //找到该用户所做的试卷
        $user_papers=UserPaper::whereNotIn('paper_id',$del_papers)->where('user_id',$stu_id)->orderBy('updated_at','desc')->get();
        //如果没有该用户的记录
        if(count($user_papers)<=0){
            $arr_paper_name=array();
            $arr_updated_at=array();
            $arr_paper_status=array();
            $arr_grade=array();
            $arr_scorers=array();
            $arr_paper_con=array();
            $arr_user_ans=array();
            $arr_paper_ans=array();
            $arr_object_grade=array();
            $arr_subject_grade=array();
            $arr_scorer_paper_id=array();
            
        }else{
            //试卷名，提交时间，提交者，阅卷人，得分，阅卷链接
            foreach ($user_papers as $user_paper){
                //我的答案
                $user_ans=$user_paper->userAnswer;
                $arr_user_ans[]=$user_ans;
                //echo 'user_ans='.$user_ans.'**';
                $paper_id=$user_paper->paper_id;
                //获得试卷信息
                $paper=Paper::where('id',$paper_id)->first();
                $paper_name=$paper->name;
                $arr_paper_name[]=$paper_name;
                //echo $paper_name.'**';
                //试卷试题
                $paper_con=$paper->content;
                $arr_paper_con[]=$paper_con;
                //echo 'paper_con='.$paper_con.'**';
                //试卷答案
                $paper_ans=$paper->answer;
                $arr_paper_ans[]=$paper_ans;
                //echo 'paper_ans='.$paper_ans.'**';
                
                
                //提交时间(现在修改为update_at)
                $updated_at=$user_paper->updated_at;
                $arr_updated_at[]=$updated_at;
                //echo $created_at.'**';
                //试卷状态“已评阅”，“已提交”
                $paper_status='';
               
                //阅卷人
                $scorers='';
                $users=$user_paper->users;
                //dd($users);//users实际上为批改该试卷的教师信息
                if(count($users)>0){

                    foreach ($users as $user){
                        //echo $user->name;
                        $scorers.=$user->name.'_';
                    }
                    $scorers=substr($scorers, 0,-1);
                    //$paper_status='已评阅';
                    //有阅卷人，但未给出分数，说明是评阅中
                    
                    
                }else{
                    $scorers='--';
                }
                $arr_scorers[]=$scorers;
                $arr_paper_status[]=$user_paper->status;//以每个user_paper的status为准
                //echo 'scorers='.$scorers.'**';
                
                //试卷分数，只要最后一个的成绩（有问题,user_paper_id）
                //echo 'paper_id='.$paper_id.'**';
                $scorer_paper=ScorerPaper::where('user_paper_id',$user_paper->id)->where('submit',1)->orderBy('updated_at','desc')->first();
                //dd($scorer_paper);
                if(empty($scorer_paper)){

                    $grade='';
                    //客观题得分
                    $arr_object_grade[]=$this->getSelectGrade($paper_con,$paper_ans,$user_ans);//计算得到
                    //主观题得分
                    $arr_subject_grade[]='';
                    $arr_scorer_paper_id[]=-1;//还没有批改结果
                }else{
                    $grade=$scorer_paper->grade;
                    $arr_object_grade[]=$scorer_paper->object_grade;
                    $arr_subject_grade[]=$scorer_paper->subject_grade;
                    //试卷对应的 某个教师批改这个试卷的id
                    //以最后的老师批改的为准
                    $scorer_paper_id=$scorer_paper->id;
                    $arr_scorer_paper_id[]=$scorer_paper_id;
                }
                //echo 'grade='.$grade.'<br/>';
                $arr_grade[]=$grade;

                
            }

            //dd($arr_grade);
            //$arr_paper_name $arr_created_at $arr_paper_status $arr_grade $arr_scorers 
            //$arr_paper_con $arr_user_ans $arr_paper_ans

        }

         return view('userEvaluate',['arr_paper_name'=>$arr_paper_name,'arr_updated_at'=>$arr_updated_at,'arr_paper_status'=>$arr_paper_status,
                        'arr_grade'=>$arr_grade,'arr_object_grade'=>$arr_object_grade,'arr_subject_grade'=>$arr_subject_grade,
                        'arr_scorers'=>$arr_scorers,'arr_paper_con'=>$arr_paper_con,'arr_user_ans'=>$arr_user_ans,
                        'arr_paper_ans'=>$arr_paper_ans,'arr_scorer_paper_id'=>$arr_scorer_paper_id
                ]);

        
        
    }
    //访问阅卷记录页面
    public function accUserScorer(){

        /*
        //找到被删除的试卷id,$del_papers被删除试卷的id
        $del_papers=Paper::where('active',0)->lists('id');
        $user_papers=UserPaper::whereNotIn('paper_id',$del_papers)->orderBy('created_at','desc')->get();
        //试卷名，提交时间，提交者，阅卷人，得分，阅卷链接
        foreach ($user_papers as $user_paper){
            $paper_id=$user_paper->paper_id;
            //获得试卷信息
            $paper=Paper::where('id',$paper_id)->first();
            $paper_name=$paper->name;
            echo $paper_name.'**';
            //提交时间
            $created_at=$user_paper->created_at;
            echo $created_at.'**';
            //阅卷人
            $scorers='';
            $users=$user_paper->users;
            //dd($users);
            if(count($users)>0){

                foreach ($users as $user){
                    //echo $user->name;
                    $scorers.=$user->name.'_';
                }
                $scorers=substr($scorers, 0,-1);
            }else{
                $scorers='--';
            }
            
            echo $scorers.'**';

            //获取提交者姓名
            $stu_id=$user_paper->user_id;
            $user_stu=User::where('id',$stu_id)->first();
            $sub_name=$user_stu->name;
            echo $sub_name.'**';
            
            //试卷分数，只要最后一个的成绩（有问题,user_paper_id）
            //echo 'paper_id='.$paper_id.'**';
            $scorer_paper=ScorerPaper::where('user_paper_id',$user_paper->id)->orderBy('updated_at','desc')->first();
            //dd($scorer_paper);
            if(empty($scorer_paper)){
                $grade='';
            }else{
                $grade=$scorer_paper->grade;
            }
            echo 'grade='.$grade.'<br/>';
        }
        */
        
        
         

        $del_papers=Paper::where('active',0)->lists('id');
        $user_papers=UserPaper::whereNotIn('paper_id',$del_papers)->orderBy('updated_at','desc')->get();
        return view('userScorer',['user_papers'=>$user_papers]);
    }
    
    //搜索页面
    public function search(Request $request){
        $search=$request->input('search');
        if(empty($search)){
            return redirect('/userScorer');
        }
        $del_papers=Paper::where('active',0)->lists('id');
        //根据试卷状态
        $user_papers_sta=UserPaper::where('status','like',"%$search%")->whereNotIn('paper_id',$del_papers)->orderBy('created_at','desc')->get();
        //dd($user_papers_sta);
        //根据用户名
        $arr_user_ids=User::where('name','like',"%$search%")->lists('id');
        $user_papers_username=UserPaper::whereIn('user_id',$arr_user_ids)->whereNotIn('paper_id',$del_papers)->orderBy('created_at','desc')->get();
        //dd($user_papers_username);
        //根据试卷名或学科
        $arr_paper_ids=Paper::where('name','like',"%$search%")->orWhere('category','like',"%$search%")->lists('id');
        $user_papers_papername=UserPaper::whereIn('paper_id',$arr_paper_ids)->whereNotIn('paper_id',$del_papers)->orderBy('created_at','desc')->get();
        //根据时间
        $user_papers_time=UserPaper::where('time','like',"%$search%")->whereNotIn('paper_id',$del_papers)->orderBy('created_at','desc')->get();

        
        
         
        //集合合并
        $user_papers=collect();
        $user_papers=$user_papers->merge($user_papers_sta);
        //去除重复的
        $diff_username=$user_papers_username->diff($user_papers);
        //dd($diff_username);

        $user_papers=$user_papers->merge($diff_username);
        $diff_papername=$user_papers_papername->diff($user_papers);
        //dd($diff_papername);
        $user_papers=$user_papers->merge($diff_papername);
        $diff_time=$user_papers_time->diff($user_papers);
        $user_papers=$user_papers->merge($diff_time);
        //dd($user_papers);

        return view('userScorer',['user_papers'=>$user_papers]);
        
        
       
    }
    //访问试卷管理页面
    public function accUserManage(){
        //获取所有的试卷信息，降序排列
        $papers=Paper::where('active',1)->orderBy('created_at','desc')->get();
        return view('userManage',['papers'=>$papers]);
    }
    public function paperNew(){
        return view('paperNew');
    }

    public function manageNews(){
        //获取所有的新闻
        $news=News::where('active',1)->orderBy('created_at','desc')->get();
        return view('manageNews',['news'=>$news]);
    }

    public function newsNew(){
        return view('newsNew');
    }

    public function manageUser(){
        //获取所有的新闻
        $users=User::where('active',1)->orderBy('created_at','desc')->get();
        //dd($users);
        /*
        foreach($users as $user){
            echo $user->name.'<br/>';
            echo $user->role->name.'<br/>';
        }
        */
        return view('manageUser',['users'=>$users]);
    }

    public function userNew(){
        return view('userNew');
    }

    public function manageResource(){
        //获取所有资源
        $res=Resource::where('active',1)->orderBy('created_at','desc')->get();
        return view('manageResource',['res'=>$res]);
    }

    public function resourceNew(){
        return view('resourceNew');
    }

    //新建新闻
    public function postNewsNew(Request $request){
        $title=$request->input('newsName');
        $content=$request->input('newsCont');
        $top=$request->input('top');
        $publisher=$request->session()->get('name');
        $news=new News;
        $news->title=$title;
        $news->content=$content;
        $news->top=$top;
        $news->publisher=$publisher;
        $news->save();
        return redirect('/manageNews');
    }

    public function postNewsEditTrue(Request $request){
        $id=$request->input('id');
        $news=News::where('id',$id)->first();
        $content=$request->input('newsCont');
        $top=$request->input('top');
        $news->content=$content;
        $news->top=$top;
        try{
            $news->save();
        }catch(Exception $e){
            return response()->json(['success'=>false]);
        }
        
        return response()->json(['success'=>true]);
    }

    //试卷详情
    public function stuPaper($id){
        //$id为score_paper_id
        if($id<0){
            return redirect('/userEvaluate');
        }
        $scorer_paper=ScorerPaper::where('id',$id)->first();
        
        $user_paper_id=$scorer_paper->user_paper_id;
        //下边的代码为修改
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
        
        //echo $score_total_sel.'<br/>';

        //$select_num选择题的数目
        $select_num=0;
        foreach ($selectSection->question as $single_select){
            //echo $single_select['id'].'<br/>';
            $select_num++;
        }
        //echo $select_num.'<br/>';
        foreach ($selectSection->questions as $multi_selects){
                foreach ($multi_selects->question as $multi_select){
                    //echo $multi_select['id'].'<br/>';
                    $select_num++;
                }
        }
        //echo $select_num.'<br/>';
       
         
        
        

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
                $cor_sele_num++;
            }
        }
        //echo $cor_sele_num.'<br/>';
        //用户选择题的分
        $select_grade=(float)$score_total_sel/($select_num*1.0)*$cor_sele_num*1.0;
        $select_grade=round($select_grade*1.0,1);
        if($score_total_sel<=0){
            //如果选择题没有设置分值，就以‘--’处理
            $select_grade='--';
            $score_total_sel='--';
        }
        //echo $select_grade.'<br/>';
        //echo $select_grade."<br/>";
        //echo $score_total_sel."<br/>";
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
       
        /*
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
        */
     

        //$scorer_paper=ScorerPaper::where('user_id',$scorer_id)->where('user_paper_id',$user_paper_id)->first();
        //dd($scorer_paper);
        //dd($scorer_paper);
        $scorer_paper=ScorerPaper::where('id',$id)->first();
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

        
        return view('stuPaper',['select_num'=>$select_num,'cor_sele_num'=>$cor_sele_num,'select_grade'=>$select_grade,
                                'str_std_sele_answer'=>$str_std_sele_answer,'str_user_sele_answer'=>$str_user_sele_answer,
                                'std_short_answers'=>$std_short_answers,'score_total_sel'=>$score_total_sel,
                                'paper_name'=>$paper_name,'stu_name'=>$stu_name,'user_paper_time'=>$user_paper_time,
                                'user_paper_id'=>$user_paper_id,'tea_save_anws'=>$tea_save_anws,'tea_save_coms'=>$tea_save_coms,
                                'comment'=>$comment,'questions_flag'=>$questions_flag,
                                'paper_content_path'=>$paper_content_path,'user_answer_path'=>$user_answer_path, 'paper_answer_path'=>$paper_answer_path,
                                 'std_scores'=>$std_scores,'std_total_answer'=>$std_total_answer,'user_total_answer'=>$user_total_answer]);
       

        






    }

    //删除新闻
    public function newsDelete($id){
        $news=News::where('id',$id)->first();
        //$news->active=0;
        //$news->save();
        $news->delete();
        return redirect('/manageNews');
    }
    //删除资源
    public function resDelete($id){
        $res=Resource::where('id',$id)->first();
        $res->delete();
        return redirect('/manageResource');
    }
    //置顶新闻
    public function newsTop($id){
        $news=News::where('id',$id)->first();
        $news->top=1;
        $news->save();
        return redirect('/manageNews');
    }
    //编辑新闻
    public function newsEdit($id){
        $news=News::where('id',$id)->first();
        return view('newsEdit',['news'=>$news]);
    }
    //编辑试卷
    public function paperEdit($id,Request $request){
        $paper=Paper::where('id',$id)->first();
        //将试卷的content和answer写入session中
        $content=$paper->content;
        $answer=$paper->answer;
        $request->session()->put('paperEdit',$content);
        $request->session()->save();//写入session后，需要马上保存
        $request->session()->put('answerEdit',$answer);
        $request->session()->save();//写入session后，需要马上保存
        return view('paperEdit',['paper'=>$paper]);
    }
    //修改密码
    public function pwdEdit($id){
        $user=User::where('id',$id)->first();
        return view('pwdEdit',['user'=>$user]);
    }
    //修改密码post
    public function postPwdEdit(Request $request){
        $id=$request->input('id');
        $user=User::where('id',$id)->first();
        $newPassword=$request->input('newPassword');
        $conPassword=$request->input('conPassword');
        if(empty($newPassword) || empty($conPassword)){
            return response()->json(['success'=>false]);
        }
        if($newPassword != $conPassword){
            return response()->json(['success'=>false]);
        }
        $user->password=$newPassword;
        try{
            $user->save();
        }catch(Exception $e){
            return response()->json(['success'=>false]);
        }
        return response()->json(['success'=>true]);
    }

    //软删除用户
    public function userDel($id){
        $user=User::where('id',$id)->first();
        $user->active=0;
        $user->save();
        return redirect('/manageUser');
    }

    //显示修改个人资料页面
    public function accProfile(Request $request){
        $name=$request->session()->get('name');
        $user=User::where('name',$name)->first();
        $role=$request->session()->get('role');
        if($role==1 || $role==4 || $role==5 || $role==6 || $role==7 ){
            return view('evaluateProfile',['user'=>$user]);
        }
        if($role==2){
            return view('scorerProfile',['user'=>$user]);
        }
        if($role==3){
            return view('manageProfile',['user'=>$user]);
        }
        
        
    }
    //修改个人资料
    public function  editProfile(Request $request){
        $company=$request->input('company');
        $password=$request->input('password');
        $newPassword=$request->input('newPassword');
        $conPassword=$request->input('conPassword');
        
        $name=$request->session()->get('name');
        $role=$request->session()->get('role');
        $user=User::where('name',$name)->first();
        if($user->password!=$password){
            $error='密码错误！';
            if($role==1){
                return view('evaluateProfile',['user'=>$user])->withErrors($error);
            }
            if($role==2){
                return view('scorerProfile',['user'=>$user])->withErrors($error);
            }
            if($role==3){
                return view('manageProfile',['user'=>$user])->withErrors($error);
            }
           
        }
        if($newPassword!=$conPassword){
            $error='新密码和确认密码不一致！';
            if($role==1){
                return view('evaluateProfile',['user'=>$user])->withErrors($error);
            }
            if($role==2){
                return view('scorerProfile',['user'=>$user])->withErrors($error);
            }
            if($role==3){
                return view('manageProfile',['user'=>$user])->withErrors($error);
            }
        }
        $user->password=$newPassword;
        $user->department=$company;
        $user->save();
        

        if($role==1){
            return redirect('/userEvaluate');
        }
        if($role==2){
            return redirect('/userScorer');
        }
        if($role==3){
            return redirect('/userManage');
        }
       

    }


     //用户退出
    public function postLogout(Request $request){
        //清楚所有的session
        //删除所有的sesion
        //echo "go outing!"."<br/>";
        $request->session()->flush();
        return redirect('/');
    }








}
