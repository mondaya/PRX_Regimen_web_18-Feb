<div class="main-bg-heading">
   <div class="common-bg">
      <div class="heading container">
         <div class="col-lg-3">
            <div class="border"></div>
         </div>
         <div class="col-lg-6">
            <h1>Redeem Request</h1>
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
         <h2>Redeem History</h2>
      </div>
      <div class="redeem-request" id="no-more-tables">
         <table>
            <tbody class="data">
               %REQUEST_LIST%
            </tbody>
         </table>

         
      </div>
      <span class="paging">
        %PAGINATION%
      </span>
      <div class="make-request">
         <div class="left-heading">
            <h2>Make a Redeem Request</h2>
         </div>
         <div class="my-profile col-lg-offset-3">
            <label>Available Balance : </label>
            <span>%SITE_CURR%%CREDIT_AMT%</span>
            <label>Enter Redeem Amount*(%SITE_CURR%) :</label>
            
            <form name="frmRedeem" id="frmRedeem" action="" method="post">
               
               <span>
                  <div class="row">
                     <div class="form-group col-lg-12">
                        <input class="form-control" placeholder="Enter amount" type="text" name="amount" id="amount" min="1">
                     </div>
                  </div>
               </span>

               <div class="clearfix"></div>
               <span class="request-btn"><input type="submit" class="btn btn-default blue-btn email-btn" value="Submit" name="submitRedeem"></span>

            </form>
         </div>
      </div>
   </div>
</div>

<script>
    
    $(document).ready(function() {

        //For validation
        $("#frmRedeem").validate({
            errorClass: 'help-block',
            errorElement: 'p',
            rules: {
                amount: { required: true, number : true}
            },
            messages: {
                amount: {
                    required: "Enter amount", number: "Enter number only"
                }
            },
            errorPlacement: function (error, element) {
                error.insertAfter(element);
            }
        });

         $( "#frmRedeem" ).submit(function( event ) {
           
           var amount = $('#amount').val();
           var credit = '%CREDIT_AMT%';

           if(amount > parseInt(credit)){
               alert('You can\'t redeem more than your credit amount');
               $('#amount').val('');
               return false;
           }
           
         });

         //For paging
          $(document).on("click",".buttonPage",function(){
            var page = $(this).data("page");
            $.ajax({
                  type: 'POST',
                  url: '<?php echo SITE_MOD; ?>redeem-nct/ajax.redeem-nct.php',
                  data: {page: page,action:'paging'},
                  dataType : 'json',
                  success: function(data) {
                      $('.data').html(data.requetList);
                      $('.paging').html(data.paging);
                      
                  }
              });
          });
   });

</script>