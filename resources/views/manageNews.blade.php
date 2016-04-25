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
			<div class="pro_title">新闻管理</div>
			<input class="newBut" type="button" value="新建新闻" onclick="location.href='{{ url("/newsNew")}}'">
		</div>
		<table>
			<tbody>
				<tr class="user_form_title">
					<td>新闻</td>
					<td>添加日期</td>
					<!--<td>试卷状态</td>-->
					<td style=" text-align:right;"  colspan="2">操作</td>
					<!-- <td>编辑</td> -->
				</tr>

				@if(count($news)>0)
				@foreach($news as $news_item)
				<tr class="user_form_item">
					<td>{{$news_item->title}}</td>
					<td>{{$news_item->created_at->format('Y年m月d日')}}</td>
					
					<td>
					<form action="./newsDelete/{{$news_item->id}}" method="POST">
					{{ csrf_field() }}
					<td><button type="submit">删除</button></td></form>
					<form action="./newsTop/{{$news_item->id}}" method="POST">
					{{ csrf_field() }}
					<td><button type="submit">置顶</button></td></form>
					
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













