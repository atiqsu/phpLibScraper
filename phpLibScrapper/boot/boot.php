<?php

set_include_path(
        get_include_path() .
        PATH_SEPARATOR  . 'lib/' .
        PATH_SEPARATOR  . 'boot/' .
        PATH_SEPARATOR  . 'txt/'
        );

require_once 'const.php';
require_once 'LIB_parse.php';
require_once 'LIB_mysql.php';
require_once 'DMM_utiles.php';

?>
