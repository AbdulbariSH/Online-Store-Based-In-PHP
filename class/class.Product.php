<?php
/** 
 * This is Class Of Product.
 * 
 * Copyright (c) 2023 AbdulbariSH
 */
class Product
{
    /**
     * Product Id 
     * 
     * @var integer
     */
    public $id;

    /**
     * Product name 
     * 
     * @var string
     */
    public $label;

    /**
     * Product category 
     * 
     * @var string
     */
    public $category;

    /**
     * Product image link 
     * 
     * @var string
     */
    public $image;

    /**
     * Product options like colors
     * 
     * @var array
     */
    public $options;

    /**
     * Product price
     * 
     * @var integer
     */
    public $price;

    /**
     * Product is available or not
     * 
     * @var integer
     */
    public $available;

    /**
	 * Initialize Product Id .
	 *
	 * @param integer $id
	 */
    public function setId($id)
    {
        $this->id = intval($id);
    }
    /**
	 * Initialize Product name .
	 *
	 * @param string $id
	 */
    public function setLabel($label)
    {
        $this->label = htmlentities($label);
    }
    /**
	 * Initialize Product category .
	 *
	 * @param string $category
	 */
    public function setcategory($category)
    {
        $this->category = htmlentities($category);
    }
    /**
	 * Initialize Product image .
	 *
	 * @param string $image
	 */
    public function setImage($image)
    {
        $this->image = htmlentities($image);
    }
    /**
	 * Initialize Product options .
	 *
	 * @param array $options
	 */
    public function setOptions($options)
    {
        $this->options = json_decode($options);
    }
    /**
	 * Initialize Product price .
	 *
	 * @param integer $price
	 */
    public function setPrice($price)
    {
        $this->price = intval($price);
    }
    /**
	 * Initialize Product available .
	 *
	 * @param integer $available
	 */
    public function setAvailable($available)
    {
        $this->available = intval($available);
    }

    /**
	 * Get Product id.
	 *
	 * @return integer
	 */
    public function getId()
    {
        return $this->id;
    }
    /**
	 * Get Product name.
	 *
	 * @return string
	 */
    public function getLabel()
    {
        return $this->label;
    }
    /**
	 * Get Product category.
	 *
	 * @return string
	 */
    public function getcategory()
    {
        return $this->category;
    }
    /**
	 * Get Product image.
	 *
	 * @return string
	 */
    public function getImage()
    {
        return $this->image;
    }
    /**
	 * Get Product options.
	 *
	 * @return array
	 */
    public function getOptions()
    {
        return $this->options;
    }
    /**
	 * Get Product price.
	 *
	 * @return integer
	 */
    public function getPrice()
    {
        return $this->price;
    }
    /**
	 * Get Product available.
	 *
	 * @return integer
	 */
    public function getAvailable()
    {
        return $this->available;
    }
}