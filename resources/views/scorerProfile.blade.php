@extends('layouts.app')
@section('nav')
<li><a href="{{ url('/')}}" >主页</a></li>
<li><a href="{{ url('/download')}}" >资源下载</a></li>
<li><a href="{{ url('/evaluate')}}" >试卷评测</a></li>
<li><a href="{{ url('/user')}}" style="color:#B44242; border-bottom:2px solid #B44242;">用户中心</a></li>

@endsection

@section('content')
<!--用户中心-个人资料-->
<div class="user">
	<!--侧边导航栏-->
	<div class="user_sidebar">
		<a href="{{ url('/userScorer')}}">阅卷记录</a>
		<a href="{{ url('/scorerProfile')}}">个人资料</a>
		
	</div>

	<!--右侧用户信息修改-->
	<div class="user_form">
		<div class="pro_title">个人资料</div>
		<form method="post" action="{{ url('/editProfile')}}" class="pro_content">
		 {{ csrf_field() }}
			<ul class="pro_mainform">
				<li>
					<div class="pro_left">用户名</div>
					<div class="pro_text">{{ $user->name }}</div>
				</li>
				<li>
					<div class="pro_left">用户身份</div>
					<div class="pro_text">{{$user->role->name}}</div>
				</li>
				<li>
					<div class="pro_left">用户单位</div>
					<div class="pro_table">
						<input title="用户单位" type="text" name="company" id="company" placeholder="{{$user->department or '北京语言大学'}}">
						</input>
					</div>
				</li>
				<li>
				<div class="pro_left">当前密码</div>
				<div class="pro_table">
					<input title="当前密码" type="password" name="password" id="password" placeholder="请输入当前密码"></input>
				</div>
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
					<input type="submit" name="save" value="保存"></input>
				</div>
			</li>
			<!-- <li>
				<div class="proBut">
					<input type="submit" name="cancel" value="取消"></input>
				</div>
			</li> -->
			</ul>
			@include('common.errors')
		</form>
	</div>
	<!--用户信息修改结束-->
</div>
<!--用户中心-个人资料结束-->

@endsection