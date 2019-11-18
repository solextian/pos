<?php

class DBConnection{
    const HOST = "localhost";
    const USERNAME = "root";
    const PASSWORD = "root";
    const DATABASE = "pos";
    function get_db_connection(){
        $conn = mysqli_connect(self::HOST,self::USERNAME,self::PASSWORD, self::DATABASE) or die("Couldn't connect");
        return $conn;
    }
}   

?> 