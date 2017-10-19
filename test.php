<?php

include_once 'class.php';
include_once 'inc.php';

// If you want to add random lines
// remove to below comment markers.

/*

    for($t=1;$t<1000;$t++){
    $gId   = rand(1, 2000);
    $gNo   = rand(1, 2000);
    $gPass = rand(51234,99999935);
    
    $ubdb->add( 'users', array(
                         'username' => 'ugur'.$gNo,
                         'password' => $gPass
                         );
    );
               
    $ubdb->add( 'cart', array('userid'=>'ugur'.$gId) );
    }
    */

$ubdb->save();
    
$get1 = $ubdb->get("users", "username", array("ugur",'%','1'));
$get2 = $ubdb->get("cart", "userid", array("ug",'%','1'));

$results = $ubdb->join($get1, $get2, "username", "userid");


print_r($results);
