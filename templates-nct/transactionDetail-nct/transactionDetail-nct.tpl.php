<div class="common-bg">
   <div class="heading container category">
      <div class="col-lg-4">
         <div class="border-btm"></div>
      </div>
      <div class="col-lg-4">
         <h1>Transaction <span class="ctm-order">%TRANSACTION_ID%</span></h1>
      </div>
      <div class="col-lg-4">
         <div class="border-btm"></div>
      </div>
   </div>
</div>
<div class="main">
   <div class="container">
      <div class="left-heading">
         <h2>Transaction Details</h2>
      </div>
      %PRODUCT_LIST%
      <div class="upcoming-reminders">
         <div class="heading-btn">
            <h2>Amount Details </h2>
         </div>
         <div class="my-profile col-lg-offset-3">
            <label class="title">Total Product Price : </label>
            <span class="price"> %CURR_SIGN%%TOTAL_PRODUCT_PRICE%</span>
            <label class="title">Duties And Handling Amount : </label>
            <span class="price">%CURR_SIGN%%DUTIES%</span>
            <div class="clearfix"></div>
            <label class="title">Admin Charges :</label>
            <span class="price">%CURR_SIGN%%ADMIN_CHARGES% </span>
            <label class="title">Shipping Amount : </label>
            <span class="price">%CURR_SIGN%%SHIPPING_CHARGE%</span>
            <div class="clearfix"></div>
            <label class="title">Coupon Code Discount : </label>
            <span class="price"> %CURR_SIGN%%DISCOUNT% </span>
            <label class="title">Total Amount : </label>
            <span class="price">%CURR_SIGN%%TOTAL_AMOUNT%</span>
            <div class="clearfix"></div>
         </div>
      </div>
      %SHIPPING_DETAIL%
      <div class="black-border"></div>
   </div>
</div>