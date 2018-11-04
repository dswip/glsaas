<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Balance_account_lib extends Custom_Model {

    public function __construct($deleted=NULL)
    {
        $this->deleted = $deleted;
        $this->tableName = 'balances';
    }
   
    protected $field = array('id', 'member_id', 'currency', 'account_id', 'beginning', 'end', 'vamount', 'budget', 'month', 'year');
            
    function create($member,$acc,$month=0,$year=0,$begin=0,$end=0)
    {
       $this->db->select($this->field); 
       $this->db->where('member_id',$member);
       $this->db->where('account_id',$acc);
       $this->db->where('month',$month);
       $this->db->where('year',$year);
       $query = $this->db->get($this->tableName)->num_rows();
//       echo $acc.' : '.$month.'-'.$year.' -- '.$query.'<br>';
       
       if ($query == 0){ $this->fill($member, $acc, $month, $year, $begin, $end); }
       else{ $this->edit($member, $acc, $month, $year, $begin, $end); }
    }
    
    private function edit($member,$acc,$month=0,$year=0,$begin=0,$end=0)
    {
       $trans = array('beginning' => $begin, 'end' => $end);
       $this->db->where('member_id', $member);
       $this->db->where('account_id', $acc);
       $this->db->where('month', $month);
       $this->db->where('year', $year);
       $this->db->update($this->tableName, $trans); 
    }
    
    function fill($member,$acc,$month,$year,$begin=0,$end=0)
    {
       $this->db->where('member_id', $member);
       $this->db->where('account_id',$acc);
       $this->db->where('month',$month);
       $this->db->where('year',$year);
       $num = $this->db->get($this->tableName)->num_rows();
       
       if ($num == 0)
       {
          $trans = array('member_id' => $member, 'account_id' => $acc, 'month' => $month, 'year' => $year, 'beginning' => $begin, 'end' => $end);
          $this->db->insert($this->tableName, $trans); 
       }
    }
    
    /// ========================= vamount ======================================
    
    function create_vamount($member,$acc,$month=0,$year=0,$amt=0)
    {
       $this->db->where('member_id',$member);
       $this->db->where('account_id',$acc);
       $this->db->where('month',$month);
       $this->db->where('year',$year);
       $query = $this->db->get($this->tableName)->num_rows();
//       echo $acc.' : '.$month.'-'.$year.' -- '.$query.'<br>';
       
       if ($query == 0)
       {
         $this->fill_vamount($member, $acc, $month, $year, $amt);
       }
       else
       {
         $this->edit_vamount($member, $acc, $month, $year, $amt);
       }
    }
    
    private function edit_vamount($member,$acc,$month=0,$year=0,$amt=0)
    {
       $trans = array('vamount' => $amt);
       $this->db->where('member_id', $member);
       $this->db->where('account_id', $acc);
       $this->db->where('month', $month);
       $this->db->where('year', $year);
       $this->db->update($this->tableName, $trans); 
    }
    
    function fill_vamount($member,$acc,$month,$year,$amt=0)
    {
       $this->db->where('member_id', $member); 
       $this->db->where('account_id',$acc);
       $this->db->where('month',$month);
       $this->db->where('year',$year);
       $num = $this->db->get($this->tableName)->num_rows();
       
       if ($num == 0)
       {
          $trans = array('member_id' => $member, 'account_id' => $acc, 'month' => $month, 'year' => $year, 'vamount' => $amt);
          $this->db->insert($this->tableName, $trans); 
       }
    }
    
    function remove_balance($member,$acc)
    {
       $this->db->where('member_id',$member);
       $this->db->where('account_id',$acc); 
       $this->db->delete($this->tableName);
    }
    
    function get($member,$acc,$month,$year)
    {
       $this->db->select($this->field);  
       $this->db->where('member_id',$member);
       $this->db->where('account_id',$acc);
       $this->db->where('month',$month);
       $this->db->where('year',$year);
       return $this->db->get($this->tableName)->row();
    }

}

/* End of file Property.php */
