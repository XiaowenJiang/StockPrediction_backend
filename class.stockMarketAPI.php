<?php
class StockMarketAPI
{
    public $symbol;
    public $history;

    public function __construct($symbol = '', $history = false)
    {
        if($symbol) $this->_setParam('symbol', $symbol);
        $this->_setParam('history', $history);
    }

    private function _setParam($param, $val)
    {

        switch($param)
        {
            case 'symbol':
                $this->symbol = $val;
                break;
            case 'history':
                $this->history = $val;
                break;
        }
    }

    private function _request()
    {
        if(!$this->history)
        {
            $file = 'http://download.finance.yahoo.com/d/quotes.csv?s='.$this->symbol.'&f=snd1t1l1v';
        }
        elseif(is_array($this->history))
        {

            $this->history['start'] = isset($this->history['start']) ? $this->history['start'] : "1-1-2014";
            $start = explode('-', $this->history['start']); // dd-mm-yyyy
            $a = $start[0] - 1; // Month
            $b = $start[1]; // Day
            $c = $start[2]; // Year

            $this->history['end'] = isset($this->history['end']) ? $this->history['end'] : "12-31-2014";
            $end = explode('-', $this->history['end']); // dd-mm-yyyy
            $d = $end[0] - 1; // Month
            $e = $end[1]; // Day
            $f = $end[2]; // Year

            $g = isset($this->history['interval']) ? $this->history['interval'] : 'd'; // d = Daily, w = Weekly, m = Monthly

            $file = 'http://ichart.yahoo.com/table.csv?s='.$this->symbol.'&a='.$a.'&b='.$b.'&c='.$c.'&d='.$d.'&e='.$e.'&f='.$f.'&g='.$g;
        }

        while(($handle = fopen($file, "r"))== false);
        $username = "root";
        $password = "";
        $host = "127.0.0.1";
        $database="stock";
        $conn = mysqli_connect($host,$username, $password);
        mysqli_select_db($conn,"stock");

        if(!$this->history)
        {
            while (($data = fgetcsv($handle)) !== FALSE)
            {
                //echo $data[3]." ";
//				print_r($data);
//				echo'<br>';
//				$return[] = $data;
                echo $data[3]." ";
                $temp=explode(":",$data[3]);
                if((int)$temp[0]<=4)
                {
                    $temp[0]+=12;
                }
                $temp2=str_split($temp[1],2);
                $temp3="$temp[0]:$temp2[0]";
                $temptime=strtotime($temp3);
                $time24=date('H:i:s',$temptime);
                echo $time24."\n";
                $a = $this->symbol.'_r';
                mysqli_query($conn,"INSERT into $a (stock, company, date, time, price, volume) values('$data[0]','$data[1]','$data[2]','$time24','$data[4]','$data[5]')");
            } //Loop through and store each item in an indice
        }
        elseif(is_array($this->history))
        {
            $return = array();
            $row = 0;


            while (($data = fgetcsv($handle)) !== FALSE)
            {
//				print_r($data);
//				echo'<br>';
                //echo $data[5];
                $a = $this->symbol.'_h';
                if($data[5]!=0)
                {
                    mysqli_query($conn,"INSERT into $a (date, open, high, low, close, volume, adj_close) values('$data[0]','$data[1]','$data[2]','$data[3]','$data[4]','$data[5]','$data[6]')");
                }
                $row++;

            } //end while

        }

        fclose($handle);
        return 0;
    }

    public function getData($symbol='')
    {

        if($symbol)
            $this->_setParam('symbol', $symbol);

        $data = $this->_request();
        return 0;
    }

}


