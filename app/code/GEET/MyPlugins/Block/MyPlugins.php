<?php
namespace GEET\MyPlugins\Block;

class MyPlugins extends \Magento\Framework\View\Element\Template
{
    public function getHelloWorldTxt()
    {
        return 'Hello world!';
    }
}
