<form action="" method="post" name="frmCP" id="frmCP" class="form-horizontal" enctype="multipart/form-data">
    <div class="form-body"><div class="flclear clearfix"></div>
			<input type="hidden" name="passvalue" id="passvalue" value="%PASS_VALUE%">
			<div class="form-group">
			  <label for="opasswd" class="control-label col-md-3"><font color="#FF0000">*</font>Old Password: &nbsp;</label>
			  <div class="col-md-4">
			    <input type="password" class="form-control logintextbox-bg required" name="opasswd" id="opasswd" value="%OLD_PASSWORD%">
			  </div>
			</div>
			<div class="form-group">
			  <label for="passwd" class="control-label col-md-3"><font color="#FF0000">*</font>New Password: &nbsp;</label>
			  <div class="col-md-4">
			    <input type="password" class="form-control logintextbox-bg required" name="passwd" id="passwd" value="%NEW_PASSWORD%">
			  </div>
			</div>
			<div class="form-group">
			  <label for="cpasswd" class="control-label col-md-3"><font color="#FF0000">*</font>Confirmed Password: &nbsp;</label>
			  <div class="col-md-4">
			    <input type="password" class="form-control logintextbox-bg required" name="cpasswd" id="cpasswd" value="%CONFIRM_PASSWORD%">
			  </div>
			</div>
			<div class="padtop20"></div>
		</div>
		<div class="form-actions fluid">
		<div class="col-md-offset-3 col-md-9">
			<button type="submit" name="submitChange" class="btn green" id="submitChange">Submit</button><button type="button" name="cn" class="btn default" id="cn" onclick="location.href='<?php echo SITE_ADM_MOD; ?>home-nct/'">Cancel</button>
		</div>
	</div>
</form>