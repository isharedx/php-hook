<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class CI_Hooks
{

//钩子启用/停用标记
    var $enabled = FALSE;

//配置文件中定义的所有钩子
    var $hooks = array();

//是否正在执行钩子中的函数
    var $in_progress = FALSE;

//构造函数
    function __construct()
    {
        $this->_initialize();
        log_message('debug', "Hooks Class Initialized");
    }

//实例化hooks
    function _initialize()
    {
        $CFG =& load_class('Config', 'core');

// If hooks are not enabled in the config file
// there is nothing else to do

        if ($CFG->item('enable_hooks') == FALSE) {
            return;
        }

// Grab the "hooks" definition file.
// If there are no hooks, we're done.

        if (defined('ENVIRONMENT') AND is_file(APPPATH . 'config/' . ENVIRONMENT . '/hooks.php')) {
            include(APPPATH . 'config/' . ENVIRONMENT . '/hooks.php');
        } else if (is_file(APPPATH . 'config/hooks.php')) {
            include(APPPATH . 'config/hooks.php');
        }


        if (!isset($hook) OR !is_array($hook)) {
            return;
        }

        $this->hooks =& $hook;
        $this->enabled = TRUE;
    }

//调用hooks中定义的类、方法
    function _call_hook($which = '')
    {
        if (!$this->enabled OR !isset($this->hooks[$which])) {
            return FALSE;
        }

        if (isset($this->hooks[$which][0]) AND is_array($this->hooks[$which][0])) {
            foreach ($this->hooks[$which] as $val) {
                $this->_run_hook($val);
            }
        } else {
            $this->_run_hook($this->hooks[$which]);
        }

        return TRUE;
    }

//执行调用hooks中定义的类、方法
    function _run_hook($data)
    {
        if (!is_array($data)) {
            return FALSE;
        }

// -----------------------------------
// Safety - Prevents run-away loops
// -----------------------------------

// If the script being called happens to have the same
// hook call within it a loop can happen

        if ($this->in_progress == TRUE) {
            return;
        }

// -----------------------------------
// Set file path
// -----------------------------------

        if (!isset($data['filepath']) OR !isset($data['filename'])) {
            return FALSE;
        }

        $filepath = APPPATH . $data['filepath'] . '/' . $data['filename'];

        if (!file_exists($filepath)) {
            return FALSE;
        }

// -----------------------------------
// Set class/function name
// -----------------------------------

        $class = FALSE;
        $function = FALSE;
        $params = '';

        if (isset($data['class']) AND $data['class'] != '') {
            $class = $data['class'];
        }

        if (isset($data['function'])) {
            $function = $data['function'];
        }

        if (isset($data['params'])) {
            $params = $data['params'];
        }

        if ($class === FALSE AND $function === FALSE) {
            return FALSE;
        }

// -----------------------------------
// Set the in_progress flag
// -----------------------------------

        $this->in_progress = TRUE;

// -----------------------------------
// Call the requested class and/or function
// -----------------------------------

        if ($class !== FALSE) {
            if (!class_exists($class)) {
                require($filepath);
            }

            $HOOK = new $class;
            $HOOK->$function($params);
        } else {
            if (!function_exists($function)) {
                require($filepath);
            }

            $function($params);
        }

        $this->in_progress = FALSE;
        return TRUE;
    }

}