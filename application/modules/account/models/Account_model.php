<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Account_model extends Custom_Model
{
    protected $logs;
    
    function __construct()
    {
        parent::__construct();
        $this->logs = new Log_lib();
        $this->tableName = 'accounts';
    }
    
    protected $field = array('id', 'member_id', 'classification_id', 'currency', 'code', 'name', 'alias', 'acc_no', 'bank', 
                             'city', 'phone', 'zip', 'contact', 'fax', 'balance_phone', 
                             'status', 'default', 'bank_stts', 'created', 'updated', 'deleted');
    
    function get_last($limit, $offset=null)
    {
        $this->db->select($this->field);
        $this->db->from($this->tableName); 
        $this->db->where('deleted', $this->deleted);
        $this->db->order_by('id', 'desc'); 
        $this->db->limit($limit, $offset);
        return $this->db->get(); 
    }
    
    function get_list($clas=null)
    {
        $this->db->select($this->field);
        $this->db->from($this->tableName); 
        $this->db->where('deleted', $this->deleted);
        $this->cek_null($clas, 'classification_id');
        $this->db->order_by('code', 'asc'); 
        return $this->db->get(); 
    }
    
    function search($member=null,$clas=null,$publish=null)
    {   
        $this->db->select($this->field);
        $this->db->from($this->tableName); 
        $this->db->where('deleted', $this->deleted);
        $this->cek_null($member, 'member_id');
        $this->cek_null($clas, 'classification_id');
        $this->cek_null($publish, 'status');
        
        $this->db->order_by('id', 'asc'); 
        return $this->db->get(); 
    }
    
    function valid_default($uid)
    {
        $this->db->where('id', $uid);
        $query = $this->db->get($this->tableName)->row();
        if($query->default == 1){ return FALSE; }else{ return TRUE; }
    }
    
    function report($member=null,$cur=null,$stts=null,$cla=null)
    {
        $this->db->select('id, member_id, classification_id, currency, code, name, alias, status');
        $this->cek_null($member, 'member_id');
        $this->cek_null($cur, 'currency');
        $this->cek_null($stts, 'status');
        $this->cek_null($cla, 'classification_id');
        $this->db->order_by('code','asc');
        return $this->db->get('accounts');
    }

}

?>