<div class="main-bg-heading">
   <div class="common-bg">
      <div class="heading container">
         <div class="col-lg-3">
            <div class="border"></div>
         </div>
         <div class="col-lg-6">
            <h1>Referral Module</h1>
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
         <h2>Invite Your Friends</h2>
      </div>
      <div class="referral-info">
         <div class="my-profile col-lg-offset-3">
            <label>My Referral URL :</label>
            <span>%REFERRAL_URL%</span>
            <div class="clearfix"></div>
            <label>Share Referral URL :</label>
            
            <form name="frmReferral" id="frmReferral" action="" method="post">
               <span>
                  <div class="row">
                     <div class="form-group col-lg-12 referral-error">
                        <input class="form-control" placeholder="Friend's Email Address" type="text" name="email">
                     </div>
                  </div>
               </span>
               <div class="clearfix"></div>
               <span><input type="submit" name="submitReferral" class="btn btn-default blue-btn email-btn" value="Email" title="Email"></span>
            </form>

         </div>
         <div class="row referral-details">
            <div class="col-lg-3">
               <div class="reffer-no">
                  <div class="number">
                     <div class="blue">%TOTAL_REFERRED_USER%</div>
                  </div>
                  <div class="reffer-info">
                     <h5>No. of users referred by logged
                        in user
                     </h5>
                  </div>
               </div>
            </div>
            <div class="col-lg-3">
               <div class="reffer-no">
                  <div class="number">
                     <div class="blue">%TOTAL_REGISTERED_USER%</div>
                  </div>
                  <div class="reffer-info">
                     <h5>No. of users registered</h5>
                  </div>
               </div>
            </div>
            <div class="col-lg-3">
               <div class="reffer-no">
                  <div class="number">
                     <div class="blue">%TOTAL_PURCHASED_USER%</div>
                  </div>
                  <div class="reffer-info">
                     <h5>No. of registered users who
                        has made a purchase
                     </h5>
                  </div>
               </div>
            </div>
            <div class="col-lg-3">
               <div class="reffer-no">
                  <div class="number">
                     <div class="blue">%SITE_CURR%%TOTAL_REFERRAL_AMOUNT%</div>
                  </div>
                  <div class="reffer-info">
                     <h5>Total referral amount earned 
                        by user
                     </h5>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

<script>
    
    $(document).ready(function() {

         function validateEmail(field) {
             var regex=/\b[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b/i;
             return (regex.test(field)) ? true : false;
         }

         jQuery.validator.addMethod("validemail", function(value, element){
            
               var result = value.split(",");
               var cnt=0;
               for(var i = 0;i < result.length;i++){
                  if(!validateEmail(result[i])){ 
                     cnt++;
                     //return false;
                  }
               }
               if(cnt > 0)
               {
                  return false;
               }else{
                  return true;
               }

         }); 

        //For validation
        $("#frmReferral").validate({
            errorClass: 'help-block',
            errorElement: 'p',
            rules: {
                email: { required: true,validemail : true},
                
            },
            messages: {
                email: {
                    required: "Enter email",
                    validemail : "Enter valid email"
                }
            },
            errorPlacement: function (error, element) {
                error.insertAfter(element);
            }
        });
   });

</script>