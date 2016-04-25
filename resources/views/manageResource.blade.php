@extends('layouts.app')
@section('nav')
<li><a href="{{ url('/')}}" >主页</a></li>
<li><a href="{{ url('/download')}}" >资源下载</a></li>
<li><a href="{{ url('/evaluate')}}" >试卷评测</a></li>
<li><a href="{{ url('/user')}}" style="color:#B44242; border-bottom:2px solid #B44242;">用户中心</a></li>

@endsection

@section('content')
<!--管理员-新建试卷-->
<div class="user">
	<!--侧边导航栏-->
	<div class="user_sidebar">
		<a href="{{ url('/userManage')}}">试卷管理</a>
		<a href="{{ url('/manageResource')}}">资源管理</a>
		<a href="{{ url('/manageNews')}}">新闻管理</a>
		<a href="{{ url('/manageUser')}}">用户管理</a>
		<a href="{{ url('/manageProfile')}}">个人资料</a>
	</div>

	<!--右侧表格-->
	<div class="user_form">
		<div class="manageTitle">
			<div class="pro_title">资源管理</div>
			<input class="newBut" type="button" value="新建资源" onclick="location.href='{{ url("/resourceNew")}}'">
		</div>
		<table>
			<tbody>
				<tr class="user_form_title">
					<td>资源名</td>
					<td>添加日期</td>
					<!--<td>试卷状态</td>-->
					<td>操作</td>
					<!-- <td>编辑</td> -->
				</tr>
				@if(count($res)>0)
				@foreach($res as $res_item)
				<tr class="user_form_item">
					<td>{{$res_item->title}}</td>
					<td>{{$res_item->created_at->format('Y年m月d日')}}</td>
					<form action="./resDelete/{{$res_item->id}}" method="POST">
					 {{ csrf_field() }}
					<td><button type="submit">删除</button></td>
					</form>
				</tr>
				@endforeach
				@endif
				
			</tbody>
		</table>
	</div>

</div>
<!--用户中心-管理员-新建试卷结束-->




@endsection













