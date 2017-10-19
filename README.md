# UBDB #
JSON based database for PHP v0.328

## REQUIREMENTS ##

PHP 4.6+

## INSTALL ##

Only include ubdb.php to your codes.

## USAGE ##
```php
$ubdb = new ubdb();
// For Selecting a language
$ubdb->lang('en');
// For Define db, table and columns
$ubdb->db('mydb', array(
                        'users'=>array(
                            'username',
                            'password',
                            'email',
                            'phone'
                        ),

                        'products'=>array(
                            'productname',
                            'productcode',
                            'title',
                            'description'
                        ),
                        'cart'=>array(
                            'productid',
                            'userid'
                        )   
                    )
                );
```
 ## ADD ROW ##
```php

// add a row
$ubdb->add('cart', array('userid'=>'ugur'.$i));

// or

	// You can add multiple rows
	for($i=0;$i<10;$i++){
	$ubdb->add('cart', array('userid'=>'ugur'.$i));
	}
	
// If you defined all rows, you can save for writing new datas.
$ubdb->save();
```
 ## GET ROW ##  
```php      
 // For any simple query for getting datas
 // When runnig below code, will be same all getting datas
 // ? Symbol holding only one character, % symbol holding multiple character as place.
 $get1 = $ubdb->get("users", "username", array("ugur",'?','1') ); // matches such as ugur11
 $get2 = $ubdb->get("users", "username", array("ug",'%','1') );   // matches such as ugur11 or ugor21
 
 // Joining two data array on one intersect point
 $results = $ubdb->join(
			$get1,       // first array
                        $get2,       // second array
                        "username",  // first intersect point
                        "userid"     // secondary intersect point
                        );
 // Will return as an array which is username equals to userid
 ```
## DELETING ##
```php
$ubdb->delete($table,$id);
```
