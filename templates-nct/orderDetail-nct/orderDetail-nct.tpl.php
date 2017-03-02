<div class="common-bg">
   <div class="heading container category">
      <div class="col-lg-4">
         <div class="border-btm"></div>
      </div>
      <div class="col-lg-4">
         <h1>Order <span class="ctm-order">%ORDER_ID%</span></h1>
      </div>
      <div class="col-lg-4">
         <div class="border-btm"></div>
      </div>
   </div>
</div>
<div class="main">
   <div class="container">
      <div class="left-heading">
         <h2>Order Details</h2>
      </div>
      <div class="row">
         <div class="col-sm-3 col-sm-12">
            <a class="order-img" href="#"><img src="%IMG_SRC%" alt="prodict-deals" style="height:230px; width:265px;"/></a>
         </div>
         <div class="col-lg-9 col-sm-9">
            <div class="heading-btn">
               <h3>%PRODUCT_NM%</h3>
               %RETURN_BUTTON%
            </div>
            <div class="my-profile product-des">
               <label>Category : </label>
               <span>%CATE_NM%</span>
               <label>Sub Category : </label>
               <span>%SUB_CATE_NM%</span>
               <div class="clearfix"></div>
               <div class="clearfix"></div>
               <label>Purchased Quantity :</label>
               <span>%QUANTITY%</span>
               <label>Purchase Date : </label>
               <span>%DATE%</span>
               <div class="clearfix"></div>
            </div>
            <div class="clearfix"></div>
         </div>
      </div>
      <div class="black-border"></div>
      <div class="upcoming-reminders">
         <div class="heading-btn">
            <h2>Amount Details </h2>
         </div>
         <div class="my-profile col-lg-offset-3">
            <label class="title">Product Price : </label>
            <span class="price"><div class="blue-price">%ACTUAL_PRICE%</div> %CURR_SIGN%%PRODUCT_PRICE%</span>
            <!-- <label class="title">Duties And Handling Amount : </label>
            <span class="price">%CURR_SIGN% %DUTIES%</span>
            <div class="clearfix"></div>
            <label class="title">Admin Charges :</label>
            <span class="price">%CURR_SIGN% %ADMIN_CHARGES%</span>
            <div class="clearfix"></div>
            <label class="title">Shipping Charges : </label>
            <span class="price">%CURR_SIGN% %SHIPPING_CHARGE%</span>
            <div class="clearfix"></div>
            <label class="title">Coupon Code Discount :</label>
            <span class="price">%CURR_SIGN% %DISCOUNT%</span>
            <div class="clearfix"></div>-->
            <label class="title">Total Amount : </label>
            <span class="price"> %CURR_SIGN%%TOTAL_AMOUNT%</span>
            <div class="clearfix"></div>
         </div>
      </div>
      <div class="upcoming-reminders">
         
         %SHIPPING_DETAIL%

      </div>
      <div class="black-border"></div>
      <div class="upcoming-reminders">
         <div class="left-heading">
            <h2>Return Policy</h2>
         </div>
         <div class="Policy-cnt">
            %RETURN_POLICY%
         </div>
         <div class="black-border"></div>
      </div>
   </div>
</div>




<!--Popup start-->
<div id="about-edit" class="modal fade" aria-labelledby="myModalLabel" role="dialog" tabindex="-1">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="exampleModalLabel">Send A Return Request </h4>
         </div>
         <form name="returnForm" action="" method="post" id="frmReturn" enctype="multipart/form-data"> 
         <div class="modal-body">
            <div class="form-group">
                  <input type="text" class="form-control" id="recipient-name" placeholder="Subject" name="subject">
               </div>
               <div class="form-group">
                  <textarea id="comment" placeholder="Description" rows="3" class="form-control description" name="message"></textarea>
               </div>
               <div class="form-group">
                  <input type="file" id="imageName" name="imageName[]" multiple accept="image/*">
               </div>
               
         </div>
         <div class="modal-footer">
         <input type="submit" class="btn btn-default blue-bg" value="Submit" name="return" title="Submit">
         <button class="btn btn-default blue-bg" type="button" data-dismiss="modal" title="Cancel">Cancel</button>
         </div>
         </form>
      </div>
   </div>
</div>
<!--End start-->

<script type="text/javascript">

$(document).ready(function(){

$("#frmReturn").validate({
         errorClass: 'help-block',
         errorElement: 'label',
         rules: {
            subject: { required: true },
            message: { required: true },
            'imageName[]': { required: true }
            
         },
         messages: {
            subject: {
               required: "Please Enter Subject"
            },
            message: {
               required: "Please Enter Description"
            },
           'imageName[]': {
               required: "Please select images"
            }
         },
         errorPlacement: function (error, element) {
            error.insertAfter(element);
         }
      });

      $('#imageName').on('change', function() {
        $(this).valid();                  
      });
});
</script>