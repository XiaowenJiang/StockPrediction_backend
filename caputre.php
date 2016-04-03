<?php
/**
 * Created by PhpStorm.
 * User: XiaowenJiang
 * Date: 4/12/15
 * Time: 20:19
 */
require_once('class.stockMarketAPI.php');
//ignore_user_abort();
set_time_limit(0);

$start = "04-01-2014";
$end = "04-30-2015";

$symbols = array('YHOO','GOOG','MSFT','EDU','AAPL','AMZN','TWTR','FB','INTC','IBM'); // you can change the data you want



while(1)
{
	sleep(30);
    for($i = 0; $i < 10; $i++)
    {
        $StockMarketAPI = new StockMarketAPI;
        $StockMarketAPI->symbol = $symbols[$i];
        // comment if you want historical data
//        $StockMarketAPI->history = array(
//            'start' 	 => $start,
//            'end' 	 => $end,
//            'interval' => 'd' // Daily
//        );
        $StockMarketAPI->getData();
    }
}
?>