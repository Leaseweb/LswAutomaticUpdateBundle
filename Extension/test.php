<?php
namespace Lsw\AutomaticUpdateBundle\Extension;

require "Ansi.php";
echo Ansi::ansi2html(file_get_contents('test'));
