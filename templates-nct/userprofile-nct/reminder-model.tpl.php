<form class="form-horizontal popup-form reminder-form" id="frmReminder" name="frmReminder" method="POST">
    <div class="form-group">
        <input class="form-control comment" id="reminder_title" name="reminder_title" placeholder="Reminder title" type="text" value="%REMINDER_TITLE%" />
    </div>
    <div class="form-group">
        <input type="text" id="reminder_date" name="reminder_date" placeholder="Reminder date" class="form-control comment datepicker" value="%REMINDER_NOTE%" />
        <i class="fa fa-calendar" aria-hidden="false"></i>
    </div>
    <!-- setReminder -->
    <input type="hidden" name="action" id="action" value="%REM_ACTION%" />
    <input type="hidden" name="id" id="id" value="%REM_ID%" />

    <div class="modal-footer">
        <button type="submit" class="btn btn-default blue-btn">Save</button>
        <button type="button" class="btn btn-default blue-btn" class="close" data-dismiss="modal" aria-label="Close">Cancel</button>
    </div>
</form>
<script type="text/javascript">
	$(document).ready(function() {
        var date = new Date();
        date.setDate(date.getDate()-1);
		$(".datepicker").datepicker({ dateFormat: 'yy-mm-dd', minDate: '1' });
	});

	$("#frmReminder").validate({
        errorClass: 'help-block',
        errorElement: 'label',
        rules: {
            reminder_title: { required: true },
            reminder_date: { required: true }
        },
        messages: {
            reminder_title: { required: "Please enter reminder title" },
            reminder_date: { required: "Please select reminder date" }
        },
        errorPlacement: function (error, element) {
            if (element.attr("name") == "reminder_date" ){
                error.insertAfter(".fa-calendar");
            }else{
                error.insertAfter(element);
            }
        }
    });
</script>