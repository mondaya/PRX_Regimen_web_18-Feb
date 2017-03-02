<script type="text/javascript">
$(document).on('submit','#frmSS', function(e){
		$("#frmSS").validate({
			ignore:[],
			errorClass: 'help-block',
			errorElement: 'span',
			rules:{
				3: {email: true},
				20: {email: true},
				22: {email: true},
				32: {email: true},
				23: {number: true},
				35: {number: true}
			},
			messages:{
				3: {email: "&nbsp;Please enter valid email address"},
				20: {email: "&nbsp;Please enter valid email address"},
				22: {email: "&nbsp;Please enter valid email address"},
				32: {email: "&nbsp;Please enter valid email address"},
				23: {email: "&nbsp;Please enter number only"},
				35: {email: "&nbsp;Please enter number only"}
			},
            highlight: function (element) {
			   $(element).closest('.form-group').addClass('has-error');
			},
			unhighlight: function (element) {
				$(element).closest('.form-group').removeClass('has-error');
			},
			errorPlacement: function (error, element) { 
				if (element.attr("data-error-container")) { 
					error.appendTo(element.attr("data-error-container"));
				} else {
					error.insertAfter(element);
				}
            }
		});
		if($("#frmSS").valid()){
			return true;
		}else{
			return false;
		}
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
    <div class="portlet box blue-dark">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-reorder"></i><?php echo $this->headTitle; ?>
            </div>
            <div class="tools">
                <a href="javascript:void(0);" class="collapse"></a>
                <a href="javascript:void(0);" class="remove"></a>
            </div>
        </div>
        <div class="portlet-body form">
        <form action="<?php echo $_SERVER["PHP_SELF"];?>" method="post" name="frmSS" id="frmSS" class="form-horizontal" enctype="multipart/form-data">
		<div class="form-body">
			<?php echo $this->getForm;?>
           </div>	
		</form> 
        </div>
     </div>   
	</div>
</div>    	