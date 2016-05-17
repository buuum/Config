<?php

namespace Buuum;


interface HandleErrorInterface
{
    public function getDebugMode();

    public function parseError($errtype, $errno, $errmsg, $filename, $linenum);

}