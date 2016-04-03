<?php
// written by: Xiaowen Jiang
// debugged by: Xiaowen Jiang


require_once('try.php');

$host = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "stock";

$symbols = array('YHOO','GOOG','MSFT','EDU','AAPL','AMZN','TWTR','FB','INTC','IBM');


//give suggestion based on weighted indicator result
function suggest($company)
{
    $suggestion=array();
    array_push($suggestion," ");
    array_push($suggestion," ");
    $close=retreive("close",$company);
    $high=retreive("high",$company);
    $low=retreive("low",$company);

    $sma_r=sma_judge($close)*0.2557;
    $macd_r=macd_judge($close)*0.2334;
    $roc_r=roc_judge($close)*0.24469;
    $so_r=so_judge($close,$high,$low)*0.122425;
    {
        $rsi_r=rsi_judge($close);
        $cc_r=cc_judge($close);
        if($rsi_r==1||$cc_r==1)
        {
            $suggestion[0]="oversold";
        }
        if($rsi_r==-1||$cc_r==-1)
        {
            $suggestion[0]="overbought";
        }
        else
        {
            $suggestion[0]="hold";
        }
    }
    $smacom_r=sma_combine($close)*0.14376646;
    $result=$sma_r+$macd_r+$roc_r+$so_r+$rsi_r+$cc_r+$smacom_r;
    echo $result."  ";
    if($result<0)
    {
        if($result<-0.25)
        {
            $suggestion[1] = "sell+";
        }
        else
            $suggestion[1] = "sell";
    }
    else
    {
        if($result>0.25)
        {
            $suggestion[1] = "buy+";
        }
        else
        {
            $suggestion[1]="buy";
        }
    }
    return $suggestion;
}

// Create connection
$conn = new mysqli($host, $username, $password, $database);
// Check connection
if ($conn->connect_error)
{
    die("Connection failed: " . $conn->connect_error);
}
for($i=0;$i<10;$i++)
{
    $temp = suggest($symbols[$i]);

    $sql = "UPDATE suggestion SET shortterm = '$temp[1]',longterm='$temp[0]' WHERE id=$i+1";
    mysqli_query($conn,$sql);
}
$conn->close();
