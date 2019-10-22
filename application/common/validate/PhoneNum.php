<?php

namespace app\common\validate;

use think\Validate;

class PhoneNum extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
	protected $rule = [
	    'phoneNum' => 'require|length:11|number',
        'code' => 'require|length:4|number',

    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [
        'phoneNum.require' => '号码不能为空',
        'phoneNum.length' => '手机号码长度不对',
        'phoneNum.number' => '号码必须为数字',
        'code.require' => '验证码不能为空',
        'code.length' => '请输入4位验证码',
        'code.number' => '验证码必须为数字',
    ];

    /**
     * 验证场景数组
     * @var array
     */
    protected $scene = [
        'pNum'  =>  ['phoneNum'],
        'register' => ['phoneNum', 'code']
    ];


}
