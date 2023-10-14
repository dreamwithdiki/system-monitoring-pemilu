<?php
if(!function_exists('get_year')){
	function get_year($pdate) {
        $date = DateTime::createFromFormat("Y-m-d", $pdate);
        return $date->format("Y");
    }
}

if(!function_exists('get_month')){
    function get_month($pdate) {
        $date = DateTime::createFromFormat("Y-m-d", $pdate);
        return $date->format("m");
    }
}

if(!function_exists('get_month_int')){
    function get_month_int($pdate) {
        $date = DateTime::createFromFormat("Y-m-d", $pdate);
        return $date->format("n");
    }
}

if(!function_exists('get_day')){
    function get_day($pdate) {
        $date = DateTime::createFromFormat("Y-m-d", $pdate);
        return $date->format("d");
    }
}

if(!function_exists('date_range')){
	function date_range($first, $last, $step = '+1 day', $format = 'Y-m-d' ) {
		$dates = array();
		$current = strtotime($first);
		$last = strtotime($last);

		while( $current <= $last ) {    
			$dates[] = date($format, $current);
			$current = strtotime($step, $current);
		}
		return $dates;
	}
}

if ( ! function_exists('format_short_date')){
	function format_short_date($date){
		if(!empty($date)){
			list($thn,$bln,$tgl)=explode('-',$date);
			return $tgl.'.'.$bln.'.'.$thn;
		}else{
			return "-";
		}
	}
}

if ( ! function_exists('format_short_local_date')){
	function format_short_local_date($date){
		if(!empty($date)){
			$namabulan = array("","Jan","Feb","Mar","Apr","Mei","Jun","Jul","Agt","Sep","Okt","Nov","Des");
			list($thn,$bln,$tgl)=explode('-',$date);
			return $tgl.' '.$namabulan[(int)$bln].' '.$thn;
		}else{
			return "-";
		}
	}
}

if( ! function_exists('get_hours_range')) {
	function get_hours_range( $start = 0, $end = 86400, $step = 3600, $format = 'g:i a' ) {
        $times = array();
        foreach ( range( $start, $end, $step ) as $timestamp ) {
                $hour_mins = gmdate( 'H:i', $timestamp );
                if ( ! empty( $format ) )
                        $times[$hour_mins] = gmdate( $format, $timestamp );
                else $times[$hour_mins] = $hour_mins;
        }
        return $times;
	}
}

if ( ! function_exists('get_days_diff')){
	function get_days_diff($startdate,$enddate){
	    return round(abs(strtotime($startdate)-strtotime($enddate))/86400);
	}
}

if( ! function_exists('month_name')){
	function month_name($number=""){
		$array_month = array(
				1=>"January",
				"February",
				"March",
				"April",
				"May", 
				"June",
				"July",
				"August",
				"September",
				"October",
				"November",
				"December");
		if(!empty($number)){
			$month = $array_month[$number];
			return $month;
		}else{
			return $array_month;
		} 
	}
}

if( ! function_exists('month_short_name')){
	function month_short_name($number=""){
		$array_month = array(
				1=>'Jan',
				'Feb',
				'Mar',
				'Apr',
				'May', 
				'Jun',
				'Jul',
				'Augt',
				'Sept',
				'Oct',
				'Nov',
				'Dec');
		if(!empty($number)){
			$month = $array_month[$number];
			return $month;
		}else{
			return $array_month;
		} 
	}
}

if ( ! function_exists('format_day')) {
	function format_day($date,$number) {
		$array_day = array(
		1=>"Monday",
		"Tuesday",
		"Wednesday",
		"Thursday", 
		"Friday",
		"Saturday",
		"Sunday",
		);
		$day = date("N", strtotime(date("Y-m-d", strtotime($date)) . "+".$number." day"));
		$day = $array_day[$day];
		return $day;
	}
}

if ( ! function_exists('format_local_date')){
	function format_local_date($date) {
		if(!empty($date)){
			$namabulan = array("","Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember");
			list($thn,$bln,$tgl)=explode('-',$date);
			return $tgl.' '.$namabulan[(int)$bln].' '.$thn;
		}else{
			return "-";
		}
	}
}

if ( ! function_exists('format_local_period')) {
	function format_local_period($date) {
		if(!empty($date)){
			$namabulan = array("","Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember");
			list($thn,$bln,$tgl)=explode('-',$date);
			return $namabulan[(int)$bln].' '.$thn;
		}else{
			return "-";
		}
	}
}

if ( ! function_exists('format_time')) {
	function format_time($time) {
		if(!empty($time)){
			return  date("H:i", strtotime($time));
		}else{
			return "00:00";
		}	
	}
}

if ( ! function_exists('format_date_sql')){
	function format_date_sql($date) {
		if(!empty($date)){
			$arraytanggal 	= explode('/',trim($date));
			$var_tanggal 	= $arraytanggal[0];
			$var_bulan 		= $arraytanggal[1];
			$var_tahun 		= $arraytanggal[2];
			return  $var_tahun . '-' . $var_bulan . '-' . $var_tanggal;
		}else{
			return "0000-00-00";
		}
	}
}

if ( ! function_exists('format_money')) {
	function format_money($money) {
		$des_digit 		= 2;
		$des_sep 		= ',';
		$thousand_sep 	= '.';
		if(!empty($money)){
			return number_format($money,0,$des_sep,$thousand_sep);
		}else{
			return number_format(0,0,$des_sep,$thousand_sep);
		}
	}
}

if(! function_exists('clean_separator')) {
    function clean_separator($val="") {
		$new_val = ($val !="" ? $val : 0);
		$result = preg_replace('/[^0-9.-]/', '', $new_val);
		
		if($result !="")
		{
			return trim($result);
		}
		else
		{
			return 0;
		}	
    }
}

if(!function_exists('time_to_second')) {
	function time_to_second($time) {
		if(!empty($time)){
			$timeExploded = explode(':', $time);
			if (isset($timeExploded[2])) {
				return $timeExploded[0] * 3600 + $timeExploded[1] * 60 + $timeExploded[2];
			}
			return $timeExploded[0] * 3600 + $timeExploded[1] * 60;
		}else{
			return 0;
		}
	}
}

if(!function_exists('second_to_time')) {
	function second_to_time($seconds) {
		if($seconds > 0){
			$hours = floor($seconds/3600);
			$hours_ = ($seconds % 3600);
			$minutes = floor($hours_ / 60);
			$seconds = ($hours_ % 60);
	 
			if(strlen($hours) == 1) { $hours = "0".$hours; }
			if(strlen($minutes) == 1) { $minutes = "0".$minutes; }
			if(strlen($seconds) == 1) { $seconds = "0".$seconds; }
			return $hours.":".$minutes.":".$seconds;
		}else{
			return "00:00:00";
		}
	}
}
?>