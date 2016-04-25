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
			<div class="pro_title">用户管理</div>
			<input class="newBut" type="button" value="新建用户" onclick="location.href='{{ url("/userNew")}}'">
		</div>
		<table>
			<tbody>
				<tr class="user_form_title">
					<td>用户名</td>
					<td>添加日期</td>
					<!--<td>试卷状态</td>-->
					<td>用户身份</td>
					<td>操作</td>
					<!-- <td>编辑</td> -->
				</tr>
				@if(count($users)>0)
				@foreach($users as $user)
				<tr class="user_form_item">
					<td>{{$user->name}}</td>
					<td>{{$user->created_at->format('Y年m月d日')}}</td>
					<!--<td>已启用</td>
					<td>启用&nbsp;|&nbsp;<a href="#">禁用</a></td>-->
					<td>{{$user->role->name}}</td>
					<td><a href="./pwdEdit/{{$user->id}}">修改密码</a></td>
					<!-- <td><a href="paperEdit.html">编辑</a></td> -->
				</tr>
				@endforeach
				@endif
				
			</tbody>
		</table>
	</div>

</div>
<!--用户中心-管理员-新建试卷结束-->




@endsection













