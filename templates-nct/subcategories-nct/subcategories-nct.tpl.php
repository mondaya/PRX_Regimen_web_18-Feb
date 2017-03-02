<div class="common-bg">
    <div class="heading container category">
        <div class="col-lg-4">
            <div class="border"></div>
        </div>
        <div class="col-lg-4">
            <h1>All Subcategory</h1>
        </div>
        <div class="col-lg-4">
            <div class="border"></div>
        </div>
    </div>
</div>
<div class="main">
   <div class="container">
      <div class="left-heading">
         <h2>Select Subcategory</h2>
      </div>
      <div class="subcategory-line">
         <h4>%CATEGORY_DESC%</h4>
      </div>
      
      <div class="list">
        %SUBCATEGORY_LIST%
        %PAGINATION%
      </div>
      
   </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $(document).on("click",".buttonPage",function(){
          var page = $(this).data("page");
          var cateId = '%CATE_ID%';
          $.ajax({
                type: 'POST',
                url: '<?php echo SITE_MOD; ?>subcategories-nct/ajax.subcategories-nct.php',
                data: {page: page,cateId: cateId,action:'paging'},
                success: function(data) {
                    $('.list').html(data);
                    $(window).scrollTop(0);
                }
            });
   });
});
</script>