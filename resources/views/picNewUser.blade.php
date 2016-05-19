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
		<a href="{{ url('/userEvaluate')}}">评测记录</a>
		<a href="{{ url('/evaluateProfile')}}">个人资料</a>
	</div>

	<!--右侧试卷编辑-->
	<div class="user_form">
	<div class="pro_title"><a href="{{ url('/userEvaluate')}}">评测记录</a>
	&nbsp;>&nbsp;
		<a href="javascript:void(0);">导入图片</a></div>
		<form id="picNewUser" method="post" action="{{ url('/picNewUser')}}" class="pro_content">
			 {{ csrf_field() }}

			<!--隐藏信息-->
			<input type="hidden" name="user_paper_id" value="{{$user_paper_id}}" />
			<ul class="pro_mainform">
				<li>
					<div class="pro_left">试卷名*</div>
					<div class="pro_table">

						<input title="试卷名" type="text" name="paperName" id="paperName" readonly="readonly" value="{{$paper_name}} ">
						</input>
					</div>
				</li>

				<li>
					<div class="pro_left">id</div>
					<div class="pro_table">
						<input title="图片id" type="text" name="pic_id" id="picID" required="required">
						</input>
					</div>
				</li>
				
			<li>
				<div class="pro_left">图片文件</div>
				
				<div class="pro_file">
					<input type="text" id="fileField1" readonly="readonly"></input>
					
					<a>上传
					<input type="file" name="uploadPic" id="upload_pic" class="file" onchange="document.getElementById('fileField1').value=this.value"></input></a>
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
		var pic_options={
			url: "{{ url('/uploadPicUser')}}",
			type: 'POST',
			dataType: 'json',//返回的数据类型
			success: showResponse,
		};
		$('#picNewUser input[name=uploadPic]').on('change',function(){
			$('#picNewUser').ajaxForm(pic_options).submit();
		});

		var submit_options={
			url: "{{ url('/picNewUser')}}",
			type: 'POST',
			dataType: 'json',//返回的数据类型
			success: showResponseSubmit,
		};

		$('#picNewUser #button_submit').on('click',function(){
			$('#picNewUser').ajaxForm(submit_options).submit();
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
			alert('保存成功');
			window.location.href="../picNewUser/{{$user_paper_id}}";
		}
		if(response.success==false){
			alert('保存失败');
		}

	}

	

</script>


@endsection













