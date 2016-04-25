@extends('layouts.app')
@section('nav')
<li><a href="{{ url('/')}}" >主页</a></li>
<li><a href="{{ url('/download')}}" >资源下载</a></li>
<li><a href="{{ url('/evaluate')}}" >试卷评测</a></li>
<li><a href="{{ url('/user')}}" style="color:#B44242; border-bottom:2px solid #B44242;">用户中心</a></li>

@endsection
@section('content')
<!--用户中心_阅卷-->
<div class="user">
	<!--侧边导航栏-->
	<div class="user_sidebar">
		<a href="{{ url('/userScorer')}}">阅卷记录</a>
		<a href="{{ url('/scorerProfile')}}">个人资料</a>
	</div>
	<!--右侧表格-->
	<div class="user_form">
		<div class="pro_title">阅卷记录</div>
		<div class="pro_search">
			<form id="searchForm" action="{{ url('/search')}}" method="POST">
			{{ csrf_field() }}
			<input class="search" name="search" type="text" title="在此输入搜索内容">
			<input class="searchBut" type="submit" title="搜索" value="搜索"></input>
			</form>
		</div>
		<table>
			<tbody>
				<tr class="user_form_title">
					<td>试卷名</td>
					<td>提交者</td>
					<td>提交时间</td>
					<td>阅卷状态</td>
					<td>阅卷人</td>
					<td>得分</td>
					<td>阅卷链接</td>
				</tr>
				<?php
				use App\User;
				use App\Role;
				use App\Paper;
				use App\UserPaper;
				use App\ScorerPaper;
				if(count($user_papers))
				foreach($user_papers as $user_paper){
					$paper_id=$user_paper->paper_id;
		            //获得试卷信息
		            $paper=Paper::where('id',$paper_id)->first();
		            $paper_name=$paper->name;

		        ?>
		            <!--<tr class="user_form_item">-->
		            <tr class="{{ $user_paper->status=='未评阅' ? 'user_form_item_un' : 'user_form_item' }}">
					<td>{{$paper_name}}</td>
				<?php
					 //获取提交者姓名
		            $stu_id=$user_paper->user_id;
		            $user_stu=User::where('id',$stu_id)->first();
		            $sub_name=$user_stu->name;
		        ?>
		            <td>{{$sub_name}}</td>
		        <?php
		            //提交时间
            		$created_at=$user_paper->created_at;
            	?>
            		<td>{{$created_at}}</td>
            		<td>{{$user_paper->status}}</td>
            	<?php
            		 //阅卷人
            		$scorers='';
		            $users=$user_paper->users;
		            if(count($users)>0){
		                foreach ($users as $user){
		                    $scorers.=$user->name.'-';
		                }
		                $scorers=substr($scorers, 0,-1);
		            }else{
		                $scorers='--';
		            }
		        ?> 
		            <td>{{$scorers}}</td>
		        <?php
		            //试卷分数，只要最后一个的成绩
		            $scorer_paper=ScorerPaper::where('user_paper_id',$user_paper->id)->where('submit',1)->orderBy('updated_at','desc')->first();
		            if(empty($scorer_paper)){
		                $grade='';
		            }else{
		                $grade=$scorer_paper->grade;
		            }
		        ?>
		            <td>{{$grade}}</td>
		            <td><a href="./paperScore/{{$user_paper->id}}">阅卷链接</a></td>
		        <?php
				}
				?>
				<!--
				<tr class="user_form_item">
					<td>2015北京市地理中考试卷</td>
					<td>用户一</td>
					<td>2016年2月3日</td>
					<td>李老师</td>
					<td>90</td>
					<td><a href="{{ url('/paperScore')}}">阅卷链接</a></td>
				</tr>
				<tr class="user_form_item">
					<td>2014北京市地理中考试卷</td>
					<td>用户2</td>
					<td>2015年2月3日</td>
					<td>李老师</td>
					<td>93</td>
					<td><a href="#">阅卷链接</a></td>
				</tr>
				<tr class="user_form_item">
					<td>2013北京市地理中考试卷</td>
					<td>用户3</td>
					<td>2014年2月3日</td>
					<td>黄老师</td>
					<td>87</td>
					<td><a href="#">阅卷链接</a></td>
				</tr>
				-->
			</tbody>
		</table>
	</div>
</div>
<!--用户中心_阅卷结束-->
@endsection