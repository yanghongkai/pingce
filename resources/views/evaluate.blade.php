@extends('layouts.app')
@section('nav')
<li><a href="{{ url('/')}}" >主页</a></li>
<li><a href="{{ url('/download')}}" >资源下载</a></li>
<li><a href="{{ url('/evaluate')}}" style="color:#B44242; border-bottom:2px solid #B44242;">试卷评测</a></li>
<li><a href="{{ url('/user')}}">用户中心</a></li>

@endsection
@section('content')
<!--评测-->
<div class="form">
	<div class="evaluate_form">
		<table>
			<tbody>
				<tr class="evaluate_form_title">
					<td>评测题目</td>
					<td>下载</td>
					<td>上传</td>
					<td>历史记录</td>
					<td>上传时间</td>
				</tr>
				<?php

				if(count($papers)>0)
				{
					for($i=0;$i<count($papers);$i++)
					{
						$paper=$papers[$i];
				?>
						<tr class="evaluate_form_item">
							<td>{{$paper->name}}</td>
							<td>

							<a href="./downloadPaper/{{$paper->id}} ">下载</a>
							</td>
							<td>
							<form id="uploadForm{{$paper->id}}" action="{{ url('/uploadUserAnswer')}}" method="POST">
		                     {{ csrf_field() }}

		                     <div class="pro_file">
		                     <!--前台页面写的有问题，方法不好，所以只能加每次的$paper->id来区别-->
							<input type="text" id="fileField{{$paper->id}}" readonly="readonly"></input>
							<!-- <input type="button" class="upload" value="上传"> -->
							<h4>上传
							<input type="file" name="user_answer" id="user_answer{{$paper->id}}" class="file" onchange="document.getElementById('fileField{{$paper->id}}').value=this.value"></input></h4>
							</div>

							<!--<input type="file" name="user_answer" id="user_answer{{$paper->id}}" >-->
							<input type="hidden" name="id" value="{{$paper->id}}">
							</form>
							</td>
							<td>{{$arr_history[$i]}}</td>
							<td>{{ $paper->created_at->format('Y年m月d日') }}</td>
						</tr>
				<?php

					}
					
				}
				?>
				

				

				

				
				
			</tbody>
		</table>
	</div>
</div>
<!--评测结束-->

<script type="text/javascript">
	$(function(){
		var user_answer={
			url: "{{ url('./uploadUserAnswer')}}",
			type: 'POST',
			dataType: 'json',//返回的数据类型
			success: showResponse,
		};

		@if(count($papers)>0)
		@foreach($papers as $paper)
		
		$('#uploadForm{{$paper->id}} #user_answer{{$paper->id}}').on('change',function(){
			$('#uploadForm{{$paper->id}}').ajaxForm(user_answer).submit();
		});
		@endforeach

		@endif
			


	});

	function showResponse(response){
		if(response.success==true){
			alert('上传成功');
			window.location.href="{{ url('/evaluate')}}";
		}
		if(response.success==false){
			alert('上传失败');
		}

	}


</script>



@endsection