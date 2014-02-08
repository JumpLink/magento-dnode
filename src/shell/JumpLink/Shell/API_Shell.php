#!/usr/bin/php
<?php
/*
 *
 */

//setproctitle("dnode-php-magento-server");

if (file_exists($shell = __DIR__.'/../../../../../../../shell/abstract.php')) {
    require_once $shell;
} else {
  file_put_contents(__DIR__.'/shell-php-magento.log', 'Error: File not found: "'.$shell.'"'.PHP_EOL,FILE_APPEND);
}

if (file_exists($autoload = __DIR__.'/../../../../../../../vendor/autoload.php')) {
    require_once $autoload;
} else {
  file_put_contents(__DIR__.'/shell-php-magento.log', 'Error: File not found: "'.$autoload.'", you need to run composer install first.'.PHP_EOL,FILE_APPEND);
}


class ApiShell extends Mage_Shell_Abstract {

  private $api = null;
  private $options;

  public function __construct () {
    parent::__construct();
    $this->api = new JumpLink_DNode_Model_API();
  }

  private function read_options() {
    print "READY";
    if ($fp = fopen("php://stdin", "r")) {
      $stdIn = "";
      while (!feof($fp)) { // send EOF with control+D
        $in = fgets($fp);
        if ($in == "EOF\n") {
          // print($in);
          break;
        }
        $stdIn .= $in;
      }
      //print "INPUT END";
      fclose($fp);
      $this->options = json_decode($stdIn);
      var_dump($this->options);
    }
  }

  private function call() {   
    $result = json_encode(array("result" => $this->api->call_dynamic($this->options), "method" => $this->options->method));
    print "RESULT\n";
    print $result."\n";
    print "FINISH\n";
  }

  public function run() {
    $this->read_options();
    $this->call();
  }
}


$api_shell = new ApiShell();
$api_shell->run();