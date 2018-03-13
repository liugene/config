<?php

namespace linkphp\config\parser;

class Ini
{

    public function parser($config)
    {
        if (is_file($config)) {
            return parse_ini_file($config, true);
        } else {
            return parse_ini_string($config, true);
        }
    }

}
