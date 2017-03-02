<div class="common-bg">
    <div class="heading container">
        <div class="col-lg-4">
            <div class="border"></div>
        </div>
        <div class="col-lg-4">
            <h1>Edit Profile</h1>
        </div>
        <div class="col-lg-4">
            <div class="border"></div>
        </div>
    </div>
</div>

<div class="main">
    <div class="container">
        
        <div class="left-heading">
            <h2>Upload Picture</h2>
        </div>

        <div class="container12" id="crop-avatar">
            <center>
                <button type="button" class="btn btn-default blue-btn profile" title="Browse">Browse</button>
                    <div class="select-user-img avatar-view">
                        <img src="%USER_AVATAR%" alt="%USER_NAME%" title="%USER_NAME%" width="100px" height="100px"/>
                    </div>
            </center>

            <!-- Cropping modal -->
            <div class="modal fade" id="avatar-modal" aria-hidden="true" aria-labelledby="avatar-modal-label" role="dialog" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <form class="avatar-form" action="%CROP_PATH%" enctype="multipart/form-data" method="post">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title" id="avatar-modal-label">Change Avatar</h4>
                            </div>
                            <div class="modal-body">
                                <div class="avatar-body">

                                    <!-- Upload image and data -->
                                    <div class="avatar-upload">
                                        <input type="hidden" class="avatar-src" name="avatar_src" />
                                        <input type="hidden" class="avatar-data" name="avatar_data" />
                                        <input type="hidden" class="user_id" name="user_id" value="%USER_ID%" />
                                        <label for="avatarInput">Local upload</label>
                                        <input type="file" class="avatar-input" id="avatarInput" name="avatar_file">
                                    </div>

                                    <!-- Crop and preview -->
                                    <div class="row">
                                        <div class="col-md-9">
                                            <div class="avatar-wrapper"></div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="avatar-preview preview-lg"></div>
                                            <div class="avatar-preview preview-md"></div>
                                            <div class="avatar-preview preview-sm"></div>
                                        </div>
                                    </div>

                                    <div class="row avatar-btns">
                                        <div class="col-md-9">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-primary" data-method="rotate" data-option="-90" title="Rotate -90 degrees">Rotate Left</button>
                                                <button type="button" class="btn btn-primary" data-method="rotate" data-option="-15">-15deg</button>
                                                <button type="button" class="btn btn-primary" data-method="rotate" data-option="-30">-30deg</button>
                                                <button type="button" class="btn btn-primary" data-method="rotate" data-option="-45">-45deg</button>
                                            </div>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-primary" data-method="rotate" data-option="90" title="Rotate 90 degrees">Rotate Right</button>
                                                <button type="button" class="btn btn-primary" data-method="rotate" data-option="15">15deg</button>
                                                <button type="button" class="btn btn-primary" data-method="rotate" data-option="30">30deg</button>
                                                <button type="button" class="btn btn-primary" data-method="rotate" data-option="45">45deg</button>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <button type="submit" class="btn btn-primary btn-block avatar-save">Done</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- /.modal -->
        </div>
        <form method="POST" name="frmUser" id="frmUser">
            <div class="left-heading">
                <h2>Personal Information </h2>
            </div>
            <div class="col-lg-9 col-lg-offset-3">
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

                <div class="form-inline">
                <div class="form-group col-lg-3 emali-group">
                        <input class="form-control reg-control" name="email" id="email" placeholder="Email*" type="email" value="%EMAIL%" disabled>
                    </div>
                    <div class="form-group col-lg-1 country-group">
                        <input class="form-control country-code" name="countryCode" id="countryCode" placeholder="Code*" type="text" value="%COUNTRY_CODE%">
                    </div>              
                    <div class="form-group col-lg-3 name-group">
                        <input class="form-control regmob-control" name="mobile" id="mobile" placeholder="Mobile no.*" type="text" value="%MOBILE%">
                    </div>
                </div>
                <div class="clearfix"></div>

                <div class="form-inline full-width-text">
                    <div class="form-group col-lg-4">
                        <select class="gender" name="gender" id="gender">
                            <option value="m" %SELECTED_M%>Male</option>
                            <option value="f" %SELECTED_F%>Female</option>
                        </select>
                    </div>
                    <div class="form-group col-lg-4">
                        <input class="form-control datepicker" name="birthDate" id="birthDate" placeholder="Birth date*" type="text" value="%BIRTH%"/>
                        <i class="fa fa-calendar edit-calender" aria-hidden="true"></i>
                    </div>
                </div>
                <div class="clearfix"></div>

                <div class="form-inline">
                    <div class="form-group col-lg-4">
                        <textarea class="form-control comment" rows="3" name="address" id="address" placeholder="Address*"  >%ADDRESS%</textarea>
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
                </div>
                <div class="clearfix"></div>

                <div class="form-inline">

                    <div class="form-group col-lg-4">
                        <input class="form-control comment" name="paypalEmail" id="paypalEmail" placeholder="Paypal Email" type="text" value="%PMAIL%" />
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="left-heading">
                <h2>Secret Word</h2>
            </div>

            <div class="col-lg-9 col-lg-offset-3">
            <div class="form-inline">
                <div class="form-group col-lg-4">
                    <input class="form-control comment" name="secret" id="secret" placeholder="Secret Word*" type="text" value="%SECRET_WORD%" />
                </div>
            </div></div>
            <div class="clearfix"></div>

            <div class="black-border"></div>
            <div class="form-inline">
                <input type="submit" name="sbtUpdate" id="sbtUpdate" value="Update" class="btn btn-default blue-btn" title="Update"></input>
                <a href="%BACK_BTN_URL%" name="CancelForm" id="CancelForm" class="btn btn-default blue-btn" title="Cancel">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){

        $(".profile").click(function(){
            $(".avatar-view").click(); 
            return false;
        });

        $(".datepicker").datepicker({ dateFormat: 'dd-mm-yy' ,changeMonth:true,changeYear:true,yearRange:"-60:+0"});

        $(document).on("change","#country",function(){
            $("#state").html('<option value="">--Please Select state*--</option>');
            $("#city").html('<option value="">--Please Select city*--</option>');

            var cId = $(this).val();
            if(cId > 0){
                $.ajax({
                    url: '<?php echo SITE_MOD.$this->module.'/';?>ajax.<?php echo $this->module;?>.php',
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
                    url: '<?php echo SITE_MOD.$this->module.'/';?>ajax.<?php echo $this->module;?>.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {action: 'getCities',id: sId},
                    success:function(response){
                        $("#city").html(response.cities);
                    }
                });

            }
        });

        $.validator.addMethod("nowhitespace", function(value, element) {
                return this.optional(element) || /^\S+$/i.test(value);
            }, "No white space allow");

        $("#frmUser").validate({
            errorClass: 'help-block',
            errorElement: 'label',
            rules: {
                firstName: { required: true },
                lastName: { required: true },
                countryCode: { required: true ,number:true},
                mobile: { required: true, digits: true, minlength:10},
                gen: { required: true },
                secret: { required: true ,nowhitespace:true},
                country: { required: true },
                state: { required: true },
                city: { required: true },
                birthDate: { required: true },
                address: { required: true },
                paypalEmail: {email: true },
                zipCode: {required: true}
            },
            messages: {
                firstName: { required: "Please Enter FirstName" },
                lastName: { required: "Please Enter Lastname" },
                country: { required: "Please select Country" },
                state: { required: "Please select State" },
                city: { required: "Please select City" },
                gen: { required: "please select gender" },
                countryCode: {
                    required: "Please enter country code",number:"Number only"
                },
                mobile: { required: "Please enter your mobile number", minlength: "Minimum 10 numbers" },
                secret: { required: "please enter secret word" },
                birthDate: { required: "Please select your birth date" },
                address: { required: "Please enter  address" },
                paypalEmail: { email: "Please enter valid email address" },
                zipCode: { required: "Please enter zip code"}
            },
            errorPlacement: function (error, element) {
                if (element.attr("name") == "birthDate" ){
                    error.insertAfter(".edit-calender");
                }else{
                    error.insertAfter(element);
                }
            }
        });
    });
</script>