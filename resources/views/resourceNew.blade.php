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

	<!--右侧试卷编辑-->
	<div class="user_form">
		<div class="pro_title"><a href="{{ url('/manageResource')}}">资源管理</a>
		&nbsp;>&nbsp;
		<a href="{{ url('/resourceNew')}}">新建资源</a></div>

		<form id="resourceForm" method="post" action="{{ url('/resourceNew')}}" class="pro_content">
		{{ csrf_field() }}
			<ul class="pro_mainform">
				<li>
					<div class="pro_left">资源名*</div>
					<div class="pro_table">
						<input title="试卷名" type="text" name="resName" id="paperName" required="required">
						</input>
					</div>
				</li>
			<li>
				<div class="pro_left">资源文件</div>
				<div class="pro_file">
					<input type="text" id="fileField1" readonly="readonly"></input>
					<!-- <input type="button" class="upload" value="上传"> -->
					<a>上传
					<input type="file" name="uploadResource" id="upload_resource" class="file" onchange="document.getElementById('fileField1').value=this.value"></input></a>
				</div>



			</li>
			<li>
				<div class="proBut">
					<input type="button" id="res_submit" name="save" value="保存"></input>
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

		var res_options={
			url: "{{ url('/uploadResource')}}",
			type: 'POST',
			dataType: 'json',//返回的数据类型
			success: showResponse,
		};
		$('#resourceForm #upload_resource').on('change',function(){
			$('#resourceForm').ajaxForm(res_options).submit();
		});

		var sub_options={
			url: "{{ url('/resourceNew')}}",
			type: 'POST',
			dataType: 'json',//返回的数据类型
			success: showResponseSubmit,
		};
		$('#resourceForm #res_submit').on('click',function(){
			$('#resourceForm').ajaxForm(sub_options).submit();
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
			//alert('保存成功');
			window.location.href="{{ url('/manageResource')}}";
		}
		if(response.success==false){
			alert('保存失败');
		}

	}

	

</script>

	

@endsection













