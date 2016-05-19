@extends('layouts.app')
@section('nav')
<li><a href="{{ url('/')}}" >主页</a></li>
<li><a href="{{ url('/download')}}" >资源下载</a></li>
<li><a href="{{ url('/evaluate')}}" >试卷评测</a></li>
<li><a href="{{ url('/user')}}" style="color:#B44242; border-bottom:2px solid #B44242;">用户中心</a></li>

@endsection

@section('content')
<!--用户中心_评测-->
<div class="user">
	<!--侧边导航栏-->
	<div class="user_sidebar">
		<a href="{{ url('/userEvaluate')}}">评测记录</a>
		<a href="{{ url('/evaluateProfile')}}">个人资料</a>
	</div>
	<!--右侧表格-->
	<div class="user_form">
		<div class="pro_title">评测记录</div>
		<table>
			<tbody>
				<tr class="user_form_title">
					<td>试卷名</td>
					<td>提交时间</td>
					<td>试卷状态</td>
					<!--<td>得分</td>-->
					<td>主观分</td>
					<td>客观分</td>
					<td>总得分</td>
					<td>阅卷人</td>
					<td><a href="#">我的试卷</a></td>
					<td><a href="#">添加图片</a></td>
					<!--
					<td><a href="#">试题</a></td>
					<td><a href="#">我的答案</a></td>
					<td><a href="#">参考答案</a></td>
					-->
				</tr>
				<?php

				if(count($arr_paper_name)>0){
					for ($i=0; $i<count($arr_paper_name); $i++){
				?>
						<tr class="user_form_item">
						<td>{{$arr_paper_name[$i]}}</td>
						<td>{{$arr_updated_at[$i]}}</td>
						<td>{{$arr_paper_status[$i]}}</td>
						<!--<td>{{$arr_grade[$i]}}</td>-->
						<td>{{$arr_subject_grade[$i]}}</td>
						<td>{{$arr_object_grade[$i]}}</td>
						<td>{{$arr_grade[$i]}}</td>

						<td>{{$arr_scorers[$i]}}</td>
						<!--
						<td><a href=" ./downloadByPath?path={{$arr_paper_con[$i]}} ">试题</a></td>
						<td><a href=" ./downloadByPath?path={{$arr_user_ans[$i]}} ">我的答案</a></td>
						<td><a href=" ./downloadByPath?path={{$arr_paper_ans[$i]}} ">参考答案</a></td>
						-->
						@if($arr_scorer_paper_id[$i]<0)
						<td></td>
						@else
						<td><a href=" ./stuPaper/{{$arr_scorer_paper_id[$i]}} ">我的试卷</a></td>
						@endif
						<td><a href="./picNewUser/{{$arr_user_paper_id[$i]}}">添加图片</a></td>
					</tr>
				<?php
					}
					
				}
				?>
				

				

				

				
				
				
			</tbody>
		</table>
	</div>
</div>
<!--用户中心_评测结束-->

@endsection

