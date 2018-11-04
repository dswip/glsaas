<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Currency_lib extends Custom_Model {

    public function __construct($deleted=NULL)
    {
        $this->deleted = $deleted;
        $this->tableName = 'currencies';
    }

    function combo()
    {
        $this->db->select('id, name, code');
        $this->db->where('deleted', $this->deleted);
        $val = $this->db->get($this->tableName)->result();
        foreach($val as $row){$data['options'][$row->name] = strtoupper($row->name);}
        return $data;
    }

    function combo_all()
    {
        $this->db->select('id, name, code');
        $this->db->where('deleted', $this->deleted);
        $val = $this->db->get($this->tableName)->result();
        $data['options'][''] = '-- All --';
        foreach($val as $row){$data['options'][$row->name] = strtoupper($row->name);}
        return $data;
    }

    function get_code($name=null)
    {
        $this->db->select('code');
        $this->db->from($this->tableName);
        $this->db->where('name', $name);
        $res = $this->db->get()->row();
        return strtoupper($res->code);
    }
    
    function cek($code=null)
    {
        $this->db->from($this->tableName);
        $this->db->where('code', $code);
        $num = $this->db->get()->num_rows();
        if ($num > 0){ return TRUE; }else { return FALSE; }
    }


}

/* End of file Property.php */