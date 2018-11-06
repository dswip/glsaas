<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Account_lib extends Custom_Model {

    public function __construct($deleted=NULL)
    {
        $this->deleted = $deleted;
        $this->tableName = 'accounts';
    }
    
    protected $field = array('id', 'member_id', 'classification_id', 'currency', 'code', 'name', 'alias', 'acc_no', 'bank', 
                             'city', 'phone', 'zip', 'contact', 'fax', 'balance_phone', 
                             'status', 'default', 'bank_stts', 'created', 'updated', 'deleted');
    
    function valid_coa($coa){
        
        $this->db->where('code', $coa);
        $val = $this->db->get($this->tableName)->num_rows();
        if ($val > 0){ return TRUE; }else{ return FALSE; }
    }
    
    function get()
    {
       $this->db->select($this->field); 
       $this->db->where('deleted', $this->deleted);
       return $this->db->get($this->tableName)->result();
    }

    function get_id($name=null)
    {
        if ($name)
        {
            $this->db->select('id,name');
            $this->db->where('name', $name);
            $val = $this->db->get($this->tableName)->row();
            return $val->id;
        }
    }

    function get_id_code($code=null)
    {
        if ($code)
        {
            $this->db->select('id,name,code');
            $this->db->where('code', $code);
            $val = $this->db->get($this->tableName)->row();
            if ($val){ return $val->id; }
        }
    }
	
    function get_code($id=null)
    {
        if ($id)
        {
            $this->db->select('id,name,code');
            $this->db->where('id', $id);
            $val = $this->db->get($this->tableName)->row();
            if ($val){ return $val->code; }
        }
    }

    function get_name($id=null)
    {
        $this->db->select('id,name');
        $this->db->where('id', $id);
        $val = $this->db->get($this->tableName)->row();
        if ($val){ return $val->name; }
    }
    
    function get_member($id=null)
    {
        $this->db->select('member_id');
        $this->db->where('id', $id);
        $val = $this->db->get($this->tableName)->row();
        if ($val){ return $val->member_id; }
    }
	
    function get_cur($id=null)
    {
        $this->db->select('id,name,currency');
        $this->db->where('id', $id);
        $val = $this->db->get($this->tableName)->row();
        return $val->currency;
    }

    function get_classi($id=null)
    {
        $this->db->select('classification_id');
        $this->db->where('id', $id);
        $val = $this->db->get($this->tableName)->row();
        return intval($val->classification_id);
    }
    
    // cek validation
    function cek_classi($id=null)
    {
        $this->db->where('classification_id', $id);
        $val = $this->db->get($this->tableName)->num_rows();
        if ($val > 0){ return FALSE;}else{ return TRUE; }
    }

    function combo()
    {
        $this->db->select('id, name, code');
        $this->db->where('status', 1);
        $val = $this->db->get($this->tableName)->result();
        foreach($val as $row){$data['options'][$row->code] = $row->name;}
        return $data;
    }
    
    function combo_based_classi($cla)
    {
        $this->db->select('id, name, code');
        $this->db->where('classification_id', $cla);
        $this->db->where('status', 1);
        $val = $this->db->get($this->tableName)->result();
        foreach($val as $row){$data['options'][$row->id] = $row->code.' : '.$row->name;}
        return $data;
    }
    
    function combo_asset()
    {
        $val = array('7', '8');
        $this->db->select('id, name, code');
//        $this->db->where_in('classification_id', $val);
        $this->db->where('status', 1);
        $this->db->where('bank_stts', 1);
        $val = $this->db->get($this->tableName)->result();
        foreach($val as $row){$data['options'][$row->id] = $row->code.' : '.$row->name;}
        return $data;
    }

    function combo_all()
    {
        $this->db->select('id, name, code');
        $this->db->where('status', 1);
        $val = $this->db->get($this->tableName)->result();
        $data['options'][''] = '-- All --';
        foreach($val as $row){$data['options'][$row->code] = $row->name;}
        return $data;
    }


}

/* End of file Property.php */