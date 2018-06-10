<?php

namespace linkphp\config\parser;


class Php
{

    public function parser($config)
    {
        if (is_file($config)) {
            $config = include($config);
        }
        return $config;
    }

}