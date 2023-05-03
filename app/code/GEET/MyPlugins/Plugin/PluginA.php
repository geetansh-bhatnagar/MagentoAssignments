<?php
namespace GEET\MyPlugins\Plugin;

class PluginA
{
public function beforeGetName($subject)
{
 $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/plugin-sortorder.log');
   $logger = new \Zend_Log();
$logger->addWriter($writer);
$logger->info('Before Execute From Plugin A');
}

public function aroundGetName($subject, $proceed)
{
$writer = new \Zend_Log_Writer_Stream(BP . '/var/log/plugin-sortorder.log');
  $logger = new \Zend_Log();
$logger->addWriter($writer);
$logger->info('Around Before Proceed Execute From Plugin A');

$return = $proceed();

$logger->info('Around After Proceed Execute From Plugin A');

return $return;
}

public function afterGetName($subject, $result)
{
$writer = new \Zend_Log_Writer_Stream(BP . '/var/log/plugin-sortorder.log');
  $logger = new \Zend_Log();
$logger->addWriter($writer);
$logger->info('After Execute From Plugin A');
return $result;
}
}
