<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
* 
*/
class Parser 
{
	//解析公式
    static function  parseLatex_ps($str,$category){
    	if($category=='chinese'){
    		$str=str_replace("$$", "$", $str);
    		return $str;
    	}
        //将$$转化为$$
    	$str=str_replace("$$", "$", $str);
	    $pattern='/\$([^$]*)\$/Usi';
        preg_match_all($pattern,$str,$matches);
        $arr_split=preg_split($pattern,$str);
        //dd($arr_split);
        //dd($matches);
        $count=count($matches[1]);
        $str_new="";
        for($i=0;$i<count($matches[1]);$i++){
            //$arr_replace[]='<img src="http://latex.codecogs.com/gif.latex?'.$matches[1][$i].'" />';
            $str_new.=$arr_split[$i];
            $str_new.='<img src="http://latex.codecogs.com/gif.latex?'.$matches[1][$i].'" />';
        }
        $str_new.=$arr_split[$count];
        if($count>0){
            return $str_new;
        }else{
            return $str;
        }


    }

    //去掉option
    static function  removeOption($str){
    	$pattern='/<option[^>]+>(.*)<\/option>/Usi';
	    preg_match_all($pattern,$str,$matches);
	    //dd($matches);
	    if(count($matches[1])>0){
	        return $matches[1][0];
	    }else{
	        return $str;
	    }
    }

    //将label处理成①②③的形式
    static function  parseLabel($str){
    	$pattern='/<label[^>]+>(.*)<\/label>/Usi';
	    preg_match_all($pattern,$str,$matches);
	    // dd($matches);
	    $arr_split=preg_split($pattern,$str);
	    // dd($arr_split);
	    $arr_mark=array();
	    $arr_mark[0]="①";
	    $arr_mark[1]="②";
	    $arr_mark[2]="③";
	    $arr_mark[3]="④";
	    $arr_mark[4]="⑤";
	    $arr_mark[5]="⑥";
	    $arr_mark[6]="⑦";
	    $arr_mark[7]="⑧";
	    $arr_mark[8]="⑨";
	    $count=count($matches[1]);
	    $str_new="";
	    for($i=0;$i<$count;$i++){
	        $str_new.=$arr_split[$i];
	        $str_new.=$arr_mark[$i];
	        $str_new.=$matches[1][$i];
	    }
	    $str_new.=$arr_split[$count];
	    if($count>0){
	        return $str_new;
	    }else{
	        return $str;
	    }


    }


    //得到pic的src 默认是试卷的pic
    static function  getImgSrc($paper_id,$pic_id,$type){
    	$paper=Paper::where('id',$paper_id)->first();
        $content_path=$paper->content;
        $content=simplexml_load_file('./content/'.$content_path);
        $content_id=(string)$content['id'];
        $content_subject=(string)$content['subject'];
        if($type=="paper"){
        	$new_name=$content_id."_".$content_subject."_".$pic_id;
        }
        if($type=="answer"){
        	$new_name=$content_id."_".$content_subject."_".$pic_id."_answer";
        }
        
        $new_name='../../content/pics/'.$new_name.".jpg";
        return $new_name;

    }


    //得到用户pic的src
    static function  getImgSrcUser($user_paper_id,$pic_id){
    	$new_name=$user_paper_id."_".$pic_id;
    	$new_name='../../content/userPics/'.$new_name.".jpg";
    	return $new_name;
    }


    //解析pic
    static function  parsePic($paper_id,$str,$type){
    	//对<Picread id=""/>进行处理
    	$str=str_replace("/>", "></Picread>", $str);
	    $pattern='/<Picread id="(.*)">.*<\/Picread>/Usi';
	    preg_match_all($pattern, $str, $matches);
	    // dd($matches);
	    $arr_split=preg_split($pattern, $str);
	    // dd($arr_split);
	    $pattern_con='/<Picread id=".*">(.*)<\/Picread>/Usi';
	    preg_match_all($pattern_con, $str, $matches_con);
	    // dd($matches_con);
	    $count=count($matches[1]);
	    $str_new='';
	    for($i=0;$i<$count;$i++){
	        $pic_id=$matches[1][$i];
	        $img_src=self::getImgSrc($paper_id,$pic_id,$type);
	        $str_new.=$arr_split[$i];
	        $str_new.=$matches_con[1][$i];
	        
	        // $str_new.='<img src="../../content/pics/2015BeijingGaokao_Math_Pic5_1.jpg"/>';
	        $str_new.='<br/><img src="'.$img_src.'"/><br/>';

	    }
	    $str_new.=$arr_split[$count];
	    if($count>0){
	    	// dd($str_new);
	    	return $str_new;
	    }else{
	    	return $str;
	    }


    }


    //解析用户试卷的pic
    static function  parsePicUser($user_paper_id,$str){
    	$pattern='/<Picread id="(.*)">.*<\/Picread>/Usi';
	    preg_match_all($pattern, $str, $matches);
	    // dd($matches);
	    $arr_split=preg_split($pattern, $str);
	    // dd($arr_split);
	    $pattern_con='/<Picread id=".*">(.*)<\/Picread>/Usi';
	    preg_match_all($pattern_con, $str, $matches_con);
	    // dd($matches_con);
	    $count=count($matches[1]);
	    $str_new='';
	    for($i=0;$i<$count;$i++){
	        $pic_id=$matches[1][$i];
	        $img_src=self::getImgSrcUser($user_paper_id,$pic_id);
	        $str_new.=$arr_split[$i];
	        $str_new.=$matches_con[1][$i];
	        
	        // $str_new.='<img src="../../content/pics/2015BeijingGaokao_Math_Pic5_1.jpg"/>';
	        $str_new.='<br/><img src="'.$img_src.'"/><br/>';

	    }
	    $str_new.=$arr_split[$count];
	    if($count>0){
	    	// dd($str_new);
	    	return $str_new;
	    }else{
	    	return $str;
	    }

    }


    // //解析表格
    // function parseTable($str){
    // 	$search_arr=array("tab","row","col");
	   //  $replace_arr=array("table border='1'","tr","td");
	   //  $str=str_replace($search_arr, $replace_arr, $str);
	   //  return $str;

    // }

    //解析表格
    static function  parseTable($str){
    	if(empty($str)){
    		return $str;
    	}
    	// dd($str);

    	$tab_xml=simplexml_load_string($str);
	    // dd($tab_xml);
	    $tab_id=$tab_xml['id'];
	    $rows=$tab_xml->xpath('//row');
	    $vals=$tab_xml->xpath('//val');
	    $vals_count=count($vals);
	    // dd($heads);
	    // dd($rows);
	    $str_new="";
	    $str_new.='<table border="1" id="'.$tab_id.'">';
	    for($i=0;$i<count($rows);$i++){
	        if($i==0 && $vals_count>0){
	            if($vals_count==2){
	                $str_new.='<thead><tr class="a-ca-t-worker"><th rowspan="2" class="tableHeadTitle"><div class="tableLine"><table><thead>';
	                $row=$rows[$i];
	                $cols=$row->xpath('.//col');
	                // dd($cols);
	                for($j=0;$j<count($cols);$j++){
	                    $col=$cols[$j];
	                    $vals=$col->xpath('.//val');
	                    if(count($vals)>0){
	                        for($k=0;$k<count($vals);$k++){
	                            // echo $vals[$k]." ";
	                            if($k==0){
                                $str_new.='<tr><th></th><th>'.$vals[$vals_count-1-$k].'</th></tr>';
	                            }
	                            if($k==1){
	                                $str_new.='<tr><th>'.$vals[$vals_count-1-$k].'</th><th></th></tr>';
	                            }
	                            
	                        }
	                        $str_new.='</tr></thead></table></div></th>';
	                    }else{
	                        // echo $col;
	                        $str_new.='<th>'.$col.'</th>';
	                    }
	                }
	                $str_new.='</tr></thead>';
	            }
	            if($vals_count==3){
	                $str_new.='<thead><tr class="a-ca-t-worker"><th rowspan="3" class="tableHeadTitle"><div class="tableLine2"><table><thead>';
	                // $str_new.='<thead><tr class=""><th rowspan="3" class=""><div class=""><table><thead>';
	                $row=$rows[$i];
	                $cols=$row->xpath('.//col');
	                // dd($cols);
	                for($j=0;$j<count($cols);$j++){
	                    $col=$cols[$j];
	                    $vals=$col->xpath('.//val');
	                    if(count($vals)>0){
	                        for($k=0;$k<count($vals);$k++){
	                            // echo $vals[$k]." ";
	                            if($k==0){
	                                $str_new.='<tr><th></th><th></th><th>'.$vals[$vals_count-1-$k].'</th></tr>';
	                            }
	                            if($k==1){
	                                $str_new.='<tr><th></th><th>'.$vals[$vals_count-1-$k].'</th><th></th></tr>';
	                            }
	                            if($k==2){
	                                $str_new.='<tr><th>'.$vals[$vals_count-1-$k].'</th><th></th><th></th></tr>';
	                            }
	                            
	                        }
	                        $str_new.='</tr></thead></table></div></th>';
	                    }else{
	                        // echo $col;
	                        $str_new.='<th>'.$col.'</th>';
	                    }
	                }
	                $str_new.='</tr></thead>';


	            }
	            
	            

	        }else{
	            $str_new.='<tr/>';
	            $row=$rows[$i];
	            $cols=$row->xpath('.//col');
	            // dd($cols);
	            for($j=0;$j<count($cols);$j++){
	                $col=$cols[$j];
	                $vals=$col->xpath('.//val');
	                // dd($vals);
	                if(count($vals)>0){
	                    for($k=0;$k<count($vals);$k++){
	                        // echo $vals[$k]." ";
	                    }
	                }else{
	                    $str_new.='<td>'.$col.'</td>';
	                }
	            }
	            $str_new.='</tr>';
	        }
	        
	    }
	    $str_new.='</table>';
	    // dd($str_new);
	    return $str_new;

	    

    }


    //解析option_label
    static function  parseOptionLabel($str){
    	$pattern='/<label id=".*_(.)"\/>/Usi';
	    preg_match_all($pattern, $str, $matches);
	    // dd($matches);
	    $arr_split=preg_split($pattern,$str);
	    // dd($arr_split);
	    $arr_mark=array();
	    $arr_mark[0]="①";
	    $arr_mark[1]="②";
	    $arr_mark[2]="③";
	    $arr_mark[3]="④";
	    $arr_mark[4]="⑤";
	    $arr_mark[5]="⑥";
	    $arr_mark[6]="⑦";
	    $arr_mark[7]="⑧";
	    $arr_mark[8]="⑨";
	    $count=count($matches[1]);
	    $str_new="";
	    for($i=0;$i<$count;$i++){
	        $str_new.=$arr_split[$i];
	        $str_new.=$arr_mark[$i];
	        // $str_new.=$matches[1][$i];
	    }
	    $str_new.=$arr_split[$count];
	    if($count>0){
	        // dd($str_new);
	        return $str_new;
	    }else{
	        return $str;
	    }

    }

    //根据question_id返回相应的question对象
    static function getQuestionById($user_answer,$question_id){
    	//$user_ans是一个数组
    	$user_ans=$user_answer->xpath('//question[@id="'.$question_id.'"]');
    	if(empty($user_ans)){
    		return ;
    	}
    	return $user_ans[0];

    }

    //解析()
    static public function parseBracket($str){
        $pattern='/[()]/';
        $str=preg_replace($pattern, "", $str);
        $str_arr=explode(",", $str);
        // dd($str_arr);
        
        $str_new="";
        if($str_arr[0]=="pic" || $str_arr[0]=="Pic"){
            if($str_arr[1]==0){
                $str_new.='<Picread id="'.$str_arr[2].'">';
            }
            if($str_arr[1]==1){
                $str_new.='</Picread>';
            }
        }
        if($str_arr[0]=="label" || $str_arr[0]=="Label"){
            if($str_arr[1]==0){
                $str_new.='<label id="'.$str_arr[2].'">';
            }
            if($str_arr[1]==1){
                $str_new.='</label>';
            }
        }

        if($str_arr[0]=="tab" || $str_arr[0]=="Tab"){
            if($str_arr[1]==0){
                $str_new.='<Tabread id="'.$str_arr[2].'">';
            }
            if($str_arr[1]==1){
                $str_new.='</Tabread>';
            }
        }

        if($str_arr[0]=="u" || $str_arr[0]=="U"){
            if($str_arr[1]==0){
                $str_new.='<u>';
            }
            if($str_arr[1]==1){
                $str_new.='</u>';
            }
        }

        if($str_arr[0]=="point" || $str_arr[0]=="Point"){
            if($str_arr[1]==0){
                // $str_new.='<point>';
                // $str_new.='<u>';
                $str_new.='<span class="emphasis">';
            }
            if($str_arr[1]==1){
                // $str_new.='</point>';
                // $str_new.='</u>';
                $str_new.='</span>';
            }
        }
        // dd($str_new);
        return $str_new;
    }

    //解析**()**
    static public function parseStr($str){
    	$pattern='/\*\*(.*)\*\*/Usi';
	    preg_match_all($pattern, $str, $matches);
	    // dd($matches);
	    $new_rule_arr=array();

	    for($i=0;$i<count($matches[1]);$i++){
	        // echo $matches[1][$i].'<br/>';
	        $new_rule_arr[]=self::parseBracket($matches[1][$i]);
	    }
	    // dd($new_rule_arr);
	    $arr_split=preg_split($pattern, $str);
	    // dd($arr_split);
	    $str_new="";
	    $count=count($new_rule_arr);
	    for($i=0;$i<count($new_rule_arr);$i++){
	        $str_new.=$arr_split[$i];
	        $str_new.=$new_rule_arr[$i];
	    }
	    $str_new.=$arr_split[$count];
	    return $str_new;
    }








	
}