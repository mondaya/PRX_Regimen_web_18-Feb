<div class="main-bg-heading">
   <div class="common-bg">
      <div class="heading container">
         <div class="col-lg-4">
            <div class="border"></div>
         </div>
         <div class="col-lg-4">
            <h1>Contact Us</h1>
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
         <h2>Contact Details</h2>
      </div>
      
      <form name="frmContact" id="frmContact" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
        <div class="col-lg-9 col-lg-offset-3">
         <div class="form-inline full-width-text">
            <div class="form-group col-lg-4">
               <input class="form-control" placeholder="First Name*" name="first_name" type="text" value="%FNAME%">
            </div>
            <div class="form-group col-lg-4">
               <input class="form-control" placeholder="Last Name*" name="last_name" type="text" value="%LNAME%">
            </div>
         </div>
         <div class="clearfix"></div>
         <div class="form-inline full-width-text">
            <div class="form-group col-lg-4">
               <input class="form-control" placeholder="Email*" name="email" type="email" value="%EMAIL%">
            </div>
            <div class="form-group col-lg-4">
                <select name="country" id="country" class="gender">
                    %COUNTRY%
                </select>
            </div>
         </div>
         <div class="clearfix"></div>
         <div class="form-inline full-width-text">
            <div class="form-group col-lg-4">
                <select name="state" id="state" class="gender">
                    %STATE%
                </select>
            </div>
            <div class="form-group col-lg-4">
                <select name="city" id="city" class="gender" >
                    %CITY%
                </select>
            </div>
         </div>
         <div class="clearfix"></div>
         <div class="form-inline">
            <div class="form-group col-lg-4">
               <input class="form-control comment" placeholder="Subject*" name="subject" type="text">
            </div>
         </div>
         <div class="form-inline">
            <div class="form-group col-lg-4">
               <textarea class="form-control comment" rows="3" placeholder="Discription*" name="message" id="message"></textarea>
            </div>
         </div>
        </div>
        <div class="clearfix"></div>
         <div class="form-inline">
            <input type="submit" class="btn btn-default blue-btn" name="submitContact" value="Send" title="Send">
            <a class="btn btn-default blue-btn" href="{SITE_URL}" title="Cancel">Cancel</a>
         </div>
      </form>
      <div class="black-border"></div>
   </div>
</div>

<script type="text/javascript">
   $(document).ready(function(){
         
         $("#frmContact").validate({
         errorClass: 'help-block',
         errorElement: 'label',
         rules: {
            first_name: { required: true },
            last_name: { required: true },
            email: { required: true },
            country: { required: true },
            state: { required: true },
            city: { required: true },
            subject: { required: true },
            message: { required: true }
         },
         messages: {
            first_name: {
               required: "Please Enter FirstName"
            },
            last_name: {
               required: "Please Enter Lastname"
            },
            email: {
               required: "Please Enter Email"
            },
            country: {
               required: "Please Select Country"
            },
            state: {
               required: "Please select State"
            },
            city: {
               required: "Please select City"
            },
            subject: {
               required: "Please Enter Subject"
            },
            message: {
               required: "Please Enter Message"
            }
         },
         errorPlacement: function (error, element) {
            error.insertAfter(element);
         }
      });
      <?php $this->module = 'settings-nct'; ?>
      $(document).on("change","#country",function(){
            $("#state").html('<option value="">--Please Select state--</option>');
            $("#city").html('<option value="">--Please Select city--</option>');

            var cId = $(this).val();
            if(cId > 0){
                $.ajax({
                    url: '<?php echo SITE_MOD.$this->module.'/';?>ajax.<?php echo $this->module;?>.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {action: 'getStates',cId: cId},
                    success:function(response){
                        $("#state").html(response.states);
                    }
                });
            }
        });

        $(document).on("change","#state",function(){
            var sId = $(this).val();
            $("#city").html('<option value="">--Please Select city*--</option>');
            if(sId > 0){
                $.ajax({
                    url: '<?php echo SITE_MOD.$this->module.'/';?>ajax.<?php echo $this->module;?>.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {action: 'getCities',sId: sId},
                    success:function(response){
                        $("#city").html(response.cities);
                    }
                });

            }
        });
   
 });
</script>