<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/20
 * Time: 3:55
 */

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class BaseModel extends Model {

    protected $attributes;
    protected $messages;
    protected $rule;

    public function validator($data){
        return Validator::make($data, $this->rule, $this->messages, $this->attributes)->errors();
    }

}