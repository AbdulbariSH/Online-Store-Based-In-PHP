<?php

/** 
 * This is Class Of User.
 * 
 * Copyright (c) 2023 AbdulbariSH
 */

class User
{
  /**
   * User username
   * 
   * @var string $username
   */
  public $username;
  /**
   * User id
   * 
   * @var integer $id
   */
  private $id;
  /**
   * User email
   * 
   * @var string $email
   */
  public $email;
  /**
   * User is Login Or Not
   * 
   * @var bool $isloginEnabled
   */
  private $isloginEnabled = false;
  /**
   * MySQL Connection (PDO)
   *
   * @var Database $conn
   */
  private $conn;
  /**
   * User errors message
   * 
   * @var string $message
   */
  public $message;
  /**
   * User shopping cart
   * 
   * @var Cart $cart
   */
  public $cart;
  /**
	 * Initialize user .
	 *
	 * @param array $options
	 */
  public function __construct($options = [])
  {
    $this->conn = $options['conn'];
    $this->message = "null";
    if (!session_id()) {
      session_start();
    }

    if (isset($_SESSION['user_id'])) {

      $stmt = $this->conn->prepare('SELECT * FROM web_users WHERE id = ?');
      $stmt->execute([$_SESSION['user_id']]);
      $account = $stmt->fetch(PDO::FETCH_ASSOC);
      if ($account) {
        $_SESSION['user_id'] = $account['id'];
        $this->setUsername($account['username']);
        $this->setEmail($account['email']);
        $this->setCart($account['cart']);
        $this->setId($account['id']);
        $this->setIsloginEnabled(true);
      }
    }

  }
  /**
   * set username for user
   * 
   * @param string $username
   */
  public function setUsername($username)
  {
    $this->username = htmlentities($username);
  }
  /**
   * set id for user
   * 
   * @param integer $id
   */
  private function setId($id)
  {
    $this->id = intval($id);
  }
  /**
   * set email for user
   * 
   * @param string $email
   */
  public function setEmail($email)
  {
    $this->email = intval($email);
  }
  /**
   * set shopping cart for user
   * 
   * @param array $cart
   */
  public function setCart($cart)
  {
    $this->cart = $cart;
    $_SESSION['cart'] = $cart;
  }
  /**
   * set login or not for user
   * 
   * @param bool $isloginEnabled
   */
  public function setIsloginEnabled($isloginEnabled)
  {
    $this->isloginEnabled = boolval($isloginEnabled);
  }
  /**
   * Get user username
   * 
   * @return string
   */
  public function getUsername()
  {
    return $this->username;
  }
  /**
   * Get user id
   * 
   * @return integer
   */
  private function getId()
  {
    return $this->id;
  }
  /**
   * Get user email
   * 
   * @return string
   */
  public function getemail()
  {
    return $this->email;
  }
  /**
   * Get user is login or not
   * 
   * @return bool
   */
  public function getIsloginEnabled()
  {
    return $this->isloginEnabled;
  }
  /**
   * Get user shopping cart
   * 
   * @return array
   */
  public function getCart()
  {
    return $this->cart;
  }
  /**
   * login user to system
   * 
   * @param string $username
   * @param string $password
   * 
   * @return bool
   */
  public function login($username, $password)
  {
    $username = htmlentities($username);
    if (($username == null || $password) == null) {
      $this->message = "Please fill out the information in the form correctly";
      return false;
    }
    $stmt = $this->conn->prepare('SELECT * FROM web_users WHERE username = ?');
    $stmt->execute([$username]);
    $account = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($account) {

      if (password_verify($password, $account['password'])) {
        session_regenerate_id();
        $_SESSION['user_id'] = $account['id'];
        $this->setUsername($account['username']);
        $this->setEmail($account['email']);
        $this->setCart($account['cart']);
        $this->setId($account['id']);
        $this->setIsloginEnabled(true);
        return true;
      } else {
        $this->message = "Incorrect username and/or password!";
        return false;
      }
    } else {
      $this->message = "Incorrect username and/or password!";
      return false;
    }
  }
  /**
   * register user to system
   * 
   * @param string $username
   * @param string $password
   * @param string $email
   * 
   * @return bool
   */
  public function register($username, $password, $email)
  {
    $username = htmlentities($username);
    $email = htmlentities($email);
    if ($username == null || $password == null || $email == null) {
      $this->message = "Please fill out the information in the form correctly";
      return false;
    }

    if (preg_match('/[^A-Za-z0-9]/', $username)) {
      $this->message = "Sorry... Allow only English letters and numbers in username";
      return false;
    }
    if (strlen($username) > 16 || strlen($username) < 3) {
      $this->message = "Sorry... Username must be from 3 to 15 characters";
      return false;
    }
    $cheakusername = $this->conn->query("SELECT count(*) FROM web_users WHERE username = '$username'");
    $cheakusernamee = $cheakusername->fetchColumn();
    if ($cheakusernamee > 0) {
      $this->message = "Sorry... Username already taken";
      return false;
    }
    $cheakemail = $this->conn->query("SELECT count(*) FROM web_users WHERE email = '$email'");
    $cheakemaill = $cheakemail->fetchColumn();
    if ($cheakemaill > 0) {
      $this->message = "Sorry... Email already taken";
      return false;
    }
    $hashpassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $this->conn->prepare('INSERT IGNORE INTO web_users (username,password,email,cart) VALUES (?,?,?,?)');
    $stmt->execute([$username, $hashpassword, $email, "[]"]);
    if ($this->login($username, $password)) {
      return true;
    } else {
      $this->message = "Sorry... Something Error";
      return false;
    }
  }
  /**
   * get Message
   * 
   * @return string
   */
  public function getMessage()
  {
    return $this->message;
  }
  /**
   * logout user
   * 
   * @return bool
   */
  public function Logout()
  {

    unset($_SESSION['user_id']);
    unset($_SESSION['cart']);
    session_destroy();
    $this->setUsername(null);
    $this->setEmail(null);
    $this->setId(null);
    $this->setIsloginEnabled(false);

    if (!isset($_SESSION['user_id'])) {
      return true;
    } else {
      $this->message = "Sorry... Something Error";
      return false;
    }
  }
  /**
   * check if user is login or not 
   * 
   * @return bool
   */
  public function userStatus()
  {
    if ($this->getIsloginEnabled()) {
      return true;
    } else {
      return false;
    }
  }
  /**
   * save shopping cart to database
   * 
   * @param array $cartArray
   * 
   * @return bool
   */
  public function saveCart($cartArray)
  {
    $userid = $this->getId();
    $cartJson = $cartArray;
    $stmt = $this->conn->prepare('UPDATE web_users SET cart = ? WHERE id = ?');
    $stmt->execute([$cartJson, $userid]);
    return true;
  }
}