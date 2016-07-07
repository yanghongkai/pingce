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
		<a href="{{ url('/paperNew')}}">新建试卷</a></div>
		<form id="pro_content_form" method="post" action="{{ url('/paperNew')}}" class="pro_content">
			 {{ csrf_field() }}

			<ul class="pro_mainform">
				<li>
					<div class="pro_left">试卷名*</div>
					<div class="pro_table">
						<input title="试卷名" type="text" name="paperName" id="paperName" value="{{old('paperName')}} ">
						</input>
					</div>
				</li>
				<li>
					<div class="pro_left">详细介绍</div>
					<div class="pro_blank">
						<textarea name="introduction" id="text" value='{{ old('introduction')}}'></textarea>
					</div>
				</li>
			<li>
				<div class="pro_left">试题文件</div>
				
				<div class="pro_file">
					<input type="text" id="fileField1" readonly="readonly"></input>
					
					<a>上传
					<input type="file" name="uploadPaper" id="upload_paper" class="file" onchange="document.getElementById('fileField1').value=this.value"></input></a>
					<!--<input type="file" name="uploadPaper" id="upload_paper" >-->
				</div>
				
			</li>
			<li>
				<div class="pro_left">答案文件</div>
				<div class="pro_file">
					<input type="text" id="fileField2" readonly="readonly"></input>
					
					<a>上传
					<input type="file" name="uploadAnswer" id="upload_answer" class="file" onchange="document.getElementById('fileField2').value=this.value"></input></a>
					<!--<input type="file" name="uploadAnswer" id='upload_answer'  >-->
				</div>
			</li>
			<!--<li>
				<div class="pro_left">是否发布</div>
				<div class="pro_radio">
					<input type="radio" name="publish" value="published">
					<label for="published">发布</label>
					<input type="radio" name="publish" value="unpublished">
					<label for="unpublished">暂不发布</label>
				</div>
			</li>-->
			<li>
				<div class="pro_left">试卷科目</div>
				
				<div class="pro_radio">
					<label class="sub_label">
						<input class="sub_radio" type="radio" name="subject" value="chinese">
						<span class="sub_radioInput"></span>语文
					</label>
					<label class="sub_label">
						<input class="sub_radio" type="radio" name="subject" value="math">
						<span class="sub_radioInput"></span>数学
					</label>
					<label class="sub_label">
						<input class="sub_radio" type="radio" name="subject" value="geography">
						<span class="sub_radioInput"></span>地理
					</label>
					<label class="sub_label">
						<input class="sub_radio" type="radio" name="subject" value="history">
						<span class="sub_radioInput"></span>历史
					</label>
				</div>
 			</li>


			<li>
				<div class="proBut">
					<input type="button" id="button_submit" name="save" value="保存"></input>
				</div>
			</li>
			</ul>
		</form>
	</div>
	<!--试卷编辑结束-->
</div>
<!--用户中心-管理员-新建试卷结束-->

<script type="text/javascript">
	$(function(){
		var paper_options={
			url: "{{ url('/uploadPaper')}}",
			type: 'POST',
			dataType: 'json',//返回的数据类型
			success: showResponse,
		};
		$('#pro_content_form input[name=uploadPaper]').on('change',function(){
			$('#pro_content_form').ajaxForm(paper_options).submit();
		});

		var paper_answer={
			url: "{{ url('/uploadAnswer')}}",
			type: 'POST',
			dataType: 'json',//返回的数据类型
			success: showResponse,
			
		};
		$('#pro_content_form input[name=uploadAnswer]').on('change',function(){
			$('#pro_content_form').ajaxForm(paper_answer).submit();
		});

		var submit_options={
			url: "{{ url('/paperNew')}}",
			type: 'POST',
			dataType: 'json',//返回的数据类型
			success: showResponseSubmit,
		};

		$('#pro_content_form #button_submit').on('click',function(){
			$('#pro_content_form').ajaxForm(submit_options).submit();
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













