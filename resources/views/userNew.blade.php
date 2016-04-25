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
		<a href="{{ url('/userNew')}}">新建用户</a></div>

		<form id="userForm" method="post" action="{{ url('/userNew')}}" class="pro_content">
		{{ csrf_field() }}
			<ul class="pro_mainform">
				<li>
					<div class="pro_left">用户名*</div>
					<div class="pro_table">
						<input title="用户名" type="text" name="userName" id="userName" required="required">
						</input>
					</div>
				</li>
				<li>
				<div class="pro_left">输入密码</div>
				<div class="pro_table">
					<input title="密码" type="password" name="newPassword" id="newPassword" placeholder="请输入密码"></input>
				</div>
			</li>
			<li>
				<div class="pro_left">确认密码</div>
				<div class="pro_table">
					<input title="确认密码" type="password" name="conPassword" id="conPassword" placeholder="确认密码"></input>
				</div>
			</li>
			<li>
				<div class="pro_left">用户身份</div>
				<div class="pro_radio">
					<label class="sub_label">
						<input class="sub_radio" type="radio" name="userStatus" value="chinese">
						<span class="sub_radioInput"></span>学生-语文
					</label>
					<label class="sub_label">
						<input class="sub_radio" type="radio" name="userStatus" value="math">
						<span class="sub_radioInput"></span>学生-数学
					</label>
					<label class="sub_label">
						<input class="sub_radio" type="radio" name="userStatus" value="geography">
						<span class="sub_radioInput"></span>学生-地理
					</label>
					<label class="sub_label">
						<input class="sub_radio" type="radio" name="userStatus" value="history">
						<span class="sub_radioInput"></span>学生-历史
					</label>
					<label class="sub_label">
						<input class="sub_radio" type="radio" name="userStatus" value="考生">
						<span class="sub_radioInput"></span>学生-全部
					</label>
					<label class="sub_label">
						<input class="sub_radio" type="radio" name="userStatus" value="阅卷人">
						<span class="sub_radioInput"></span>阅卷老师
					</label>
					<label class="sub_label">
						<input class="sub_radio" type="radio" name="userStatus" value="管理员">
						<span class="sub_radioInput"></span>管理员
					</label>
				</div>
 			</li>
			<li>
				<div class="proBut">
					<input type="button" id="user_submit" name="save" value="保存"></input>
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

		

		var user_options={
			url: "{{ url('/userNew')}}",
			type: 'POST',
			dataType: 'json',//返回的数据类型
			success: showResponseSubmit,
		};
		$('#userForm #user_submit').on('click',function(){
			$('#userForm').ajaxForm(user_options).submit();
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













