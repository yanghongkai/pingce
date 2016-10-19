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
	use App\Paper;
	use App\Parser;


	?>


	<!--试题开始-->
	@if(count($arr_ques_title)>0)
	@for($i=0,$k=0;$i<count($arr_ques_title);$i++)
	<div class="questions">
		
		<div class="questions_detail">
			<div class="questions_title">
				<!-- {!!$arr_ques_head_text[$i]!!} -->
				<?php
					$str=$arr_ques_head_text[$i];
					$str=Parser::parseStr($str);	//**()** 解析
					//label解析
					$str=Parser::parseLabel($str);
					//解析pic
					$str=Parser::parsePic($paper_id,$str,'paper');
					//latex
					echo Parser::parseLatex_ps($str,$paper_category);

				?>
				(总分{{$arr_ques_score[$i]}}分)
			</div>
			<div class="questions_title">
				{!!$arr_ques_title[$i]!!}
			</div>
			<!-- <div class="questions_text">
				{!!$arr_ques_text[$i]!!}
			</div> -->
			<div class="questions_text">
			<?php
				$str=$arr_ques_text[$i];
				$str=Parser::parseStr($str);	//**()** 解析
				//label解析
				$str=Parser::parseLabel($str);
				//解析pic
				$str=Parser::parsePic($paper_id,$str,'paper');
				//latex
				echo Parser::parseLatex_ps($str,$paper_category);
				//换行
				echo '<br/>';
				//notetext
				$str_notetext=$arr_ques_notetext[$i];
				$str_notetext=Parser::parseStr($str_notetext);
				$str_notetext=Parser::parseLabel($str_notetext);
				echo $str_notetext;
				//table
				// echo Parser::parseTable($arr_ques_table[$i]);
				$str_tab=$arr_ques_table[$i];
				$tab_patt='/@@/U';
				$tab_split=preg_split($tab_patt, $str_tab);
				if(count($tab_split)>2){
					// dd($tab_split);
					for($j=0;$j<count($tab_split);$j++){
						$str_tab=$tab_split[$j];
						if(!empty($str_tab)){
							$str_tab=Parser::parseStr($str_tab);	//**()** 解析
							//table
							$str_tab=Parser::parseTable($str_tab);
							//latex
							$str_tab=Parser::parseLatex_ps($str_tab,$paper_category);
							echo $str_tab;
							echo '<br/>';		
						}
					}
				}
				else{
					$str_tab=Parser::parseStr($str_tab);	//**()** 解析
					//table
					$str_tab=Parser::parseTable($str_tab);
					//latex
					$str_tab=Parser::parseLatex_ps($str_tab,$paper_category);
					echo $str_tab;
				}

			?>
			</div>

		</div>

		@for($j=0; $j<$arr_ques_count[$i];$j++,$k++)
		<ul class="question">
			@if((string)$arr_que[$k]['type']=='select')
			<li class="question_Name">
				{{$arr_que[$k]['id']}}
				{!!$arr_que[$k]->headtext->asXML()!!}
				<?php
					$str=$arr_que[$k]->text->asXML();
					$str=Parser::parseStr($str);	//**()** 解析
					//label解析
					$str=Parser::parseLabel($str);
					//解析pic
					$str=Parser::parsePic($paper_id,$str,'paper');
					//latex
					echo Parser::parseLatex_ps($str,$paper_category);
					//table
					// echo Parser::parseTable($arr_que[$k]->tab->asXML());
					//1018
					$str_tabs=$arr_que[$k]->xpath("./tab");
					for($tab_i=0;$tab_i<count($str_tabs);$tab_i++){
						$str_tab=$str_tabs[$tab_i]->asXML();
						$str_tab=Parser::parseStr($str_tab);	//**()** 解析
						$str_tab=Parser::parseTable($str_tab);
						$str_tab=Parser::parseLatex_ps($str_tab,$paper_category);
						//latex
						echo $str_tab;	
					}

					//1018
					// $str_tab=$arr_que[$k]->tab->asXML();
					// $str_tab=Parser::parseStr($str_tab);	//**()** 解析
					// $str_tab=Parser::parseTable($str_tab);
					// $str_tab=Parser::parseLatex_ps($str_tab,$paper_category);
					// //latex
					// echo $str_tab;
				?>

			</li>
				<?php
				$sel_options=$arr_que[$k]->select->xpath('.//option');
                foreach($sel_options as $sel_option){
                	?>
                    <li class="question_select">{{$sel_option['value']}} &nbsp;
                   <?php
                    //县解析option_label
                   	$str=$sel_option->asXMl();
                   	$str=Parser::parseStr($str);	//**()** 解析
                   	//pic
                   	$str=Parser::parsePic($paper_id,$str,'paper');
                   	$str=Parser::parseOptionLabel($str);
                   	$str=Parser::parseLatex_ps($str,$paper_category);
                   	$str=Parser::removeOption($str);
					echo $str;
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
					
					$str=$arr_que[$k]->text->asXML();
					$str=Parser::parseStr($str);	//**()** 解析
					//label
					$str=Parser::parseLabel($str);
					//解析pic
					$str=Parser::parsePic($paper_id,$str,'paper');
					//公式
					echo Parser::parseLatex_ps($str,$paper_category);
					//table
					// echo Parser::parseTable($arr_que[$k]->tab->asXML());
					$str_tab=$arr_que[$k]->tab->asXML();
					$str_tab=Parser::parseStr($str_tab);	//**()** 解析
					//table
					$str_tab=Parser::parseTable($str_tab);
					//latex
					$str_tab=Parser::parseLatex_ps($str_tab,$paper_category);
					echo $str_tab;
				?>
				</li>
			@endif

			@if((string)$arr_que[$k]['type']=='fillblank')
				<li class="question_Name">
				{{$arr_que[$k]['id']}}
				{!!$arr_que[$k]->headtext->asXML()!!}
				<?php
					$str_text=$arr_que[$k]->text->asXML();
					$str_text=Parser::parseStr($str_text);	//**()** 解析
					//label
					$str_text=Parser::parseLabel($str_text);
					//pic
					$str_text=Parser::parsePic($paper_id,$str_text,'paper');
					//latex
					echo Parser::parseLatex_ps($str_text,$paper_category);
					//table
					// echo Parser::parseTable($arr_que[$k]->tab->asXML());
					// echo Parser::parseLatex_ps($arr_que[$k]->blank->asXML());
					//数学blank里面有label
					$str=$arr_que[$k]->blank->asXML();
					$str=Parser::parseStr($str);	//**()** 解析
					$str=Parser::parseLabel($str);
					//解析pic
					$str=Parser::parsePic($paper_id,$str,'paper');
					//latex
					echo Parser::parseLatex_ps($str,$paper_category);
					//table
					// echo Parser::parseTable($arr_que[$k]->tab->asXML());
					$str_tab=$arr_que[$k]->tab->asXML();
					$str_tab=Parser::parseStr($str_tab);	//**()** 解析
					// echo $str_tab;
					$str_tab=Parser::parseTable($str_tab);
					//latex
					$str_tab=Parser::parseLatex_ps($str_tab,$paper_category);
					echo $str_tab;
					// echo Parser::parseTable($str_tab);

				?>
				</li>
			@endif

			@if((string)$arr_que[$k]['type']=='composition')
				<li class="question_Name">
				{{$arr_que[$k]['id']}}
				{!!$arr_que[$k]->title->asXML()!!}
				<?php
				$str=$arr_que[$k]->text->asXML();
				$str=Parser::parseStr($str);
				$str=Parser::parseLabel($str);
				echo $str;
				?>
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
					$user_answer_get=Parser::getQuestionById($user_answer,$arr_que[$k]['id']);
					if(!empty($user_answer_get)){
						$ans_texts=$user_answer_get->xpath('.//text');
						foreach ($ans_texts as $ans_text){
	                		$str=$ans_text->asXML();
	                		$str=Parser::parseStr($str);	//**()** 解析
	                		$str=Parser::parseLabel($str);
							//解析pic
							$str=Parser::parsePicUser($user_paper_id,$str);
							//latex
							echo Parser::parseLatex_ps($str,$paper_category)."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	                		// echo Parser::parseLatex_ps($ans_text)."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	            		}
	            		//table
	            		// $str=$user_answer_get->tab->asXML();

	            		$str_tabs=$user_answer_get->xpath("./tab");
	            		for($tab_i=0;$tab_i<count($str_tabs);$tab_i++){
	            			$str_tab=$str_tabs[$tab_i]->asXML();
	            			$str_tab=Parser::parseStr($str_tab);	//**()** 解析
		            		$str_tab=Parser::parseTable($str_tab);
		            		//latex
							$str_tab=Parser::parseLatex_ps($str_tab,$paper_category);
							echo $str_tab;
		            		// echo Parser::parseTable($str);
	            		}

					}
				?>
			</li>
			<li>
				<div class="question_left">参考答案：</div>
				<?php 
					$paper_answer_get=Parser::getQuestionById($paper_answer,$arr_que[$k]['id']);
					if(!empty($paper_answer_get)){
						$ans_texts=$paper_answer_get->xpath('.//text');
						foreach ($ans_texts as $ans_text){
	                		// echo $ans_text.' ';
	                		$str=$ans_text->asXML();
	                		$str=Parser::parseStr($str);	//**()** 解析
	                		$str=Parser::parseLabel($str);
							//解析pic
							$str=Parser::parsePic($paper_id,$str,'answer');
							//latex
							echo Parser::parseLatex_ps($str,$paper_category)."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

	                		// echo Parser::parseLatex_ps($ans_text->asXML())."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

	            		}
	            		//table 先进行table处理，再进行LaTeX处理
	            		// $str=$paper_answer_get->tab->asXML();
	            		$str_tabs=$paper_answer_get->xpath("./tab");
	            		for($tab_i=0;$tab_i<count($str_tabs);$tab_i++){
	            			$str_tab=$str_tabs[$tab_i]->asXML();
	            			$str_tab=Parser::parseStr($str_tab);	//**()** 解析
		            		$str_tab=Parser::parseTable($str_tab);
		            		//latex
							$str_tab=Parser::parseLatex_ps($str_tab,$paper_category);
							echo $str_tab;
		            		// echo Parser::parseTable($str);
	            		}

					}

				?>
			</li>

			
			<li>
				<div class="question_left">得分：</div>
				<input title="试卷名" type="text" name="paperName_scorer" id="paperName" value="<?php $que_id=(string)$arr_que[$k]['id']; echo $tea_save_anws[$que_id]; ?>">
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