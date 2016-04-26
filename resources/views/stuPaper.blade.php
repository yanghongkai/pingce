@extends('layouts.app')
@section('nav')
<li><a href="{{ url('/')}}" >主页</a></li>
<li><a href="{{ url('/download')}}" >资源下载</a></li>
<li><a href="{{ url('/evaluate')}}" >试卷评测</a></li>
<li><a href="{{ url('/user')}}" style="color:#B44242; border-bottom:2px solid #B44242;">用户中心</a></li>

@endsection

@section('content')
<!--面包树-->
<div class="navBar">
	<a href="{{ url('/userEvaluate')}}">评测记录</a>&nbsp;>&nbsp; <a href="javascript:void(0);">{{$paper_name}}</a>
</div>
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
				<input title="试卷名" type="text" name="paperName_scorer" id="paperName" readonly="readonly" value="{{$select_grade}}">
				</input>
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
					<li class="queCom">
					<div class="question_left">试题备注：</div>
					<textarea class="queCom" name="queText" id="queText"  placeholder="请在此填写试题备注...">{{$tea_save_coms[$i-$select_num]}}</textarea>
					</li>
					<li>
						<div class="question_left">得分：</div>
						<input title="试卷名" type="text" name="paperName_scorer" id="paperName" readonly="readonly" value="{{$tea_save_anws[$i-$select_num]}}">
						</input>
					</li>
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
	<!-- 相关文件下载 -->
	<div class="sourceDnld">
		<div class="dnldName">相关资源下载：</div>
		<p><a href="../downloadByPath?path={{$paper_content_path}}">下载试题</a></p>
		<p><a href="../downloadByPath?path={{$user_answer_path}}">下载我的答案</a></p>
		<p><a href="../downloadByPath?path={{$paper_answer_path}}">下载参考答案</a></p>
	</div>

	<!--返回评测页-->
	<div class="backEva"><a href="{{ url('/userEvaluate')}}"><返回</a></div>
	
</div>
	

<script type="text/javascript">
	

</script>



	@endsection