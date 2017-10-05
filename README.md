# ubdb
JSON based database for PHP

## Install ##
Only include ubdb.php to your codes.

## USAGE ##

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
                
                
 // For any simple query for getting datas
 
 // When runnig below code, will be same all getting datas 
 
 $get1 = $ubdb->get("users", "username", array("ugur",'%','1') );
 
 $get2 = $ubdb->get("users", "username", array("ug",'%','1') );
 
 // Joining two data array on one intersect point
 
 $results = $ubdb->join(

		            $get1,       // first array
 
                        $get2,       // second array
												
                        "username",  // first intersect point
												
                        "userid"     // secondary intersect point
												
                        );
 
 
