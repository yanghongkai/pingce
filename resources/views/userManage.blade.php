@extends('layouts.app')
@section('nav')
<li><a href="{{ url('/')}}" >主页</a></li>
<li><a href="{{ url('/download')}}" >资源下载</a></li>
<li><a href="{{ url('/evaluate')}}" >试卷评测</a></li>
<li><a href="{{ url('/user')}}" style="color:#B44242; border-bottom:2px solid #B44242;">用户中心</a></li>

@endsection
@section('content')


<!--用户中心_管理员-->
<div class="user">
	<!--侧边导航栏-->
	<div class="user_sidebar">
		<a href="{{ url('/userManage')}}">试卷管理</a>
		<!--<a href="{{ url('/paperNew')}}">新建试卷</a>-->
		<a href="{{ url('/manageResource')}}">资源管理</a>
		<a href="{{ url('/manageNews')}}">新闻管理</a>
		<a href="{{ url('/manageUser')}}">用户管理</a>
		<a href="{{ url('/manageProfile')}}">个人资料</a>
	</div>
	<!--右侧表格-->
	<div class="user_form">
		<div class="manageTitle">
			<div class="pro_title">试卷管理</div>
			<input class="newBut" type="button" value="新建试卷" onclick="location.href='{{ url("/paperNew")}}'">
		</div>
		<table>
			<tbody>
				<tr class="user_form_title">
					<td>试卷名</td>
					<td>添加日期</td>
					<!--<td>试卷状态</td>-->
					<td colspan="2">操作</td>
					<!--<td>编辑</td>-->
				</tr>

				@if(count($papers)>0)
				@foreach($papers as $paper)

				<tr class="user_form_item">
					<td>{{$paper->name}}</td>
					<td>{{$paper->created_at->format('Y年m月d日')}}</td>
					<form action="./paperDelete/{{$paper->id}}" method="POST">
					 {{ csrf_field() }}
					<td><button type="submit">删除</button></td>
					</form>
					<form action="./paperEdit/{{$paper->id}}" method="POST">
					 {{ csrf_field() }}
					<td><button type="submit">编辑</button></td>
					</form>
					<!--<td><a href="paperEdit.html">编辑</a></td>-->
				</tr>
				@endforeach
				@endif
			</tbody>
		</table>
	</div>
</div>
<!--用户中心_管理员-->


@endsection



