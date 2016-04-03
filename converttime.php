<?php
/**
 * Created by PhpStorm.
 * User: XiaowenJiang
 * Date: 4/30/15
 * Time: 22:48
 */
$host = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "stock";
$symbols=array("AAPL","EDU","GOOG","MSFT","YHOO","FB","TWTR","AMZN","INTC","IBM");

$conn = mysql_connect($host, $username, $password);
mysql_select_db('stock');

if(! $conn )
{
    die('Could not connect: ' . mysql_error());
}

$temp="MSFT_r";

$sql = "SELECT time FROM $temp";

$retval = mysql_query( $sql, $conn );
if(! $retval )
{
    die('Could not get data: ' . mysql_error());
}
$queryp=array();
while($row = mysql_fetch_array($retval, MYSQL_ASSOC))
{
    array_push($queryp,$row["time"]);
}
mysql_close($conn);

$temptime=array();
for($i=0;$i<count($queryp);$i++)
{
    $arr2 = explode(":",$queryp[$i]);
   if((int)$arr2[0]<=4)
   {
       $arr2[0]+=12;
   }
    array_push($temptime,"$arr2[0]:$arr2[1]:$arr2[2]");

}
$timea=array();
for($i=0;$i<count($temptime);$i++)
{
    $temp=strtotime("$temptime[$i]");
    $time24=date('H:i:s',$temp);

    mysqli_query($conn,$sql);
}