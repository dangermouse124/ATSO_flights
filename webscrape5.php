<?php
//echo $_POST["month"];
//echo $_POST["site"];

$month = $_POST["month"];
$chosenSite = $_POST["site"];

$flights = array();
$expected = array();
$stations = array();

$hedland = array(1,1,0,0,1,0,1,"94312");
$learmonth = array(2,2,2,2,2,2,2,"94302");
$cocos = array(1,1,1,1,1,1,1,"96996");
$meekatharra = array(0,1,1,1,0,1,0,"94430");
$kalgoorlie = array(1,0,1,1,0,1,0,"94637");

$stations["hedland"] = $hedland;
$stations["learmonth"] = $learmonth;
$stations["cocos"] = $cocos;
$stations["meekatharra"] = $meekatharra;
$stations["kalgoorlie"] = $kalgoorlie;


$date = "2018-" . $month . "-01";

$firstday = (int)date('w', strtotime($date));
$firstday--;
if ($firstday < 0) {
	$firstday = 6;
}

$days = cal_days_in_month(CAL_GREGORIAN, (int)$month, 2018);
for ($i = 1; $i < $days + 1; $i++) {
	$flights[$i] = 0;
	$expected[$i] = $stations[$chosenSite][$firstday];
	$firstday++;
	if ($firstday == 7) {
		$firstday = 0;
	}
}
$monthdays = array("flights" => $flights, "expected" => $expected);


$siteNum = $stations[$chosenSite][7];
$urlP1 = "http://weather.uwyo.edu/cgi-bin/sounding?region=pac&TYPE=TEXT%3ALIST&YEAR=";
$URL = $urlP1 . "2018&MONTH=" . $month . "&FROM=0100&TO=" . $days . "00&STNM=" . $siteNum;

$pagecontent = file_get_contents($URL);
$doc = new DOMDocument();
$doc->loadHTML($pagecontent);
$items = $doc->getElementsByTagName('h2');
if(count($items) > 0) {
    foreach ($items as $tag1) {
        $flight = $tag1->nodeValue;
		//echo $flight . "<br>";
		$pieces = explode(" ", $flight);
		//echo $pieces[count($pieces)-3] . "<br>";
		$monthdays["flights"][(int)$pieces[count($pieces)-3]] += 1; 
    }/*
	foreach($monthdays as $key => $value) {
		echo $key . ":" . $value . "<br>";
	}*/
}


$rows = array();
$table = array();

$table['cols'] = array(
	array('label' => 'Day', 'type' => 'string'),
	array('label' => 'Flights', 'type' => 'number'),
	array('label' => 'Expected', 'type' => 'number')
);

foreach ($monthdays["flights"] as $key => $value) {
	$sub_array = array();

	$sub_array[] =  array(
	  "v" => "Day " . $key 
	 );
	$sub_array[] =  array(
	  "v" => $value
	 );
	$sub_array[] =  array(
	  "v" => $monthdays["expected"][$key]
	 );
	$rows[] =  array(
	 "c" => $sub_array
	);
}
$table['rows'] = $rows;

/*
echo '<pre>';
echo json_encode($table, JSON_PRETTY_PRINT);
echo '</pre>';
*/

$jsonTable = json_encode($table);
echo $jsonTable;

?>