<div class="main-bg-heading">
   <div class="common-bg">
      <div class="notification heading container">
         <div class="col-lg-4">
            <div class="border"></div>
         </div>
         <div class="col-lg-4">
            <h1>Notifications</h1>
         </div>
         <div class="col-lg-4">
            <div class="border"></div>
         </div>
      </div>
   </div>
</div>
<div class="main">
   <div class="container">
      <div class="left-heading">
         <h2>My Notifications</h2>
      </div>
      <div class="all-notificaton">
         %NOTIFICATION_LIST%
         %PAGINATION%
      </div>
   </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
    
        //For paging
        $(document).on("click",".buttonPage",function(){
          var page = $(this).data("page");
          $.ajax({
                type: 'POST',
                url: '<?php echo SITE_MOD; ?>notifications-nct/ajax.notifications-nct.php',
                data: {page: page,action:'paging'},
                success: function(data) {
                    $('.all-notificaton').html(data);
                    $(window).scrollTop(0);
                }
            });
        });

    });
</script>