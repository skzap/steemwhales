<?php
//ini_set('display_errors', 'On');
//error_reporting(E_ALL);

// if (substr($_SERVER['HTTP_HOST'], 0, 4) === 'www.') {
//     header('Location: http'.(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on' ? 's':'').'://' . substr($_SERVER['HTTP_HOST'], 4).$_SERVER['REQUEST_URI']);
//     exit;
// }

include('functions.php');


$mysqli = new mysqli("127.0.0.1", "steemw", "", "steemw");

// loading globals
$sql = 'SELECT * FROM globals LIMIT 1';
$result = $mysqli->query($sql);
$globals = $result->fetch_object();
?>