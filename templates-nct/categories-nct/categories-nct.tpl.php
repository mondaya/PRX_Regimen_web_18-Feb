<div class="common-bg">
   <div class="heading container category">
      <div class="col-lg-4">
         <div class="border"></div>
      </div>
      <div class="col-lg-4">
         <h1>All Category</h1>
      </div>
      <div class="col-lg-4">
         <div class="border"></div>
      </div>
   </div>
</div>
<div class="main">
   <div class="container">
      <div class="left-heading">
         <h2>Select Category</h2>
      </div>
      <div class="product-category">
        
        <div class="list">
	        %CATEGORIES_LIST%
	        %PAGINATION%
    	  </div>
        
      </div>
   </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $(document).on("click",".buttonPage",function(){
          var page = $(this).data("page");
          $.ajax({
                type: 'POST',
                url: '<?php echo SITE_MOD; ?>categories-nct/ajax.categories-nct.php',
                data: {page: page,action:'paging'},
                success: function(data) {
                    $('.list').html(data);
                    $(window).scrollTop(0);
                }
            });
        });

        //For favorite unfavorite
        $(document).on('click','.favourite',function(e){
          var $this = $(this);
          var id = $this.data('id');
          var value = $this.data('value');
          
          $.ajax({
                  type: 'POST',
                  url: '<?php echo SITE_MOD; ?>categories-nct/ajax.categories-nct.php',
                  data: {id:id,val:value,action:'favourite'},
                  success: function(data) {

                      if(data == 'notLogin'){
                        toastr["error"]("Please login to continue");
                      }else{

                        $this.children('img').attr('src','<?php echo SITE_IMG;?>fav-'+value+'.png');
                        var x = value == 'on' ? 'off' : 'on';
                        $this.data('value',x)
                      }
                  }
              });
            
          });
});
</script>