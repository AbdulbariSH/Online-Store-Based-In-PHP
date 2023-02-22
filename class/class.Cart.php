<?php

/*
  This is Class Of Cart.

  Copyright (c) 2023 AbdulbariSH
 */
class Cart
{

	/** 
	 * User Class
	 *
	 * @var User
	 */
	protected $UserInfo;
	/** 
	 * Maximum items allowed in the shopping cart.
	 *
	 * @var integer
	 */
	protected $cartMaxItem = 0;

	/** 
	 * Maximum quantity of a items allowed in the shopping cart.
	 *
	 * @var integer $itemMaxQuantity
	 */
	protected $itemMaxQuantity = 0;


	/**
	 * Array of items.
	 *
	 * @var array $items
	 */
	private $items = [];

	/**
	 * Initialize shopping cart.
	 *
	 * @param array $options
	 */
	public function __construct($options = [])
	{
		if (!session_id()) {
			session_start();
		}

		if (!isset($_SESSION['cart'])){
			$_SESSION['cart'] ="[]";
		}

		if (isset($options['cartMaxItem']) && preg_match('/^\d+$/', $options['cartMaxItem'])) {
			$this->cartMaxItem = $options['cartMaxItem'];
		}

		if (isset($options['itemMaxQuantity']) && preg_match('/^\d+$/', $options['itemMaxQuantity'])) {
			$this->itemMaxQuantity = $options['itemMaxQuantity'];
		}

		if (isset($options['UserInfo'])) {
			$this->UserInfo = $options['UserInfo'];

		}


		$this->read();
	}

	/**
	 * Get items in  cart.
	 *
	 * @return array
	 */
	public function getItems()
	{
		return $this->items;
	}

	/**
	 * Check if the cart is empty.
	 *
	 * @return bool
	 */
	public function isEmpty()
	{
		return empty(array_filter($this->items));
	}

	/**
	 * Get the total of item in cart.
	 *
	 * @return int
	 */
	public function getTotalItem()
	{
		$total = 0;

		foreach ($this->items as $items) {
			foreach ($items as $item) {
				++$total;
			}
		}

		return $total;
	}

	/**
	 * Get the total of item quantity in cart.
	 *
	 * @return int
	 */
	public function getTotalQuantity()
	{
		$quantity = 0;

		foreach ($this->items as $items) {
			foreach ($items as $item) {
				$quantity += $item['quantity'];
			}
		}

		return $quantity;
	}

	/**
	 * Get the sum of a attribute from cart.
	 *
	 * @param string $attribute
	 *
	 * @return int
	 */
	public function getAttributeTotal($attribute = 'price')
	{
		$total = 0;

		foreach ($this->items as $items) {
			foreach ($items as $item) {
				if (isset($item['options'][$attribute])) {
					$total += $item['options'][$attribute] * $item['quantity'];
				}
			}
		}

		return $total;
	}

	/**
	 * Remove all items from cart.
	 */
	public function clear()
	{
		$this->items = [];
		$this->write();
	}

	/**
	 * Check if a item exist in cart.
	 *
	 * @param string $id
	 * @param array  $options
	 *
	 * @return bool
	 */
	public function isItemExists($id, $options = [])
	{
		$options = (is_array($options)) ? array_filter($options) : [$options];

		if (isset($this->items[$id])) {
			$hash = md5(json_encode($options));
			foreach ($this->items[$id] as $item) {
				if ($item['hash'] == $hash) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Get one item from cart
	 *
	 * @param string $id
	 * @param string $hash
	 *
	 * @return array
	 */
	public function getItem($id, $hash = null)
	{
		if($hash){
			$key = array_search($hash, array_column($this->items[$id], 'hash'));
			if($key !== false)
				return $this->items[$id][$key];
			return false;
		}
		else
			return reset($this->items[$id]);
	}

	/**
	 * Add item to cart.
	 *
	 * @param string $id
	 * @param int    $quantity
	 * @param array  $options
	 *
	 * @return bool
	 */
	public function add($id, $quantity = 1, $options = [])
	{
		$quantity = (preg_match('/^\d+$/', $quantity)) ? $quantity : 1;
		$options = (is_array($options)) ? array_filter($options) : [$options];
		$hash = md5(json_encode($options));

		if (count($this->items) >= $this->cartMaxItem && $this->cartMaxItem != 0) {
			return false;
		}

		if (isset($this->items[$id])) {
			foreach ($this->items[$id] as $index => $item) {
				if ($item['hash'] == $hash) {
					$this->items[$id][$index]['quantity'] += $quantity;
					$this->items[$id][$index]['quantity'] = ($this->itemMaxQuantity < $this->items[$id][$index]['quantity'] && $this->itemMaxQuantity != 0) ? $this->itemMaxQuantity : $this->items[$id][$index]['quantity'];

					$this->write();

					return true;
				}
			}
		}

		$this->items[$id][] = [
			'id'         => $id,
			'quantity'   => ($quantity > $this->itemMaxQuantity && $this->itemMaxQuantity != 0) ? $this->itemMaxQuantity : $quantity,
			'hash'       => $hash,
			'options' => $options,
		];

		$this->write();

		return true;
	}

	/**
	 * Update item quantity.
	 *
	 * @param string $id
	 * @param int    $quantity
	 * @param array  $options
	 *
	 * @return bool
	 */
	public function update($id, $quantity = 1, $options = [])
	{
		$quantity = (preg_match('/^\d+$/', $quantity)) ? $quantity : 1;

		if ($quantity == 0) {
			$this->remove($id, $options);

			return true;
		}

		if (isset($this->items[$id])) {
			$hash = md5(json_encode(array_filter($options)));

			foreach ($this->items[$id] as $index => $item) {
				if ($item['hash'] == $hash) {
					$this->items[$id][$index]['quantity'] = $quantity;
					$this->items[$id][$index]['quantity'] = ($this->itemMaxQuantity < $this->items[$id][$index]['quantity'] && $this->itemMaxQuantity != 0) ? $this->itemMaxQuantity : $this->items[$id][$index]['quantity'];

					$this->write();

					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Remove item from cart.
	 *
	 * @param string $id
	 * @param array  $options
	 *
	 * @return bool
	 */
	public function remove($id, $options = [])
	{
		if (!isset($this->items[$id])) {
			return false;
		}

		if (empty($options)) {
			unset($this->items[$id]);

			$this->write();

			return true;
		}
		$hash = md5(json_encode(array_filter($options)));

		foreach ($this->items[$id] as $index => $item) {
			if ($item['hash'] == $hash) {
				unset($this->items[$id][$index]);
				$this->items[$id] = array_values($this->items[$id]);

				$this->write();

				return true;
			}
		}

		return false;
	}

	/* 	
	  Destroy cart session.
	  */
	public function destroy()
	{
		$this->items = [];
		unset($_SESSION['cart']);
		
	}

	/**
	 * Read items from cart session.
	 */
	public function read()
	{
		$this->items = json_decode($_SESSION['cart'], true);
	}

	/**
	 * Write changes into cart session.
	 */
	private function write()
	{
		if($this->UserInfo->getIsloginEnabled()){
			$this->UserInfo->saveCart(json_encode(array_filter($this->items)));
		}
		$_SESSION['cart'] = json_encode(array_filter($this->items));
		
	}
}