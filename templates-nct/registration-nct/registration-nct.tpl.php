<div class="main-bg-heading">
	<div class="common-bg">
		<div class="heading container">
			<div class="col-lg-4">
				<div class="border"></div>
			</div>
			<div class="col-lg-4">
				<h1>Registration</h1>
			</div>
			<div class="col-lg-4">
				<div class="border"></div>
			</div>
		</div>
	</div>
</div>

<div class="main">
	<form name="frmRegi" id="frmRegi" method="POST">
		<div class="container">
			<h3>Tell Us About Yourself... </h3>
			<div class="col-lg-9 col-lg-offset-3">
			<div class="form-inline">
				<div class="form-group col-lg-1">
					<select id="salute" name="salute">
					 	<option value="mr">Mr.</option>
					 	<option value="mrs">Mrs.</option>
					 	<option value="ms">Miss.</option>
					 	<option value="dr">Dr.</option>
					</select>
				</div>
				<div class="form-group col-lg-3 name-group">
					<input class="form-control reg-control" name="firstName" id="firstName" placeholder="First Name*" type="text">
				</div>
				<div class="form-group col-lg-3 name-group">
					<input class="form-control reg-control" name="lastName" id="lastName" placeholder="Last Name*" type="text">
				</div>
			</div>
			<div class="clearfix"></div>
			<div class="form-inline">
				<div class="form-group col-lg-3 emali-group">
					<input class="form-control reg-control" name="email" id="email" placeholder="Email*" type="email">
				</div>
				<div class="form-group col-lg-1 country-group">
					<input class="form-control country-code" name="countryCode" id="countryCode" placeholder="Code*" type="text">
				</div>				
				<div class="form-group col-lg-3 name-group">
					<input class="form-control regmob-control" name="mobile" id="mobile" placeholder="Mobile no.*" type="text">
				</div>
			</div>
			<div class="clearfix"></div>

			<div class="form-inline">
				<div class="form-group col-lg-4">
					<textarea class="form-control comment" rows="3" name="address" id="address" placeholder="Address" id="comment"></textarea>
				</div>
			</div>
			<div class="clearfix"></div>

			<div class="form-inline full-width-text">
				<div class="form-group col-lg-4">
					<select class="gender" name="gen" id="gen">
						<option value="">Gender*</option>
						<option value="m">Male</option>
						<option value="f">Female</option>
					</select>
				</div>
				<div class="form-group col-lg-4">
					<input class="form-control"  name="secret" id="secret" placeholder="Secret Word*" type="text">
				</div>
			</div>
			<div class="clearfix"></div>

			<div class="form-inline">
				<div class="form-group col-lg-4">
					<input class="form-control comment" name="paypalEmail" id="paypalEmail" placeholder="Paypal Email" type="text">
				</div>
			</div>
			<div class="clearfix"></div>

			<div class="form-inline full-width-text">
				<div class="form-group col-lg-4">
					<input class="form-control" name="password1" id="password1" placeholder="Password*" type="password">
				</div>
				<div class="form-group col-lg-4">
					<input class="form-control" name="cpassword" id="cpassword" placeholder="Confirm Password*" type="password">
				</div>
			</div>
			<div class="clearfix"></div>

			<div class="form-inline full-width-text">
				<div class="form-group col-lg-4">
					<select name="country" id="country" class="gender" >
						%OPT_COUNTRY%
					</select>
				</div>
				<div class="form-group col-lg-4">
					<select name="state" id="state" class="gender" >
						%OPT_STATE%
					</select>
				</div>
			</div>
			<div class="clearfix"></div>

			<div class="form-inline full-width-text">
				<div class="form-group col-lg-4">
					<select name="city" id="city" class="gender" >
						%OPT_CITY%
					</select>
				</div>
				<div class="form-group col-lg-4">
					<input class="form-control"  name="zip" id="zip" placeholder="Zip code" type="text">
				</div>
			</div>
			<div class="clearfix"></div>

			<div class="form-inline">
				<div class="form-group col-lg-3">
					<img id="imgCaptcha" src="{SITE_INC}captcha-nct/random.php" class="captcha_img" height="35" alt="Captcha Code" title="Captcha Code" />
				</div>
				<div class="form-group col-lg-1 refresh">
					<?php echo  '<a href="javascript:void(0)" onclick="document.getElementById(\'imgCaptcha\').src=\''.SITE_INC.'captcha-nct/random.php?\'+Math.random();$(\'#captcha\').focus();$(\'#captcha\').val(\'\');" id="change-image" >
					<img src="{SITE_IMG}/12.png" alt="Refresh Captcah" title="Refresh Captcah" /></a>';?>
					<input class="form-control" placeholder="" type="text" readonly="" disabled="" />
				</div>

				<div class="form-group col-lg-3">
					<input class="form-control captcha-control" name="code" id="code" placeholder="Enter captcha*" type="text">
				</div>
			</div>
			</div>
			<div class="clearfix"></div>

			<div class="form-inline">
				<input type="hidden" name="action" name="action" value="sbt_regi" />
				<input type="submit"  name="sbtRegi" id="sbtRegi" class="btn btn-default blue-btn" value="Register" title="Register"/>
				<a name="sbtCancel" class="btn btn-default blue-btn" title="Cancel" href="{SITE_URL}">Cancel</a>
			</div>
	</div>
	</form>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		$(document).on("change","#country",function(){
			$("#state").html('<option value="">--Please Select State*--</option>');
			$("#city").html('<option value="">--Please Select City*--</option>');

			var cId = $(this).val();
			if(cId > 0){
				$.ajax({
					url: AJAX_URL,
					type: 'POST',
					dataType: 'json',
					data: {action: 'getStates',cId: cId},
					success:function(response){
						$("#state").html(response.states);
					}
				});
			}
		});

		$(document).on("change","#state",function(){
			var sId = $(this).val();
			$("#city").html('<option value="">--Please Select City*--</option>');
			if(sId > 0){
				$.ajax({
					url: AJAX_URL,
					type: 'POST',
					dataType: 'json',
					data: {action: 'getCities',sId: sId},
					success:function(response){
						$("#city").html(response.cities);
					}
				});

			}


			jQuery.validator.addMethod("lettersonly", function(value, element) {
				return this.optional(element) || /^[a-z]+$/i.test(value);
			}, "Only Alphabets allowed. ");

			jQuery.validator.addMethod("digitsonly", function(value, element) {
				return this.optional(element) || /^[0-9]+$/i.test(value);
			}, "Only Digits allowed. ");

			$.validator.addMethod("nowhitespace", function(value, element) {
			    return this.optional(element) || /^\S+$/i.test(value);
			}, "No white space allow");
		});

		$("#frmRegi").validate({
			errorClass: 'help-block',
			errorElement: 'label',
			rules: {
				firstName: { required: true },
				lastName: { required: true },
				email : { required: true, email: true, remote: '<?php echo SITE_MOD.$this->module.'/ajax.'.$this->module.'.php'; ?>' },
				mobile: { required: true, digits: true, minlength:10 },
				password1: { required: true, minlength: 6 },
				cpassword: { required: true, minlength: 6, equalTo: "#password1" },
				gen: { required: true },
				secret: { required: true,nowhitespace:true },
				countryCode: { required: true ,number:true},
				country: { required: true },
				state: { required: true },
				city: { required: true },
				zip: { number: true},
				code: { required: true, remote: '<?php echo SITE_MOD.$this->module.'/ajax.'.$this->module.'.php'; ?>' }
			},
			messages: {
				firstName: {
					required: "Please Enter FirstName"
				},
				lastName: {
					required: "Please Enter Lastname"
				},
				email: {
					required: "Please enter your email address",
					email: "Please enter valid email address",
					remote: "Email already exists"
				},
				password1: {
					required: "Please enter password",
					minlength: "Minimum 6 characters"
				},
				cpassword: {
					required: "Please re-enter password",
					equalTo: "Passwords doesnt match",
					minlength: "Minimum 6 characters"
				},
				country: {
					required: "Please select Country"
				},
				countryCode: {
					required: "Please enter country code",number:"Number only"
				},
				state: {
					required: "Please select State"
				},
				city: {
					required: "Please select City"
				},
				gen: {
					required: "please select gender"
				},
				mobile: {
					required: "Please enter your mobile number",
					minlength: "Minimum 10 numbers"
				},
				code: {
					required: "please enter captcha",
					remote: "Please enter valid captcha code"
				},
				secret: {
					required: "please enter secret word"
				},
				zip: {},
			},
			errorPlacement: function (error, element) {
				error.insertAfter(element);
			}
		});
	});
</script>