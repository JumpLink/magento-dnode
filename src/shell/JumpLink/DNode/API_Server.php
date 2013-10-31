#!/usr/bin/php
<?php
/*
 *
 */

if (file_exists($shell = __DIR__.'/../../../../../../../shell/abstract.php')) {
    require_once $shell;
} else {
  print ('Error: File not found: "'.$shell.'"'.PHP_EOL);
}

if (file_exists($autoload = __DIR__.'/../../../../../../../vendor/autoload.php')) {
    require_once $autoload;
} else {
  print ('Error: File not found: "'.$autoload.'", you need to run composer install first.'.PHP_EOL);
}


class DNode extends Mage_Shell_Abstract {

  private $dnode = null;
  private $port = null;

  public function __construct ($port) {
    parent::__construct();
    $this->port = $port;
    $this->loop = new React\EventLoop\StreamSelectLoop();
    $this->dnode = new DNode\DNode($this->loop, new JumpLink_DNode_Model_API);
  }

  public function run() {
    $this->dnode->listen($this->port);
    //print (var_dump ($this->dnode));
    $this->loop->run();

  }
}

$dnode = new DNode(6060);
$dnode->run();