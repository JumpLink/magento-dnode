#!/usr/bin/php
<?php
/*
 *
 */

//setproctitle("dnode-php-magento-server");

if (file_exists($shell = __DIR__.'/../../../../../../../shell/abstract.php')) {
    require_once $shell;
} else {
  file_put_contents(__DIR__.'/dnode-php-magento.log', 'Error: File not found: "'.$shell.'"'.PHP_EOL,FILE_APPEND);
}

if (file_exists($autoload = __DIR__.'/../../../../../../../vendor/autoload.php')) {
    require_once $autoload;
} else {
  file_put_contents(__DIR__.'/dnode-php-magento.log', 'Error: File not found: "'.$autoload.'", you need to run composer install first.'.PHP_EOL,FILE_APPEND);
}


class DNode extends Mage_Shell_Abstract {

  private $dnode = null;
  private $port = null;

  public function __construct ($port) {
    parent::__construct();
    $this->port = $port;
    $this->loop = new React\EventLoop\StreamSelectLoop();
    $api = new JumpLink_DNode_Model_API();
    $this->dnode = new DNode\DNode($this->loop, $api);
  }

  public function info() {
    $info_string = "\n"
    ."Starting DNode Server...           \n"
    ." _____  _   _           _          \n"
    ."|  __ \\| \\ | |         | |       \n"
    ."| |  | |  \\| | ___   __| | ___    \n"
    ."| |  | | . ` |/ _ \\ / _` |/ _ \\  \n"
    ."| |__| | |\\  | (_) | (_| |  __/   \n"
    ."|_____/|_| \\_|\\___/ \\__,_|\\___|\n"
    ."listening on port ".$this->port."  \n";

    print ($info_string);
  }

  public function run() {
    $server = $this->dnode->listen($this->port);

    $server->on('connection', function ($connection) {
      //file_put_contents(__DIR__.'/dnode-php-magento.log', "event connection",FILE_APPEND);
    });
    $server->on('request', function ($request) {
      //file_put_contents(__DIR__.'/dnode-php-magento.log', "event request",FILE_APPEND);
    });
    $server->on('data', function ($data) {
      //file_put_contents(__DIR__.'/dnode-php-magento.log', "event data",FILE_APPEND);
    });
    $server->on('end', function () {
      //file_put_contents(__DIR__.'/dnode-php-magento.log', "event end",FILE_APPEND);
    });
    $server->on('error', function ($error) {
      file_put_contents(__DIR__.'/dnode-php-magento.log', "error event: ".$error."\n",FILE_APPEND);
    });

    $this->info();
    $this->loop->run();

  }
}

$dnode = new DNode(6060);
$dnode->run();
file_put_contents(__DIR__.'/dnode-php-magento.log', "\nClosed dnode server!\n",FILE_APPEND);