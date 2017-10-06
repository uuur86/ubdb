# UBDB #
JSON based database for PHP

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
// Define a data for adding database
$ubdb->define('userid','ugur');
// Table name to add
$ubdb->add('cart');
// Required for writing data
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
