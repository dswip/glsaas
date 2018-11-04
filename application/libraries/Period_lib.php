<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Period_lib extends Custom_Model {

    public function __construct($deleted=NULL)
    {
        $this->deleted = $deleted;
        $this->tableName = 'periods';
    }

    private $ci;

    protected $field = array('id', 'member_id', 'month', 'year', 'closing_month', 'start_month', 'start_year', 'status', 'created', 'updated', 'deleted');


    public function get($member,$type=null)
    {
       $this->db->select($this->field);
       $this->db->where('member_id', $member);
       $val = $this->db->get($this->tableName)->row();
       if ($type == 'month'){ return $val->month; }
       elseif ($type == 'year') { return $val->year; }
       else { return $val; }
    }
    
    function update_period($uid, $users)
    {
        $this->db->where('id', $uid);
        $this->db->update($this->tableName, $users);
        
        $val = array('updated' => date('Y-m-d H:i:s'));
        $this->db->where('id', $uid);
        $this->db->update($this->tableName, $val);
    }
}

/* End of file Property.php */