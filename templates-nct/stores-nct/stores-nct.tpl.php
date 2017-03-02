<div class="common-bg">
  <div class="heading container">
    <div class="col-lg-4">
      <div class="border"></div>
    </div>
    <div class="col-lg-4">
      <h1>Product Brands</h1>
    </div>
    <div class="col-lg-4">
      <div class="border"></div>
    </div>
  </div>
</div>
<div class="main">
    <div class="container">
      <div class="product-brand">
        <div class="row">
          %STORE_LIST%
          <div class="clearfix"></div>
        </div>
      </div>
    </div>
    </div>

<script type="text/javascript">
    $(document).ready(function(){
        $(document).on("click",".buttonPage",function(){
          var page = $(this).data("page");
          var cateId = '%CATE_ID%';
          var subCateId = '%SUBCATE_ID%';
          $.ajax({
                type: 'POST',
                url: '<?php echo SITE_MOD; ?>stores-nct/ajax.stores-nct.php',
                data: {page: page,cateId: cateId,subCateId: subCateId,action:'paging'},
                success: function(data) {
                    $('.list').html(data);
                    //$(window).scrollTop(0);
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
                  url: '<?php echo SITE_MOD; ?>stores-nct/ajax.stores-nct.php',
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