<?php
/**
 * Created by PhpStorm.
 * User: cjiali
 * Date: 2019/2/2
 * Time: 12:58
 */
namespace library;

header("Content-type:text/html;charset=utf-8");

include "Hook.php";

// 数据定义
class Test
{
    public function before()
    {
        echo 'Function library\Test\before invoked.</br>';
    }

    public function after(){
        echo 'Function library\Test\after invoked.</br>';
    }
}

function before(){ echo 'Function library\before invoked.</br>'; }
function after(){ echo 'Function library\after invoked.</br>'; }

class Demo{
    public function doSomething(){
        Hook::listen('before_do_something');
        echo 'You can do something here.';
        Hook::listen('after_do_something');
    }
}

// 类测试
echo "</br>=============== 类测试 ==============</br>";
Hook::insert('before_do_something','library\Test','before');
Hook::insert('after_do_something','library\Test','after');
$demo1 = new Demo();
$demo1->doSomething();

// 对象测试
echo "</br>============== 对象测试 =============</br>";
$test = new Test();
Hook::insert('before_do_something',$test,'before');
Hook::insert('after_do_something',$test,'after');
$demo2 = new Demo();
$demo2->doSomething();

// 函数测试
echo "</br>============== 函数测试 =============</br>";
Hook::insert('before_do_something',null,'library\before');
Hook::insert('after_do_something',null,'library\after');
$demo3 = new Demo();
$demo3->doSomething();