<?php 
    require './LogPrint.php';

    const FOREGROUND_WHITE = "\e[1;37;";
    const FOREGROUND_GREEN = "\e[0;32;";
    const FOREGROUND_YELLOW = "\e[1;33;";
    const FOREGROUND_RED = "\e[0;31;";
    const FOREGROUND_GREY = "\e[1;30;";
    const FOREGROUND_BLACK = "\e[0;30;";
    const FOREGROUND_BLUE = "\e[0;34;";

    const BACKGROUND_BLACK = "40m";
    const BACKGROUND_RED = "41m";  
    const BACKGROUND_GREEN = "42m";  
    const BACKGROUND_YELLOW = "43m";
    const BACKGROUND_NULL = "00m";

    const TAB_COLOR_TAG = 
        [
            'ERROR' =>
                [ 
                    'TAG' => '[ ERROR ]',
                    'COLOR' => FOREGROUND_WHITE . BACKGROUND_RED
                ],
            'WARNING' => 
                [
                    'TAG' => '[WARNING]',
                    'COLOR' => FOREGROUND_YELLOW . BACKGROUND_BLACK
                ],
            'SUCCESS' => 
                [ 
                    'TAG' => '[SUCCESS]',
                    'COLOR' => FOREGROUND_BLACK . BACKGROUND_GREEN
                ],
            'LOG' => 
                [
                    'TAG' => '[  LOG  ]',
                    'COLOR' => FOREGROUND_WHITE . BACKGROUND_BLACK
                ],
            'IMPORTANT' => 
                [
                    'TAG' => '[IMPORTANT]',
                    'COLOR' => FOREGROUND_BLUE . BACKGROUND_BLACK
                ]

        ];

    class Writer{

        public function __construct(){
            new LogPrint("Writer init !");
        }

        public function writeWithColor($tag,$message){
            new LogPrint(TAB_COLOR_TAG[$tag]['TAG']." : ".$message, TAB_COLOR_TAG[$tag]['COLOR']);
        }

        public function log($message){
            $this->writeWithColor('LOG',$message);
        }

        public function success($message){
            $this->writeWithColor('SUCCESS',$message);
        }

        public function warning($message){
            $this->writeWithColor('WARNING',$message);
        }

        public function error($message){
            $this->writeWithColor('ERROR',$message);
        }

        public function important($message){
            $this->writeWithColor('IMPORTANT',$message);
        }
        public function tableLog($mask, ...$valueTable){
            printf($mask, ...$valueTable);
        }

    }

?>