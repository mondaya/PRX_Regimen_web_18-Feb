<form action="" method="post" name="frmCP" id="frmCP" class="form-horizontal" enctype="multipart/form-data">
    <div class="form-body"><div class="flclear clearfix"></div>
			
			<div class="form-group">
			  <label for="opasswd" class="control-label col-md-3"><font color="#FF0000">*</font>Step-1 Title: &nbsp;</label>
			  <div class="col-md-8">
			    <textarea class="form-control logintextbox-bg required" name="step1Title" id="step1Title">%STEP1TITLE%</textarea>
			  </div>
			</div>
			<script type="text/javascript">$(function(){loadCKE("step1Title");});</script>
			<div class="form-group">
			  <label for="cpasswd" class="control-label col-md-3"><font color="#FF0000">*</font>Step-1 Description: &nbsp;</label>
			  <div class="col-md-8">
			    <textarea class="form-control logintextbox-bg required" name="step1Desc" id="step1Desc">%STEP1DESC%</textarea>
			  </div>
			</div>
			<script type="text/javascript">$(function(){loadCKE("step1Desc");});</script>

			<div class="form-group">
			  <label for="opasswd" class="control-label col-md-3"><font color="#FF0000">*</font>Step-2 Title: &nbsp;</label>
			  <div class="col-md-8">
			    <textarea class="form-control logintextbox-bg required" name="step2Title" id="step2Title">%STEP2TITLE%</textarea>
			  </div>
			</div>
			<script type="text/javascript">$(function(){loadCKE("step2Title");});</script>
			<div class="form-group">
			  <label for="cpasswd" class="control-label col-md-3"><font color="#FF0000">*</font>Step-2 Description: &nbsp;</label>
			  <div class="col-md-8">
			    <textarea class="form-control logintextbox-bg required" name="step2Desc" id="step2Desc">%STEP2DESC%</textarea>
			  </div>
			</div>
			<script type="text/javascript">$(function(){loadCKE("step2Desc");});</script>

			<div class="form-group">
			  <label for="opasswd" class="control-label col-md-3"><font color="#FF0000">*</font>Step-3 Title: &nbsp;</label>
			  <div class="col-md-8">
			    <textarea class="form-control logintextbox-bg required" name="step3Title" id="step3Title">%STEP3TITLE%</textarea>
			  </div>
			</div>
			<script type="text/javascript">$(function(){loadCKE("step3Title");});</script>
			<div class="form-group">
			  <label for="cpasswd" class="control-label col-md-3"><font color="#FF0000">*</font>Step-3 Description: &nbsp;</label>
			  <div class="col-md-8">
			    <textarea class="form-control logintextbox-bg required" name="step3Desc" id="step3Desc">%STEP3DESC%</textarea>
			  </div>
			</div>
			<script type="text/javascript">$(function(){loadCKE("step3Desc");});</script>

			<div class="form-group">
			  <label for="opasswd" class="control-label col-md-3"><font color="#FF0000">*</font>Why Title: &nbsp;</label>
			  <div class="col-md-4">
			    <input type="text" class="form-control logintextbox-bg required" name="whyTitle" id="whyTitle" value="%WHYTITLE%">
			  </div>
			</div>

			<div class="form-group">
			  <label for="cpasswd" class="control-label col-md-3"><font color="#FF0000">*</font>Why Description: &nbsp;</label>
			  <div class="col-md-8">
			    <textarea class="form-control logintextbox-bg required" name="whyWellness" id="whyWellness">%WHYWLLNESS%</textarea>
			  </div>
			</div>
			<script type="text/javascript">$(function(){loadCKE("whyWellness");});</script>

			<div class="padtop20"></div>
		</div>
		<div class="form-actions fluid">
		<div class="col-md-offset-3 col-md-9">
			<button type="submit" name="submitChange" class="btn green" id="submitChange">Submit</button><button type="button" name="cn" class="btn default" id="cn" onclick="location.href='<?php echo SITE_ADM_MOD; ?>home-nct/'">Cancel</button>
		</div>
	</div>
</form>