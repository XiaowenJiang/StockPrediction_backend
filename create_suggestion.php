<?php
// written by:Xiaowen Jiang
// debugged by: Xiaowen Jiang

$host = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "stock";

$symbols = array('YHOO','GOOG','MSFT','EDU','AAPL','AMZN','TWTR','FB','INTC','IBM');

$conn = mysqli_connect($host,$username, $password);

mysqli_select_db($conn,"stock");

$sql1="drop table if exists suggestion";
$sql = "create table suggestion
              (
              id int(10) primary key not null auto_increment ,
              stock char(20),
              shortterm char(20),
              longterm char(20),
              day1 double,
              day2 double,
              day3 double
              )";

mysqli_query($conn,$sql1);
mysqli_query($conn,$sql);

for($i=0;$i<10;$i++)
{
    //$su=suggest($symbols[$i]);
    $sql = "INSERT INTO suggestion (stock)
VALUES ('$symbols[$i]')";
    mysqli_query($conn,$sql);
}
mysqli_close($conn);