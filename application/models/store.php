<?php

class Store extends Model_Abstract
{
    private $isAuthenticated;

    function authenticate()
    {   $app = & get_instance();
        //@ setup logic to decide when to authenticate
        $this->isAuthenticated = true;
    }

    public static function isAuthenticated(){
        $app = & get_instance();
        if($app->store->isAuthenticated == true){
           return true;
        }else{
            return false;
        }



    }

}