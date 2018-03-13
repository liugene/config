<?php

namespace linkphp\config\parser;

class Json
{

    public function parser($config)
    {
        if (is_file($config)) {
            $config = file_get_contents($config);
        }
        $result = json_decode($config, true);
        return $result;
    }

}