<?php
// written by: Xiaowen Jiang
// debugged by: Xiaowen Jiang
// Calculation of indicators

$symbols=array("AAPL","EDU","GOOG","MSFT","YHOO","FB","TWTR","AMZN","INTC","IBM");

$username = "root";
$password = "";
$host = "127.0.0.1";
$database="stock";

//retrieve historical close price and return as an array
function retreive($price,$company)
{
    $username = "root";
    $password = "";
    $host = "127.0.0.1";
    $conn = mysql_connect($host, $username, $password);
    if(! $conn )
    {
        die('Could not connect: ' . mysql_error());
    }
    $temp=$company.'_h';

    $sql = "SELECT close,high,low FROM $temp";

    mysql_select_db('stock');
    $retval = mysql_query( $sql, $conn );
    if(! $retval )
    {
        die('Could not get data: ' . mysql_error());
    }
    $queryp=array();
    while($row = mysql_fetch_array($retval, MYSQL_ASSOC))
    {
        array_push($queryp,$row[$price]);
    }
    mysql_close($conn);
    return $queryp;
}


//calculate simple moving average of interval n, return a sma array
function sma($n,$close)
{
    $number=count($close);
    $sma = array();
    for ($i = 0; $i < $number; $i++) {
        if ($i < $n - 1) {
            array_push($sma, 0);
        } else {
            $temp = 0;
            for ($j = $i; $j >= $i - $n + 1; $j--) {
                $temp += $close[$j];
            }
            $temp /= $n;
            array_push($sma, $temp);
        }
    }
    return $sma;
}

//exponential moving average
function ema($n,$close)
{
    $mul=2/($n+1);
    $ema=array();
    for($i=0;$i<count($close);$i++)
    {
        if($i==0)
        {
            array_push($ema,$close[$i]);
        }
        else
        {
            array_push($ema,(($close[$i]-$ema[$i-1])*$mul+$ema[$i-1]));
        }
    }
    return $ema;
}

//calculate weighted moving average of interval n
    function wma($n,$close)
    {
    $number=count($close);
    $wma=array();
        for ($i = 0; $i < $number; $i++) {
            if ($i < $n - 1) {
                array_push($wma, 0);
            } else {
                $temp1 = 0;
                for ($j = $i; $j >= $i - $n + 1; $j--) {
                    $temp1 += $close[$j]*($j-($i-$n));
                }
                $temp2=(1+$n)*$n/2;
                $temp1 /= $temp2;
                array_push($wma, $temp1);
            }
        }
        return $wma;
    }

//calculate rate of change of interval n
function roc($n,$close)
{
    $number=count($close);
    $roc=array();
    for($i=0;$i<$number;$i++)
    {
        if($i<$n)
            array_push($roc,0);
        else if($close[$i-$n]!=0)
        {
            array_push($roc,($close[$i]-$close[$i-$n])/$close[$i-$n]*100);
        }
    }
    return $roc;
}

//stochastic oscillator
function stochastic_o($close,$high,$low)
{
    $so=array();
    for($i=0;$i<13;$i++)
    {
        array_push($so,0);
    }
    for($i=13;$i<count($high);$i++)
    {
        $temphigh=array();
        $templow=array();
        for($j=$i;$j>$i-14;$j--)
        {
            array_push($temphigh,$high[$j]);
            array_push($templow,$low[$j]);
        }
        array_push($so,($close[$i]-min($templow))/(max($temphigh)-min($templow))*100);
    }
    return $so;
}


//coppock curve
function coppock_curve($close)
{
    $cc=array();
    //weekly close price
    $wclose=array();
    for($i=0;$i<count($close);$i++)
    {
        if($i%5==0)
            array_push($wclose,$close[$i]);
    }
    $roc11=roc(11,$wclose);
    $roc14=roc(14,$wclose);
    $tempa=array();
    {
        for ($i = 13; $i < count($roc14); $i++)
            array_push($tempa, ($roc11[$i] + $roc14[$i]));
    }
    $tempwma=wma(10,$tempa);

    for($i=0;$i<22;$i++)
    {
        array_push($cc,0);
    }
    for($i=22;$i<count($wclose);$i++)
    {
        array_push($cc,$tempwma[$i-22]);
    }
    return $cc;
}

//relative strength index
function rsi($close)
{
    $gain=array();
    $lose=array();
    for($i=0;$i<count($close);$i++)
    {
        if($i==0)
        {
            array_push($gain,0);
            array_push($lose,0);
        }
        else
        {
            if($close[$i]>$close[$i-1])
            {
                array_push($gain,$close[$i]-$close[$i-1]);
                array_push($lose,0);
            }
            else
            {
                array_push($lose,$close[$i-1]-$close[$i]);
                array_push($gain,0);
            }
        }
    }

    $avegain=array();
    $avelose=array();
    $tempgain=array();
    $templose=array();
    $rs=array();
    $rsi=array();
    for($j=1;$j<=14;$j++)
    {
        array_push($tempgain,$gain[$j]);
        array_push($templose,$lose[$j]);
    }

    for($i=0;$i<count($close);$i++)
    {
        if($i<14)
        {
            array_push($avegain,0);
            array_push($avelose,0);
            array_push($rs,0);
            array_push($rsi,0);
        }
        else
        {
            if ($i == 14)
            {
                array_push($avegain, array_sum($tempgain) / 14);
                array_push($avelose, array_sum($templose) / 14);
            }
            else
            {
                array_push($avegain, ($avegain[$i - 1] * 13 + $gain[$i]) / 14);
                array_push($avelose, ($avelose[$i - 1] * 13 + $lose[$i]) / 14);
            }
            array_push($rs,$avegain[$i]/$avelose[$i]);
            array_push($rsi,(100-100/(1+$rs[$i])));

        }

    }
    return $rsi;
}

function macd($close)
{
    $macd=array();
    $ema12=ema(12,$close);
    $ema26=ema(26,$close);
    for($i=0;$i<count($close);$i++)
    {
        array_push($macd,$ema12[$i]-$ema26[$i]);
    }
    return $macd;
}



function updatetable($company)
{
    $username = "root";
    $password = "";
    $host = "127.0.0.1";
    $database="stock";

    $high="high";
    $close="close";
    $low="low";

    $close=retreive($close,$company);
    $high=retreive($high,$company);
    $low=retreive($low,$company);

    $so=stochastic_o($close,$high,$low);
    $rsi=rsi($close);
    $roc=roc(14,$close);
    $cc=coppock_curve($close);
    $macd=macd($close);
    $sma=sma(20,$close);
    $wma=wma(20,$close);


// Create connection
    $conn = new mysqli($host, $username, $password, $database);
// Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $k=0;
    for($i=1;$i<count($macd)+1;$i++)
    {

        $j=$i-1;
        $sql = "UPDATE $company SET SMA=$sma[$j],WMA=$wma[$j],ROC=$roc[$j],RSI=$rsi[$j],MACD=$macd[$j],SO=$so[$j] WHERE id=$i";
        mysqli_query($conn,$sql);
        if($j%5==0)
        {
            $sql = "UPDATE $company SET CC=$cc[$k] WHERE id=$i";
            mysqli_query($conn,$sql);
            $k++;
        }
    }
    $conn->close();
}

/* functions to make suggestion */
function sma_judge($close)
{
    $sma=sma(20,$close);

    $number=count($sma);

    if($close[$number-1]>$sma[$number-1])
    {
        return -1;
    }
    else return 1;
}

function macd_judge($close)
{
    $macd=macd($close);
    $number=count($macd);
    if($macd[$number-1]>0)
        return 1;
    else if($macd[$number-1]<0)
        return -1;
    else return 0;
}

function roc_judge($close)
{
    $roc=roc(12,$close);
    $number=count($roc);
    if($roc[$number-1]>0)
        return 1;
    else if($roc[$number-1]<0)
        return -1;
    else return 0;
}

function so_judge($close,$high,$low)
{
    $so=stochastic_o($close,$high,$low);
    $number=count($so);
    if($so[$number-1]<20)
        return 1; //over sold
    else if($so[$number-1]>80)
        return -1; //over bought
    else return 0;
}

function rsi_judge($close)
{
    $rsi=rsi($close);
    $number=count($rsi);
    if($rsi[$number-1]<30)
        return 1; //over sold
    else if($rsi[$number-1]>70)
        return -1; //over bought
    else return 0;
}

function cc_judge($close)
{
    $cc=coppock_curve($close);
    $number=count($cc);
    if($cc[$number-1]>0&&$cc[$number-2]<0)
        return 1;
    else if($cc[$number-1]<0&&$cc[$number-2]>0)
        return -1;
    else return 0;
}


//compare 200day sma to 20day sma, find if two lines cross on the last day and judge
function sma_combine($close)
{
    $number=count($close);
    $sma5=sma(5,$close);
    $sma10=sma(10,$close);
    $sma30=sma(30,$close);
    //golden cross
    if(($sma5[$number-1]>$sma10[$number-1]&&$sma5[$number-2]<$sma10[$number-2])||($sma10[$number-1]>$sma30[$number-1]&&$sma10[$number-2]<$sma30[$number-2]))
    {
        return 1; //buy
    }
    //dead cross
    else if(($sma5[$number-1]<$sma10[$number-1]&&$sma5[$number-2]>$sma10[$number-2])||($sma10[$number-1]<$sma30[$number-1]&&$sma10[$number-2]>$sma30[$number-2]))
    {
        return -1;//sell
    }
    else return 0; //hold
}


//function to calculate indicator weight
//function cal_weight($symbols)
//{
//    $sma_weight = 0;
//    $macd_weight = 0;
//    $so_weight = 0;
//    $roc_weight = 0;
//    $smaco_weight=0;
//    $rsi_weight = 0;
//    $cc_weight = 0;
//    for ($m = 0; $m < count($symbols); $m++) {
//        $com = $symbols[$m];
//        $close = retreive("close", $com);
//        $high = retreive("high", $com);
//        $low = retreive("low", $com);
//
////record how many times these indicators have correct predictions
//        $smaj = 0;
//        $macdj = 0;
//        $rocj = 0;
//        $soj = 0;
//        $rsij = 0;
//        $ccj = 0;
//        $smaco=0;
//
//        $trend = 0;
//        $trend2 = 0;
////test the accuracy of indicators using historical data
//        for ($number = 60; $number <= 220; $number += 2) {
//
//            $futureave = 0;
//            $futureave2 = 0;
//            for ($i = 0; $i < 5; $i++) {
//                $futureave += $close[$number + $i];
//            }
//            $futureave /= 5;
//            for ($i = 0; $i < 15; $i++) {
//                $futureave2 += $close[$number + $i];
//            }
//            $futureave2 /= 15;
//            if ($futureave > $close[$number - 1]) {
//                $trend++;
//                if (sma_judge($close, $number) == 1) {
//                    $smaj++;
//                }
//                if (macd_judge($close, $number) == 1) {
//                    $macdj++;
//                }
//                if (roc_judge($close, $number) == 1) {
//                    $rocj++;
//                }
//                if (so_judge($close, $high, $low, $number) == 1) {
//                    $soj++;
//                }
//                if(sma_combine($close,$number)==1)
//                {
//                    $smaco++;
//                }
//
//            }
//
//            if (rsi_judge($close, $number) == 1) {
//                $rsij++;
//                if ($futureave2 > $close[$number - 1]) {
//                    $trend2++;
//                }
//            }
//            if ($futureave < $close[$number - 1]) {
//                $trend++;
//                if (sma_judge($close, $number) == -1) {
//                    $smaj++;
//                }
//                if (macd_judge($close, $number) == -1) {
//                    $macdj++;
//                }
//                if (roc_judge($close, $number) == -1) {
//                    $rocj++;
//                }
//                if (so_judge($close, $high, $low, $number) == -1) {
//                    $soj++;
//                }
//                if(sma_judge($close,$number)==-1)
//                {
//                    $smaco++;
//                }
//            }
//
//            if (rsi_judge($close, $number) == -1) {
//                $rsij++;
//                if ($futureave2 < $close[$number - 1]) {
//                    $trend2++;
//                }
//            }
//        }
//
////calculate coppock curve accuracy
//        $trend3 = 0;
//
//        for ($i = 0; $i < count($close) - 15; $i++) {
//
//            if ($i % 5 == 0 && $i >= 155) {
//                $futureave2 = 0;
//                for ($j = 0; $j < 15; $j++) {
//                    $futureave2 += $close[$i + $j];
//                }
//                $futureave2 /= 15;
//                if (cc_judge($close, $i / 5) == 1) {
//                    $trend3++;
//                    if ($futureave2 > $close[$i]) {
//                        $ccj++;
//                    }
//                }
//                if (cc_judge($close, $i / 5) == -1) {
//                    $trend3++;
//                    if ($futureave2 < $close[$i]) {
//                        $ccj++;
//                    }
//                }
//            }
//        }
//        //echo $smaj, " ", $macdj, " ", $rocj, " ", $soj, " ", $rsij, " ", $ccj;
//        $smaj /= $trend;
//        $macdj /= $trend;
//        $rocj /= $trend;
//        $soj /= $trend;
//        $smaco/=$trend;
//        $rsij /= $trend2;
//        $ccj /= $trend3;
//        $total_accuracy = $smaj + $macdj + $rocj + $soj + $smaco;
//        $sma_weight += ($smaj / $total_accuracy);
//        $macd_weight += ($macdj / $total_accuracy);
//        $roc_weight += ($rocj / $total_accuracy);
//        $so_weight += ($soj / $total_accuracy);
//        $smaco_weight+=($smaco/$total_accuracy);
//        //$rsi_weight += ($rsij / $total_accuracy);
//        //$cc_weight += ($ccj / $total_accuracy);
//    }
//    $sma_weight /= 10;
//    $macd_weight /= 10;
//    $roc_weight /= 10;
//    $so_weight /= 10;
//    //$rsi_weight /= 10;
//    //$cc_weight /= 10;
//    $smaco_weight/=10;
//    echo "\n", $sma_weight, " ", $macd_weight, " ", $roc_weight, " ", $so_weight, " ", $rsi_weight, " ", $cc_weight," ",$smaco_weight;
//}

//calculate indicator values and update table
for($i=0;$i<count($symbols);$i++)
{
    updatetable($symbols[$i]);
}


//cal_weight($symbols);















