<div class="common-bg">
   <div class="heading container category">
      <div class="col-lg-4">
         <div class="border"></div>
      </div>
      <div class="col-lg-4">
         <h1>Checkout</h1>
      </div>
      <div class="col-lg-4">
         <div class="border"></div>
      </div>
   </div>
</div>
<div class="main">
   <div class="container">
      <div class="left-heading">
         <h2>Review Your Custom Order</h2>
      </div>
      <div class="table-responsive place-order">
         <table class="table custom-order">
            <thead>
               <tr>
                  <th>Name</th>
                  <th>Size</th>
                  <th>Color</th>
                  <th>Price</th>
                  <th>Qty.</th>
                  <th>Total</th>
               </tr>
            </thead>
            <tbody>
               <tr>
                  <td>%PRODUCT_NM%</td>
                  <td>%SIZE%</td>
                  <td>%COLOR%</td>
                  <td class="blue">%CURR_SIGN%%PRODUCT_PRICE%</td>
                  <td>%QUANTITY%</td>
                  <td class="blue">%CURR_SIGN%%TOTAL_PRODUCT_PRICE%</td>
               </tr>
            </tbody>
         </table>
      </div>
      <div class="left-heading">
         <h2>Select Your Delivery Method </h2>
      </div>
      <div class="col-lg-9 col-lg-offset-3">
         <div class="form-group col-lg-8">
            <select class="comment" name="deliveryOption" id="deliveryOption">
               <option value="">Select Delivery Option</option>
               <option value="d" %DELIVERY_D%>Door to door delivery</option>
               <option value="p" %DELIVERY_P%>Pickup</option>
            </select>
         </div>
         <div class="col-lg-8 pickCenter">
            %PICK_CENTER%
         </div>
         <div class="col-lg-8 pickOption">
            %PICK_OPTION%
         </div>
         <div class="form-group d2dOption">
            %D2D_OPTION%
         </div>
         <div class="addNewAddress">
         </div>
         <div class="clearfix"></div>
      </div>
      <div class="clearfix"></div>
      <div class="black-border"></div>
      <div class="left-heading">
         <h2>Payment Details</h2>
      </div>
      <div class="col-lg-9 col-lg-offset-3">
         <div class="form-inline">
            <div class="form-group col-lg-8">
               <select class="comment" id="paymentType" name="paymentType">
                  <option value="wallet">Wallet</option>
                  <option value="paypal">Paypal</option>
               </select>
            </div>
         </div>
      </div>
      <div class="clearfix"></div>
      <div class="black-border"></div>
      <div class="left-heading">
         <h2>Coupon Code</h2>
      </div>
      <div class="col-lg-9 col-lg-offset-3">
         <div class="form-inline">
            <div class="form-group col-lg-10">
               <input type="text" placeholder="Enter Code" class="form-control Coupon-code col-lg-8" name="couponCode" id="couponCode">
               <button class="btn btn-default blue-btn apply-btn col-lg-2" type="submit" id="coupon" title="Apply">Apply</button>
               <div class="clearfix"></div>
               <div class="message"></div>
            </div>
         </div>
      </div>
      <div class="clearfix"></div>
      <div class="black-border"></div>
      <div class="upcoming-reminders">
         <div class="heading-btn">
            <h2>Amount Details </h2>
         </div>
         <div class="my-profile col-lg-9 col-lg-offset-3">
            <label class="title col-lg-4">Total Product Price : </label>
            <span class="price">%CURR_SIGN%%TOTAL_PRODUCT_PRICE% </span>
            <label class="title col-lg-4">Duties And Handling Amount : </label>
            <span class="price dutiesAmount">%CURR_SIGN%%DUTIES%</span>
            <div class="clearfix"></div>
            <label class="title col-lg-4">Admin Charges :</label>
            <span class="price adminCharge">%CURR_SIGN%%ADMIN_CHARGES%</span>
            <div class="clearfix"></div>
            <label class="title col-lg-4">Shipping Charges : </label>
            <span class="price shppingAmount">%CURR_SIGN%%SHIPPING_CHARGE%</span>
            <div class="clearfix"></div>
            <label class="title col-lg-4">Coupon Code Discount :</label>
            <span class="price discountPrice">%CURR_SIGN%%DISCOUNT%</span>
            <div class="clearfix"></div>
            <label class="title col-lg-4">Total Amount : </label>
            <span class="price totalPrice">%CURR_SIGN%%TOTAL_AMOUNT%</span> 
         </div>
      </div>
      <div class="clearfix"></div>
      <div class="black-border"></div>
      <div class="form-group text-center" id="wallet">
         <form name="payWallet" method="post" action="">
            <input type="hidden" name="id" value="%ID%">
            <input type="hidden" name="amount" value="%TOTAL_AMOUNT_NAIRA%">
            <input type="submit" class="btn btn-default blue-btn apply-btn" type="submit" id="payNow" value="Pay" name="payWallet" title="Pay">
         </form>
      </div>
      <div class="form-group text-center" id="paypal" style="display:none;">
         <form name="payWallet" method="post" action="">
            <input type="hidden" name="id" value="%ID%">
            <input type="hidden" name="amount" value="%TOTAL_AMOUNT_USD%">
            <input type="hidden" name="amountOrignal" value="%TOTAL_AMOUNT_NAIRA%">
            <input type="submit" class="btn btn-default blue-btn apply-btn" type="submit" id="payNow" value="Pay" name="payPaypal" title="Pay"> 
         </form>
      </div>
      <div class="black-border"></div>
   </div>
</div>
<script type="text/javascript">
   $(document).ready(function(){
   
       //For change shipping
       $(document).on("change","#deliveryOption",function(){
         var deliveryOption = $('#deliveryOption').val();
   
           if(deliveryOption != ''){
             $.ajax({
                   type: 'POST',
                   url: '<?php echo SITE_MOD; ?>customOrderCart-nct/ajax.customOrderCart-nct.php',
                   data: {deliveryOption:deliveryOption,id:"%ID%",action:'changeShipping'},
                   dataType : 'json',
                   success: function(data) {
                       if(deliveryOption == 'd'){
                           $('.shppingAmount').html(data.shipping);
                           $('.totalPrice').html(data.totalPrice);
                           $('.dutiesAmount').html(data.dutiesAmount);
                           $('.adminCharge').html(data.adminCharge);
                           $('.d2dOption').html(data.d2dOption);
                           $('.pickCenter').hide();
                           $('.pickOption').hide();
                           $('.d2dOption').show();
                       }else if(deliveryOption == 'p'){
                           $('.pickCenter').show();
                           $('.pickOption').show();
                           $('.d2dOption').hide();
                           $('.addNewAddress').hide();
                           $('.pickCenter').html(data.pickCenter);
                           $('.pickOption').empty();
                       }
                       //$(window).scrollTop(0);
                   }
               });
         }else{
           $('.pickCenter').hide();
           $('.pickOption').hide();
           $('.d2dOption').hide();
           $('.addNewAddress').hide();
         }
       });
   
       $(document).on("change","#pickCenter",function(){
         var stateId = $('#pickCenter').val();
           if(stateId != ''){
             $.ajax({
                   type: 'POST',
                   url: '<?php echo SITE_MOD; ?>customOrderCart-nct/ajax.customOrderCart-nct.php',
                   data: {stateId:stateId,action:'changePickCenter'},
                   dataType : 'json',
                   success: function(data) {
                           //$('.shppingAmount').html(data.shipping);
                           //$('.totalPrice').html(data.totalPrice);
                           $('.pickOption').html(data.pickOption);
                       
                   }
               });
         }
       });
   
       $(document).on("change","#pickOption",function(){
         var pickId = $('#pickOption').val();
           if(pickId != ''){
             $.ajax({
                   type: 'POST',
                   url: '<?php echo SITE_MOD; ?>customOrderCart-nct/ajax.customOrderCart-nct.php',
                   data: {pickId:pickId,id:"%ID%",action:'changePickPoints'},
                   dataType : 'json',
                   success: function(data) {
                           $('.shppingAmount').html(data.shipping);
                           $('.totalPrice').html(data.totalPrice);
                           $('.dutiesAmount').html(data.dutiesAmount);
                           $('.adminCharge').html(data.adminCharge);
                       
                   }
               });
         }
       });
   
       $(document).on("change","#d2dOption",function(){
         var d2dOption = $('#d2dOption').val();
           if(d2dOption == 'addNewAddress'){
             $.ajax({
                   type: 'POST',
                   url: '<?php echo SITE_MOD; ?>customOrderCart-nct/ajax.customOrderCart-nct.php',
                   data: {action:'addNewAddress'},
                   dataType : 'json',
                   success: function(data) {
                           $('.addNewAddress').html(data.addNewAddress);
                           $('.addNewAddress').show();
                       
                   }
               });
           }else{
               $('.addNewAddress').hide();
           }
       });
   
       $(document).on("click","#payNow",function(){
         var deliveryOption = $('#deliveryOption').val();
         var pickOption = $('#pickOption').val();
         var pickCenter = $('#pickCenter').val();
   
         if(deliveryOption == ''){
           alert("Select delivery option");
           return false;
         }
         else if(pickOption == '' && deliveryOption == 'p'){
           alert("Select pick point");
           return false;
         }
         else if(pickCenter == '' && deliveryOption == 'p'){
           alert("Select pick center");
           return false;
         }
           
       });
   
       $(document).on("click","#coupon",function(){
         var couponCode = $('#couponCode').val();
         
         if(couponCode == ''){
           alert("Enter code");
           return false;
         }else{
   
           $.ajax({
               type: 'POST',
               url: '<?php echo SITE_MOD; ?>customOrderCart-nct/ajax.customOrderCart-nct.php',
               data: {couponCode:couponCode,id:"%ID%",action:'applyCoupon'},
               dataType : 'json',
               success: function(data) {
                       $('.message').html(data.message);
                       $('.discountPrice').html(data.discountPrice);
                       $('.totalPrice').html(data.finalAmount);
                   
               }
           });
   
         }
           
       });
   
       $(document).on("change","#paymentType",function(){
         var paymentType = $('#paymentType').val();
       
         if(paymentType == 'paypal'){
               $('#paypal').show();
               $('#wallet').hide();
         }else{
               $('#wallet').show();
               $('#paypal').hide();
         }
                     
       });
       
   
   });
</script>