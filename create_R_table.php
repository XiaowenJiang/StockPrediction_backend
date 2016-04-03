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

$companies=array("AAPL_r","EDU_r","GOOG_r","MSFT_r","YHOO_r","FB_r","TWTR_r","AMZN_r","INTC_r","IBM_r");

$conn = mysqli_connect($host,$username, $password);

mysqli_select_db($conn,"stock");

for($i=0;$i<10;$i++)
{
$sql1="drop table $companies[$i]";
$sql = "create table $companies[$i]
              (       
              stock char(20),
              company char(20),
              date char(20),
              time Time,
              price double,
              volume int,
			  primary key(stock,company,date,time)
              )";


mysqli_query($conn,$sql1);   
mysqli_query($conn,$sql);

}





mysqli_close($conn);


?>