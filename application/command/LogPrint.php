<?php

    class LogPrint
    {
        public function __construct($message = '', $color = '1;37;00m')
        {
            echo "\e[".$color.$message." \e\n";
            echo "\e[1;37;00m\e[0m";
        }
    }
