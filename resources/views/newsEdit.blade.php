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
	@if(count($news)>0)
	<div class="user_form">
		<div class="pro_title"><a href="{{ url('/manageNews')}}">新闻管理</a>
		&nbsp;>&nbsp;
		<a href="../newsEdit/{{$news->id}}">编辑新闻</a></div>

		<form id="newsEditForm" method="post" action="{{ url('/newsEditTrue')}}" class="pro_content">
			 {{ csrf_field() }}
			 <input type="hidden" name="id" value="{{$news->id}}" />
			<ul class="pro_mainform">
				<li>
					<div class="pro_left">新闻名*</div>
					<div class="pro_table">
						<input title="新闻名" type="text" name="newsName" id="newsName" readonly="readonly" value="{{$news->title}}" required="required">
						</input>
					</div>
				</li>
				<li>
					<div class="pro_left">新闻内容</div>
					<div class="pro_blank">
						<textarea name="newsCont" id="text">{{$news->content}}</textarea>
					</div>
				</li>
			<li>
				<div class="pro_left">是否置顶</div>
				<div class="pro_radio">
				@if($news->top==1)
					<label class="sub_label">
						<input class="sub_radio" type="radio" checked="checked"  name="top" value="1">
						<span class="sub_radioInput"></span>是
					</label>
					<label class="sub_label">
						<input class="sub_radio" type="radio"  name="top" value="0">
						<span class="sub_radioInput"></span>否
					</label>
				@else
					<label class="sub_label">
						<input class="sub_radio" type="radio"   name="top" value="1">
						<span class="sub_radioInput"></span>是
					</label>
					<label class="sub_label">
						<input class="sub_radio" type="radio" checked="checked"  name="top" value="0">
						<span class="sub_radioInput"></span>否
					</label>
				@endif
				</div>
 			</li>
			<li>
				<div class="proBut">
					<input type="button" id="news_edit_btn" name="save" value="保存"></input>
				</div>
			</li>
			</ul>
		</form>

	</div>
	<!--试卷编辑结束-->
</div>
@endif
<!--用户中心-管理员-新建试卷结束-->
<script type="text/javascript">
	$(function(){

		

		var newsedit_options={
			url: "{{ url('/newsEditTrue')}}",
			type: 'POST',
			dataType: 'json',//返回的数据类型
			success: showResponseSubmit,
		};
		$('#newsEditForm #news_edit_btn').on('click',function(){
			$('#newsEditForm').ajaxForm(newsedit_options).submit();
		});

		


	});

	

	function showResponseSubmit(response){
		if(response.success==true){
			//alert('保存成功');
			window.location.href="{{ url('/manageNews')}}";
		}
		if(response.success==false){
			alert('保存失败');
		}

	}

	

</script>


@endsection













