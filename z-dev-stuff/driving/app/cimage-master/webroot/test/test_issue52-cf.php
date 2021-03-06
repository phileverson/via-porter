<?php
// Include config for all testcases
include __DIR__ . "/config.php";



// The title of the test case
$title = "Testing issue 52 - Fill to fit fails with aspect ratio";



// Provide a short description of the testcase.
$description = "Verify that Fill To Fit resize strategy works with all variants of sizes.";



// Use these images in the test
$images = array(
    'car.png',
);



// For each image, apply these testcases
$nc = '&nc'; 
$testcase = array(
    $nc . '&w=300&h=300&crop-to-fit',
    $nc . '&w=300&ar=1&crop-to-fit',
    $nc . '&w=300&ar=3&crop-to-fit',
    $nc . '&h=300&ar=1&crop-to-fit',
    $nc . '&h=300&ar=3&crop-to-fit',
    $nc . '&w=50%&ar=1&crop-to-fit',
    $nc . '&w=50%&ar=3&crop-to-fit',
    $nc . '&h=50%&ar=1&crop-to-fit',
    $nc . '&h=50%&ar=3&crop-to-fit',
);



// Apply testcases and present results
include __DIR__ . "/template.php";
