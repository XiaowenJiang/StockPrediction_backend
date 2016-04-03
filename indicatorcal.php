<?php
/**
 * Created by PhpStorm.
 * User: XiaowenJiang
 * Date: 4/5/15
 * Time: 16:38
 */

$symbols = array('YHOO','GOOG','MSFT','EDU','AAPL','IBM','FB','TWTR','AMZN','INTC'); // you can change the data you want

$username = "root";
$password = "";
$host = "127.0.0.1";
$database="stock";
// Create connection
$conn = mysql_connect($host, $username, $password);
if(! $conn )
{
    die('Could not connect: ' . mysql_error());
}
$sql = 'SELECT close FROM HISTORYYHOO';

mysql_select_db('stock');
$retval = mysql_query( $sql, $conn );
if(! $retval )
{
    die('Could not get data: ' . mysql_error());
}
$close=array();
while($row = mysql_fetch_array($retval, MYSQL_ASSOC))
{
    array_push($close,$row['close']);
}
for($i=0;$i<count($close);$i++)
{
    echo $close[$i],'  ';
}
mysql_close($conn);