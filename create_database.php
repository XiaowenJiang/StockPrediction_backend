<?php
/**
 * Created by PhpStorm.
 * User: XiaowenJiang
 * Date: 4/5/15
 * Time: 12:40
 */

$servername = "127.0.0.1";
$username = "root";
$password = "";

// Create connection
$conn = mysql_connect($servername, $username, $password);
// Check connection
if (!$conn){

    die('cannot connect ' . mysql_error());

}else{

    mysql_query("drop  database if exists  stock",$conn);

    if (mysql_query("create  database  stock default charset utf8",$conn))
    {}
    else{

        echo " creation failure " . mysql_error();

    }

}

mysql_close($conn);