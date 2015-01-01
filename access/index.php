<?php

$passedPassID = $_GET['passID'];
$passedPassI = $_GET['i'];

$fileName = $passedPassID . '.pkpass';
$fullPassPath = '../process/_passes/' . $passedPassID . '/' . $passedPassI . '/' . $passedPassID . '.pkpass';

header('Pragma: no-cache');
header('Content-type: application/vnd.apple.pkpass');
// header('Content-length: '.filesize($paths['pkpass']));
header('Content-Disposition: attachment; filename="'.$fileName.'"');
echo file_get_contents($fullPassPath);
echo 'here';
?>