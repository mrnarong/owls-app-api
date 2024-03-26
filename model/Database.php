<?php
class Database
{
    protected $connection = null;
    public function __construct()
    {
        try {
            $this->connection = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE_NAME);
    	
            if ( mysqli_connect_errno()) {
                throw new Exception("Could not connect to database.");   
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());   
        }			
    }

    public function select($query = "" , $params = [], $sort=[], $skip=0, $limit=0)
    {
        // echo $query;
        try {
            if($sort) {
                $query .= (" ORDER BY ". $sort["key"]." ". $sort["order"]);
                // $params[0] .= "ss";
                // array_push($params, $sort["key"], $sort["order"]);
                // print_r($params);	
            }
            $stmt = $this->executeStatement( $query , $params );
            $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);		
            	
            $stmt->close();
            return $result;
        } catch(Exception $e) {
            // echo $e->getMessage();
            throw New Exception( $e->getMessage() );
        }
        return false;
    }

    public function insert($query = "" , $params = [])
    {
        echo $query;
        try {
            $stmt = $this->executeStatement( $query , $params );
            if($stmt) {
                $stmt->close();
            }
            return true;
        } catch(Exception $e) {
            // print_r($e);
            throw New Exception( $e->getMessage() );
        }
        return false;
    }

    public function update($query = "" , $params = [])
    {
        try {
            $stmt = $this->executeStatement( $query , $params );
            $result = false;
            if($stmt) {
                // print_r($stmt);
                $result = $stmt->affected_rows;
                // echo "---->".$result;
                $stmt->close();
            }
            return $result; // true;
        } catch(Exception $e) {
            // print_r($e);
            throw New Exception( $e->getMessage() );
        }
        return false;
    }

    private function executeStatement($query = "" , $params = [])
    {
        try {
            $stmt = $this->connection->prepare( $query );
            if($stmt === false) {
                throw New Exception("Unable to do prepared statement: " . $query);
            }
            if( $params ) {
                $format = $params[0];
                array_shift($params);
                $test = $stmt->bind_param($format, ...$params); //$params);
            }
            // echo $stmt;
            // print_r($stmt);
    
            $stmt->execute();
            if($stmt->errno) {
                // echo "-----\n";
                throw New Exception( $stmt->error );
            }

            return $stmt;
        } catch(Exception $e) {
            // print_r($e);
            throw New Exception( $e->getMessage() );
        }
        return false;
    }
}
