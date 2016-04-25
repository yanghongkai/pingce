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
		<div class="pro_title"><a href="{{ url('/manageUser')}}">用户管理</a>
		&nbsp;>&nbsp;
		<a href="../pwdEdit/{{$user->id}}">修改用户密码</a></div>

		<form id="pwdForm" method="post" action="{{ url('/pwdEdit')}}" class="pro_content">
			 {{ csrf_field() }}
			 @if(count($user)>0)
			<ul class="pro_mainform">
				<li>
					<div class="pro_left">用户名</div>
					<div class="pro_text">{{$user->name}}</div>
				</li>
				<li>
					<div class="pro_left">用户身份</div>
					<div class="pro_text">{{$user->role->name}}</div>
				</li>
				<li>
				<div class="pro_left">新密码</div>
				<div class="pro_table">
					<input title="新密码" type="password" name="newPassword" id="newPassword" placeholder="请输入新密码"></input>
				</div>
			</li>
			<li>
				<div class="pro_left">确认密码</div>
				<div class="pro_table">
					<input title="确认密码" type="password" name="conPassword" id="conPassword" placeholder="确认新密码"></input>
				</div>
			</li>
			<li>
				<div class="proBut">
					<input type="hidden" name="id" value="{{$user->id}}" />
					<input type="button" id="pwd_submit" name="save" value="保存"></input>
				</div>
			</li>
			</ul>
			@endif
		</form>

	</div>
	<!--试卷编辑结束-->
</div>
<!--用户中心-管理员-新建试卷结束-->
<script type="text/javascript">
	$(function(){

		

		var pwd_options={
			url: "{{ url('/pwdEdit')}}",
			type: 'POST',
			dataType: 'json',//返回的数据类型
			success: showResponseSubmit,
		};
		$('#pwdForm #pwd_submit').on('click',function(){
			$('#pwdForm').ajaxForm(pwd_options).submit();
		});

		


	});

	

	function showResponseSubmit(response){
		if(response.success==true){
			//alert('保存成功');
			window.location.href="{{ url('/manageUser')}}";
		}
		if(response.success==false){
			alert('保存失败');
		}

	}

	

</script>



@endsection













