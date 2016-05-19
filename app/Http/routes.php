<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', 'HomeController@home');



Route::get('/download','DirectAccessController@accDownload');
Route::get('/evaluate','DirectAccessController@accEvaluate');
Route::get('/user','DirectAccessController@accUser');
//新闻
Route::get('/news/{id}','HomeController@accNews');
//评测记录
Route::get('/userEvaluate','DirectAccessController@accUserEvaluate');
//阅卷记录
Route::get('/userScorer','DirectAccessController@accUserScorer');
//检索阅卷记录
Route::post('/search','DirectAccessController@search');
//试卷管理
Route::get('/userManage','DirectAccessController@accUserManage');
//新建试卷
Route::get('/paperNew','DirectAccessController@paperNew');
//新建图片上传页面
Route::get('/picNew/{id}','DirectAccessController@picNew');
//图片上传post提交
Route::post('/picNew','PaperController@postPicNew');
//考生图片上传页面
Route::get('/picNewUser/{id}','DirectAccessController@picNewUser');
//考生图片上传post请求
Route::post('/picNewUser','PaperController@postPicNewUser');
//新闻管理
Route::get('/manageNews','DirectAccessController@manageNews');
//新建新闻
Route::get('/newsNew','DirectAccessController@newsNew');


//用户管理
Route::get('/manageUser','DirectAccessController@manageUser');
//新建用户
Route::get('/userNew','DirectAccessController@userNew');
//资源管理
Route::get('/manageResource','DirectAccessController@manageResource');
//新建资源
Route::get('/resourceNew','DirectAccessController@resourceNew');

//post 新建新闻
Route::post('/newsNew','DirectAccessController@postNewsNew');
//编辑新闻
Route::post('/newsEditTrue','DirectAccessController@postNewsEditTrue');
//删除新闻
Route::post('/newsDelete/{id}','DirectAccessController@newsDelete');
//置顶新闻
Route::post('/newsTop/{id}','DirectAccessController@newsTop');
//编辑新闻
Route::post('/newsEdit/{id}','DirectAccessController@newsEdit');
Route::get('/newsEdit/{id}','DirectAccessController@newsEdit');
//编辑试卷
Route::post('/paperEdit/{id}','DirectAccessController@paperEdit');
Route::get('/paperEdit/{id}','DirectAccessController@paperEdit');
//新建资源
Route::post('/resourceNew','PaperController@postResourceNew');
//新建用户
Route::post('/userNew','PaperController@postUserNew');
//删除资源
Route::post('/resDelete/{id}','DirectAccessController@resDelete');
//修改用户密码
Route::get('/pwdEdit/{id}','DirectAccessController@pwdEdit');
Route::post('/pwdEdit','DirectAccessController@postPwdEdit');
//删除用户
Route::get('/userDel/{id}','DirectAccessController@userDel');
//试卷详情
Route::get('/stuPaper/{id}','DirectAccessController@stuPaper');


//修改个人资料
Route::get('/evaluateProfile','DirectAccessController@accProfile');
Route::post('/editProfile','DirectAccessController@editProfile');
Route::get('/scorerProfile','DirectAccessController@accProfile');
Route::post('/scorerProfile','DirectAccessController@editProfile');
Route::get('/manageProfile','DirectAccessController@accProfile');
Route::post('/manageProfile','DirectAccessController@editProfile');
//新建试卷表单
Route::post('/paperNew','PaperController@paperNew');
//修改试卷
ROute::post('/paperEditTrue','PaperController@paperEditTrue');
//上传试卷
//Route::get('/uploadPaper','PaperController@getUploadPaper');
Route::post('/uploadPaper','PaperController@postUploadPaper');
Route::post('/uploadAnswer','PaperController@postUploadAnswer');
//编辑试卷页面中的删除试卷和答案
Route::post('/uploadPaperEdt','PaperController@postUploadPaperEdt');
Route::post('/uploadAnswerEdt','PaperController@postUploadAnswerEdt');

//上传资源
Route::post('/uploadResource','PaperController@postUploadResource');
//上传图片
Route::post('/uploadPic','PaperController@postUploadPic');
//上传用户试卷图片
Route::post('/uploadPicUser','PaperController@postUploadPicUser');
//下载试卷
Route::get('/downloadPaper/{id}','PaperController@downloadPaper');
//
Route::post('/uploadUserAnswer','PaperController@uploadUserAnswer');
//测试删除功能
Route::get('/delete','PaperController@delete');
//阅卷页面
Route::get('/paperScore/{id}','PaperController@paperScore');
//删除试卷
Route::post('/paperDelete/{id}','PaperController@paperDelete');
//保存每道题的批改结果
Route::post('/answerSave','PaperController@answerSave');
//保存试卷的总成绩
Route::post('/gradeSave','PaperController@gradeSave');
//保存试卷的备注信息
Route::post('/commentSave','PaperController@commentSave');
//通过路径下载
Route::get('/downloadByPath','PaperController@downloadByPath');

/* 
//考生，阅卷人，管理员对应的角色
Route::get('/userEvaluate',function(){
	return view('userEvaluate');
});

Route::get('/userScorer',function(){
	return view('userScorer');
});
Route::get('/userManage',function(){
	return view('userManage');
});
*/





//用户登录
Route::post('login','UserController@postLogin');

//用户退出
//Route::post('logout','HomeController@postLogout');
Route::post('logout','DirectAccessController@postLogout');

//对数据库中数据的填充（使用隐式控制器）
Route::controller('seed','SeedController');







