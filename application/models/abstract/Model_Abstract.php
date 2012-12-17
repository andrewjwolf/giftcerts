<?php
//@todo abstraction of $app = & get_instance();
abstract class Model_Abstract extends CI_Model
{
    var $exists;

    function __construct()
    {
        parent::__construct();
        $this->exists = false;
    }

    function array_to_params($array)
    {
        foreach ($array as $k => $v) {
/*            if (is_array($v)) {
                $self->{$k} = array_to_object($v); //RECURSION
            } else {
                $this->{$k} = $v;
            }*/
            $this->{$k} = $v;
        }
    }

    /*
     * Perform a select based on an array of passed params and return the results
     */
    function find($params){
        //Select the data from the database based on those params to include auto increment data in pass back to model
        $query = $this->db->get_where($this->table, $params);
        return $query->result_array();
    }


    public function __call($name, $parameters)
    {
//_magic should equal get or set
//_aftermagic is the value to get or set
        $_magic = substr($name, 0, 3);
        $_aftermagic = substr($name, 3);


        //print_r ($this->$_aftermagic);


        switch ($_magic) {
            case "get":
                return $this->$_aftermagic;
                break;
            case "set":
                $this->$_aftermagic = $parameters[0];

                //$this->__set($_aftermagic,$parameters);
                break;
            default:
                echo "fail";
        }


        //substr( string $string , int $start [, int $length ] )


        // Note: value of $name is case sensitive.
        /*        echo "Calling object method '$name' "
    . implode(', ', $arguments). "\n";*/
    }


//load data to model
    function load()
    {
        //todo add ability to pass value
        $app = & get_instance();
        $this->db->select('*')->from($this->table)->where($this->index, $app->get($this->index));
        $query = $this->db->get();
        if (count($query->result()) > 0) {
            $data = $query->result();
            $this->array_to_params($data[0]);
            $this->exists = true;

            //return $this;
        }
    }

    /*
    * array $params
    * key value pairs that match with table.column_names
    * sets model params and saves to database
    */

    function make($params)
    {
//commit params to the database
        $this->db->insert($this->table, $params);
        //Select the data from the database based on those params to include auto increment data in pass back to model
        $query = $this->db->get_where($this->table, $params, 1);
        $result = $query->result();
        //Set that result to the current model
        $this->array_to_params($result[0]);
        //Set exists to true
        $this->exists = true;
        return $this;
    }

    //Save entity to database
    function save()
    {
        //get a list of the available fields from table
        $fields = $this->db->list_fields($this->table);
        //build the set array from the available object data cast against the fields that are available
        foreach ($fields as $k => $v){
            $set[$v] = $this->$v;
        }
        $this->db->where($this->index, $this->{$this->index});
        $this->db->update($this->table, $set);
    }


}