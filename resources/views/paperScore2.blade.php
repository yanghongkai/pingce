@extends('layouts.app')
@section('nav')
<li><a href="{{ url('/')}}" >主页</a></li>
<li><a href="{{ url('/download')}}" >资源下载</a></li>
<li><a href="{{ url('/evaluate')}}" >试卷评测</a></li>
<li><a href="{{ url('/user')}}" style="color:#B44242; border-bottom:2px solid #B44242;">用户中心</a></li>

@endsection

@section('content')
<!--阅卷-->
<div class="paperScore">
	<!--试卷信息-->
	<div class="paperDetail">
		<div class="paperName">{{$paper_name}}</div>
		<div class="paperInfo">
			<span>上传者：{{$stu_name}}</span>
			<span>上传时间：{{$user_paper_time}}</span>
		</div>
	</div>
	<!--试卷信息结束-->
	<?php
	use App\Paper;
		//解析公式
    function parseLatex_ps($str){
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
    function removeOption($str){
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
    function parseLabel($str){
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
    function getImgSrc($paper_id,$pic_id,$type){
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
    function getImgSrcUser($user_paper_id,$pic_id){
    	$new_name=$user_paper_id."_".$pic_id;
    	$new_name='../../content/userPics/'.$new_name.".jpg";
    	return $new_name;
    }

    //解析pic
    function parsePic($paper_id,$str,$type){
    	//对<Picread id=""/>进行处理
    	$str=str_replace("/>", "></Picread>", $str);
	    $pattern='/<Picread id="(.*)">.*<\/Picread>/Us';
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
	        $img_src=getImgSrc($paper_id,$pic_id,$type);
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
    function parsePicUser($user_paper_id,$str){
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
	        $img_src=getImgSrcUser($user_paper_id,$pic_id);
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
    function parseTable($str){
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
    function parseOptionLabel($str){
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


	?>


	<!--试题开始-->
	@if(count($arr_ques_title)>0)
	@for($i=0,$k=0;$i<count($arr_ques_title);$i++)
	<div class="questions">
		
		<div class="questions_detail">
			<div class="questions_title">
				<!-- {!!$arr_ques_head_text[$i]!!} -->
				<?php
					$str=$arr_ques_head_text[$i];
					//label解析
					$str=parseLabel($str);
					//解析pic
					$str=parsePic($paper_id,$str,'paper');
					//latex
					echo parseLatex_ps($str);

				?>
				(总分{{$arr_ques_score[$i]}}分)
			</div>
			<div class="questions_title">
				{!!$arr_ques_title[$i]!!}
			</div>
			<!-- <div class="questions_text">
				{!!$arr_ques_text[$i]!!}
			</div> -->
			<div class="questions_text">
			<?php
				$str=$arr_ques_text[$i];
				//label解析
				$str=parseLabel($str);
				//解析pic
				$str=parsePic($paper_id,$str,'paper');
				//latex
				echo parseLatex_ps($str);
				//table
				// echo parseTable($arr_ques_table[$i]);
				$str_tab=$arr_ques_table[$i];
				//table
				$str_tab=parseTable($str_tab);
				//latex
				$str_tab=parseLatex_ps($str_tab);
				echo $str_tab;
			?>
			</div>
			
		</div>

		@for($j=0; $j<$arr_ques_count[$i];$j++,$k++)
		<ul class="question">
			@if((string)$arr_que[$k]['type']=='select')
			<li class="question_Name">
				{{$arr_que[$k]['id']}}
				{!!$arr_que[$k]->headtext->asXML()!!}
				<!-- {!!$arr_que[$k]->text->asXML()!!} -->
				<?php
					$str=$arr_que[$k]->text->asXML();
					//label解析
					$str=parseLabel($str);
					//解析pic
					$str=parsePic($paper_id,$str,'paper');
					//latex
					echo parseLatex_ps($str);
					//table
					// echo parseTable($arr_que[$k]->tab->asXML());
					$str_tab=$arr_que[$k]->tab->asXML();
					$str_tab=parseTable($str_tab);
					$str_tab=parseLatex_ps($str_tab);
					//latex
					echo $str_tab;
				?>

			</li>
				<?php
				$sel_options=$arr_que[$k]->select->xpath('.//option');
                foreach($sel_options as $sel_option){

                	?>
                    <!--<li class="question_select">{{$sel_option['value']}} &nbsp;{{$sel_option}}</li>-->
                   <!-- <li class="question_select">{{$sel_option['value']}} &nbsp;{!!$sel_option->asXML()!!}</li> -->
                   <li class="question_select">{{$sel_option['value']}} &nbsp;
                   <?php
                    //县解析option_label
                   	$str=$sel_option->asXMl();
                   	//pic
                   	$str=parsePic($paper_id,$str,'paper');
                   	$str=parseOptionLabel($str);
                   	$str=parseLatex_ps($str);
                   	$str=removeOption($str);
					echo $str;
					?>
					</li>
					<?php
                }
                ?>
			@endif

			@if((string)$arr_que[$k]['type']=='shortanswer')
				<li class="question_Name">
				{{$arr_que[$k]['id']}}
				<!-- {!!$arr_que[$k]->text->asXML()!!} -->
				<?php
					
					$str=$arr_que[$k]->text->asXML();
					//label
					$str=parseLabel($str);
					//解析pic
					$str=parsePic($paper_id,$str,'paper');
					//公式
					echo parseLatex_ps($str);
					//table
					// echo parseTable($arr_que[$k]->tab->asXML());
					$str_tab=$arr_que[$k]->tab->asXML();
					//table
					$str_tab=parseTable($str_tab);
					//latex
					$str_tab=parseLatex_ps($str_tab);
					echo $str_tab;
				?>
				</li>
			@endif

			@if((string)$arr_que[$k]['type']=='fillblank')
				<li class="question_Name">
				{{$arr_que[$k]['id']}}
				{!!$arr_que[$k]->headtext->asXML()!!}
				<!-- {!!$arr_que[$k]->text->asXML()!!} -->
				<?php
					$str_text=$arr_que[$k]->text->asXML();
					//label
					$str_text=parseLabel($str_text);
					//pic
					$str_text=parsePic($paper_id,$str_text,'paper');
					//latex
					echo parseLatex_ps($str_text);
					//table
					// echo parseTable($arr_que[$k]->tab->asXML());
					// echo parseLatex_ps($arr_que[$k]->blank->asXML());
					//数学blank里面有label
					$str=parseLabel($arr_que[$k]->blank->asXML());
					//解析pic
					$str=parsePic($paper_id,$str,'paper');
					//latex
					echo parseLatex_ps($str);
					//table
					// echo parseTable($arr_que[$k]->tab->asXML());
					$str_tab=$arr_que[$k]->tab->asXML();
					// echo $str_tab;
					$str_tab=parseTable($str_tab);
					//latex
					$str_tab=parseLatex_ps($str_tab);
					echo $str_tab;
					// echo parseTable($str_tab);

				?>
				<!-- {!!$arr_que[$k]->blank->asXML()!!} -->
				</li>
			@endif

			@if((string)$arr_que[$k]['type']=='composition')
				<li class="question_Name">
				{{$arr_que[$k]['id']}}
				{!!$arr_que[$k]->title->asXML()!!}
				{!!$arr_que[$k]->text->asXML()!!}
				</li>
			@endif

			@if((string)$arr_que[$k]['type']=='punctuation')
				<li class="question_Name">
				{{$arr_que[$k]['id']}}
				{!!$arr_que[$k]->text->asXML()!!}<br/>
				{!!$arr_que[$k]->passage->asXML()!!}<br/>
				{!!$arr_que[$k]->term->asXML()!!}
				</li>
			@endif

			<!--
			<li class="question_select">
				A.你猜
			</li>
			-->
			<li>
				<!--<div class="question_total"><div class="question_left">总分：</div>{{$arr_que[$k]['score']}}分</div>-->
				<div class="question_left">总分：</div>{{$arr_que[$k]['score']}}分
			</li>
			<li>
				<div class="question_left">学生答案：</div>
				<?php 
					$ans_texts=$arr_user_answer[$k]->xpath('.//text');
					foreach ($ans_texts as $ans_text){
                		$str=$ans_text->asXML();
                		$str=parseLabel($str);
						//解析pic
						$str=parsePicUser($user_paper_id,$str);
						//latex
						echo parseLatex_ps($str)."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                		// echo parseLatex_ps($ans_text)."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
            		}
            		//table
            		$str=$arr_user_answer[$k]->tab->asXML();
            		$str=parseTable($str);
            		//latex
					$str=parseLatex_ps($str);
					echo $str;
				?>
			</li>
			<li>
				<div class="question_left">参考答案：</div>
				<?php 
					$ans_texts=$arr_paper_answer[$k]->xpath('.//text');
					foreach ($ans_texts as $ans_text){
                		// echo $ans_text.' ';
                		$str=$ans_text->asXML();
                		$str=parseLabel($str);
						//解析pic
						$str=parsePic($paper_id,$str,'answer');
						//latex
						echo parseLatex_ps($str)."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

                		// echo parseLatex_ps($ans_text->asXML())."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

            		}
            		//table 先进行table处理，再进行LaTeX处理
            		$str=$arr_paper_answer[$k]->tab->asXML();
            		$str=parseTable($str);
            		//latex
					$str=parseLatex_ps($str);
					echo $str;
            		// echo parseTable($str);

				?>
			</li>

			<form id="saveForm{{$arr_que[$k]['id']}}" action="{{ url('/answerSave')}}" method="POST" >
			{{ csrf_field() }}

			@if((string)$arr_que[$k]['type']!='select')
			<li class="queCom">
				<div class="question_left">试题备注：</div>
				<textarea class="queCom" name="queText" id="queText" placeholder="请在此填写试题备注...">{{ $tea_save_coms[$k] }}</textarea>
			</li>
			@endif
			<li>
				<div class="question_left unread">得分：</div>
				<input title="试卷名" type="text" name="paperName_scorer" id="paperName" value="{{$tea_save_anws[$k]}}">
				</input>
				<!--<div class="select">*任选四道题</div>-->
				@if($arr_maxnum[$i]>0)
					<div class="select">{{'任选其中'.$arr_maxnum[$i].'道题'}}</div>
				@endif
				<!--隐藏信息-->
				<input type="hidden" name="user_paper_id" value="{{$user_paper_id}}" />
				<input type="hidden" name="scorer_id" value="{{$scorer_id}}" />
				<!--题号-->
				<input type="hidden" name="question_id" value="{{$arr_que[$k]['id']}}" />
				<!--该题分值-->
				<input type="hidden" name="question_score" value="{{$arr_que[$k]['score']}}" />

				<input type="button" id="save{{$arr_que[$k]['id']}}" name="save" value="保存"></input>
				<div id="con{{$arr_que[$k]['id']}}" class="confirm"></div>

			</li>
			</form>
		</ul>
		@endfor
		<hr/>
	@endfor
</div>
@endif

<!--试卷备注-->
	
	<div class="comments">
		<div class="secName">*试卷备注</div>
		<form id="commentForm" action="{{ url('/commentSave')}}" method="POST" >
		{{ csrf_field() }}
		<!--隐藏传输的信息-->
		
		<input type="hidden" name="user_paper_id" value="{{$user_paper_id}}" />
		<input type="hidden" name="scorer_id" value="{{$scorer_id}}" />

		<textarea class="comTextarea" name="comment" id="comText" placeholder="请在此填写试卷备注...">{{$comment}}</textarea>
		<!--<input type="button" id="comment_but" name="save" value="确认"></input>-->
		<div class="res">
			<input type="button" id="comment_but" name="save" value="确认"></input>
			<div id="com_div" class="comPro"></div>
		</div>
		</form>

	</div>
	
	<!--试卷备注结束-->

	<!--提交开始-->
	<div class="submitBar">
		<div class="inner">
		<a href="{{ url('/userScorer')}}"><input type="button" id="lf-cancle" name="cancle" value="返回"></input></a>

		<form id="tot_submit" action="{{ url('/gradeSave')}}" method="POST" >
		{{ csrf_field() }}
		<!--隐藏传输的信息-->
		
		<input type="hidden" name="user_paper_id" value="{{$user_paper_id}}" />
		<input type="hidden" name="scorer_id" value="{{$scorer_id}}" />
		
		<!--计算总成绩-->
		<input type="button" name="submit" id="rl-submit" value="提交"></input>
		</form>

		</div>
	</div>
	<!--提交结束-->

	
	

<script type="text/javascript">
	$(function(){
		var answer_save={
			url: "{{ url('/answerSave')}}",
			type: 'POST',
			dataType: 'json',//返回的数据类型
			success: showResponse,
		};

		<?php
		for($i=0;$i<count($arr_que);$i++){
			//试卷简答题试题
			?>
			$('#saveForm{{$arr_que[$i]['id']}} #save{{$arr_que[$i]['id']}}').on('click',function(){
			$('#saveForm{{$arr_que[$i]['id']}}').ajaxForm(answer_save).submit();
			});
		

		<?php

		}
	?>


		var comment_save={
			url: "{{ url('/commentSave')}}",
			type: 'POST',
			dataType: 'json',//返回的数据类型
			success: showResponseCom,
		};

		$('#commentForm #comment_but').on('click',function(){
			$('#commentForm').ajaxForm(comment_save).submit();
		});

		var total_save={
			url: "{{ url('/gradeSave')}}",
			type: 'POST',
			dataType: 'json',//返回的数据类型
			success: showResponseTot,
		};

		$('#tot_submit #rl-submit').on('click',function(){
			$('#tot_submit').ajaxForm(total_save).submit();
		});




	});

	
	function showResponse(response){
		if(response.success==true){
			$("#con"+response.id).html("保存成功!");
		}
		if(response.success==false){
			$("#con"+response.id).html("保存失败!");
		}

	}

	function showResponseCom(response){
		if(response.success==true){
			$("#com_div").html("保存成功!");
		}
		if(response.success==false){
			$("#com_div").html("保存失败!");
		}

	}

	function showResponseTot(response){
		if(response.success==true){
			window.location.href="{{ url('/userScorer')}}"; 
		}
		if(response.success==false){
			alert("有试题未批改!");
		}

	}



	


</script>



	@endsection