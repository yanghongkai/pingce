@extends('layouts.app')
@section('nav')
<li><a href="{{ url('/')}}" >主页</a></li>
<li><a href="{{ url('/download')}}" style="color:#B44242; border-bottom:2px solid #B44242;">资源下载</a></li>
<li><a href="{{ url('/evaluate')}}">试卷评测</a></li>
<li><a href="{{ url('/user')}}">用户中心</a></li>

@endsection

@section('content')
<!--资源下载-->
<div class="form">
	<div class="scoreDl_form">
		<table>
			<tbody>
				<tr class="scordDl_form_title">
					<td>资源名</td>
					<td>上传日期</td>
					<td>上传人</td>
					<td>下载链接</td>
				</tr>
				@if(count($res)>0)
				@foreach($res as $res_item)
				<tr class="scordDl_form_item">
					<td>{{$res_item->title}}</td>
					<td>{{$res_item->created_at->format('Y年m月d日')}}</td>
					<td>{{$res_item->publisher}}</td>
					<td><a href="./downloadByPath?path={{$res_item->content}}">下载</a></td>
				</tr>
				@endforeach
				@endif
				
				
			</tbody>
		</table>
	</div>
</div>
<!--资源下载结束>



@endsection


