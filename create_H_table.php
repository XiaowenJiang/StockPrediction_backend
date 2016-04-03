<?php
/**
 * Created by PhpStorm.
 * User: XiaowenJiang
 * Date: 3/1/15
 * Time: 15:10
 */

$host = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "stock";


$conn = mysqli_connect($host,$username, $password);

$companies=array("AAPL_h","EDU_h","GOOG_h","MSFT_h","YHOO_h","FB_h","TWTR_h","AMZN_h","INTC_h","IBM_h");



mysqli_select_db($conn,"stock");

for($i=0;$i<10;$i++)
{
    $sql1="drop table $companies[$i]";
// change the table name to create corresponding table
    $sql = "create table $companies[$i]
              (       
              date Date,
              open double,
              high double,
              low  double,
              close double,
              volume int,
              adj_close double,
			  primary key(date,open,high,low,close,volume,adj_close)
			  )";
    mysqli_query($conn,$sql1);
    mysqli_query($conn,$sql);



}






mysqli_close($conn);


?>