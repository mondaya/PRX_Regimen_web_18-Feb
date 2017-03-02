<form action="" method="post" name="frmCP" id="frmCP" class="form-horizontal" enctype="multipart/form-data">
    <div class="form-body"><div class="flclear clearfix"></div>
			
			<div class="form-group">
			  <label for="opasswd" class="control-label col-md-3"><font color="#FF0000">*</font>Return Policy: &nbsp;</label>
			  <div class="col-md-8">
			    <textarea class="form-control logintextbox-bg required" name="returnPolicy" id="returnPolicy">%RETURN_POLICY%</textarea>
			  </div>
			</div>
			<script type="text/javascript">$(function(){loadCKE("returnPolicy");});</script>
			
			<div class="padtop20"></div>
		</div>
		<div class="form-actions fluid">
		<div class="col-md-offset-3 col-md-9">
			<button type="submit" name="submitChange" class="btn green" id="submitChange">Submit</button><button type="button" name="cn" class="btn default" id="cn" onclick="location.href='<?php echo SITE_ADM_MOD; ?>home-nct/'">Cancel</button>
		</div>
	</div>
</form>