<?php

/** 
 * This is Class Of Database.
 * 
 * Copyright (c) 2023 AbdulbariSH
 */

class Database
{
    /**
     * IP Or localhost For MySQL Connection 
     * @var string
     */
    private $host = "";

    /**
     * Username For MySQL Connection
     * @var string
     */
    private $user = "";

    /**
     * Password For MySQL Connection
     * @var string
     */
    private $password = '';
    /**
     * Database name 
     * 
     * Default value: store
     * 
     * @var string
     */
    private $db_name = "";
    /**
     * Var That handle MySQL Action
     * @var SQL
     */
    private $conn;

    /**
     * MySQL Connection (PDO)
     * @return PDO|SQL
     */
    public function __construct()
    {
        $this->conn = null;

        try {

            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->user, $this->password);

        } catch (PDOException $exception) {

            die("Connection error: Please Contact With Devoloper Team ");

        }
    }
    /**
     * MySQL Connection (PDO)
     * @return PDO|SQL
     */
    public function getConnection()
    {
        return $this->conn;
    }
}

?>