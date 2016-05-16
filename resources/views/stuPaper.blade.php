@extends('layouts.app')
@section('nav')
<li><a href="{{ url('/')}}" >主页</a></li>
<li><a href="{{ url('/download')}}" >资源下载</a></li>
<li><a href="{{ url('/evaluate')}}" >试卷评测</a></li>
<li><a href="{{ url('/user')}}" style="color:#B44242; border-bottom:2px solid #B44242;">用户中心</a></li>

@endsection

@section('content')
<!--面包树-->
<div class="navBar">
	<a href="{{ url('/userEvaluate')}}">评测记录</a>&nbsp;>&nbsp; <a href="javascript:void(0);">{{$paper_name}}</a>
</div>
<!--阅卷-->
<div class="paperScore">
	<!--试卷信息-->
	<div class="paperDetail">
		<div class="paperName">{{$paper_name}}</div>
		<div class="paperInfo">
			<span>上传者：{{$stu_name}}</span>
			<span>上传时间：{{$user_paper_time}}</span>
		</div>
	</div>
	<!--试卷信息结束-->
	<?php
		//解析公式
    	function parseLatex_ps($str){
        $pattern='/\$([^$]*)\$/U';
        preg_match_all($pattern,$str,$matches);
        $arr_split=preg_split($pattern,$str);
        //dd($arr_split);
        //dd($matches);
        $count=count($matches[1]);
        $str_new="";
        for($i=0;$i<count($matches[1]);$i++){
            //$arr_replace[]='<img src="http://latex.codecogs.com/gif.latex?'.$matches[1][$i].'" />';
            $str_new.=$arr_split[$i];
            $str_new.='<img src="http://latex.codecogs.com/gif.latex?'.$matches[1][$i].'" />';
        }
        $str_new.=$arr_split[$count];
        if($count>0){
            return $str_new;
        }else{
            return $str;
        }
    }
    //去掉option
    function removeOption($str){
    	$pattern='/<option[^>]+>(.*)<\/option>/U';
	    preg_match_all($pattern,$str,$matches);
	    //dd($matches);
	    if(count($matches[1])>0){
	        return $matches[1][0];
	    }else{
	        return $str;
	    }
    }

    //将label处理成①②③的形式
    function parseLabel($str){
    	$pattern='/<label[^>]+>(.*)<\/label>/U';
	    preg_match_all($pattern,$str,$matches);
	    // dd($matches);
	    $arr_split=preg_split($pattern,$str);
	    // dd($arr_split);
	    $arr_mark=array();
	    $arr_mark[0]="①";
	    $arr_mark[1]="②";
	    $arr_mark[2]="③";
	    $arr_mark[3]="④";
	    $arr_mark[4]="⑤";
	    $arr_mark[5]="⑥";
	    $arr_mark[6]="⑦";
	    $arr_mark[7]="⑧";
	    $arr_mark[8]="⑨";
	    $count=count($matches[1]);
	    $str_new="";
	    for($i=0;$i<$count;$i++){
	        $str_new.=$arr_split[$i];
	        $str_new.=$arr_mark[$i];
	        $str_new.=$matches[1][$i];
	    }
	    $str_new.=$arr_split[$count];
	    if($count>0){
	        return $str_new;
	    }else{
	        return $str;
	    }


    }


	?>


	<!--试题开始-->
	@if(count($arr_ques_title)>0)
	@for($i=0,$k=0;$i<count($arr_ques_title);$i++)
	<div class="questions">
		
		<div class="questions_detail">
			<div class="questions_title">
				{!!$arr_ques_head_text[$i]!!}(总分{{$arr_ques_score[$i]}}分)
			</div>
			<div class="questions_title">
				{!!$arr_ques_title[$i]!!}
			</div>
			<div class="questions_text">
				{!!$arr_ques_text[$i]!!}
			</div>
			<!--
			<div class="questions_score">
				总分值：20分
			</div>
			-->
		</div>

		@for($j=0; $j<$arr_ques_count[$i];$j++,$k++)
		<ul class="question">
			@if((string)$arr_que[$k]['type']=='select')
			<li class="question_Name">
				{{$arr_que[$k]['id']}}
				{!!$arr_que[$k]->headtext->asXML()!!}
				<?php
					//先进行label解析
					$str=parseLabel($arr_que[$k]->text->asXML());
					// echo parseLatex_ps($arr_que[$k]->text->asXML());
					echo parseLatex_ps($str);
				?>

			</li>
				<?php
				$sel_options=$arr_que[$k]->select->xpath('.//option');
                foreach($sel_options as $sel_option){
                	?>
                    <li class="question_select">{{$sel_option['value']}} &nbsp;
                   <?php
                   	$str=removeOption($sel_option->asXMl());
					echo parseLatex_ps($str);
					?>
					</li>
					<?php
                }
                ?>
			@endif

			@if((string)$arr_que[$k]['type']=='shortanswer')
				<li class="question_Name">
				{{$arr_que[$k]['id']}}
				<?php
					echo parseLatex_ps($arr_que[$k]->text->asXML());
				?>
				</li>
			@endif

			@if((string)$arr_que[$k]['type']=='fillblank')
				<li class="question_Name">
				{{$arr_que[$k]['id']}}
				{!!$arr_que[$k]->headtext->asXML()!!}
				<?php
					echo parseLatex_ps($arr_que[$k]->text->asXML());
					echo parseLatex_ps($arr_que[$k]->blank->asXML());

				?>
				</li>
			@endif

			@if((string)$arr_que[$k]['type']=='composition')
				<li class="question_Name">
				{{$arr_que[$k]['id']}}
				{!!$arr_que[$k]->title->asXML()!!}
				{!!$arr_que[$k]->text->asXML()!!}
				</li>
			@endif

			@if((string)$arr_que[$k]['type']=='punctuation')
				<li class="question_Name">
				{{$arr_que[$k]['id']}}
				{!!$arr_que[$k]->text->asXML()!!}<br/>
				{!!$arr_que[$k]->passage->asXML()!!}<br/>
				{!!$arr_que[$k]->term->asXML()!!}
				</li>
			@endif

			<!--
			<li class="question_select">
				A.你猜
			</li>
			-->
			<li>
				<!--<div class="question_total"><div class="question_left">总分：</div>{{$arr_que[$k]['score']}}分</div>-->
				<div class="question_left">总分：</div>{{$arr_que[$k]['score']}}分
			</li>
			<li>
				<div class="question_left">学生答案：</div>
				<?php 
					$ans_texts=$arr_user_answer[$k]->xpath('.//text');
					foreach ($ans_texts as $ans_text){
                		// echo $ans_text.' ';
                		echo parseLatex_ps($ans_text);
            		}
				?>
			</li>
			<li>
				<div class="question_left">参考答案：</div>
				<?php 
					$ans_texts=$arr_paper_answer[$k]->xpath('.//text');
					foreach ($ans_texts as $ans_text){
                		// echo $ans_text.' ';
                		echo parseLatex_ps($ans_text);
            		}
				?>
			</li>

			<!--
			@if((string)$arr_que[$k]['type']!='select')
			<li class="queCom">
				<div class="question_left">试题备注：</div>
				<textarea class="queCom" name="queText" id="queText" placeholder="请在此填写试题备注...">{{ $tea_save_coms[$k] }}</textarea>
			</li>
			@endif
			-->
			<li>
				<div class="question_left">得分：</div>
				<input title="试卷名" type="text" name="paperName_scorer" readonly="readonly" id="paperName" value="{{$tea_save_anws[$k]}}">
				</input>


			</li>
		</ul>
		@endfor
		<hr/>
	@endfor
</div>
@endif

<!-- 相关文件下载 -->
	<div class="sourceDnld">
		<div class="dnldName">相关资源下载：</div>
		<p><a href="../downloadByPath?path={{$paper_content_path}}">下载试题</a></p>
		<p><a href="../downloadByPath?path={{$user_answer_path}}">下载我的答案</a></p>
		<p><a href="../downloadByPath?path={{$paper_answer_path}}">下载参考答案</a></p>
	</div>

	<!--返回评测页-->
	<div class="backEva"><a href="{{ url('/userEvaluate')}}"><返回</a></div>
	





	@endsection