<?php

// +----------------------------------------------------------------------
// | LinkPHP [ Link All Thing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2017 http://linkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liugene <liujun2199@vip.qq.com>
// +----------------------------------------------------------------------
// |               配置类
// +----------------------------------------------------------------------

namespace linkphp\config;

use framework\interfaces\ConfigInterface;

class Config implements ConfigInterface
{

    /**
     * @var Parser
     */
    private $_parser;

    private $platform;

    private $load_path;

    //保存已经加载的配置信息
    private $config = [];

    public function __construct(Parser $parser)
    {
        $this->_parser = $parser;
    }

    public function setLoadPath($path)
    {
        $this->load_path = $path;
        return $this;
    }

    public function getLoadPath()
    {
        return $this->load_path;
    }

    public function import($file)
    {
        if(is_array($file)) {
            $this->config = $file;
            return $this;
        }
        $this->set($file);
        return $this;
    }

    public function complete()
    {
        if(is_dir($this->load_path)){
            $handle = opendir($this->load_path);
            while( ($filename = readdir($handle)) !== false )
            {
//                if($filename != '.' && $filename != '..')
//                {
//                    //这里简单的用echo来输出文件名
//                    dump($filename);
//                }
                if(is_file($this->load_path . $filename)){
                    list($scope, $ext) = explode('.', $filename, 2);
                    $content = $this->load_path . $filename;
                    if($scope == 'configure'){
                        $this->set($content);
                    }
                    $this->set($content, $scope);
                }
            }
        }
    }

    private function setScope($scope,$value, $type)
    {
        if (empty($type)) $type = pathinfo($value, PATHINFO_EXTENSION);
        $config = $this->_parser->parser($type,$value);
        if(isset($this->config[$scope])){
            $this->config[$scope] = array_merge($this->config[$scope],$config);
            return true;
        }
        $this->config[$scope] = $config;
    }

    private function hasScope($scope)
    {
        return isset($this->config[$scope]);
    }

    public function set($name,$scope=null,$type='')
    {
        if(is_null($scope)){
            if (empty($type)) $type = pathinfo($name, PATHINFO_EXTENSION);
            $config = $this->_parser->parser($type,$name);
            $this->config = array_merge($this->config,$config);
            return;
        }
        $this->setScope($scope, $name, $type);
        return;
    }

    public function setPlatform($platform)
    {
        $this->platform = $platform;
        return $this;
    }

    /**
     * @param [string] $name 配置名
     * @param [string] $value 配置值
     * @return [string] 返回指定键名的键值
     * 未传入$value时默认null，进行获取项目配置
     * 传入$value时此时为动态配置
     * array_merge() 将2个以及多个数组合并成一个数组，重复的键名在后传入的键值
     * 覆盖最先传入的键值
     * 大C方法加载顺序 当扩展配置开启时首先加载应用模块扩展配置默认扩展配置关闭不进行加载
     * 分组扩展配置的值不会被之后加载进来的配置值覆盖，当应用模块中的键名不存在
     * 然后加载LinkPHP框架系统配置 -> 网站公共配置
     */
    public function get($name = null, $value = null)
    {
        if(!isset($name)) {
            return $this->config;
        }

        if (!strpos($name, '.')) {
            return $this->config[strtolower($name)];
        }

        // 二维数组设置和获取支持
        $name = explode('.', $name, 2);
        return '' === $name[1] ? $this->config[strtolower($name[0])] : $this->config[strtolower($name[0])][$name[1]];
    }

    /**
     * 检测配置是否存在
     * @access public
     * @param  string $name 配置参数名（支持二级配置 . 号分割）
     * @return bool
     */
    public function has($name)
    {
        if (!strpos($name, '.')) {
            return isset($this->config[strtolower($name)]);
        }

        // 二维数组设置和获取支持
        $name = explode('.', $name, 2);
        return isset($this->config[strtolower($name[0])][$name[1]]);
    }
}