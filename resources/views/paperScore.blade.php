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
		//解析公式
    	function parseLatex_ps($str){
        $pattern='/\$([^$]*)\$/U';
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
    	$pattern='/<option[^>]+>(.*)<\/option>/U';
	    preg_match_all($pattern,$str,$matches);
	    //dd($matches);
	    if(count($matches[1])>0){
	        return $matches[1][0];
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
				{!!$arr_ques_head_text[$i]!!}(总分{{$arr_ques_score[$i]}}分)
			</div>
			<div class="questions_title">
				{!!$arr_ques_title[$i]!!}
			</div>
			<div class="questions_text">
				{!!$arr_ques_text[$i]!!}
			</div>
			<!--
			<div class="questions_score">
				总分值：20分
			</div>
			-->
		</div>

		@for($j=0; $j<$arr_ques_count[$i];$j++,$k++)
		<ul class="question">
			@if((string)$arr_que[$k]['type']=='select')
			<li class="question_Name">
				{{$arr_que[$k]['id']}}
				{!!$arr_que[$k]->headtext->asXML()!!}
				<!-- {!!$arr_que[$k]->text->asXML()!!} -->
				<?php
					echo parseLatex_ps($arr_que[$k]->text->asXML());
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
                   	$str=removeOption($sel_option->asXMl());
					echo parseLatex_ps($str);
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
					echo parseLatex_ps($arr_que[$k]->text->asXML());
				?>
				</li>
			@endif

			@if((string)$arr_que[$k]['type']=='fillblank')
				<li class="question_Name">
				{{$arr_que[$k]['id']}}
				{!!$arr_que[$k]->headtext->asXML()!!}
				<!-- {!!$arr_que[$k]->text->asXML()!!} -->
				<?php
					echo parseLatex_ps($arr_que[$k]->text->asXML());
					echo parseLatex_ps($arr_que[$k]->blank->asXML());

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
                		// echo $ans_text.' ';
                		echo parseLatex_ps($ans_text);
            		}
				?>
			</li>
			<li>
				<div class="question_left">参考答案：</div>
				<?php 
					$ans_texts=$arr_paper_answer[$k]->xpath('.//text');
					foreach ($ans_texts as $ans_text){
                		// echo $ans_text.' ';
                		echo parseLatex_ps($ans_text);

            		}
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