<?php

namespace tpext\myadmin\common;

class RouteMaker
{
    /**
     * 生成路由文件
     * @param mixed $force
     * @return bool|int
     */
    public static function make($force = false)
    {
        $file = app()->getRootPath() . 'route/tpext-myadmin.php';
        if (!$force && file_exists($file)) {
            return true;
        }

        $randomString = self::randomString();

        $root = Module::getInstance()->getRoot();
        $tpl = file_get_contents($root . 'src/route.php');
        $tpl = str_replace('__entrance__', $randomString, $tpl);

        return @file_put_contents($file, $tpl);
    }

    /**
     * 获取入口地址
     * @return string
     */
    public static function getEntrance()
    {
        $file = app()->getRootPath() . 'route/tpext-myadmin.php';
        if (!file_exists($file)) {
            return '路由不存在';
        }

        $content = file_get_contents($file);
        if (preg_match('/.*?\$entrance\s*=\s*[\'\"]([^\'\"]+?)[\'\"].*?/i', $content, $matches)) {
           return 'http://' . request()->host() . $matches[1];
        }

        return '';
    }

    /**
     * 移除路由文件
     * @return bool
     */
    public static function remove()
    {
        $file = app()->getRootPath() . 'route/tpext-myadmin.php';
        if (file_exists($file)) {
            return @unlink($file);
        }

        return true;
    }

    public static function randomString($length = 10)
    {
        $characters = '23456789abcdefghijkmnpqrstuvwxyz';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i += 1) {
            $randomIndex = mt_rand(0, $charactersLength - 1);
            $randomString .= $characters[$randomIndex];
        }

        return $randomString;
    }
}