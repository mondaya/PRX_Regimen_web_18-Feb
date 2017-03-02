<div class="common-bg">
    <div class="heading container">
        <div class="col-lg-2">

            <div class="border">
            </div>
        </div>
        <div class="col-lg-8">
            <div class="row">
                <h1>Favorite Product Categories</h1></div>
        </div>
        <div class="col-lg-2">

            <div class="border"></div>

        </div>
    </div>
</div>

<div class="main">
    <div class="container">

        <div class="heading-btn">
            <h2>Categories</h2>
            <button type="button" class="btn btn-default blue-btn right-btn" data-toggle="modal" data-target="#about-edit" title="Add New">Add New</button>
        </div>

        <div class="store-list">
            %CATE_LIST%
            %PAGINATION%
        </div>
    </div>
</div>


<div class="modal fade" id="about-edit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
        <form name="frmCate" action="" method="post" id="frmCate">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h2>Set Favorite Categories</h2>
         </div>
         <div class="modal-body fav-cate">
               <div class="form-inline select-stores">
                  <div class="form-group comment">
                     <select id="example-getting-started" multiple="multiple" name="category[]" id="category">
                        %OPTION%
                     </select>
                  </div>
               </div>
               <div class="error"></div>
         </div>
         
         <div class="clearfix"></div>
         <div class="modal-footer">
            <input type="submit" name="submitCate" class="btn btn-default blue-btn" value="Save" title="Save">
            <button type="button" class="btn btn-default blue-btn" class="close" data-dismiss="modal" aria-label="Close" title="Cancel">Cancel</button>
         </div>
        </form>
      </div>
   </div>
</div>

<link type="text/css" rel="stylesheet" href="{SITE_CSS}bootstrap-multiselect.css">
<script type="text/javascript">
    $(document).ready(function(){
    
        $('#example-getting-started').multiselect();
        
        //For paging
        $(document).on("click",".buttonPage",function(){
          var searchText = $('#searchText').val();
          var date = $('#datepicker').val();
          var status = $('#status').val();
          var page = $(this).data("page");
          $.ajax({
                type: 'POST',
                url: '<?php echo SITE_MOD; ?>favoriteCate-nct/ajax.favoriteCate-nct.php',
                data: {page: page,action:'paging'},
                success: function(data) {
                    $('.store-list').html(data);
                    $(window).scrollTop(0);
                }
            });
        });

        //For delete confirmation
        $(document).on("click","#deleteCate",function(){
            
            if (confirm('Are you sure to remove this category?')) {
                return true;
            }else{
                return false;
            }

        });

        $("#frmCate").validate({
           errorClass: 'help-block',
           errorElement: 'label',
           rules: {
              'category[]': { required: true }
           },
           messages: {
              'category[]': {
                 required: "Please select category"
              }
           },
           errorPlacement: function (error, element) {
              error.appendTo('.error');
           }
        });

    });
</script>