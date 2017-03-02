<script type="text/javascript">
    $(document).ready(function(){
        $(document).click("#submitpassword",function(e){
//e.preventDefault;
$("#changePass").validate({
    errorClass: 'help-block',
    errorElement: 'label',
    errorPlacement:function errorPlacement(error,element){

        element.after(error);
        error.css({display: 'block'});
    },

    rules : {
        curruntpass : {
            required :true,minlength: 6           

        },
        newpass :{
            required :true,minlength: 6
        },
        confirmpass :{
            required :true,
            equalTo : '#newpass',
            minlength: 6
        },

    },
    messages : {
        curruntpass : {
            required : "Please Enter Current Password",
            minlength: "Minimum 6 characters"          

        },
        newpass :{
            required : "   Please Enter the New Password",
            minlength: "Minimum 6 characters"
        },
        confirmpass :{
            required : "Please Enter Confirm Password",
            equalTo : "passwords doesnt match.",
            minlength: "Minimum 6 characters"
        },

    },
});

$("#settings").validate({
    errorClass: 'help-block',
    errorElement: 'label',
    errorPlacement:function errorPlacement(error,element){

        element.after(error);
        error.css({display: 'block'});
    },

    rules : {
        address : {
            required :true           

        },
        country : {
            required :true           

        },
        state : {
            required :true           

        },
        city : {
            required :true           

        },
        zipCode :{
            required :true,minlength: 6,number:true
        }

    },
    messages : {
        address : {
            required : "Please Enter Address" 

        },
        country : {
            required : "Please Select Country" 

        },
        state : {
            required : "Please Select State" 

        },
        city : {
            required : "Please Select City" 

        },
        zipCode :{
            required : "Please Enter Zip Code",
            minlength: "Minimum 6 characters"
        }

    },
});

});

        $(document).on("change","#newletter",function(){
          
                $.ajax({
                    url: '<?php echo SITE_MOD.$this->module.'/';?>ajax.<?php echo $this->module;?>.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {action: 'setNewsLetter'},
                    success:function(response){
                        if(response.status == 'n')
                        {
                            toastr["success"]("Unsubscribe successfully ");
                        }
                        else if(response.status == 'y')
                        {
                            toastr["success"]("Subscribe successfully ");
                            
                        }
                    }
                });
            });


        $(document).on("change","#country",function(){
            $("#state").html('<option value="">--Please Select state*--</option>');
            $("#city").html('<option value="">--Please Select city*--</option>');

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



<div class="common-bg">
    <div class="heading container">
        <div class="col-lg-3">
            <div class="border"></div>
        </div>
        <div class="col-lg-6">
            <h1>Account Settings</h1>
        </div>
        <div class="col-lg-3">
            <div class="border"></div>
        </div>
    </div>
</div>

<!-- change password begin -->
<div class="main">
    <div class="container">
        <div class="left-heading">
            <h2>Change Password</h2>
        </div>

        <form action="" autocomplete="on" method="POST" name="changePass" id="changePass"> 
            <div class="form-inline current-pass">
                <div class="form-group">
                    <input class="form-control comment" name="curruntpass" id="curruntpass" placeholder="Current Password" type="password">
                    <label class="error"></label>
                </div>
            </div>
            <div class="form-inline">
                <div class="form-group">
                    <input class="form-control comment" name="newpass" id="newpass" placeholder="New Password" type="password">
                </div>
            </div>
            <div class="form-inline">
                <div class="form-group">
                    <input class="form-control comment" name="confirmpass" id="confirmpass" placeholder="Re-type New Password" type="password">
                </div>
            </div>

            <div class="form-inline">
                <input type="submit" name="submitpassword" id="submitpassword" class="btn btn-default blue-btn" value="Save" title="Save"> </input>
                <a name="sbtCancel" class="btn btn-default blue-btn" title="Cancel" href="{SITE_URL}">Cancel</a>
            </input>
        </div>
    </form>

    <!-- change password end -->

    <!-- change address start -->

    <div class="black-border"></div>

    <div class="left-heading">
        <h2>Change Shipping Address</h2>
    </div>
    <form action="" autocomplete="on" method="POST" name="settings" id="settings">
        <div class="form-inline">

            <div class="form-group">
                <textarea class="form-control comment" rows="3" placeholder="Address*"  name="address" id="address">%ADDRESS%</textarea>
            </div>
        </div>
        <div class="form-inline full-width-text">

            <div class="form-group">
                <select name="country" id="country" class="gender">
                    %COUNTRY%
                </select>
            </div>
            <div class="form-group">
                <select name="state" id="state" class="gender">
                    %STATE%
                </select>
            </div>

        </div>
        <div class="form-inline full-width-text">

            <div class="form-group">
                <select name="city" id="city" class="gender" >
                    %CITY%
                </select>
            </div>
            <div class="form-group">
                <input class="form-control"  name="zipCode" id="zipCode" placeholder="Zip code" type="text" value="%ZIP%">
            </div>
        </div>

        <div class="form-inline">
            <input type="submit" name="sbtUpdate" id="sbtUpdate" value="Save" class="btn btn-default blue-btn" title="Save"></input>
            <a name="sbtCancel" class="btn btn-default blue-btn" title="Cancel" href="{SITE_URL}">Cancel</a>
        </div>
    </form>

    <!--   change address end -->

    <div class="black-border"></div>
    <div class="left-heading">
        <h2>Subscribe to Newsletter</h2>
    </div>
    <div class="notification-box">
        <input tabindex="3" class="" name="newletter" %CHKED% id="newletter" type="checkbox">
        <h4>Subscribe to Newsletter</h4>
    </div>
    <div class="black-border"></div>
    <div class="left-heading">
        <h2>Manage Email Notification</h2>
    </div>
    <form action="" autocomplete="on" method="POST" name="settings" id="settings">
        <div class="notification-box">
            <input tabindex="3" value="y" name="newProductPosted" id="newProductPosted" type="checkbox" %NEW_PRODUCT%>
            <h4>When a new product deal is posted on website by admin</h4>
        </div>
        <div class="notification-box">
            <input tabindex="3" value="y" name="amountAddedInWallet" id="amountAddedInWallet" type="checkbox" %AMOUNT_WALLET%>
            <h4>When amount is added in user’s wallet</h4>
        </div>
        
        <div class="notification-box">
            <input tabindex="3" value="y" name="newPrormoPosted" id="newPrormoPosted" type="checkbox" %PROMO_CODE%>
            <h4>When a new promo code is posted on website by admin</h4>
        </div>
        <div class="notification-box">
            <input tabindex="3" value="y" name="orderStatusByAdmin" id="orderStatusByAdmin" type="checkbox" %ORDER_STATUS%>
            <h4>When my custom Order status is accepted/rejected by admin</h4>
        </div>
        <div class="notification-box">
            <input tabindex="3" value="y" name="reminder" id="reminder" type="checkbox" %REMINDER%>
            <h4>When for the reminder set by me</h4>
        </div>

        <div class="form-inline">
            <button type="submit" name="btnNotification" class="btn btn-default blue-btn" title="Save">Save</button>
            <a name="sbtCancel" class="btn btn-default blue-btn" title="Cancel" href="{SITE_URL}">Cancel</a>
        </div>
    </form>
    <div class="black-border"></div>
</div>
</div>