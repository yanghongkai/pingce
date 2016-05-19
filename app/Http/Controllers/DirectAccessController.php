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
            $arr_user_paper_id=array();
            
        }else{
            //试卷名，提交时间，提交者，阅卷人，得分，阅卷链接
            foreach ($user_papers as $user_paper){
                //我的答案
                $user_ans=$user_paper->userAnswer;
                $arr_user_ans[]=$user_ans;
                $arr_user_paper_id[]=$user_paper->id;
                //echo 'user_ans='.$user_ans.'**';
                $paper_id=$user_paper->paper_id;
                //获得试卷信息
                $paper=Paper::where('id',$paper_id)->first();
                //dd($paper);
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
                        if($user->name!='vteacher'){
                            $scorers.=$user->name.'_';
                        }
                        
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
                    //虚拟试卷信息(如果刚提交，vteacher，未评阅， 评阅中，也显示出来)
                    $scorer_paper_vtea=ScorerPaper::where('user_paper_id',$user_paper->id)->orderBy('updated_at','desc')->first();
                    if(empty($scorer_paper_vtea)){
                        //如果没有批改信息，将这条记录删除
                        $user_paper->delete();
                    }else{
                        //总分
                        $grade='';
                        //客观题得分
                        $arr_object_grade[]=$scorer_paper_vtea->object_grade;
                        //主观题得分
                        $arr_subject_grade[]='';
                        $arr_scorer_paper_id[]=$scorer_paper_vtea->id;//还没有批改结果
                        }
                    
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
                        'arr_paper_ans'=>$arr_paper_ans,'arr_scorer_paper_id'=>$arr_scorer_paper_id,
                        'arr_user_paper_id'=>$arr_user_paper_id
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

    public function picNew($id){
        $paper=Paper::where('id',$id)->first();
        return view('picNew',['paper'=>$paper]);
    }

    public function picNewUser($id){
        $user_paper=UserPaper::where('id',$id)->first();
        $paper_id=$user_paper->paper_id;
        //获得试卷信息
        $paper=Paper::where('id',$paper_id)->first();
        //dd($paper);
        $paper_name=$paper->name;
        // dd($paper_name);
        // dd($id);
        return view('picNewUser',['user_paper_id'=>$id,'paper_name'=>$paper_name]);
        // return view('picNewUser');
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

    //解析公式
    // public function parseLatex($str){
    //     $pattern='/\$([^$]*)\$/U';
    //     preg_match_all($pattern,$str,$matches);
    //     $arr_split=preg_split($pattern,$str);
    //     //dd($arr_split);
    //     //dd($matches);
    //     $count=count($matches[1]);
    //     $str_new="";
    //     for($i=0;$i<count($matches[1]);$i++){
    //         //$arr_replace[]='<img src="http://latex.codecogs.com/gif.latex?'.$matches[1][$i].'" />';
    //         $str_new.=$arr_split[$i];
    //         $str_new.='<img src="http://latex.codecogs.com/gif.latex?'.$matches[1][$i].'" />';
    //     }
    //     if($count>0){
    //         return $str_new;
    //     }else{
    //         return $str;
    //     }
    // }

    //试卷详情
    public function stuPaper($id){
        //$id为score_paper_id
        
        $scorer_paper=ScorerPaper::where('id',$id)->first();       
        $user_paper_id=$scorer_paper->user_paper_id;
        //下边的代码未修改
        //$user_paper_id=$id;
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
        $arr_ques_table=array();
        $arr_ques_count=array();
        $arr_ques_score=array();
        foreach($arr_questions as $arr_question){
            //dd($arr_question);
            $questions_head_text=(string)$arr_question->headtext->asXML();
            $questions_title=(string)$arr_question->title->asXML();
            $questions_text=(string)$arr_question->text->asXML();
            $questions_table=(string)$arr_question->tab->asXML();
            $arr_ques_head_text[]=$questions_head_text;
            $arr_ques_title[]=$questions_title;
            $arr_ques_text[]=$questions_text;
            $arr_ques_table[]=$questions_table;
            // $arr_ques_text[]=$this->parseLatex($questions_text);
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
        // dd($arr_ques_text);


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
        
        //echo $arr_que[0]['id'].'<br/>';
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
        /*
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
        */
        $comment="";

        //$scorer_paper=ScorerPaper::where('user_id',$scorer_id)->where('user_paper_id',$user_paper_id)->first();
        //dd($scorer_paper);
       
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
            
       
        $comment=$scorer_paper->comment;
        //dd($tea_save_anws);
        //dd($user_paper_id);
        //dd($scorer_id);
        //echo $user_paper_id.'--'.$scorer_id.'<br/>';
        //dd($tea_save_coms);

        

        
        



         



        
        return view('stuPaper',['paper_name'=>$paper_name,'stu_name'=>$stu_name,'user_paper_time'=>$user_paper_time,'user_paper_id'=>$user_paper_id,
                                   'arr_ques_head_text'=>$arr_ques_head_text,'arr_ques_title'=>$arr_ques_title,'arr_ques_text'=>$arr_ques_text,
                                    'arr_ques_count'=>$arr_ques_count,'arr_que'=>$arr_que,'arr_paper_answer'=>$arr_paper_answer,
                                    'arr_user_answer'=>$arr_user_answer,'arr_ques_score'=>$arr_ques_score,'comment'=>$comment,
                                    'paper_content_path'=>$paper_content_path,'user_answer_path'=>$user_answer_path, 'paper_answer_path'=>$paper_answer_path,
                                    'tea_save_anws'=>$tea_save_anws,'tea_save_coms'=>$tea_save_coms,'paper_id'=>$paper_id,
                                    'arr_ques_table'=>$arr_ques_table,'user_paper_id'=>$user_paper_id
                                    ]);
        
        

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
