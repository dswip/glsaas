<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Account extends MX_Controller
{
    function __construct()
    {
        parent::__construct();
        
        $this->load->model('Account_model', 'Model', TRUE);
        $this->title = strtolower(get_class($this));

        $this->currency = new Currency_lib();
        $this->classification = new Classification_lib();
        $this->account = new Account_lib();
        $this->balance = new Balance_account_lib();
        $this->period = new Period_lib();
        $this->journal = new Journalgl_lib();
        $this->api = new Api_lib();
        
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token'); 
    }

    private $properti, $modul, $title, $model, $account, $balance;
    private $currency, $classification, $period, $journal,$api;

    private  $atts = array('width'=> '400','height'=> '200',
                      'scrollbars' => 'yes','status'=> 'yes',
                      'resizable'=> 'yes','screenx'=> '0','screenx' => '\'+((parseInt(screen.width) - 400)/2)+\'',
                      'screeny'=> '0','class'=> 'print','title'=> 'print', 'screeny' => '\'+((parseInt(screen.height) - 200)/2)+\'');

    function index(){}
    
    public function search()
    {
        try{
           $datax = (array)json_decode(file_get_contents('php://input'));  
           $result = $this->Model->search($datax['member'],$datax['classification'],$datax['publish']); 
           
           if ($result->num_rows() > 0){ return $this->api->response($result->result()); }else{
            return $this->api->response(null,404);
           }    
        }catch(\Exception $e){
            return $this->api->response(null,403);
        }
    }    
    
    public function publish($uid)
    {
       try{
          $val = $this->Model->get_by_id($uid)->row();
          if ($val->status == 0){ $lng = array('status' => 1); }else { $lng = array('status' => 0); }
          $this->Model->update($uid,$lng);
          $this->api->response(array('status' => true, 'error' => 'Status Changed...!'));
       }catch (\Exception $e){
         return $this->api->response(null,403);  
       }
    }
        
    private function get_balance($acc=null)
    {
        $ps = new Period();
        $gl = new Gl();
        $ps->get();
        
        $gl->where('approved', 1);
        $gl->where('MONTH(dates)', $ps->month);
        $gl->where('YEAR(dates)', $ps->year)->get();
        
        $this->load->model('Account_model','am',TRUE);
        $val = $this->am->get_balance($acc,$ps->month,$ps->year)->row_array();
        return $val['vamount'];
    }

    private function get_cost($acc=null,$month=0)
    {
        $ps = new Period();
        $bl = new Balance();
        $ps->get();
        
        $bl->where('account_id', $acc);
        $bl->where('month', $month);
        $num = $bl->where('year', $ps->year)->count();

        $val = null;
        if ( $num > 0)
        {
           $bl->where('account_id', $acc);
           $bl->where('month', $month);
           $bl->where('year', $ps->year)->get(); 
            
           $val[0] = get_month($month);
           $val[1] = $ps->year;
           $val[2] = $bl->beginning + $this->get_balance($acc);
        }
        else
        {
           $val[0] = get_month($month);
           $val[1] = $ps->year;
           $val[2] = 0; 
        }

        return $val;
    }

    function cost($acc = null)
    {
        $this->acl->otentikasi1($this->title);

        $data['title'] = $this->properti['name'].' | Administrator Account Balance '.ucwords($this->modul['title']);
        $data['h2title'] = 'Account Balance '.$this->modul['title'];
        $data['main_view'] = 'account_balance';
        $data['link'] = array('link_back' => anchor($this->title,'<span>back</span>', array('class' => 'back')));

        $data['accname'] = $this->account->get_name($acc);
        $data['acccur'] = $this->account->get_cur($acc);

        $tmpl = array('table_open' => '<table cellpadding="2" cellspacing="1" class="tablemaster">');

        $this->table->set_template($tmpl);
        $this->table->set_empty("&nbsp;");

        //Set heading untuk table
        $this->table->set_heading('Month', 'Year', 'Budget');
        
        $account = null;
        for ($x=1; $x<=12; $x++)
        {
           $account[$x] = $this->get_cost($acc,$x);
           $this->table->add_row
           (
               $account[$x][0], $account[$x][1], number_format($account[$x][2])
           );
        }

        $data['table'] = $this->table->generate();
        $this->load->view('account_balance', $data);
    }

    function delete($uid)
    {
       try{
            if ( $this->journal->valid_account_transaction($uid) == TRUE && $this->valid_default($uid) == TRUE )
            {
                // hapus balance
                if ($this->balance->remove_balance($this->account->get_member($uid),$uid) == TRUE){
                   $this->Model->delete($uid);
                   $this->api->response(array('status' => true, 'error' => 'successfully removed balance..!'));
                }else{ $this->api->response(array('status' => false, 'error' => "Cant removed..!")); }
            }
            else{ echo  "invalid|$this->title related to another component..!"; } 
           
       }catch (\Exception $e){
         return $this->api->response(null,403);  
       }
    }
    
    private function valid_required($datax=null,$type='add'){
        $error = null;
        if (!isset($datax['name'])){ $error = 'name'; }
        else if(!isset($datax['member'])){ $error = 'member'; }
        else if(!isset($datax['code'])){ $error = 'code'; }
        else if(!isset($datax['currency'])){ $error = 'currency'; }
        else if(!isset($datax['classification'])){ $error = 'classification'; }
        else if (!isset($datax['alias'])){ $error = 'alias'; }
        else if (!isset($datax['acc_no'])){ $error = 'acc_no'; }
        else if (!isset($datax['bank'])){ $error = 'bank'; }
        else if (!isset($datax['city'])){ $error = 'city'; }
        else if (!isset($datax['phone'])){ $error = 'phone'; }
        else if (!isset($datax['zip'])){ $error = 'zip'; }
        else if (!isset($datax['contact'])){ $error = 'contact'; }
        else if (!isset($datax['fax'])){ $error = 'fax'; }
        else if (!isset($datax['balance_phone'])){ $error = 'balance_phone'; }
        else if (!isset($datax['bank'])){ $error = 'bank'; }
        else if (!isset($datax['id'])){ $error = 'id'; }
        if ($error != null){ $error = $error.' field required'; }
        return $error;
    }
    
    function add(){
        $datax = (array)json_decode(file_get_contents('php://input'));  
        try{
         $errorfield = $this->valid_required($datax);
         $error = null;
         $code = 0;

         if ($errorfield == null){
             $code = $this->classification->get_no($datax['classification']).'-'.$datax['code'];
             if ($this->Model->valid('name',$datax['name']) == FALSE){ $error = 'Account Name Registered..!'; }
             elseif ($this->Model->valid('code',$code) == FALSE){ $error = 'Account Code Registered..!'; }
         }
         

         if ($errorfield == null && $error == null ){
             
             if ($datax['classification'] == 7 || $datax['classification'] == 8){ $bank = 1; }
             else { $bank  = $datax['bank']; }
             
             $account = array('member_id' => $datax['member'], 'classification_id' => $datax['classification'], 'currency' => $datax['currency'],
                              'code' => $code, 'name' => $datax['name'], 'alias' => $datax['alias'],
                              'acc_no' => $datax['acc_no'], 'bank' => $datax['bank'], 'city' => $datax['city'],
                              'phone' => $datax['phone'], 'zip' => $datax['zip'], 'contact' => $datax['contact'], 'fax' => $datax['fax'],
                              'balance_phone' => $datax['balance_phone'], 'status' => 1, 'bank_stts' => $bank,
                              'created' => date('Y-m-d H:i:s'));
             
             if ($this->Model->add($account) == TRUE){ 
                 $this->create_balance($datax['member'],$code); 
                 $this->api->response(array('status' => true, 'error' => $this->title.' : '.$code.' successfully saved..!..!'));
             } 
             
         }else{ echo $errorfield.' - '.$error; }         
             
       }catch (\Exception $e){ return $this->api->response(null,403); }
        
    }
    
    private function create_balance($member,$code=null)
    {
        $ps = $this->period->get($member);
        $accid = $this->account->get_id_code($code);
        $this->balance->create($member,$accid, $ps->month, $ps->year, 0, 0);
    }

    function get($uid)
    {
        $acc = $this->Model->get_by_id($uid);
        if ($acc->num_rows() > 0){ return $this->api->response($acc->row_array());}else{
          return $this->api->response(null,404);
        }
    }
    
    function update(){
        
       try{
         $datax = (array)json_decode(file_get_contents('php://input'));  
         $error = null; $errorfield = null;
         
         $errorfield = $this->valid_required($datax,'update');
         
         if ($errorfield == null){
            $code = $this->classification->get_no($datax['classification']).'-'.$datax['code'];
            $uid = $datax['id'];
         
            if ($this->validation_code($code, $uid) == FALSE){ $error = 'This '.$this->title.' code is already registered!'; }
            if ($this->validation_name($datax['name'], $uid) == FALSE){ $error = 'This '.$this->title.' is already registered!'; }    
         }
        
         if ($errorfield == null && $error == null){
             
             if ($datax['classification'] == 7 || $datax['classification'] == 8){ $bank = 1; }else { $bank  = $datax['bank_status']; }
            
             $account = array('classification_id' => $datax['classification'], 'currency' => $datax['currency'],
                              'code' => $code, 'name' => $datax['name'], 'alias' => $datax['alias'], 'acc_no' => $datax['acc_no'],
                              'bank' => $datax['bank'], 'city' => $datax['city'], 'phone' => $datax['phone'], 'zip' => $datax['zip'],
                              'contact' => $datax['contact'], 'fax' => $datax['fax'], 'balance_phone' => $datax['balance_phone'],
                              'bank_stts' => $bank);
            
             $this->Model->update($datax['id'], $account);
             $this->api->response(array('status' => true, 'error' => $this->title.' successfully saved..!..!'));
             
         }else{ echo $errorfield.' - '.$error; }         
         
       }catch (\Exception $e){ return $this->api->response(null,403); }
        
    }

    // Fungsi update untuk mengupdate db
    function update_process()
    {
        if ($this->acl->otentikasi2($this->title,'ajax') == TRUE){

        $data['title'] = $this->properti['name'].' | Administrator  '.ucwords($this->modul['title']);
        $data['h2title'] = $this->modul['title'];
        $data['main_view'] = 'account_update';
	$data['form_action'] = site_url($this->title.'/update_process');
	$data['link'] = array('link_back' => anchor('account/','<span>back</span>', array('class' => 'back')));
        
	// Form validation
        $this->form_validation->set_rules('tname', 'Name', 'required|callback_validation_name');
        $this->form_validation->set_rules('tno', 'No', 'required|numeric');
        $this->form_validation->set_rules('tcode', 'Code', 'required|numeric|callback_validation_code');
        $this->form_validation->set_rules('ccurrency', 'Currency', 'required');
        $this->form_validation->set_rules('cclassification', 'Classification', 'required');

        if ($this->form_validation->run($this) == TRUE)
        {
            if ($this->input->post('cclassification') == 7 || $this->input->post('cclassification') == 8){ $bank = 1; }
            else { $bank  = $this->input->post('cbank'); }
            
            $account = array('classification_id' => $this->input->post('cclassification'), 'currency' => $this->input->post('ccurrency'),
                             'code' => $this->input->post('tcode').'-'.$this->input->post('tno'), 'name' => $this->input->post('tname'),
                             'alias' => $this->input->post('talias'), 'status' => $this->input->post('cactive'), 'bank_stts' => $bank);
            
            $this->Model->update($this->session->userdata('langid'), $account);
            echo 'true|Data successfully saved..!';
        }
        else{ echo 'error|'.validation_errors(); }
        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
    }
 
    public function valid_default($uid=null)
    {
        if ($this->Model->valid_default($uid) == FALSE)
        {
            $this->form_validation->set_message('valid_default', "Default Account - [Can't Changed]..!");
            return FALSE;
        }
        else{ return TRUE; }
    }

    public function validation_name($name,$uid)
    {   
	if ($this->Model->validating('name',$name,$uid) == FALSE)
        {
//            $this->form_validation->set_message('validation_name', 'This '.$this->title.' is already registered!');
            return FALSE;
        }
        else { return TRUE; }
    }

    public function validation_code($code,$uid)
    {
	if ($this->Model->validating('code',$code,$uid) == FALSE)
        {
//            $this->form_validation->set_message('validation_code', 'This '.$this->title.' code is already registered!');
            return FALSE;
        }
        else { return TRUE; }
    }

    public function valid_code($code)
    {   
        if ($this->Model->valid('code',$code) == FALSE)
        {
//            $this->form_validation->set_message('valid_code', "Account No already registered.!");
            return FALSE;
        }
        else{ return TRUE; }
    }

// ====================================== REPORT =========================================


    function report()
    {
        $this->acl->otentikasi2($this->title);
        $data['title'] = $this->properti['name'].' | Report '.ucwords($this->modul['title']);

        $cur = $this->input->post('ccurrency');
        $status = $this->input->post('cstatus');

        $data['currency'] = 'null';
        $data['rundate'] = tgleng(date('Y-m-d'));
        $data['log'] = $this->session->userdata('log');

//        Property Details
        $data['company'] = $this->properti['name'];

        // assets
        $data['kas'] = $this->Model->report($cur,$status,7)->result();
        $data['bank'] = $this->Model->report($cur,$status,8)->result();
        $data['piutangusaha'] = $this->Model->report($cur,$status,20)->result();
        $data['piutangnonusaha'] = $this->Model->report($cur,$status,27)->result();
        $data['persediaan'] = $this->Model->report($cur,$status,14)->result();
        $data['biayadimuka'] = $this->Model->report($cur,$status,13)->result();
        $data['investasipanjang'] = $this->Model->report($cur,$status,29)->result();
        $data['hartatetapwujud'] = $this->Model->report($cur,$status,26)->result();
        $data['hartatetaptakwujud'] = $this->Model->report($cur,$status,30)->result();
        $data['hartalain'] = $this->Model->report($cur,$status,31)->result();
        
        // kewajiban
        $data['hutangusaha'] = $this->Model->report($cur,$status,10)->result();
        $data['pendapatandimuka'] = $this->Model->report($cur,$status,34)->result();
        $data['hutangjangkapanjang'] = $this->Model->report($cur,$status,35)->result();
        $data['hutangnonusaha'] = $this->Model->report($cur,$status,32)->result();
        $data['hutanglain'] = $this->Model->report($cur,$status,36)->result();
        
        // modal & laba
        $data['modal'] = $this->Model->report($cur,$status,22)->result();
        $data['laba'] = $this->Model->report($cur,$status,18)->result();
        
        // income
        $data['income'] = $this->Model->report($cur,$status,16)->result();
        $data['otherincome'] = $this->Model->report($cur,$status,37)->result();
        $data['outincome'] = $this->Model->report($cur,$status,21)->result();
        
        // biaya
        $data['biayausaha'] = $this->Model->report($cur,$status,15)->result();
        $data['biayausahalain'] = $this->Model->report($cur,$status,17)->result();
        $data['biayaoperasional'] = $this->Model->report($cur,$status,19)->result();
        $data['biayanonoperasional'] = $this->Model->report($cur,$status,24)->result();
        $data['pengeluaranluarusaha'] = $this->Model->report($cur,$status,25)->result();
        
        
        $this->load->view('account_report', $data); 
    }


// ====================================== REPORT =========================================
   
// ====================================== CLOSING ======================================
   function reset_process(){ $this->Model->closing(); }

}

?>