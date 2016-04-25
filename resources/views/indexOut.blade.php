@extends('layouts.app')

@section('nav')
<li><a href="{{ url('/')}}" style="color:#B44242; border-bottom:2px solid #B44242;">主页</a></li>
<li><a href="./index.php/download">资源下载</a></li>
<li><a href="./index.php/evaluate">试卷评测</a></li>
<li><a href="./index.php/user">用户中心</a></li>

@endsection

@section('content')
<!--login-->
<div class="login">



	<form method="POST" action="./index.php/logout">
	{{ csrf_field() }}
	

	<h2 class="title">用户登录</h2>
	<div class="inputInfo">
		<p class="clearfix">
			<label for="login">用户名</label>
			<input type="text" name="name" id="login" placeholder="{{ session('name')}}" readonly="readonly"></input>
		</p>
		<!--
		<p class="clearfix">
			<label for="password">密码</label>
			<input type="password" name="password" id="password" placeholder="请输入密码"></input>
			@include('common.errors')
		</p>
		-->
	</div>
	<div class="singBut">
		<p class="clearfix">
			<input type="submit" name="submit" value="Log Out"></input>

		</p>
	</div>
	<form>
</div>
<!--login end-->

<!--news-->
<div class="news">
	<h2 class="title">通知新闻</h2>
	<ul class="article">
		@if(count($news)>0)
		@foreach($news as $news_item)
		@if($news_item->top==1)
		<li class="clearfix">
			<a href="./index.php/news/{{$news_item->id}}" title="{{$news_item->title}}">{{$news_item->title}}<sup>&nbsp;NEW</sup></a>
			<span>{{$news_item->created_at->format('Y-m-d')}}</span>
		</li>
		@else
		<li class="clearfix">
			<a href="./index.php/news/{{$news_item->id}}" title="{{$news_item->title}}">{{$news_item->title}}</a>
			<span>{{$news_item->created_at->format('Y-m-d')}}</span>
		</li>
		@endif
		@endforeach
		@endif

	</ul>
</div>
<!--news end-->

@endsection