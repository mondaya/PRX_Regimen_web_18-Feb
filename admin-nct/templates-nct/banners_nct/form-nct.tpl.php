<form action="" method="post" name="frmCont" id="frmCont" class="form-horizontal" enctype="multipart/form-data" novalidate="novalidate">
 <div class="form-body">
 	<div class="form-group">
 		<label for="banner_name" class="control-label col-md-3"><font color="#FF0000">*</font>Banner Name: &nbsp;</label>
 		<div class="col-md-4"> <input type="text" class="form-control logintextbox-bg required" name="banner_name" id="banner_name" value="%BANNER_NAME%"></div>
 	</div>
 	<div class="form-group">
 		<label for="banner_image" class="control-label col-md-3"><font color="#FF0000">*</font>Banner Image: &nbsp;</label>
 		<div class="col-md-4">
 			<input type="file" class="form-control logintextbox-bg %REQUIRED%" name="banner_image" id="banner_image" />
	 		<?php if(!empty($this->id)) { ?>
	 			<label style="margin-top: 20px;"><font color="#FF0000">Current image</font></label><br />
	 			<img src="%BANNER_SRC%" height="100" width="100" />
	 		<?php } ?>
			</div>
 	</div>
 	<div class="form-group">
 		<label for="banner_link" class="control-label col-md-3"><font color="#FF0000">*</font>Banner Link: &nbsp;</label>
 		<div class="col-md-4"> <input type="text" class="form-control logintextbox-bg required" name="banner_link" id="banner_link" value="%BANNER_LINK%" /> </div>
 	</div>
 	<div class="form-group">
 	 	<label class="control-label col-md-3">Status: &nbsp;</label>
 	 	 <div class="col-md-4">
 	 	 	<div class="radio-list" data-error-container="#form_2_Status: _error">
 	 	 <label class=""> <input class="radioBtn-bg required" id="y" name="status" type="radio" value="y" %STATUS_A%> Active</label><span for="status" class="help-block"></span>
 	 	 <label class="">
 	 	 <input class="radioBtn-bg required" id="n" name="status" type="radio" value="n" %STATUS_D%> Deactive</label><span for="status" class="help-block"></span>
 	 	 </div>
 	 	 <div id="form_2_Status: _error"></div>
 	 	  </div>
 	</div>
 	<div class="flclear clearfix">
 </div>
  <input type="hidden" name="old_image" id="old_image" value="%OLD_IMAGE%"><div class="flclear clearfix"></div>
  <input type="hidden" name="type" id="type" value="%TYPE%"><div class="flclear clearfix"></div>
  <input type="hidden" name="id" id="id" value="%ID%"><div class="padtop20"></div>
</div>
<div class="form-actions fluid">
	<div class="col-md-offset-3 col-md-9">
		<button type="submit" name="submitAddForm" class="btn green" id="submitAddForm">Submit</button><button type="button" name="cn" class="btn btn-toggler" id="cn">Cancel</button>
	</div>
</div>
</form>