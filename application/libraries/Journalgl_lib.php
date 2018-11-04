<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Journalgl_lib extends Custom_Model {
    
    public function __construct($deleted=NULL)
    {
        $this->deleted = $deleted;
        $this->tableName = 'gls';
        $this->le = new Ledger_lib();
        $this->period = new Period_lib();
    }

    private $ci,$period;
    private $le,$currency;

    // no, dates, code, currency, notes, balance, log
    public function new_journal($member, $no, $dates, $code, $currency, $notes, $amount=0, $log)
    {
        $journal = array('member_id' => $member, 'no' => $no, 'dates' => $dates, 'code' => $code, 'currency' => $currency,
                         'notes' => $notes, 'balance' => $amount, 'log' => $log, 'approved' => 1);
        
        if ($this->cek_journal($member, $no,$code, $dates, $currency) == TRUE)
        { $this->db->insert('gls', $journal); $this->currency = $currency; }
    }
    
    function cek_journal($member,$no,$code,$date,$currency)
    {
        $this->db->where('member_id', $member);
        $this->db->where('no', $no);
        $this->db->where('code', $code);
        $this->db->where('dates', $date);
        $this->db->where('currency', $currency);
        $num = $this->db->get('gls')->num_rows();
        if ($num > 0){ return FALSE; }else { return TRUE; }
    }
    
    public function add_trans($gl,$acc,$debit=0,$credit=0)
    {
        $trans = array('gl_id' => $gl, 'account_id' => $acc, 'debit' => $debit, 
                       'credit' => $credit, 'vamount' => $this->calculate_vamount($acc, $debit, $credit));
        
        $this->db->insert('transactions', $trans);
        $this->update_trans($gl);
        $this->le->set_profit_loss($this->currency); 
    }
    
    private function update_trans($gl)
    {
        $this->db->select_sum('debit');
        $this->db->where('gl_id', $gl);
        $res = $this->db->get('transactions')->row_array();
        $res = intval($res['debit']);
        
        $trans = array('balance' => $res);
        $this->db->where('id', $gl);
        $this->db->update('gls', $trans);
    }
    
    public function get_journal_id($member,$code,$no)
    {
        $this->db->where('memberid', $member);
        $this->db->where('code', $code);
        $this->db->where('no', $no);
        $jid = $this->db->get('gls')->row();
        $jid = $jid->id;
        return $jid;
    }

//    ============================  remove transaction journal ==============================

    function remove_journal($member,$codetrans,$no)
    {
        // ============ update transaction ===================
        $year = $this->period->get('year');
        
        $this->db->where('member_id', $member);
        $this->db->where('no', $no);
        $this->db->where('code', $codetrans);
        $this->db->where('YEAR(dates)', $year);
        
        $jid = $this->db->get('gls')->row();
        // ====================================================
        
        if ($jid)
        {
            $this->db->where('gl_id', $jid->id);
            $this->db->delete('transactions');

            $this->db->where('id', $jid->id);
            $this->db->delete('gls');
            $this->le->set_profit_loss($this->currency);   
        }
    }
    
    public function calculate_account_amount($member,$acc,$debit=0,$credit=0)
    {
        $classi = $this->load->library('classification_lib');
        $account = $this->load->library('account_lib');
        
        $type = $classi->get_type($account->get_classi($member,$acc));
        $res = 0;

        if ($type == 'harta'){ $res = 0 + $debit - $credit; }
        elseif ($type == 'kewajiban'){ $res = 0 - $debit + $credit; }
        elseif ($type == 'modal'){ $res = 0 - $debit + $credit; }
        elseif ($type == 'pendapatan'){ $res = 0 - $debit + $credit; }
        elseif ($type == 'biaya'){ $res = 0 + $debit - $credit; }
        return $res;
    }
    
    private function calculate_vamount($member,$acc,$debit=0,$credit=0)
    {
        $classi = $this->load->library('classification_lib');
        $account = $this->load->library('account_lib');
        
        $type = $classi->get_type($account->get_classi($member,$acc));
        $res = 0;

        if ($type == 'harta'){ $res = 0 + $debit - $credit; }
        elseif ($type == 'kewajiban'){ $res = 0 - $debit + $credit; }
        elseif ($type == 'modal'){ $res = 0 - $debit + $credit; }
        elseif ($type == 'pendapatan'){ $res = 0 - $debit + $credit; }
        elseif ($type == 'biaya'){ $res = 0 + $debit - $credit; }
        return $res;
    }
    
    // cek account in trsanction table
    function valid_account_transaction($accid=0)
    {
        $this->db->where('account_id', $accid);
        $res = $this->db->get('transactions')->num_rows();
        if ($res > 0){ return FALSE; }else{ return TRUE; }
    }
    
}