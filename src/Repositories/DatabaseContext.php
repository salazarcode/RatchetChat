<?php
namespace RatchetChat\Repositories;

use RatchetChat\Transversal\Util;
use \PDO;
use \PDOException;

class DatabaseContext
{
    public $conn = null;

    public function __construct($host, $db, $user, $pass, $port = "3306", $charset = "utf8mb4")
    {             
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset;port=$port";      

        try 
        {               
            $pdo = new PDO($dsn, $user, $pass, $options);
            $this->conn = $pdo;
        } 
        catch (PDOException $ex) 
        {
            throw $ex;
        }    
    }

    public function Select(String $sql, Array $args = null)
    {
        try 
        {
            $q = $this->conn->prepare($sql);

            if($args != null)
                $q->execute($args);
            else
                $q->execute();

            $result = [];

            while ($row = $q->fetch()) 
                $result[] = $row;

            return $result;
        } 
        catch (\Exception $ex) 
        {
            throw $ex;
        }
    }

    public function Insert(String $sql, Array $data, bool $returnID = true)
    {
        try 
        {
            $q = $this->conn->prepare($sql);
            $q->execute($data);

            if($returnID)
            {
                $res = $this->Select("select last_insert_id() ID;");
                $newID = $res[0]["ID"];

                return $newID;                
            }
        }
        catch (\Exception $ex) 
        {
            throw $ex;
        }
    }
}