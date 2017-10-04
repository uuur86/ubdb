<?php


$ubdb = new ubdb();
//$ubdb->dir('datas/'); Default db/
$ubdb->lang('en');
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

