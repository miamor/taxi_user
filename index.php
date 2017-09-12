<?php
// config file
include_once 'config.php';

//if (!file_exists('pages/'.$page.'.php')) $page = 'error';

include 'api/'.$page.'.php';
