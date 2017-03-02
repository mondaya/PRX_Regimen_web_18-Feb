    <script type="text/javascript">
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
				{ "sName": "firstName", 'sTitle' : 'First Name'},
				{ "sName": "lastName", 'sTitle' : 'Last Name'},
				{ "sName": "email", 'sTitle' : 'Email',bSortable:false},
				{ "sName": "creditAmount", 'sTitle' : 'Credit Amount'}
				<?php if(in_array('status',$this->Permission)){ ?>
				,{ "sName": "isActive", 'sTitle' : 'Status' ,bSearchable:false},
				{ "sName": "buyStatus", 'sTitle' : 'Purchase Status' ,bSearchable:false}
				<?php } ?>


				
				<?php if(in_array('edit',$this->Permission) || in_array('delete',$this->Permission) || in_array('view',$this->Permission) ){ ?>
				,{ "sName": "operation", 'sTitle' : 'Operation' ,bSortable:false,bSearchable:false}
				<?php } ?>
			],
			"fnServerParams": function(aoData){
				setTitle(aoData, this)
				var country = $("#country").val();
				if(country !=''){ aoData.push({ "name": "country", "value": country}); }

				var state = $("#state").val();
				if(state !=''){ aoData.push({ "name": "state", "value": state}); }

				var city = $("#city").val();
				if(city !=''){ aoData.push({ "name": "city", "value": city}); }
			},
			"fnDrawCallback": function( oSettings ) {
				$('.make-switch').bootstrapSwitch();
				$('.make-switch').bootstrapSwitch('setOnClass', 'success');
				$('.make-switch').bootstrapSwitch('setOffClass', 'danger');

			}
			
   });
	$('.dataTables_filter').css({float:'right'});
	$('.dataTables_filter input').addClass("form-control input-inline"); 

	var oTable = $('#example123').dataTable();
	$('#country,#state,#city').on('change', function(e){
		oTable.fnDraw();
	});

	$.ajax({
         url:"<?php echo SITE_ADM_MOD . $this->module ?>/ajax.users-nct.php",
         type:"post",
         dataType:"json",
         data: {action:'countryData'},
         success: function(data){
          $('#country').html('<option value="">Select country</option>'); 
          $('#country').append(data);
          //$('#f_location').html(data); 
         }
    });

    /*$.ajax({
         url:"<?php echo SITE_ADM_MOD . $this->module ?>/ajax.users-nct.php",
         type:"post",
         dataType:"json",
         data: {action:'stateData'},
         success: function(data){
          $('#state').html('<option value="">Select state</option>'); 
          $('#state').append(data);
          //$('#f_location').html(data); 
         }
    });*/

    /*$.ajax({
         url:"<?php echo SITE_ADM_MOD . $this->module ?>/ajax.users-nct.php",
         type:"post",
         dataType:"json",
         data: {action:'cityData'},
         success: function(data){
          $('#city').html('<option value="">Select city</option>'); 
          $('#city').append(data);
          //$('#f_location').html(data); 
         }
    });*/ 

    $('#country').change(function() {
        var countryId = $('#country').val();
		if(countryId != ''){
			$.ajax({
		         url:"<?php echo SITE_ADM_MOD . $this->module ?>/ajax.users-nct.php",
		         type:"post",
		        // dataType:"json",
		         data: {action:'changeState','country':countryId},
		         success: function(dataState){
		          $('#state').empty(); 	
		          $('#state').append(dataState);
		         }
		    });   
		}     
    });

    $('#state').change(function() {
    	var stateId = $('#state').val();
		if(stateId != ''){
			$.ajax({
		         url:"<?php echo SITE_ADM_MOD . $this->module ?>/ajax.users-nct.php",
		         type:"post",
		        // dataType:"json",
		         data: {action:'changeCity','state':stateId},
		         success: function(dataCity){
		          $('#city').empty();
		          $('#city').append(dataCity);
		         }
		    });   
		}     
    });

	$.validator.addMethod('pagenm',function (value, element) { 
		return /^[a-zA-Z0-9][a-zA-Z0-9\_\-]*$/.test(value); 
		},'Page name is not valid. Only alphanumeric and _ are allowed'
	);
	$.validator.addMethod('notBoth', function (value, element, param) {
	    return this.optional(element) || ($(element).is(':filled') && $('[name="' + param + '"]').is(':blank'));
	}, "you must leave one of from add money or remove money");

	$(document).on('submit','#frmCont', function(e){
		$("#frmCont").on('submit', function() {
			for(var instanceName in CKEDITOR.instances) {
				CKEDITOR.instances[instanceName].updateElement();
			}
		})
		$("#frmCont").validate({
			ignore:[],
			errorClass: 'help-block',
			errorElement: 'span',
			rules:{
				addAmount : {notBoth:'removeAmount'},
				removeAmount : {notBoth:'addAmount'}
			},
			groups: {
	              mygroup: 'addAmount removeAmount'
	          },
			messages:{
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
                <div class="caption"><i class="fa fa-list-alt"></i><?php echo $this->headTitle; ?></div>
                <div class="actions portlet-toggler">

                 <?php
					 	if(in_array('add',$this->Permission)){
					 ?>
<!--                	 <a href="ajax.<?php echo $this->module;?>.php?action=add" class="btn blue btn-add"><i class="fa fa-plus"></i> Add</a>
-->                     <?php } ?>
                    <div class="btn-group"></div>
                </div>
            </div>

            <!-- filter menu start-->
            <div class="portlet-body portlet-toggler">
                <div class="col-md-3">
                    <label>Country :</label>&nbsp;
                    <select id="country" name="country" class="form-control">
                    </select>
                </div>

                
                <div class="col-md-3">
                    <label>State :</label>&nbsp;
                    <select id="state" name="state" class="form-control">
                    	<option value="">Select state</option>
                    </select>
                </div>
                
               	<div class="col-md-3">
                    <label>City :</label>&nbsp;
                    <select id="city" name="city" class="form-control">
                    	<option value="">Select city</option>
                    </select>
                </div>
                
            	<div style="margin-bottom:10px; clear:both;"></div>
                <div class="clear"></div>            
            </div>
            <!-- filter menu start-->

            <div class="portlet-body portlet-toggler">
            	
                <table id="example123" class="table table-striped table-bordered table-hover"></table>
            </div>
            <div class="portlet-toggler pageform" style="display:none;"></div>
        </div>
    </div>
</div>     