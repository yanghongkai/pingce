<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>评测主页</title>
<link rel="stylesheet" type="text/css" href="{{asset('/css/common.css')}}"/>
<link rel="stylesheet" type="text/css" href="{{asset('/css/info.css')}}"/>
<link rel="stylesheet" type="text/css" href="{{asset('/css/user.css')}}"/>
<link rel="stylesheet" type="text/css" href="{{asset('/css/paper.css')}}"/>
<!--引入jQuery-->
<script type="text/javascript" src="{{asset('/scripts/jquery-1.7.1.js')}}"></script>
<script type="text/javascript" src="{{asset('/scripts/jquery-form.js')}}"></script>
</head>

<body>
<div class="header">
	<div class="headinner">
		<!--logo-->
		<div class="logo left">
			<a href="#" class="left";><h1>评测网站</h1></a>
		</div>
		<!--logo end-->
		<!--nav-->
		<ul class="nav right">
			@yield('nav')
		</ul>
		<!--nav end-->
	</div>
</div>

@yield('content')
</body>
</html>