#!/usr/bin/php
<?php

setproctitle("dnode-php-magento-server-keep-alive");

$info_string = "\n"
."Starting DNode Server...           \n"
." _____  _   _           _          \n"
."|  __ \\| \\ | |         | |       \n"
."| |  | |  \\| | ___   __| | ___    \n"
."| |  | | . ` |/ _ \\ / _` |/ _ \\  \n"
."| |__| | |\\  | (_) | (_| |  __/   \n"
."|_____/|_| \\_|\\___/ \\__,_|\\___|\n"
."listening on port ".$this->port."  \n"
."\n";


while(1) {
  print ($info_string);
  exec ("php ".__DIR__."/API_Server.php");
  print ("restart...\n");
}