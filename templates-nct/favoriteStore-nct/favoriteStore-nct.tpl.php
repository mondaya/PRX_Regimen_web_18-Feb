<div class="common-bg">
    <div class="heading container">
        <div class="col-lg-4">

            <div class="border">
            </div>
        </div>
        <div class="col-lg-4">
            <div class="row">
                <h1>Favorite Brands</h1></div>
        </div>
        <div class="col-lg-4">

            <div class="border"></div>

        </div>
    </div>
</div>

<div class="main">
    <div class="container">

        <div class="heading-btn">
            <h2>Brands</h2>
            <button type="button" class="btn btn-default blue-btn right-btn" data-toggle="modal" data-target="#about-edit" title="Add New">Add New</button>
        </div>

        <div class="store-list">
            %STORE_LIST%
            %PAGINATION%
        </div>
    </div>
</div>


<div class="modal fade" id="about-edit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
        <form name="frmStore" action="" method="post" id="frmStore">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h2>Set Favorite Brands</h2>
         </div>
         <div class="modal-body fav-cate">
               <div class="form-inline select-stores">
                  <div class="form-group comment">
                     <select id="example-getting-started" multiple="multiple" name="store[]" id="store">
                        %OPTION%
                     </select>
                  </div>
                  <div class="error"></div>
               </div>
         </div>

         <div class="clearfix"></div>
         <div class="modal-footer">
            <input type="submit" name="submitCate" class="btn btn-default blue-btn" value="Save">
            <button type="button" class="btn btn-default blue-btn" class="close" data-dismiss="modal" aria-label="Close">Cancel</button>
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
                url: '<?php echo SITE_MOD; ?>favoriteStore-nct/ajax.favoriteStore-nct.php',
                data: {page: page,action:'paging'},
                success: function(data) {
                    $('.store-list').html(data);
                    $(window).scrollTop(0);
                }
            });
        });

        //For delete confirmation
        $(document).on("click","#deleteStore",function(){
            
            if (confirm('Are you sure to remove this store?')) {
                return true;
            }else{
                return false;
            }

        });

        $("#frmStore").validate({
           errorClass: 'help-block',
           errorElement: 'label',
           rules: {
              'store[]': { required: true }
           },
           messages: {
              'store[]': {
                 required: "Please select store"
              }
           },
           errorPlacement: function (error, element) {
              error.appendTo('.error');
           }
        });

    });
</script>