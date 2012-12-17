<?php
require APPPATH . '/models/abstract/Model_Abstract.php';

class Certificate extends Model_Abstract
{

    public $table = "certificates";
    public $index = "id";

    /*
    * Activates a gift certificate
    */
    function activate(){
        $app = & get_instance();
        $app->store->authenticate();
        //Make sure that store is authenticated
        if (store::isAuthenticated() == true) {
            $find = array('key' => $app->get('key'));
            $found = $this->find($find);
            //todo add error if no certificate found
            $this->array_to_params($found[0]);
            $this->active = '1';
            $this->save();
            return $this;
        }
        //todo add error if store is not authenticated
    }
    /*
    * Create Gift Certificate
    */
    function create($app)
    {
        $app = & get_instance();
        $app->store->authenticate(); //todo add else instructions (store has not been authenticated)
        //Make sure that store is authenticated
        if (store::isAuthenticated() == true) {
            //Ensure that all required data is included in the request
            $errormsg = "";
            //Build Error Message Possibilities @todo build into configuration what fields are required and what uniquely identifies customer
/*            if ($app->get('fromemail')== "") {
                $errormsg .= "userid ";
            }*/

            if ($app->get('email')== "") {
                $errormsg .= "toemail ";
            }
            //if there are any errors
            if ($errormsg != "") {
                $this->error =  $errormsg . "are required";
                  }
                //if there are no errors
                else {
                //make sure the customer exists and is loaded to the model
                //@todo incorporate into configuration options
                $app->customer->load();

                //if customer exists
                if ($app->customer->exists){
                //todo do we want to do something if they already exist?

                }
                //if customer does not exist
                elseif (!$app->customer->exists){
                    $params = array (
                      'email' => $app->get('email')
                    );
                    $app->customer->make($params)->load();
                }
                  //Set params for SQL Query To create new certificate
                   $params = array(
                     'customer_id' => $app->customer->id,
                       'key' => $this->makeCertKey(),
                       'value' => "12",
                       'active' => "0" //todo offer configuration option to set default to active
                   );
                    //execute sql to create certificate
                    $this->make($params);

                    //append customer data to the return object
                    $return = array(
                        'id' => $this->id,
                        'key' => $this->key,
                        'value' => $this->value,
                        'active' => $this->active,
                        'customer_id' => $app->customer->id,
                        'from_email' => $app->customer->email,
                    );
                    return $return;
            }


        }
        //Check to make sure that the store key is registered in database
        //@todo validate against remote ip address
        // $this->db->select(*)->from('stores')->where('storekey',$app->storekey);
        // $query = $this->db->get();
    }
    /*
    * Returns data from a particular certificate
    */
    function get()
    {
        $app = & get_instance();
        if ($app->get('id') && $app->get('key')) {
            $this->db->select('*')->from($this->table)->where($this->index, $app->get($this->index));
            $query = $this->db->get();


            //@todo build logic to ensure only one result is returned
            $result = $query->result();


            if (empty($result)) {
                //If no results are found in database
                $error = array('error' => 'Gift Certificate Id or Key mismatch');
            } else {
                $data = $query->result();

                $this->array_to_params($data[0]);
            }
        } else {
            $this->error = "Key and Id are Required to get Gift Certificate";

        }
    }
    /*
    * Returns a random set of characters for certificate key values
    */
    function makeCertKey($length = 15){
        //todo make configurable
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }

        return strtoupper($randomString);
    }

    /*
    * Spend funds from a gift certificate
    */
    function spend(){
        $app = & get_instance();
        //check to ensure store authentication
        $app->store->authenticate();
        if (store::isAuthenticated() == true) {
            //load certificate todo add configuration option for how validation is conducted

            $find = array('key' => $app->get('key'),'id' => $app->get('id'));
            $found = $this->find($find);
            //if certificate could be loaded

            if(isset($found[0])){
                //Set the results to this object params
                $this->array_to_params($found[0]);

                //var_dump($found[0]['active']);
                //if certificate is active
                if($found[0]['active'] == 1){

                //if there are enough funds available
                if($this->value >= $app->get('amount')){
                    //todo add transactional history
                    $this->value -= $app->get('amount');
                    $this->transaction_status = 'success'; //todo set up as an object linked to transactional history model
                    $this->save();
                    return $this;
                }else{
                //if there are not enough funds available
                    $this->transaction_status = 'fail'; //todo set up as an object linked to transactional history model
                    $this->transaction_error = 'funds unavailable';
                    return $this;
                }


                }else{
                    //if the certificate is not active
                    $this->transaction_status = 'fail'; //todo set up as an object linked to transactional history model
                    $this->transaction_error = 'certificate not active'; //todo set up as an object linked to transactional history model
                    return $this;

                }


        }else{
            //if the certificate could not be found
        }
        }else{
        //if store is not authenticated

        }

    }

}