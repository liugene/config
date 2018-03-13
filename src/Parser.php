<?php

namespace linkphp\config;

class Parser
{

    public function parser($type,$config)
    {
        $class = '\\linkphp\\config\\parser\\' . ucwords($type);
        return (new $class())->parser($config);
    }

}