<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ledger_lib extends Custom_Model {

    public function __construct($deleted=NULL)
    {
        $this->balance_lib = new Balance_account_lib();
        $this->deleted = $deleted;
        $this->tableName = 'balance';
    }

    private $balance_lib;
    
    public function calculate_profit_loss($cur='IDR',$month=0, $year=0, $emonth=0, $eyear=0)
    {
        $ac = $this->load->model('Account_model','', TRUE);
        
        $incometot = floatval($ac->get_balance_by_classification($cur,16,$month,$year,$emonth,$eyear));
        $hpptot = floatval($ac->get_balance_by_classification($cur,15,$month,$year,$emonth,$eyear));
        $operationalcosttot = floatval($ac->get_balance_by_classification($cur,19,$month,$year,$emonth,$eyear));
        $nonoperationalcosttot = floatval($ac->get_balance_by_classification($cur,24,$month,$year,$emonth,$eyear));
        $othercosttot = floatval($ac->get_balance_by_classification($cur,17,$month,$year,$emonth,$eyear));
        
        $outincometot = floatval($ac->get_balance_by_classification($cur,21,$month,$year,$emonth,$eyear));
        $outcosttot = floatval($ac->get_balance_by_classification($cur,25,$month,$year,$emonth,$eyear));
        
        $result = floatval($incometot-$hpptot+$othercosttot-$operationalcosttot+$nonoperationalcosttot+$outincometot-$outcosttot);
        return $result;
    }
    
    public function set_profit_loss($cur='IDR')
    {
        $ac = $this->load->model('Account_model','', TRUE);
        $ps = new Period();
        $bl = new Balances();
        $ps->get();
        
        // pendapatan
        $incometot = floatval($ac->get_balance_by_classification($cur,16,$ps->month,$ps->year,$ps->month,$ps->year));
        $outincometot = floatval($ac->get_balance_by_classification($cur,21,$ps->month,$ps->year,$ps->month,$ps->year));
        $otherincometot = floatval($ac->get_balance_by_classification($cur,37,$ps->month,$ps->year,$ps->month,$ps->year));
        
        //biaya
        $hpptot = floatval($ac->get_balance_by_classification($cur,15,$ps->month,$ps->year,$ps->month,$ps->year));
        $operationalcosttot = floatval($ac->get_balance_by_classification($cur,19,$ps->month,$ps->year,$ps->month,$ps->year));
        $nonoperationalcosttot = floatval($ac->get_balance_by_classification($cur,24,$ps->month,$ps->year,$ps->month,$ps->year));
        $othercosttot = floatval($ac->get_balance_by_classification($cur,17,$ps->month,$ps->year,$ps->month,$ps->year));
        $outcosttot = floatval($ac->get_balance_by_classification($cur,25,$ps->month,$ps->year,$ps->month,$ps->year));
        
        // laba tahun berjalan
        $laba = floatval($ac->get_balance_by_classification($cur,18,$ps->month,$ps->year,$ps->month,$ps->year));
        
        $pendapatan = floatval($incometot + $outincometot + $otherincometot);
        $biaya = floatval($hpptot+$operationalcosttot+$nonoperationalcosttot+$othercosttot+$outcosttot);
        $result = floatval($pendapatan-$biaya);
        
        $bl->where('account_id', 21);
        $bl->where('month', $ps->month);
        $bl->where('year', $ps->year)->get();
        
//        $bl->vamount = floatval($bl->beginning + $result);
//        $bl->save();
//        $this->balance_lib->create_vamount(21, $ps->month, $ps->year, floatval($bl->beginning + $result));
        $this->balance_lib->create_vamount(21, $ps->month, $ps->year, floatval($result));
        
    }

}