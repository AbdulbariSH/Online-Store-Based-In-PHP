<?php

/*
  This is Class Of Product.

  Copyright (c) 2023 AbdulbariSH
 */
require_once 'class/class.Product.php';

class Products 
{
    /**
     * Array of Products.
	 *
	 * @var array $ProductsArray
     */
    private $ProductsArray = [];
	/**
     * MySQL Connection (PDO)
	 *
	 * @var Database $conn
     */
    private $conn;
	/**
	 * Initialize Products.
	 *
	 * @param array $options
	 */
    public function __construct($options = []) {
        $this->conn = $options['conn'];
		if(isset($options['Category']) && $options['Category'] != -1){
			$categoryId2 = intval($options['Category']);
		}
		if(isset($options['available']) && $options['available'] == -1){
			$Products = $this->conn->query("SELECT * FROM web_items");
		}elseif (isset($options['available']) && $options['available'] == 0) {
			$Products = $this->conn->query("SELECT * FROM web_items WHERE available = 0");
		}else{
			$Products = $this->conn->query("SELECT * FROM web_items WHERE available = 1");
		}
		foreach ($Products as $row) {
			if(isset($categoryId2) && $categoryId2 != $row['category_id']){
				continue;
			}
			$Product = new Product();
			$Product->setId($row['id']);
			$Product->setLabel($row['label']);
			$categoryId = intval($row['category_id']);
			$categories = $this->conn->query("SELECT * FROM web_category WHERE id = $categoryId");
			foreach($categories as $category){
				$labelCategory = $category['label'];
			}
			$Product->setCategory($labelCategory);
			$Product->setImage($row['image']);
			$Product->setOptions($row['options']);
			$Product->setPrice($row['price']);
			$Product->setAvailable($row['available']);
			if(empty($this->ProductsArray)){
				$this->ProductsArray = array($Product);
			}else{
				array_push($this->ProductsArray, $Product);
			}

		}
    }

	/**
	 * Get All Items.
	 *
	 * @return array
	 */
	public function getItems(){
		return $this->ProductsArray;
	}

	/**
	 * Check if the cart is empty.
	 *
	 * @return bool
	 */
	public function isEmpty()
	{
		return empty(array_filter($this->ProductsArray));
	}

		/**
	 * Get the total of item in cart.
	 *
	 * @return int
	 */
	public function getTotalItem()
	{
		return count($this->ProductsArray);
	}


}

?>