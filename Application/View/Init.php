<?php

class ProductView
{
    private $converter;
    private $content; 

    public function __construct(ProductManager $converter)
    {
        $this->converter = $converter;

        $tmp = debug_backtrace();
        $this->controller = str_replace("controller", "", strtolower($tmp[1]['class']));
        $this->action = str_replace("action", "", strtolower($tmp[1]['function']));
    }

    public function __destruct()
    {
        include '../Application/View/Layout/layout.phtml';
    }

    public function renderView($variables = null)
    {
        ob_start();
        require "../Application/View/$this->controller/$this->action.phtml";
        $this->content = ob_get_clean();
    }
}