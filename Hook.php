<?php
/**
 * Created by PhpStorm.
 * User: cjiali
 * Date: 2019/2/2
 * Time: 11:11
 */

namespace library;

header("Content-type:text/html;charset=utf-8");

class Hook
{
    private static $hooks = array();

    /**
     * 获取入口索引
     * @param $entry 入口
     * @return int|string 索引
     */
    private static function indexOf($entry){
        // $arguments = func_get_args();
        $hook_index = -1;
        foreach (self::$hooks as $index => $value) {
            //  && ($value['host'] === $host) && ($value['invoke'] === $invoke)
            if ($value['entry'] === $entry)
                $hook_index = $index;
        }
        return $hook_index;
    }

    /**
     * 插入（注册）钩子
     * @param $entry 入口
     * @param $host 宿主
     * @param $invoke 引用方法名
     */
    public static function insert($entry,$host,$invoke){
        $hook_index = self::indexOf($entry);
        //if ($hook_index !== -1)
        //    throw new \Error("Hook.insertMethod:The hook has inserted.");
        //else
        //    self::$hooks[$entry] = array('entry' => $entry, 'host' => $host, 'invoke' => $invoke);
        if ($hook_index !== -1){
            // do something to notice that: Hook.insertMethod:The hook has inserted.
        }
        self::$hooks[$entry] = array('entry' => $entry, 'host' => $host, 'invoke' => $invoke);
    }

    /**
     * 移除（注销）钩子
     * @param $entry 入口
     */
    public static function remove($entry){
        $hook_index = self::indexOf($entry);
        if ($hook_index !== -1)
             unset(self::$hooks[$hook_index]);
        else
            throw new \Error("Hook.insertMethod:The hook has inserted.");
    }

    /**
     * 监听挂载点
     * @param $entry 入口
     * @param null $args 参数
     */
    public static function listen($entry,&$args = null){
        $hook_index = self::indexOf($entry);
        $hook = ($hook_index !== -1) ? self::$hooks[$hook_index] : null;

        if (isset($hook) && !!$hook) {
            $host = '';
            $invoke = '';

            if(isset($hook['host']))
                $host = $hook['host'];
            if(isset($hook['invoke']))
                $invoke = $hook['invoke'];

            // 1、类调用
            if (!!$host && !!$invoke && is_string($host) && class_exists($host) && method_exists($host, $invoke)) {
                $clazz = new $host;
                $clazz->$invoke($args);
                // call_user_func_array(array($host, $invoke), $args);
            }
            // 2、对象调用
            else if(!!$host && !!$invoke && is_object($host) && method_exists($host, $invoke)) {
                $host->$invoke($args);
            }
            // 3、函数调用
            else if (!$host && !!$invoke && function_exists($invoke))
                $invoke($args);
            else
                throw new \Error('Hook->execute: params is invalid!');
        }
    }

}