@extends('layouts.app')

@section('nav')
<li><a href="{{ url('/')}}" style="color:#B44242; border-bottom:2px solid #B44242;">主页</a></li>
<li><a href="{{ url('/download')}}">资源下载</a></li>
<li><a href="{{ url('/evaluate')}}">试卷评测</a></li>
<li><a href="{{ url('/user')}}">用户中心</a></li>

@endsection

@section('content')

<!--news-->
	@if(count($news)>0)
	
	

	<div class="news">
		<h2 class="news_name">{{$news->title}}</h2>
		<div class="news_info">
			<span>发布时间：{{$news->created_at->format('Y-m-d')}}</span><span>发布人：{{$news->publisher}}</span>
		</div>
		<div class="news_content">
			<!--<p>{{$news->content}}</p>-->
			<p>{!!$news->content!!}</p>
			<!--<p>@{{$news->content}}</p>-->

		</div>
		<div class="back_homepage"><a href="{{ url('/')}}"> &nbsp;<&nbsp;返回</a>
		</div>
	</div>
	
	@endif
	<!--news end-->

@endsection