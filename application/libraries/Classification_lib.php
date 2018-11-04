<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Classification_lib extends Custom_Model {

    public function __construct($deleted=NULL)
    {
        $this->deleted = $deleted;
        $this->tableName = 'classifications';
    }

    function combo()
    {
        $this->db->select('id, no, name');
        $this->db->where('deleted', $this->deleted);
        $val = $this->db->get($this->tableName)->result();
        foreach($val as $row){$data['options'][$row->id] = $row->name;}
        return $data;
    }

    function combo_all()
    {
        $this->db->select('id, no, name');
        $this->db->where('deleted', $this->deleted);
        $val = $this->db->get($this->tableName)->result();
        $data['options'][''] = '-- All --';
        foreach($val as $row){$data['options'][$row->id] = $row->name;}
        return $data;
    }

    function get_no($id=null)
    {
        $this->db->select('no');
        $this->db->from($this->tableName);
        $this->db->where('id', $id);
        $res = $this->db->get()->row();
        return $res->no;
    }
    
    function get_name($id=null)
    {
        $this->db->select('name');
        $this->db->from($this->tableName);
        $this->db->where('id', $id);
        $res = $this->db->get()->row();
        return $res->name;
    }
	
    function get_type($id=null)
    {
        if ($id)
        {
        $this->db->select('type');
        $this->db->from($this->tableName);
        $this->db->where('id', $id);
        $res = $this->db->get()->row();
        return $res->type;
        }
    }


}

/* End of file Property.php */
