<script type="text/javascript">
    $(function() {
		$(document).on('submit','#frmCP', function(e){
		$("#frmCP").on('submit', function() {
			for(var instanceName in CKEDITOR.instances) {
				CKEDITOR.instances[instanceName].updateElement();
			}
		})
		$("#frmCP").validate({
			ignore:[],
			errorClass: 'help-block',
			errorElement: 'span',
			errorPlacement: function (error, element) { 
				if (element.attr("data-error-container")) { 
					error.appendTo(element.attr("data-error-container"));
				} else {
					error.insertAfter(element);
				}
            }
		});
		if($("#frmCP").valid()){
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
    <div class="portlet box blue-dark">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-reorder"></i><?php echo $this->headTitle; ?>
            </div>
            <div class="tools">
                <a href="javascript:;" class="collapse"></a>
                <a href="javascript:;" class="remove"></a>
            </div>
        </div>
        <div class="portlet-body form">
            <?php echo $this->getForm;?>
        </div>
     </div>   
	</div>
</div>    	