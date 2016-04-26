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

	<!--客观题自动评分-->
	<div class="objSection">
		<div class="secName">一、选择题</div>
		<ul class="question">
		
		<li>
			<div class="question_left">总分:</div>{{$score_total_sel}}分
		</li>
		<li>
			<div class="question_left">学生答案:</div>&nbsp;{{$str_user_sele_answer}}
		</li>
		<li>
			<div class="question_left">参考答案:</div>&nbsp;{{$str_std_sele_answer}}
		</li>

		<li>
			<div class="question_left">一共:</div>&nbsp;{{$select_num}}&nbsp;题
		</li>
		<li>
			<div class="question_left">答对:</div>&nbsp;{{$cor_sele_num}}&nbsp;题
		</li>

		<li>
				<div class="question_left">得分：</div>

				<form id="selectSave" action="./answerSave" method="POST" >
				{{ csrf_field() }}
				<input title="试卷名" type="text" name="paperName_scorer" id="paperName" value="{{$select_grade}}">
				</input>
				<!--隐藏传输的信息-->
				<input type="hidden" name="user_paper_id" value="{{$user_paper_id}}" />
				<input type="hidden" name="scorer_id" value="{{$scorer_id}}" />
				<!--题号-->
				<input type="hidden" name="question_id" value="select" />
				<!--该题分值-->
				<input type="hidden" name="question_score" value="{{$score_total_sel}}" />
				<input type="button" name="save" id="btn_select" value="确认"></input>
				
				</form>
				<div id="conselect" class="confirm">.</div>

			</li>
		
		</ul>
	</div>
	<!--客观题结束-->

	<!--主观题-->
	<div class="subSection">
		<div class="secName">二、综合题</div>

		<?php
		for($i=$select_num;$i<count($std_total_answer);$i++){
	            //试卷简答题试题
			?>
				<ul class="question">
					<li class="question_Name">
					<!--{{$std_total_answer[$i]['id'].'  '.$std_short_answers[$i-$select_num]}}-->
					{!!$std_total_answer[$i]['id'].'  '.$std_short_answers[$i-$select_num]!!}
					</li>
					<li>
						<div class="{{ $questions_flag[$i-$select_num]==1 ? 'question_total' : '' }}"><div class="question_left">总分：</div>{{$std_scores[$i-$select_num]}}分</div>
					</li>
					<li>
						<div class="question_left">学生答案：</div>{{$user_total_answer[$i]->text}}
					</li>
					<li>
						<div class="question_left">参考答案：</div>{{$std_total_answer[$i]->text}}
					</li>

					<form id="saveForm{{$std_total_answer[$i]['id']}}" action="{{ url('/answerSave')}}" method="POST" >
					<li class="queCom">
					<div class="question_left">试题备注：</div>
					<textarea class="queCom" name="queText" id="queText"  placeholder="请在此填写试题备注...">{{$tea_save_coms[$i-$select_num]}}</textarea>
					</li>


					<li>
						<!--<form id="saveForm{{$std_total_answer[$i]['id']}}" action="{{ url('/answerSave')}}" method="POST" >-->
						{{ csrf_field() }}
						<div class="question_left">得分：</div>
						<input title="试卷名" type="text" name="paperName_scorer" id="paperName" value="{{$tea_save_anws[$i-$select_num]}}">
						</input>
						<input type="hidden" name="user_paper_id" value="{{$user_paper_id}}" />
						<input type="hidden" name="scorer_id" value="{{$scorer_id}}" />
						<!--题号-->
						<input type="hidden" name="question_id" value="{{$std_total_answer[$i]['id']}}" />
						<!--该题分值-->
						<input type="hidden" name="question_score" value="{{$std_scores[$i-$select_num]}}" />
						<input type="button" id="save{{$std_total_answer[$i]['id']}}" name="save" value="保存"></input>
						<!--</form>-->
						<div id="con{{$std_total_answer[$i]['id']}}" class="confirm"></div>
					</li>
					</form>
				</ul>

			<?php 
				/*
	            echo $std_total_answer[$i]['id'].'  ';
	            echo $std_scores[$i-$select_num].'  ';
	            echo '试题:'.$std_short_answers[$i-$select_num].'<br/>';
	            echo '参考答案   '.$std_total_answer[$i]->text.'<br/>';
	            echo '用户答案  '.$user_total_answer[$i]->text.'<br/>';
	            */
	        }




		?>

		
	</div>
	<!--主观题结束-->

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
</div>
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

		$('#selectSave #btn_select').on('click',function(){
			$('#selectSave').ajaxForm(answer_save).submit();
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

		var comment_save={
			url: "{{ url('/commentSave')}}",
			type: 'POST',
			dataType: 'json',//返回的数据类型
			success: showResponseCom,
		};

		$('#commentForm #comment_but').on('click',function(){
			$('#commentForm').ajaxForm(comment_save).submit();
		});

		<?php
		for($i=$select_num;$i<count($std_total_answer);$i++){
			//试卷简答题试题
			?>
			$('#saveForm{{$std_total_answer[$i]['id']}} #save{{$std_total_answer[$i]['id']}}').on('click',function(){
			$('#saveForm{{$std_total_answer[$i]['id']}}').ajaxForm(answer_save).submit();
			});
		

		<?php

		}
	?>

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