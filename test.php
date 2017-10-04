<?php

include_once 'ubdb.php';
include_once 'inc.php';

    for($t=1;$t<100;$t++){
    $gArmor  = rand(5, 30);
    $gHealth = rand(900,2000);
    $gResist = rand(5,35);
    $gPower  = rand(36,40);
    $gMagic  = rand(30,90);
    $gOwner  = rand(1,1000);
    
    $ubdb->define('username','uğ'.$gArmor);
    $ubdb->define('password',$gHealth);
    
    $ubdb->add('users');
    
    $ubdb->define('userid','uğ'.$gResist);
    $ubdb->add('cart');
    }
    
$ubdb->save();
    
$get1 = $ubdb->get("users","username","uğ%");
$get2 = $ubdb->get("cart","userid","uğ%");

$results = $ubdb->join($get1,$get2,"username","userid");

print_r($results);
