<?php

require './Writer.php';

    $writer = new Writer();

    $writer->log('simple message');
    $writer->important('my important message');
    $writer->success('Awesome message');
    $writer->warning('My warning message');
    $writer->error('My error message');
