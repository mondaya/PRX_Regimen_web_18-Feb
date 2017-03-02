
<div class="main-bg-heading">
	<div class="common-bg">
		<div class="heading container">
			<div class="user-img">
				<img src="%PROFILEIMG%" alt="user-img" title="%SAL% %FNAME% %LNAME%">
				<h1>%SAL% %FNAME% %LNAME%</h1>
			</div>

		</div>
	</div>
</div>

<div class="main">
	<div class="container">
		<div class="heading-btn">
			<h2>Basic Profile</h2>
			<a href="%EDIT_PROF_URL%" class="btn btn-default blue-btn right-btn" title="Edit Profile"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>Edit Profile</a>
		</div>
		<div class="my-profile col-lg-offset-3">
			<label>Email Address :</label> <span>%EMAIL%</span>
			<label>Member Since :</label> <span>%MEMBER%</span>
			<div class="clearfix"></div>
			<label>Gender :</label> <span>%GENDER%</span>
			<div class="clearfix"></div>
			<label>Birthday :</label> <span>%BIRTH%</span>
			<div class="clearfix"></div>
			<label>Address :</label> <span>%ADDRESS%</span>
			<div class="clearfix"></div>
			<label>City :</label> <span>%CITY%</span>
			<div class="clearfix"></div>
			<label>State : </label> <span>%STATE%</span>
			<div class="clearfix"></div>
			<label>Country :</label> <span>%COUNTRY%</span>
			<div class="clearfix"></div>
			<label>Zip Code :</label> <span>%ZIP%</span>
			<div class="clearfix"></div>
			<label>Mobile Number :</label> <span>%COUNTRY_CODE% %MOBILE%</span>
			<div class="clearfix"></div>
			<label>Paypal Email :</label> <span>%PMAIL%</span>
			<div class="clearfix"></div>
		</div>

		<div class="black-border"></div>
		<div class="upcoming-reminders">
			<div class="heading-btn">
				<h2>Reminders</h2>
				<a href="%MODEL_PATH%" title="Add New" class="btn btn-default blue-btn right-btn load-myModal" data-toggle="modal" data-target="#temp-model" data-title="Set Reminder">Add New</a>
			</div>
			<div id="reminder-content">
				%REMINDERS%
			</div>
		</div>
	</div>
</div>


<div class="modal fade" id="about-edit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h2>Set Reminder</h2>
				<button type="button"  class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times"></i>

				</span></button>
			</div>

			<div class="modal-body">
				<form class="form-horizontal popup-form">

					<div class="form-group">
						<input class="form-control comment" value="%REMINDER_COMMENT%" id="reminder-comment" placeholder="Reminder note" type="text">
					</div>

					<div class="form-group">
						<input type="text" value="%REMINDER%" id="reminder" name="reminder" class="form-control comment">
						<i class="fa fa-calendar" aria-hidden="true"></i>

					</div>


				</form>
			</div>

			<div class="clearfix"></div>
			<div class="modal-footer">
				<button type="button" id="btn_set_reminder" class="btn btn-default blue-btn">Save</button>
				<button type="button" class="btn btn-default blue-btn" class="close" data-dismiss="modal" aria-label="Close">Cancel</button>
			</div>
		</div>
	</div>
</div>
<script>
	$(document).ready(function() {

		$(document).ajaxStart(function(e) { $('.loading').fadeIn(); });
		$(document).ajaxComplete(function( event,request, settings ) { $('.loading').fadeOut(); });

		$(document).on('click', '.remove_reminder', function() {

			var con = confirm("Are you sure to delete this reminder?");

			if(con == true){

	            var _this = $(this);
	            var id = _this.data('id');
	            if(id && id!='') {
	                $.ajax({
	                    url: '<?php echo SITE_MOD.$this->module."/ajax.".$this->module.".php"; ?>',
	                    type: 'POST',
	                    dataType: 'json',
	                    data: {action: 'delReminder', id: id}
	                })
	                .done(function(data) {
	                    if(data && data['result']=='1') {
	                        location.reload();
	                    } else {
	                        toastr['error']('Something went wrong');
	                    }
	                });
	            }
            }else{
            	return false;
            }
        });
	});
</script>