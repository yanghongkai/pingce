<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    //
    protected $table='users';
    protected $primaryKey='id';
    protected $guarded=[];
    public $tamestamps=true;
    /**
     * 获取当前时间
     *
     * @return int
     */
    public function freshTimestamp() {
        return time();
    }
    /**
     * 避免转换时间戳为时间字符串
     *
     * @param DateTime|int $value
     * @return DateTime|int
     */
    public function fromDateTime($value) {
        return $value;
    }
    /**
     * 从数据库获取的为获取时间戳格式
     *
     * @return string
     */
    public function getDateFormat() {
        return 'U';
    }

    public function role(){
    	return $this->belongsTo('App\Role','role_id','id');
    }

    public function papers(){
    	return $this->belongsToMany('App\Paper','user_paper','user_id','paper_id');
    }
    //定义user和user_paper之间的多对多关系
    public function user_papers(){
        return $this->belongsToMany('App\UserPaper','scorer_paper','user_id','user_paper_id');
    }
}
