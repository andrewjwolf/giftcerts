<?php defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH.'/libraries/REST_Controller.php';

class Cert extends REST_Controller
{


    /*
    * Activate Gift Certificate
    * @todo change to post
    * http://localhost/giftcerts/index.php/api/cert/activate/storekey/1/key/1URH3L0V442XLJ4/
    */
    function activate_get()
    {        if(!$this->get('storekey') || !$this->get('key'))
        {
            $this->response(array('error' => 'Store Key and Cert Key Required'), 200);
        }
        $this->load->model('certificate');
        $value = $this->certificate->activate($this);

        if($value)
        {
            $this->response($value, 200); // 200 being the HTTP response code
        }

        else
        {
            $this->response(array('error' => 'Value Could Not Be Established'), 404);
        }
    }

    /*
    * Activate Gift Certificate
    * @todo change to post
    * http://localhost/giftcerts/index.php/api/cert/spend/storekey/1/key/1URH3L0V442XLJ4/id/23
    */
    function spend_get()
    {        if(!$this->get('storekey') || !$this->get('key')||!$this->get('id')||!$this->get('amount'))
    {
        $this->response(array('error' => 'Store Key Cert and Amount Key Required'), 200);
    }
        $this->load->model('certificate');
        $value = $this->certificate->spend($this);

        if($value)
        {
            $this->response($value, 200); // 200 being the HTTP response code
        }

        else
        {
            $this->response(array('error' => 'Value Could Not Be Established'), 404);
        }
    }


/*
 * Create Gift Certificate
 * @todo change to post
 * http://localhost/giftcerts/index.php/api/cert/create/storekey/1/value/1/email/a.james.wolf@gmail.com
 */
    function create_get()
    {
        if(!$this->get('storekey') || !$this->get('value'))
        {
            $this->response(array('error' => 'Store Key and Cert Value Required'), 200);
        }
        $this->storekey = $this->get('storekey');

        $this->load->model('certificate');
        $value = $this->certificate->create($this);

        if($value)
        {
            $this->response($value, 200); // 200 being the HTTP response code
        }

        else
        {
            $this->response(array('error' => 'Value Could Not Be Established'), 404);
        }
    }












    function get_get()
    {

        if(!$this->get('id') || !$this->get('key'))
        {
            $this->response(array('error' => 'Key and Id are Required to get Value of Gift Certificate'), 200);
        }
        $this->load->model('certificate');
        $this->certificate->get();
        $value =$this->certificate;

        if($value)
        {
            $this->response($value, 200); // 200 being the HTTP response code
        }

        else
        {
            $this->response(array('error' => 'Value Could Not Be Established'), 404);
        }
    }











    function user_get()
    {
        if(!$this->get('id'))
        {
            $this->response(NULL, 400);
        }

        // $user = $this->some_model->getSomething( $this->get('id') );
        $users = array(
            1 => array('id' => 1, 'name' => 'Some Guy', 'email' => 'example1@example.com', 'fact' => 'Loves swimming'),
            2 => array('id' => 2, 'name' => 'Person Face', 'email' => 'example2@example.com', 'fact' => 'Has a huge face'),
            3 => array('id' => 3, 'name' => 'Scotty', 'email' => 'example3@example.com', 'fact' => 'Is a Scott!', array('hobbies' => array('fartings', 'bikes'))),
        );

        $user = @$users[$this->get('id')];

        if($user)
        {
            $this->response($user, 200); // 200 being the HTTP response code
        }

        else
        {
            $this->response(array('error' => 'User could not be found'), 404);
        }
    }

    function user_post()
    {
        //$this->some_model->updateUser( $this->get('id') );
        $message = array('id' => $this->get('id'), 'name' => $this->post('name'), 'email' => $this->post('email'), 'message' => 'ADDED!');

        $this->response($message, 200); // 200 being the HTTP response code
    }

    function value_delete()
    {
        //$this->some_model->deletesomething( $this->get('id') );
        $message = array('id' => $this->get('id'), 'message' => 'DELETED!');

        $this->response($message, 200); // 200 being the HTTP response code
    }

    function users_get()
    {
        //$users = $this->some_model->getSomething( $this->get('limit') );
        $users = array(
            array('id' => 1, 'name' => 'Some Guy', 'email' => 'example1@example.com'),
            array('id' => 2, 'name' => 'Person Face', 'email' => 'example2@example.com'),
            3 => array('id' => 3, 'name' => 'Scotty', 'email' => 'example3@example.com', 'fact' => array('hobbies' => array('fartings', 'bikes'))),
        );

        if($users)
        {
            $this->response($users, 200); // 200 being the HTTP response code
        }

        else
        {
            $this->response(array('error' => 'Couldn\'t find any users!'), 404);
        }
    }


    public function send_post()
    {
        var_dump($this->request->body);
    }


    public function send_put()
    {
        var_dump($this->put('foo'));
    }
}