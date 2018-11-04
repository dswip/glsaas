<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Api_lib extends Custom_Model {

    public function __construct($deleted=NULL)
    {
        $this->deleted = $deleted;
    }
    
    function response($data, $status = 200){ 
        $this->output
          ->set_status_header($status)
          ->set_content_type('application/json', 'utf-8')
          ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ))
          ->_display();
          exit;  
    }

}

/* End of file Property.php */