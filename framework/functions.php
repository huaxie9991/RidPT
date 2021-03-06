<?php

/**
 * 助手函数
 */

if (!function_exists('app')) {
    /** 返回当前 App 实例
     * @return \Rid\Base\Application
     */
    function app()
    {
        return \Rid\Rid::getApp();
    }
}

if (!function_exists('env')) {
    /** 获取一个环境变量的值
     * @param null $name
     * @param string $default
     * @return array|mixed|string
     */
    function env($name = null, $default = '')
    {
        if ($name === null) {
            return $_ENV;
        }
        return $_ENV[$name] ?? $default;
    }
}

if (!function_exists('__')) {
    function __(string $string, array $avg = [], $domain = null, $lang = null)
    {
        return \Rid\Helpers\ContainerHelper::getContainer()->get('i18n')->trans($string, $avg, $domain, $lang);
    }
}

if (!function_exists('config')) {
    function config(string $config)
    {
        return \Rid\Helpers\ContainerHelper::getContainer()->get('config')->get($config);
    }
}

if (!function_exists('value')) {
    /**
     * Return the default value of the given value.
     *
     * @param mixed $value
     * @return mixed
     */
    function value($value)
    {
        return $value instanceof Closure ? $value() : $value;
    }
}
