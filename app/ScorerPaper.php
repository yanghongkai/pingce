<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ScorerPaper extends Model
{
    //
    protected $table='scorer_paper';
    protected $primaryKey='id';
    protected $guarded=[];
    //protected $dateFormat='U';
     public $tamestamps=true;
    /**
     * 获取当前时间
     *
     * @return int
     */
    public function freshTimestamp() {
        return time();
    }
    /**
     * 避免转换时间戳为时间字符串
     *
     * @param DateTime|int $value
     * @return DateTime|int
     */
    public function fromDateTime($value) {
        return $value;
    }
    /**
     * 从数据库获取的为获取时间戳格式
     *
     * @return string
     */
    public function getDateFormat() {
        return 'U';
    }

    //计算客观题分值
    static public function calObjectScore($arr_que,$arr_paper_answer,$user_answer){
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
                // $paper_ans_arr=array();
                // $user_ans_arr=array();
                $paper_ans_texts=$arr_paper_answer[$i]->xpath('.//text');
                    foreach ($paper_ans_texts as $paper_ans_text){
                        $paper_ans_item=(string)$paper_ans_text;
                        $paper_ans_item=trim($paper_ans_item);
                        $paper_ans.=$paper_ans_item;
                        //echo $paper_ans.' ';
                        //echo $paper_ans_text.' ';
                }
                $user_ans_get=Parser::getQuestionById($user_answer,$arr_paper_answer[$i]['id']);
                if(!empty($user_ans_get)){
                    $user_ans_texts=$user_ans_get->xpath('.//text');
                    foreach ($user_ans_texts as $user_ans_text){
                        $user_ans_item=(string)$user_ans_text;
                        $user_ans_item=trim($user_ans_item);
                        $user_ans.=$user_ans_item;
                    }
                }else{
                    $user_ans="";
                }
                $user_ans_arr=str_split($user_ans);
                sort($user_ans_arr);
                $user_ans=implode("", $user_ans_arr);
                $paper_ans_arr=str_split($paper_ans);
                sort($paper_ans_arr);
                $paper_ans=implode("", $paper_ans_arr);
                // dd($user_ans);
                $score=(float)$arr_que[$i]['score'];
                if(empty($score)){
                    //如果没有分值默认为1
                    $score=1;
                }
                // $id=$arr_que[$i]['id'];
                $id=(string)$arr_paper_answer[$i]['id'];
                if($paper_ans==$user_ans){
                    $user_que_score=$score;
                }else{
                    $user_que_score=0;
                }
                //echo $user_que_score;
                // file_put_contents("./dataTest.txt", "user_que_score=".$user_que_score."\n",FILE_APPEND);
                $object_grade+=$user_que_score;
                //新建节点
                $question=$detail_xml->addChild('question');
                $question->addAttribute('id',$id);
                $question->addAttribute('type','select');
                $question->addChild('text',$user_que_score);
                $question->addChild('comment','');//选择题不要备注， 

            }

        }

        return $object_grade;


    }

    //得到客观题的批改详情
    static public function getDetailXML($arr_que,$arr_paper_answer,$user_answer){
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
                        $paper_ans_item=trim($paper_ans_item);
                        $paper_ans.=$paper_ans_item;
                        //echo $paper_ans.' ';
                        //echo $paper_ans_text.' ';
                }
                $user_ans_get=Parser::getQuestionById($user_answer,$arr_paper_answer[$i]['id']);
                if(!empty($user_ans_get)){
                    $user_ans_texts=$user_ans_get->xpath('.//text');
                    foreach ($user_ans_texts as $user_ans_text){
                        $user_ans_item=(string)$user_ans_text;
                        $user_ans_item=trim($user_ans_item);
                        $user_ans.=$user_ans_item;
                    }
                }else{
                    $user_ans="";
                }
                
                $score=(float)$arr_que[$i]['score'];
                if(empty($score)){
                    //如果没有分值默认为1
                    $score=1;
                }
                // $id=$arr_que[$i]['id'];
                $id=(string)$arr_paper_answer[$i]['id'];
                if($paper_ans==$user_ans){
                    $user_que_score=$score;
                }else{
                    $user_que_score=0;
                }
                //echo $user_que_score;
                // file_put_contents("./dataTest.txt", "user_que_score=".$user_que_score."\n",FILE_APPEND);
                $object_grade+=$user_que_score;
                //新建节点
                $question=$detail_xml->addChild('question');
                $question->addAttribute('id',$id);
                $question->addAttribute('type','select');
                $question->addChild('text',$user_que_score);
                $question->addChild('comment','');//选择题不要备注， 

            }

        }

        return $detail_xml;

    }

    //初始化detai_xml，以questions为单位
    static public function initDetailXML($arr_questions){
        //新建一个detail_xml对象（保存教师的批改详情，选择题自动加入）
        $str='<?xml version="1.0" encoding="UTF-8"?>';
        $str.='<paperanswer></paperanswer>';
        $detail_xml=simplexml_load_string($str);
        // dd($arr_questions);

        for($i=0;$i<count($arr_questions);$i++){
            $questions_item=$arr_questions[$i];
            $ques_id=(string)$questions_item['id'];
            $maxnum=(string)$questions_item['maxnum'];
            if(empty($maxnum)){
                $maxnum=0;
            }else{
                $maxnum=(int)$questions_item['maxnum'];
            }
            $questions=$detail_xml->addChild('questions');
            $questions->addAttribute('id',$ques_id);
            $questions->addAttribute('maxnum',$maxnum);
            $question_arr=$questions_item->xpath('.//question');
            for($j=0;$j<count($question_arr);$j++){
                $question_item=$question_arr[$j];
                $id=(string)$question_item['id'];
                $type=(string)$question_item['type'];
                $question=$questions->addChild('question');
                $question->addAttribute('id',$id);
                $question->addAttribute('type',$type);
                $question->addAttribute('submit',"0");
                $question->addChild('text','');
                $question->addChild('comment','');
            }


        }
        // dd($detail_xml);
        // $str_detail_xml=$detail_xml->asXML();
        // dd($str_detail_xml);
        return $detail_xml;

    }

    //初始化选择题
    static public function initSelectXML($detail_xml,$arr_que,$arr_paper_answer,$user_answer){
        //计算客观题分值
        for($i=0;$i<count($arr_paper_answer);$i++){
            if((string)$arr_paper_answer[$i]['type']=='select'){
                $paper_ans='';
                $user_ans='';
                $user_que_score='';//用户这道选择题的得分
                $paper_ans_texts=$arr_paper_answer[$i]->xpath('.//text');
                    foreach ($paper_ans_texts as $paper_ans_text){
                        $paper_ans_item=(string)$paper_ans_text;
                        $paper_ans_item=trim($paper_ans_item);
                        $paper_ans.=$paper_ans_item;
                        //echo $paper_ans.' ';
                        //echo $paper_ans_text.' ';
                }
                $user_ans_get=Parser::getQuestionById($user_answer,$arr_paper_answer[$i]['id']);
                if(!empty($user_ans_get)){
                    $user_ans_texts=$user_ans_get->xpath('.//text');
                    foreach ($user_ans_texts as $user_ans_text){
                        $user_ans_item=(string)$user_ans_text;
                        $user_ans_item=trim($user_ans_item);
                        $user_ans.=$user_ans_item;
                    }
                }else{
                    $user_ans="";
                }
                $user_ans_arr=str_split($user_ans);
                sort($user_ans_arr);
                $user_ans=implode("", $user_ans_arr);
                $paper_ans_arr=str_split($paper_ans);
                sort($paper_ans_arr);
                $paper_ans=implode("", $paper_ans_arr);
                $score=(float)$arr_que[$i]['score'];
                if(empty($score)){
                    //如果没有分值默认为1
                    $score=1;
                }
                // $id=$arr_que[$i]['id'];
                $id=(string)$arr_paper_answer[$i]['id'];
                if($paper_ans==$user_ans){
                    $user_que_score=$score;
                }else{
                    $user_que_score=0;
                }
                //echo $user_que_score;
                // file_put_contents("./dataTest.txt", "user_que_score=".$user_que_score."\n",FILE_APPEND);
                
                //先确定<question id="">存在吗
                $question_exist=$detail_xml->xpath("//question[@id='$id']");
                if(count($question_exist)>0){
                    //存在的话，修改分值
                    $question_exist[0]['submit']='1';
                    $question_exist[0]->text=$user_que_score;
                    $question_exist[0]->comment="";

                } 

            }

        }
        // dd($detail_xml);
        return $detail_xml;



    }








    
}
