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
                <div class="actions portlet-toggler">
                <?php if(in_array('add',$this->Permission)){?>
                    <a href="ajax.<?php echo $this->module;?>.php?action=add" class="btn blue btn-add"><i class="fa fa-plus"></i> Add</a>
                <?php } ?>
                
                <div class="btn-group"></div>
                </div>
            </div>

            <!-- filter menu start-->
            <div class="portlet-body portlet-toggler">
                <div class="col-md-3">
                    <label>Category :</label>&nbsp;
                    <select id="category" name="category" class="form-control">
                    </select>
                </div>

                
                <div class="col-md-3">
                    <label>Sub Category :</label>&nbsp;
                    <select id="subcategory" name="subcategory" class="form-control">
                    </select>
                </div>
                
               	<div style="margin-bottom:10px; clear:both;"></div>
                <div class="clear"></div>            
            </div>
            <!-- filter menu start-->

            <div class="portlet-body portlet-toggler">
                <table id="example123" class="table table-striped table-bordered table-hover"></table>
            </div>
            <div class="portlet-body portlet-toggler pageform" style="display:none;"></div>
        </div>
    </div>
</div>
<script type="text/javascript">
$(function() {
	OTable= $('#example123').dataTable( {
		bProcessing: true,
		bServerSide: true,
		sAjaxSource: "ajax.<?php echo $this->module;?>.php",
		fnServerData: function (sSource, aoData, fnCallback) {
			$.ajax({
			   dataType: 'json',
			   type: "POST",
			   url: sSource,
			   data: aoData,
			   success: fnCallback
			});
		 },
		 aoColumns: [
		 	{ sName: "productName", sTitle : 'Product Name'},
			{ sName: "categoryName", sTitle : 'Category Name'},
			{ sName: "subcategoryName", sTitle : 'Sub Category Name'},
			{ sName: "actualPrice", sTitle : 'Product Price'}
			<?php if(in_array('status',$this->Permission)){ ?>
			,{ "sName": "isActive", 'sTitle' : 'Status' ,bSortable:false,bSearchable:false}
			<?php } ?>			
			<?php if(in_array('edit',$this->Permission) || in_array('delete',$this->Permission) || in_array('view',$this->Permission) ){ ?>
				,{ "sName": "operation", 'sTitle' : 'Operation' ,bSortable:false,bSearchable:false}
				<?php } ?>
		],
		fnServerParams: function(aoData){setTitle(aoData, this)
			var category = $("#category").val();
			if(category !=''){ aoData.push({ "name": "category", "value": category}); }

			var subcategory = $("#subcategory").val();
			if(subcategory !=''){ aoData.push({ "name": "subcategory", "value": subcategory}); }
		},
		fnDrawCallback: function( oSettings ) {
			$('.make-switch').bootstrapSwitch();
			$('.make-switch').bootstrapSwitch('setOnClass', 'success');
			$('.make-switch').bootstrapSwitch('setOffClass', 'danger');

		}		
	});
	$('.dataTables_filter').css({float:'right'});
	$('.dataTables_filter input').addClass("form-control input-inline");

	var oTable = $('#example123').dataTable();
	$('#category,#subcategory').on('change', function(e){
		oTable.fnDraw();
	});

	$.ajax({
         url:"<?php echo SITE_ADM_MOD . $this->module ?>/ajax.products-nct.php",
         type:"post",
         dataType:"json",
         data: {action:'categoryData'},
         success: function(data){
          $('#category').html('<option value="">Select category</option>'); 
          $('#category').append(data);
          //$('#f_location').html(data); 
         }
    });

    $.ajax({
         url:"<?php echo SITE_ADM_MOD . $this->module ?>/ajax.products-nct.php",
         type:"post",
         dataType:"json",
         data: {action:'subcateData'},
         success: function(data){
          $('#subcategory').html('<option value="">Select sub category</option>'); 
          $('#subcategory').append(data);
          //$('#f_location').html(data); 
         }
    });

    $('#category').change(function() {
        var categoryId = $('#category').val();
		if(category != ''){
			$.ajax({
		         url:"<?php echo SITE_ADM_MOD . $this->module ?>/ajax.products-nct.php",
		         type:"post",
		        // dataType:"json",
		         data: {action:'changeSubcate','category':categoryId},
		         success: function(dataSubcate){
		          $('#subcategory').empty(); 	
		          $('#subcategory').append(dataSubcate);
		         }
		    });   
		}     
    }); 

	$.validator.addMethod('pagenm',function (value, element) { 
		return /^[a-zA-Z0-9][a-zA-Z0-9\-\_]*$/.test(value); 
		},'Page name is not valid. Only alphanumeric and -,_ are allowed'
	);
	$(document).on('submit','#frmCont', function(e){
		$("#frmCont").on('submit', function() {
			for(var instanceName in CKEDITOR.instances) {
				CKEDITOR.instances[instanceName].updateElement();
			}
		})
		$.validator.addMethod('lessThan', function(value, element, param) {
		    return this.optional(element) || parseInt(value) < parseInt($(param).val());
		}, "Discount price must be less than price");
		$("#frmCont").validate({
			ignore:[],
			errorClass: 'help-block',
			errorElement: 'span',
			rules:{
				productName: {required: true},                    
				categoryId: {required: true},
				subcategoryId: {required: true},
				quantity: {required: true},
				productDescription: {required: true},
				actualPrice: {required: true},
				discountPrice: {
					required:{
						depends: function() {
			                    return $('input[name=isDiscount]:checked').val() == 'y';
			                }
			            },
					lessThan:'#actualPrice'
				},
				weight: {required: true}
			},
			messages:{
				productName: {required: "&nbsp;Please enter product name"},
				categoryId: { required: "&nbsp;Please select category"},
				subcategoryId: { required: "&nbsp;Please select sub category"},
				quantity: { required: "&nbsp;Please enter quantity"},
				productDescription: { required: "&nbsp;Please enter description"},
				actualPrice: { required: "&nbsp;Please enter price"},
				discountPrice: { required: "&nbsp;Please enter discount price"},
				weight: { required: "&nbsp;Please enter weight"}
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
