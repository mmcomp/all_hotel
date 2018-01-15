<?php function __autoload($class_name){
		if(file_exists("../class/".$class_name.".php")){
			require_once("../class/".$class_name.".php");
		}else{
			die($class_name." is Undefined!!!");
		}
	}
	$is_qeshm = (strpos($_SERVER['SERVER_NAME'],'eqeshm')!==FALSE);
	$is_tourism = (strpos($_SERVER['SERVER_NAME'],'tourism')!==FALSE);
	include_once 'pdate.php';
	include_once 'jdf.php';
	include_once 'inc.php';	
	//include_once 'simplejson.php';
//         require("../class/nusoap.php");
	require_once ('../class/jpgraph-3.5.0b1/src/jpgraph.php');
	require_once ('../class/jpgraph-3.5.0b1/src/jpgraph_line.php');
	require_once ('../class/jpgraph-3.5.0b1/src/jpgraph_bar.php');
	date_default_timezone_set("Asia/Tehran");
	ini_set('session.gc_maxlifetime', 9999);
	ini_set('display_errors','off');
	session_set_cookie_params(0);
	$conf = new conf;
	?>