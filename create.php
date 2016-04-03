<?php
/**
 * Created by PhpStorm.
 * User: XiaowenJiang
 * Date: 4/12/15
 * Time: 21:00
 */
$symbols = array('YHOO','GOOG','MSFT','EDU','AAPL','AMZN','TWTR','FB','INTC','IBM');

$host = "127.0.0.1";
$username = "root";
$password = "";


$conn = mysqli_connect($host,$username, $password);
mysqli_select_db($conn,"stock");

/* check connection */
	if (mysqli_connect_errno())
    {
        printf("Connect failed: %s\n", mysqli_connect_error());
        exit();
    }


for($i = 0; $i < 10; $i++)
{
    $sql1="drop table if exists $symbols[$i]";
    // change the table name to create corresponding table
    mysqli_query($conn,$sql1);

    $sql = "create table $symbols[$i]
				  (
				  id int(10) primary key not null auto_increment ,
				  date Date,
				  close double,
				  SMA double,
				  WMA double,
				  ROC double,
				  CC double,
				  RSI double,
				  MACD double,
				  SO double
				  )";

    mysqli_query($conn,$sql);

    $a = $symbols[$i].'_h';
    $sql = "insert into $symbols[$i] (date,close) select date,close from $a";
    mysqli_query($conn,$sql);
}


mysqli_close($conn);
?>