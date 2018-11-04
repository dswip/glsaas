<div class="modal-dialog">
        
<!-- Modal content-->
<div class="modal-content">
<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal">&times;</button>
  <h4 class="modal-title"> Edit - COA </h4>
</div>
<div class="modal-body">

 <!-- error div -->
 <div class="alert alert-success success"> </div>
 <div class="alert alert-warning warning"> </div>
 <div class="alert alert-error error"> </div>
 
 <!-- form add -->
<div class="x_panel" >

<div class="x_content">

 <form id="edit_form_non" data-parsley-validate class="form-horizontal form-label-left" method="POST" 
 action="<?php echo $form_action_update; ?>" enctype="multipart/form-data">
    
      <div class="col-md-11 col-sm-9 col-xs-12 form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12"> Classification </label>
        <div class="col-md-7 col-sm-6 col-xs-12">
       <?php $js = "class='form-control' id='cclassi_update' tabindex='-1' style='width:100%;' "; 
       echo form_dropdown('cclassification', $classi, isset($default['classi']) ? $default['classi'] : '', $js); ?>
        </div>
      </div>
    
      <div class="col-md-11 col-sm-9 col-xs-12 form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12"> Currency </label>
        <div class="col-md-4 col-sm-5 col-xs-12">    
       <?php $js = "class='form-control' id='ccur_update' tabindex='-1' style='width:100%;' "; 
       echo form_dropdown('ccurrency', $currency, isset($default['currency']) ? $default['currency'] : '', $js); ?>
        </div>
      </div>
    
      
      <div class="col-md-11 col-sm-12 col-xs-12 form-group">  
          <label class="control-label col-md-3 col-sm-3 col-xs-12"> Code / No </label>  
          <div class="col-md-3 col-sm-12 col-xs-12 form-group">
            <input type="text" class="form-control has-feedback-left" id="tcode_update" name="tcode" readonly>
          </div>
          <div class="col-md-4 col-sm-12 col-xs-12 form-group">  
    <input type="number" class="form-control has-feedback-left" id="tno_update" required name="tno" placeholder="No"> 
          </div>
      </div>
   					
    
      <div class="col-md-12 col-sm-12 col-xs-12 form-group has-feedback">
        <input type="text" class="form-control has-feedback-left" id="tname_update" name="tname" required placeholder="Name">
        <span class="fa fa-user form-control-feedback left" aria-hidden="true"></span> 
      </div>
      
      <div class="col-md-12 col-sm-12 col-xs-12 form-group has-feedback">
   <input type="text" class="form-control has-feedback-left" id="talias_update" name="talias" required placeholder="Alias">
   <span class="fa fa-book form-control-feedback left" aria-hidden="true"></span> 
      </div>
    
    <div class="col-md-11 col-sm-12 col-xs-12 form-group">  
          <label class="control-label col-md-3 col-sm-3 col-xs-12"> </label>  
          <div class="col-md-6 col-sm-12 col-xs-12 form-group">
            <input type="checkbox" name="cactive" id="cactive_update" value="1" class="" /> Active &nbsp;
            <input type="checkbox" name="cbank" id="cbank_update" value="1" class="" /> Bank / Cash 
          </div>
    </div>
      
      <div class="ln_solid"></div>
      <div class="form-group">
        <div class="col-md-9 col-sm-9 col-xs-12 col-md-offset-3 btn-group">
          <button type="submit" class="btn btn-primary" id="button">Save</button>
          <button type="button" id="bclose" class="btn btn-danger" data-dismiss="modal">Close</button>
          <button type="button" id="breset" class="btn btn-warning" onClick="reset();">Reset</button>
        </div>
      </div>
</form> 

</div>
</div>
<!-- form add -->

</div>
    <div class="modal-footer"> </div>
</div>
  
</div>