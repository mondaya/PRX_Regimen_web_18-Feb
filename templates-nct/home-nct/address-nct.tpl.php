    <form method="POST" name="frmUser" id="frmUser">
       <div class="form-inline">
          <div class="form-group col-lg-1">
             <select id="salute" name="salute">
                <option value="mr" %SELECTED_MR% >Mr.</option>
                <option value="mrs" %SELECTED_MRS% >Mrs.</option>
                <option value="ms" %SELECTED_MS% >Miss.</option>
                <option value="dr" %SELECTED_DR% >Dr.</option>
             </select>
          </div>
          <div class="form-group col-lg-3 name-group">
             <input class="form-control reg-control" name="firstName" id="firstName" placeholder="First Name*" type="text" value="%FNAME%">
          </div>
          <div class="form-group col-lg-3 name-group">
             <input class="form-control reg-control" name="lastName" id="lastName" placeholder="Last Name*" type="text" value="%LNAME%">
          </div>
       </div>
       <div class="clearfix"></div>
       <div class="form-inline full-width-text">
          <div class="form-group col-lg-4">
             <input class="form-control" name="email"  id="email" placeholder="Email*" type="email" value='%EMAIL%' readonly="" disabled="" />
          </div>
          <div class="form-group col-lg-4">
             <input class="form-control" name="mobile" id="mobile" placeholder="Mobile no.*" type="text" value="%MOBILE%">
          </div>
       </div>
       <div class="form-inline">
          <div class="form-group col-lg-4">
             <textarea class="form-control comment" rows="3" name="address" id="address" placeholder="Address"  >%ADDRESS%</textarea>
          </div>
       </div>
       <div class="clearfix"></div>
       <div class="form-inline full-width-text">
          <div class="form-group col-lg-4">
             <select name="country" id="country" class="gender" >
             %COUNTRY%
             </select>
          </div>
          <div class="form-group col-lg-4">
             <select name="state" id="state" class="gender" >
             %STATE%
             </select>
          </div>
       </div>
       <div class="clearfix"></div>
       <div class="form-inline full-width-text">
          <div class="form-group col-lg-4">
             <select name="city" id="city" class="gender" >
             %CITY%
             </select>
          </div>
          <div class="form-group col-lg-4">
             <input class="form-control"  name="zipCode" id="zipCode" placeholder="Zip code" type="text" value="%ZIP%">
          </div>
          <div class="clearfix"></div>
       </div>
       <input type="hidden" name="action" value="updateAddress">
    </form>   
        
    

    <script type="text/javascript">
    $(document).ready(function(){

        $(document).on("change","#country",function(){
            $("#state").html('<option value="">--Please Select state*--</option>');
            $("#city").html('<option value="">--Please Select city*--</option>');

            var cId = $(this).val();
            if(cId > 0){
                $.ajax({
                    url: '<?php echo SITE_MOD; ?>userprofile-nct/ajax.userprofile-nct.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {action: 'getStates',id: cId},
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
                    url: '<?php echo SITE_MOD; ?>userprofile-nct/ajax.userprofile-nct.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {action: 'getCities',id: sId},
                    success:function(response){
                        $("#city").html(response.cities);
                    }
                });

            }
        });

        $("#frmUser").validate({
            errorClass: 'help-block',
            errorElement: 'label',
            rules: {
                firstName: { required: true },
                lastName: { required: true },
                mobile: { required: true, digits: true, minlength:10},
                country: { required: true },
                state: { required: true },
                city: { required: true },
                address: { required: true },
                paypalEmail: {email: true },
                zipCode: {required: true ,number:true}
            },
            messages: {
                firstName: { required: "Please Enter FirstName" },
                lastName: { required: "Please Enter Lastname" },
                country: { required: "Please select Country" },
                state: { required: "Please select State" },
                city: { required: "Please select City" },
                mobile: { required: "Please enter your mobile number", minlength: "Minimum 10 numbers" },
                address: { required: "Please enter  address" },
                paypalEmail: { email: "Please enter valid email address" },
                zipCode: { required: "Please enter zip code"}
            },
            errorPlacement: function (error, element) {
                error.insertAfter(element);
            }
        });
        

        $('#payNow').click(function() {
            var d2dOption = $('#d2dOption').val();
            if(d2dOption == 'addNewAddress'){     
                if($("#frmUser").valid()){
                    return true;
                }else{
                    return false;
                }
            }
        });

        $(document).on("click","#payNow",function(){
           var datastring = $("#frmUser").serialize();
           $.ajax({
                type: 'POST',
                url: '<?php echo SITE_MOD; ?>home-nct/ajax.home-nct.php',
                data: datastring,
                success: function(data) {
                        
                }
            });
        });


    });
</script>