<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ScorerPaper extends Model
{
    //
    protected $table='scorer_paper';
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

    
}
