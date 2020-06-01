<?php
class ProductController
{
    private $productManager;
    
    public function __construct($productModel)
    {
        $this->productManager = $productModel;
    }

    public function indexAction($request)
    {
        $View = new ProductView($this->productManager);
        $View->renderView($request);        
    }

    public function productlistAction($request)
    {
        $prods = $this->productManager->getAllProduct();
        $View = new ProductView($this->productManager);
        $View->renderView($prods);
    }

    public function saveproductAction($request)
    {
        $last_id = $this->productManager->addProduct($request['url']);
        if($last_id > 0) {
            $this->redirectAction("/?action=productview&id=" . $last_id . "?>");
        } else {
            $this->redirectAction("/");
        }
    }  

    public function productviewAction($request)
    {
        $prod = $this->productManager->getProduct($request['id']);
        $View = new ProductView($this->productManager);
        $View->renderView($prod);
    }
    
    public function redirectAction($route="/")
    {
        header("location: $route");
        exit;
    }
}