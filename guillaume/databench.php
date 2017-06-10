<?php
$GLOBALS['plot_db'] = db_connect();

$feed = isset($_REQUEST['feed']);
$dump = isset($_REQUEST['dump']);

$offset = $_REQUEST['offset'];
$limit = $_REQUEST['limit'];

if ($feed == true) {
  	$json = array();
  	
  	$object = json_decode(file_get_contents('php://input'));
  	
  	$humidity = $object->{'humidity'};
  	$soundVolume = $object->{'soundVolume'};
	$peopleCount = $object->{'peopleCount'};
	$windSpeed = $object->{'windSpeed'};
	$temperature = $object->{'temperature'};
	$raining = $object->{'raining'};
	$place = $object->{'place'};// masque binaire
	
  	$request = "INSERT INTO benchData ('humidity', 'soundVolume', 'peopleCount', 'windSpeed', 'temperature','raining','place') VALUES ($humidity, $soundVolume, $peopleCount, $windSpeed, $temperature,$raining,$place);";
  	error_log($request);

  	if ($GLOBALS['plot_db']->exec($request)) {
    	$json['error'] = "ok";
  	} else {
      	$json['error'] = "error";
  	}

  	die(json_encode($json));
}

if ($dump == true) {
	
	// construction de la requette sql
	$request = "SELECT * FROM benchData";
	
	if ($limit != null) {
		$request .= " LIMIT ".$limit;
		
		if ($offset != null) {
			$request .= " OFFSET ".$offset;
		}
	}
	
  	$results = $GLOBALS['plot_db']->query($request);
	$dataBench = array();

 	while($res = $results->fetchArray(SQLITE3_ASSOC)){
		$datas = array();

		$datas['id'] = 				$res['id'];
		$datas['humidity'] =		$res['humidity'];
		$datas['peopleCount'] =		$res['peopleCount'];
		$datas['windSpeed'] =		$res['windSpeed'];
		$datas['temperature'] =		$res['temperature'];
		$datas['raining'] =		$res['raining'];
		$datas['place'] =		$res['place'];
		$datas['date'] =		$res['date'];
		
		$dataBench["data"][] = $datas;
	}
  	$dataBench['error'] = "ok";
	$GLOBALS['plot_db']->close();
	die(json_encode($dataBench));
}

function db_connect() {

  class DB extends SQLite3 {
    function __construct( $file ) {
      $this->open( $file,SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE);
    }
  }

  $adb = new DB('databench.db');
  if ($adb->lastErrorMsg() != 'not an error') {
    error_log("Database Error: " . $adb->lastErrorMsg()."\n",3);
  }
  return $adb;
}

?>