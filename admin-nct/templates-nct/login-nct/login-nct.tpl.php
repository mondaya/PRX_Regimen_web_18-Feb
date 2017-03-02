
<!-- BEGIN LOGO -->
<div class="logo">
	<a href="javascript:void(0);" class="logotext">
		<?php echo SITE_NM; ?>
	</a>
</div>
<!-- END LOGO -->
<!-- BEGIN LOGIN -->
<div class="content pre-load" style="display:none">
	<!-- BEGIN LOGIN FORM -->
	<form class="login-form" name="loginform" action="" method="post">
		<?php //echo $this->objUser->getForm();?>
        <h3 class="form-title">Login to your account</h3>
		<div class="alert alert-danger display-hide">
			<button class="close" data-close="alert"></button>
			<span>
				 Enter any username and password.
			</span>
		</div>
		<div class="form-group">
			<!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
			<label class="control-label ">Username</label>
			<div class="input-icon">
				<i class="fa fa-user"></i>
				<input class="form-control placeholder-no-fix" type="text" autocomplete="off" name="uName" value="%EMAIL%"/>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label ">Password</label>
			<div class="input-icon">
				<i class="fa fa-lock"></i>
				<input class="form-control placeholder-no-fix" type="password" autocomplete="off"  name="uPass" value="%PASSWORD%"/>
			</div>
		</div>
		<div class="form-actions">
			<input type="checkbox" tabindex="3" class="" name="remember" id="remember" value="y" %CHECKED%>
            <label for="remember"> Remember Me</label>
			<input type="hidden" name="submitLogin" value="submit">
			<button type="submit" name="submitLogin" class="btn green pull-right">
			Login <i class="m-icon-swapright m-icon-white"></i>
			</button>
		</div>
        <div class="forget-password">
			<h4>Forgot your password ?</h4>
			<p>
				 no worries, click
				<a href="javascript:void(0);" id="forget-password">
					 here
				</a>
				 to reset your password.
			</p>
		</div>
	</form>
	<!-- END LOGIN FORM -->
	<!-- BEGIN FORGOT PASSWORD FORM -->
	<form class="forget-form" action="" method="post">
		<?php //echo $this->objUser->forgotPassword_form(); ?>
        <h3>Forget Password ?</h3>
		<p>
			 Enter your e-mail address below to reset your password.
		</p>
		
		<div class="form-group">
			<div class="input-icon">
				<i class="fa fa-envelope"></i>
				<input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Email" name="uEmail"/>
			</div>
		</div>
		<div class="form-actions">
			<button type="button" id="back-btn" class="btn">
			<i class="m-icon-swapleft"></i> Back </button>
			<button type="submit" name="submitEmail" class="btn green pull-right">
			Submit <i class="m-icon-swapright m-icon-white"></i>
			</button>
		</div>
	</form>
	<!-- END FORGOT PASSWORD FORM -->
</div>
<!-- END LOGIN -->
<div class="pre-load load-img">
    <img src="<?php echo SITE_ADM_IMG;?>Gears.gif" alt="Loading...." width="200"/>
</div>
<script type="text/javascript">
$(function(){
	$('.pre-load').toggle();
});
</script>
