<div class="main-bg-heading">
   <div class="common-bg">
      <div class="heading container">
         <div class="col-lg-3">
            <div class="border"></div>
         </div>
         <div class="col-lg-6">
            <h1>Payment History</h1>
         </div>
         <div class="col-lg-3">
            <div class="border"></div>
         </div>
      </div>
   </div>
</div>
<div class="main">
   <div class="container">
      <div class="left-heading">
         <h2>History</h2>
      </div>
      <div class="payment-history" id="no-more-tables">
         <table>
            <thead>
               <tr>
                  <th>No.</th>
                  <th>Transaction ID</th>
                  <th>Title</th>
                  <th>Payment Gateway</th>
                  <th>Date</th>
                  <th>Amount</th>
               </tr>
            </thead>
            <tbody class="data">
               %PAYMENT_LIST%
            </tbody>
         </table>
      </div>
      <span class="paging">
        %PAGINATION%
      </div>
   </div>
</div>

<link type="text/css" rel="stylesheet" href="{SITE_CSS}bootstrap-multiselect.css">
<script type="text/javascript">
    $(document).ready(function(){
    
        //For paging
        $(document).on("click",".buttonPage",function(){
          var searchText = $('#searchText').val();
          var date = $('#datepicker').val();
          var status = $('#status').val();
          var page = $(this).data("page");
          $.ajax({
                type: 'POST',
                url: '<?php echo SITE_MOD; ?>paymentHistory-nct/ajax.paymentHistory-nct.php',
                data: {page: page,action:'paging'},
                dataType : 'json',
                success: function(data) {
                    $('.data').html(data.paymentList);
                    $('.paging').html(data.paging);
                    //$(window).scrollTop(0);
                }
            });
        });
    });
</script>