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
		<a href="{{ url('/manageUsers')}}">用户管理</a>
		<a href="{{ url('/manageProfile')}}">个人资料</a>
	</div>

	<!--右侧试卷编辑-->
	<div class="user_form">
	
	<div class="pro_title"><a href="{{ url('/userManage')}}">试卷管理</a>
	&nbsp;>&nbsp;
		<a href="../paperEdit/{{$paper->id}}">试卷编辑</a></div>
		@if(count($paper)>0)
		<form id="paper_edit_form" method="post" action="{{ url('/paperEditTrue')}}" class="pro_content">
			 {{ csrf_field() }}
			 <input type="hidden" name="id" value="{{$paper->id}}" />
			<ul class="pro_mainform">
				<li>
					<div class="pro_left">试卷名*</div>
					<div class="pro_table">
						<input title="试卷名" type="text" name="paperName" readonly="readonly" id="paperName" value="{{$paper->name}} ">
						</input>
					</div>
				</li>
				<li>
					<div class="pro_left">详细介绍</div>
					<div class="pro_blank">
						<textarea name="introduction" id="text" >{{$paper->introduction}}</textarea>
					</div>
				</li>
			<li>
				<div class="pro_left">试题文件</div>
				
				<div class="pro_file">
					<input type="text" id="fileField1" readonly="readonly" value="{{$paper->content}}"></input>
					
					<a>上传
					<input type="file" name="uploadPaper" id="upload_paper_edit" class="file" onchange="document.getElementById('fileField1').value=this.value"></input></a>
					<!--<input type="file" name="uploadPaper" id="upload_paper" >-->
				</div>
				
			</li>
			<li>
				<div class="pro_left">答案文件</div>
				<div class="pro_file">
					<!--<input type="text" id="fileField2" readonly="readonly" value="{{$paper->answer}}"></input>-->
					<input type="text" id="fileField2" readonly="readonly" value="{{$paper->answer}}"></input>
					
					<a>上传
					<input type="file" name="uploadAnswer" id="upload_answer_edit" class="file" onchange="document.getElementById('fileField2').value=this.value"></input></a>
					<!--<input type="file" name="uploadAnswer" id='upload_answer'  >-->
				</div>
			</li>
			
			<li>
				<div class="pro_left">试卷科目</div>
				<div class="pro_table">
						<input title="试卷名" type="text" name="paperSub" id="paperSub" placeholder="地理" readonly="readonly" value="{{$paper->category}}">
						</input>
				</div>
				
 			</li>


			<li>
				<div class="proBut">
					<input type="button" id="button_submit_edit" name="save" value="保存"></input>
				</div>
			</li>
			</ul>
		</form>
		@endif
	</div>
	<!--试卷编辑结束-->
</div>
<!--用户中心-管理员-新建试卷结束-->

<script type="text/javascript">
	$(function(){
		var paper_edit_options={
			url: "{{ url('/uploadPaperEdt')}}",
			type: 'POST',
			dataType: 'json',//返回的数据类型
			success: showResponse,
		};
		$('#paper_edit_form #upload_paper_edit').on('change',function(){
			$('#paper_edit_form').ajaxForm(paper_edit_options).submit();
		});

		var paper_answer_edit={
			url: "{{ url('/uploadAnswerEdt')}}",
			type: 'POST',
			dataType: 'json',//返回的数据类型
			success: showResponse,
			
		};
		$('#paper_edit_form #upload_answer_edit').on('change',function(){
			$('#paper_edit_form').ajaxForm(paper_answer_edit).submit();
		});

		var submit_options={
			url: "{{ url('/paperEditTrue')}}",
			type: 'POST',
			dataType: 'json',//返回的数据类型
			success: showResponseSubmit,
		};

		$('#paper_edit_form #button_submit_edit').on('click',function(){
			$('#paper_edit_form').ajaxForm(submit_options).submit();
		});


	});

	function showResponse(response){
		if(response.success==true){
			alert('上传成功');
		}
		if(response.success==false){
			alert('上传失败');
		}

	}

	function showResponseSubmit(response){
		if(response.success==true){
			// alert('保存成功');
			window.location.href="{{ url('/userManage')}}";
		}
		if(response.success==false){
			alert('保存失败');
		}

	}

</script>


@endsection













