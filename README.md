# UBDB #

> [!WARNING]
> **ubdb is no longer maintained.** Use its successor,
> [FyndFast DB](https://github.com/uuur86/fyndfast-db), for new and existing projects.
>
> ubdb stores every value, passwords included, as readable JSON, has no locking (concurrent
> requests can overwrite each other's changes) and has known data-loss bugs: after `edit()`
> every later `add()` overwrites the edited row, and `delete()` damages the files it rewrites.
>
> FyndFast DB keeps ubdb's one-file-per-column design and adds:
>
> - binary storage with checksums
> - O(1) lookups by id and by indexed value
> - atomic transactions with crash recovery
> - encryption at rest and Argon2id password columns
> - tests and documentation
>
> It imports ubdb data directly:
>
> ```bash
> composer require uuur86/fyndfast-db
> vendor/bin/fyndfast import:ubdb db/ mydb en storage/app --legacy-id=ubdb_id
> ```
>
> See [Migration from ubdb](https://github.com/uuur86/fyndfast-db/wiki/Migration-from-ubdb).

JSON based database for PHP v0.328

## REQUIREMENTS ##

PHP >= 5.2.0

## INSTALL ##

```bash
composer require uuur86/ubdb
```

## USAGE ##
```php
$ubdb = new \Ubdb\Main();
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
