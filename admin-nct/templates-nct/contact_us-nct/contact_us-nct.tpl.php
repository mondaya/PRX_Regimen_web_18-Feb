<script type="text/javascript">
	$(document).on('click','.btn-sendbtn',function(e){
        e.preventDefault();
        var $this = $(this);
        var viewLink = $this.attr('href');
        var PageTitle = $this.attr('data-page_title');
        PageTitle = (PageTitle!=null)?PageTitle:'user';
        $(".modal-title").html('Reply to '+PageTitle);
        $(".modal-body").html('<div class="popup-loader"><img src="<?php echo SITE_ADM_IMG;?>ajax-loading.gif" align="middle" /></div>');
        $("#myModal2_autocomplete").modal();
        $.get(viewLink,function(r){$("#myModal2_autocomplete .modal-body").html(r);});
    });

    $(function() {
	  	OTable = $('#example123').dataTable( {
			"bProcessing": true,
			"bServerSide": true,
			"sAjaxSource": "ajax.<?php echo $this->module;?>.php",
			"fnServerData": function (sSource, aoData, fnCallback) {
				$.ajax({
				   "dataType": 'json',
				   "type": "POST",
				   "url": sSource,
				   "data": aoData,
				   "success": fnCallback
				});
			 },
			 "aoColumns": [
				{ "sName": "first_name", 'sTitle' : 'Name'},
				{ "sName": "email", 'sTitle' : 'Email'},
				{ "sName": "c.country_name", 'sTitle' : 'Location', bSortable: false},
				{ "sName": "subject", 'sTitle' : 'Subject'},
				{ "sName": "message", 'sTitle' : 'Description'}
				<?php if(in_array('edit',$this->Permission) || in_array('delete',$this->Permission) || in_array('view',$this->Permission) ){ ?>
					,{ "sName": "operation", 'sTitle' : 'Operation' ,bSortable:false,bSearchable:false}
				<?php } ?>
			],
			"fnServerParams": function(aoData){setTitle(aoData, this);},
			"fnDrawCallback": function( oSettings ) {
			}
   		});
		$('.dataTables_filter').css({float:'right'});
		$('.dataTables_filter input').addClass("form-control input-inline");

		$(document).on('submit','#frmCont', function(e){
			$("#frmCont").validate({
				ignore:[],
				errorClass: 'help-block',
				errorElement: 'span',
				rules: {
					reply_user: { required: true }
				},
				messages:{
					reply_user: { required: 'Please enter your reply' }
				},
	            errorPlacement: function (error, element) {
					if (element.attr("data-error-container")) {
						error.appendTo(element.attr("data-error-container"));
					} else {
						error.insertAfter(element);
					}
	            },
	            highlight: function (element) {
				   $(element).closest('.form-group').addClass('has-error');
				},
				unhighlight: function (element) {
					$(element).closest('.form-group').removeClass('has-error');
				}
			});
			if($("#frmCont").valid()){
				return true;
			}else{
				return false;
			}
		});
	});
</script>
 <!-- BEGIN PAGE HEADER-->
    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN PAGE TITLE & BREADCRUMB-->
           	<?php
				echo $this->breadcrumb;
			?>
            <!-- END PAGE TITLE & BREADCRUMB-->
        </div>
    </div>
    <!-- END PAGE HEADER-->
<div class="row">
    <div class="col-md-12">
    <!-- Begin: life time stats -->
        <div class="portlet box blue-dark">
            <div class="portlet-title ">
                <div class="caption">
                	<i class="fa fa-dot-circle-o"></i><?php echo $this->headTitle; ?>
                </div>
                
            </div>

            <!-- filter menu start-->
            <div class="portlet-body portlet-toggler">
            </div>
            <!-- filter menu start-->

            <div class="portlet-body portlet-toggler">
                <table id="example123" class="table table-striped table-bordered table-hover"></table>
            </div>
            <div class="portlet-toggler pageform" style="display:none;"></div>
        </div>
    </div>
</div>
<div class="modal fade" id="myModal2_autocomplete" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        	<form method="POST" id="frmCont" name="frmCont">
	            <div class="modal-header">
	              <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
	              <h4 class="modal-title"></h4>
	            </div>
	            <div class="modal-body">
	            </div>
	            <div class="modal-footer">
	            	<button type="submit" id="submitAddForm" name="submitAddForm" class="btn green default">Send</button>
	                <button type="button" class="btn default" data-dismiss="modal">Close</button>
				</div>
        	</form>
        </div>
    </div>
</div>