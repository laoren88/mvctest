<?php

class ProductManager
{
    private $connectionParam = [
        'host' => '',
        'port' => '',
        'user' => '',
        'password' => '',
        'dbname' => ''
    ];
    private $db;

    public function __construct($appConfig = null)
    {
        $this->connectionParam = $appConfig['connection']['params'];        
        $this->db = new mysqli($this->connectionParam['host'], $this->connectionParam['user'], $this->connectionParam['password'], $this->connectionParam['dbname']);
        
        if (mysqli_connect_errno())
        {
            printf("Connect failed: %s\n", mysqli_connect_error());
            exit();
        }
    }
    
    /**
     * Get all product from the database
     */
    public function getAllProduct()
    {
        $data = array();
        $query = ""
                . "SELECT *"
                . "FROM product "
                . "ORDER BY date_created DESC";
        $result = $this->db->query($query);
        if ($result)
        {
            while ($row = $result->fetch_assoc()) {
                $data[] = array(
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'description' => $row['description'],
                    'price' => $row['price'],
                    'url_image' => $row['url_image']
                );
            }
            $result->close();
        }

        return $data;
    }

    /**
     * Get one product by id
     */
    public function getProduct($id)
    {
        $post = array();
        $query = ""
                . "SELECT * "
                . "FROM product "
                . "WHERE product.id = '".$id."'";
        if ($result = $this->db->query($query))
        {
            $row = $result->fetch_assoc();
            $post = array(
                'id' => $row['id'],
                'name' => $row['name'],
                'description' => $row['description'],
                'price' => $row['price'],
                'url_image' => $row['url_image']
            );

            $result->close();
        } 
        return $post;
    }

    /**
     * Add product from url
     */
    public function addProduct($url)
    {
        $data = $this->getProductData($url);
        
        if(is_null($data)) return -1;

        $query =  "INSERT INTO product(`name`, `description`, `price`, `url_image`) "
                . "VALUES ( '".$data['name']."', '".$data['description']."', ".$data['price'].", '".$data['url_image']."')";
        $query = sprintf($query, $this->db->real_escape_string($data['name']),  $this->db->real_escape_string($data['description']),  $this->db->real_escape_string($data['price']), 
                $this->db->real_escape_string($data['url_image']) );

        if ($result = $this->db->query($query))
        {
            $last_id = $this->db->insert_id;
            return $last_id;
        } 
    }

    /**
     * Processing rawdata
     */
    protected function getProductData($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        curl_close($ch);

        $dom = new DOMDocument();
        libxml_use_internal_errors( 1 );
        $dom->loadHTML( $response );
        $xpath = new DOMXpath( $dom );
        $script = $dom->getElementsByTagName( 'script' );
        $script = $xpath->query( '//script[@type="application/ld+json"]' );
        $json = $script->item(0)->nodeValue;

        $rawData = json_decode(str_replace(array("\n", "\r"), '', $json), true);
        
        if(is_null($rawData)) return null;

        if(isset($rawData['name'])) {
            $data['name'] = $rawData['name'];
        } else {
            $data['name'] = 'No Name';
        }

        if(isset($rawData['offers']['price'])) {
            $data['price'] = $rawData['offers']['price'];
        } else {
            echo "Price not found.";
            $data['price'] = 0;
        }

        if(isset($rawData['description'])) {
            $data['description'] = str_replace("'", "`", strip_tags($rawData['description']));
        } else {
            $data['description'] = 'No Description';
        }
        

        $script = $xpath->query("//script[contains(.,'mage/gallery/gallery')]");
        $json = $script->item(0)->nodeValue;
        $imageRawData = json_decode($json, true);

        if(isset($imageRawData['[data-gallery-role=gallery-placeholder]']['mage/gallery/gallery']['data'])) {
            $temp = array();
            foreach ($imageRawData['[data-gallery-role=gallery-placeholder]']['mage/gallery/gallery']['data'] as $key => $value) {
                if(isset($value['img'])) {
                    $temp[$key] = $value['img'];
                }
            }
            $data['url_image'] = json_encode($temp);
        } else {
            $data['url_image'] = 'No Image';
        }
         
        return $data;
    }
}
