<form action="" method="post" name="frmCP" id="frmCP" class="form-horizontal" enctype="multipart/form-data">
    <div class="form-body"><div class="flclear clearfix"></div>
			
			<div class="form-group">
			  <label for="opasswd" class="control-label col-md-3"><font color="#FF0000">*</font>Profile: &nbsp;</label>
			  <div class="col-md-4">
			    <input type="text" class="form-control logintextbox-bg required" name="profile" id="step1Title" value="%PROFILE%">
			  </div>
			</div>

			<div class="form-group">
			  <label for="opasswd" class="control-label col-md-3"><font color="#FF0000">*</font>Payment History: &nbsp;</label>
			  <div class="col-md-4">
			    <input type="text" class="form-control logintextbox-bg required" name="paymentHistory" id="step1Title" value="%PAYMENT_HISTORY%">
			  </div>
			</div>

			<div class="form-group">
			  <label for="opasswd" class="control-label col-md-3"><font color="#FF0000">*</font>Wallet: &nbsp;</label>
			  <div class="col-md-4">
			    <input type="text" class="form-control logintextbox-bg required" name="wallet" id="step1Title" value="%WALLET%">
			  </div>
			</div>

			<div class="form-group">
			  <label for="opasswd" class="control-label col-md-3"><font color="#FF0000">*</font>My Order: &nbsp;</label>
			  <div class="col-md-4">
			    <input type="text" class="form-control logintextbox-bg required" name="orders" id="step1Title" value="%ORDER%">
			  </div>
			</div>

			<div class="form-group">
			  <label for="opasswd" class="control-label col-md-3"><font color="#FF0000">*</font>My Custom Order: &nbsp;</label>
			  <div class="col-md-4">
			    <input type="text" class="form-control logintextbox-bg required" name="customOrder" id="step1Title" value="%CUSTOM_ORDER%">
			  </div>
			</div>

			<div class="form-group">
			  <label for="opasswd" class="control-label col-md-3"><font color="#FF0000">*</font>New Custom Order: &nbsp;</label>
			  <div class="col-md-4">
			    <input type="text" class="form-control logintextbox-bg required" name="newCustomOrder" id="step1Title" value="%NEW_CUSTOM_ORDER%">
			  </div>
			</div>

			<div class="form-group">
			  <label for="opasswd" class="control-label col-md-3"><font color="#FF0000">*</font>Settings: &nbsp;</label>
			  <div class="col-md-4">
			    <input type="text" class="form-control logintextbox-bg required" name="settings" id="step1Title" value="%SETTINGS%">
			  </div>
			</div>

			<div class="form-group">
			  <label for="opasswd" class="control-label col-md-3"><font color="#FF0000">*</font>Notifications: &nbsp;</label>
			  <div class="col-md-4">
			    <input type="text" class="form-control logintextbox-bg required" name="notifications" id="step1Title" value="%NOTIFICATIONS%">
			  </div>
			</div>

			<div class="form-group">
			  <label for="opasswd" class="control-label col-md-3"><font color="#FF0000">*</font>Favorite Categories: &nbsp;</label>
			  <div class="col-md-4">
			    <input type="text" class="form-control logintextbox-bg required" name="favoriteCate" id="step1Title" value="%FAV_CATE%">
			  </div>
			</div>

			<div class="form-group">
			  <label for="opasswd" class="control-label col-md-3"><font color="#FF0000">*</font>Favorite Store: &nbsp;</label>
			  <div class="col-md-4">
			    <input type="text" class="form-control logintextbox-bg required" name="favoriteStore" id="step1Title" value="%FAV_STORE%">
			  </div>
			</div>

			<div class="form-group">
			  <label for="opasswd" class="control-label col-md-3"><font color="#FF0000">*</font>Referral: &nbsp;</label>
			  <div class="col-md-4">
			    <input type="text" class="form-control logintextbox-bg required" name="referral" id="step1Title" value="%REFERRAL%">
			  </div>
			</div>

			<div class="form-group">
			  <label for="opasswd" class="control-label col-md-3"><font color="#FF0000">*</font>My Cart: &nbsp;</label>
			  <div class="col-md-4">
			    <input type="text" class="form-control logintextbox-bg required" name="cart" id="step1Title" value="%MY_CART%">
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