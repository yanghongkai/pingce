<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserPaper extends Model
{
    //
     protected $table='user_paper';
    protected $primaryKey='id';
    protected $guarded=[];
    //protected $dateFormat='U';
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

    //定义和user之间的多对多关系，即一张试卷可以被多个老师批改，一个老师也可以批改多个试卷
    public function users(){
    	return $this->belongsToMany('App\User','scorer_paper','user_paper_id','user_id');
    }


}
