<!DOCTYPE html>
<html lang="en">
	<head>
		%HEAD%

	</head>
	<body>
            <div class="header-wrapper">
                <div class="header">
			%SITE_HEADER%
			%BODY%
			%FOOTER%
		</div>
                
            </div>
        <script src="{SITE_JS}script.js"></script>
		<script src="{SITE_JS}jquery.validate.min.js" type="text/javascript"></script>
		<script type="text/javascript" src="{SITE_JS}additional-methods.min.js"></script>
		<script type="text/javascript" src="{SITE_JS}toastr.min.js"></script>
		<script src="{SITE_JS}nct-bootstrap.min.js" type="text/javascript"></script>
		<script src="{SITE_JS}oauthpopup.js" type="text/javascript" ></script>
		<script type="text/javascript" src="{SITE_JS}bootstrap-multiselect.js"></script>
		

		
		<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,300,300italic,400italic,600,600italic,700,800,700italic,800italic" rel="stylesheet" type="text/css" />
		<link type="text/css" rel="stylesheet" href="{SITE_CSS}nct-custom.css" />
		<link type="text/css" rel="stylesheet" href="{SITE_CSS}toggle.css" />
		<link type="text/css" rel="stylesheet" href="{SITE_CSS}toastr.min.css"/>
		<link rel="shortcut icon" type="image/ico" href="{SITE_IMG}{SITE_FAVICON}">
		
		%EXTRA_JS%
		<script language="javascript" type="text/javascript">
			function dispValiMsg(msg){
				return msg+" {IS_REQUIRED}";
			}

			var SITE_URL = '{SITE_URL}';
			var SITE_LOGIN = '{SITE_URL}login';
			var SITE_MOD = '{SITE_MOD}';
			var SITE_NM = '{SITE_NM}';
			var MODULE = '<?php echo $this->module; ?>';
			var AJAX_URL = '{SITE_URL}ajax-'+MODULE;
			var SITE_ADM_MOD = '{SITE_ADM_MOD}';
			var fb_appid = '{FB_APP_ID}';

			window.fbAsyncInit = function() {
				FB.init({
					appId: fb_appid,
					status: true,
					cookie: true,
					xfbml: true
				});
			};

			function confirm_del(content) { return confirm('Are you sure to delete '+content+'?'); };

			function isNumberKey(evt) {
				var charCode = (evt.which) ? evt.which : evt.keyCode;
				// Added to allow decimal, period, or delete
				if (charCode == 110 || charCode == 190 || charCode == 46)  return true;
				if (charCode > 31 && (charCode < 48 || charCode > 57)) return false;
				return true;
			}

			toastr.options = {
				"closeButton": true,
				"debug": false,
				"positionClass": "toast-top-full-width",
				"onclick": null,
				"showDuration": "300",
				"hideDuration": "1000",
				"timeOut": "3000",
				"extendedTimeOut": "1000",
				"showEasing": "swing",
				"hideEasing": "linear",
				"showMethod": "fadeIn",
				"hideMethod": "fadeOut"
			}

			$(document).ready(function () {
				$(document).on('click', '.load-myModal', function () {
					var _this = $(this);
					var pUrl = _this.attr('href');
					var title = _this.data('title');
					$('.loading').fadeIn();
					$.ajax({
						type: 'get',
						url: pUrl,
						cache: false,
						success: function (r) {
							$('#popup_title').empty().text(title);
							$('#popup-body').empty();
							$('#popup-body').html(r);
							$("#bootstrap-myModal").modal('show');
						},
						complete: function() {
							$('.loading').fadeOut();
						}
					});
				});

				$("#frmSubscribe").validate({
					errorClass: 'help-block',
					errorElement: 'label',
					rules: { subEmail: { required: true, email: true } },
					messages: { subEmail: { required: 'Please enter email address', email: 'Please enter valid email address' } },
					errorPlacement: function (error, element) {
						error.insertAfter(element.parent().after());
					}
				});

				$('#login_dropdown').on('hide.bs.dropdown', function (e) {
					e.stopPropagation();
					$('#login_div').show();
					$('#forgot_div').hide();
				});

				$('#forgotpass_btn').click(function() {
					$('#login_div').hide();
					$('#forgot_div').show();
				});

				$('#go_back_btn').click(function() {
					$('#login_div').show();
					$('#forgot_div').hide();
				});

				$('.menuTrigger').click(function () {
					$('.panel').toggleClass('isOpen');
					$('.wrapper').toggleClass('pushed');
				});

				$('.openSubPanel').click(function () {
					$(this).next('.subPanel').addClass('isOpen');
				});

				$('.closeSubPanel').click(function () {
					$('.subPanel').removeClass('isOpen');
				});

				$('.closePanel').click(function () {
					$('.panel').removeClass('isOpen');
					$('.wrapper').removeClass('pushed');
				});

				//Handles menu drop down
				$('.dropdown-menu').find('form').click(function (e) {
					e.stopPropagation();
				});

				$('#Carousel').carousel({
					interval: 5000
				});

				$(".box-height").click(function () {
					$(".category-list").toggleClass("full-height");
					if($(".box-height i").hasClass('fa-angle-down')) {
						$(".box-height i").attr('class', 'fa fa-angle-up arrow-btn')
					} else {
						$(".box-height i").attr('class', 'fa fa-angle-down arrow-btn')
					}
				});

				$.validator.addMethod("nowhitespace", function(value, element) {
	                return this.optional(element) || /^\S+$/i.test(value);
	            }, "No white space allow");

				$("#frmLogin").validate({
					errorClass: 'help-block',
					errorElement: 'label',
					rules: {
						username : { required: true, email: true },
						password : { required: true },
						secret : { required: true ,nowhitespace:true}
					},
					messages: {
						username : { required: "Please enter email", email: "Please enter valid email address" },
						password : { required: "Please enter password"},
						secret : { required: "Please enter secret"}
					},
					errorPlacement: function (error, element) {
						error.insertAfter(element);
						// error.insertAfter(element.parent().after());
					}
				});

				$("#frmForgot").validate({
					errorClass: 'help-block',
					errorElement: 'label',
					rules:{
						forgot_username:{required:true,email:true},
					},
					messages:{
						forgot_username:{required:"Please Enter Email",email:"Enter valid email address"},
					}
				});

				$(document).on('click', '#btn_login', function(e) {
					$('.panel').removeClass('isOpen');
					$('.wrapper').removeClass('pushed');
					e.stopPropagation();
					$('#login_dropdown_link').dropdown('toggle');
				});

				$('#login_fb').oauthpopup({
					path: '<?php echo SITE_SOCIAL."login.php?facebook"; ?>',
					width:600,
					height:300
				});

				$('#login_google').oauthpopup({
					path: '<?php echo SITE_SOCIAL."login.php?google"; ?>',
					width:600,
					height:300
				});

				$(".arrow-up").click(function() {
	                $(".toggle-ul").toggleClass("full-height");
	                $(".slide-down").toggleClass("slide-up");
	            });
			});

			//For IE 9 placeholder issue
			/*$(function() {
		     if(!$.support.placeholder) {
		          var active = document.activeElement;
		          $('textarea').each(function(index, element) {
		           if($(this).val().length == 0) {
		               $(this).html($(this).attr('id')).addClass('hasPlaceholder');
		               }
		        });
		          $('input, textarea').focus(function () {
		               if ($(this).attr('placeholder') != '' && $(this).val() == $(this).attr('placeholder')) {
		                    $(this).val('').removeClass('hasPlaceholder');
		               }
		          }).blur(function () {
		               if (($(this).attr('placeholder') != '' && ($(this).val() == '' || $(this).val() == $(this).attr('placeholder')))) {
		                    //$(this).val($(this).attr('placeholder')).addClass('hasPlaceholder');
		                    //$(this).css('background', 'red');
		               }
		          });
		          $(':text').blur();
		          $(active).focus();
		          $('form').submit(function () {
		               $(this).find('.hasPlaceholder').each(function() { $(this).val(''); });
		          });
		     }
		});*/
		
		</script>
		%MESSAGE_TYPE%

		<!-- empty popup -->
		<div class="modal fade in" id="bootstrap-myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		    <div class="modal-dialog" role="document">
		        <div class="modal-content">
		            <div class="modal-header">
		                 <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            			 <h2>Set Reminder</h2>
		            </div>
		            <div class="modal-body fav-cate" id="popup-body">
		            </div>
		            <div class="clearfix"></div>
		            <!--  -->
		        </div>
		    </div>
		</div>
		<!-- empty popup -->
	</body>
</html>