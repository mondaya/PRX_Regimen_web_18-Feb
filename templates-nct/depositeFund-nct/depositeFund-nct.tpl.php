<div class="main-bg-heading">
   <div class="common-bg">
      <div class="heading container">
         <div class="col-lg-4">
            <div class="border"></div>
         </div>
         <div class="col-lg-4">
            <h1>My wallet</h1>
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
         <h2>Deposit Amount</h2>
      </div>
      <div class="referral-info deposit-amount">
         <div class="my-profile col-lg-offset-2 col-lg-8">
            <form action="" name="deposite" id="deposite" method="post">
               <label class="price">First Name :</label>
               <span class="title">
                  <div class="row">
                     <div class="form-group col-lg-12">
                        <input class="form-control" type="text" value="%FNAME%" disabled>
                     </div>
                  </div>
               </span>
               <div class="clearfix"></div>
               <label class="price">Last Name :</label>
               <span class="title">
                  <div class="row">
                     <div class="form-group col-lg-12">
                        <input class="form-control" type="text" value="%LNAME%" disabled>
                     </div>
                  </div>
               </span>
               <div class="clearfix"></div>
               <label class="price">Email Address :</label>
               <span class="title">
                  <div class="row">
                     <div class="form-group col-lg-12">
                        <input class="form-control" type="text" value="%EMAIL%" disabled>
                     </div>
                  </div>
               </span>
               <div class="clearfix"></div>
               <label class="price">Enter Deposit Amount(%SITE_CURR%) :</label>
               <span class="title">
                  <div class="row">
                     <div class="form-group col-lg-12">
                        <input class="form-control" placeholder="Enter amount" type="text" name="amount">
                     </div>
                  </div>
               </span>
               <!-- <div class="clearfix"></div>
               <label>Pay Via :</label>
               <span>
                  <div class="row">
                     <div class="form-group col-lg-12">
                        <select class="gender">
                           <option value="Paypal">Paypal</option>
                           <option value="verve">Verve</option>
                           <option value="paga">Paga</option>
                        </select>
                     </div>
                  </div>
               </span> -->
               <div class="clearfix"></div>
               <span class="title"><input type="submit" class="btn btn-default blue-btn email-btn" name="submitDeposite" value="Pay" title="Pay"></span>
            </form>
         </div>
      </div>
   </div>
</div>

<script>
    
    $(document).ready(function() {

        //For validation
        $("#deposite").validate({
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
   });

</script>