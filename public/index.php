<?php

$appConfig = require __DIR__ . '/../config/application.config.php';

if (isset($_GET['action']))
{
    // router 
    switch ($_GET['action'])
    {
        case 'productlist':
            $controller_name = 'ProductController';
            $action = 'productlistAction';
            break;

        case 'addproduct':
            $controller_name = 'ProductController';
            $action = 'addproductAction';
            break;

        case 'saveproduct':
            $controller_name = 'ProductController';
            $action = 'saveproductAction';
            break;

        case 'productview':
            $controller_name = 'ProductController';
            $action = 'productviewAction';
            break;

        default:
            $controller_name = 'ProductController';
            $action = 'indexAction';
            break;
    }
} else
{
    $controller_name = 'ProductController';
    $action = 'indexAction';
}
require '../Application/Controller/' . $controller_name . '.php';

require '../Application/Model/ProductManager.php';
require '../Application/View/Init.php';
$productManager = new ProductManager($appConfig);
$controller = new $controller_name($productManager);
$controller->{$action}($_REQUEST);

