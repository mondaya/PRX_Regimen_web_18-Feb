<li class="dropdown" id="login_dropdown">
    <a class="dropdown-toggle" id="login_dropdown_link" href="javascript:void(0);" data-toggle="dropdown" title="Login"><h5>LOGIN</h5></a>
    <div class="dropdown-menu login-dropdown">
        <span class="menu-arrow"></span>
        <div id="login_div">
            <form id="frmLogin" action="<?php echo SITE_URL; ?>" name="frmLogin" method="POST">
                <div class="form-group">
                    <input type="email" name="username" id="username" tabindex="1" class="form-control" placeholder="Email Id" value="%EMAIL%">
                    <input type="password" name="password" id="password" tabindex="2" class="form-control" placeholder="Password" value="%PASSWORD%">
                    <input type="text" name="secret" id="secret" tabindex="2" class="form-control" placeholder="Secret" value="%SECRET%">
                    <input type="checkbox" tabindex="3" class="" name="isRemember" id="isRemember" value="y" %CHECKED%>
                    <label for="remember"> Remember Me</label>

                    <a href="javascript:void(0);" id="forgotpass_btn" tabindex="5" class="forgot-password" title="Forgot Password?">Forgot Password?</a>

                    <div class="form-group text-center">
                        <input type="submit" name="login-submit" id="login-submit" tabindex="4" class="form-control btn btn-login" value="Log In" title="Log In">
                    </div>
                </div>
                <div class="form-group text-center">
                    <p class="or-line"><span class="or">OR</span></p>
                </div>
                <div class="social-login-btn">
                    <a href="javascript:void(0);" id="login_fb" title="Login With Facebook">
                        <img src="{SITE_IMG}fb.jpg" alt="Facebook">
                    </a>
                    <a href="javascript:void(0);" id="login_google" title="Login With Google+">
                        <img src="{SITE_IMG}google+.png" alt="Google+">
                    </a>
                </div>
            </form>
        </div>
        <div id="forgot_div" style="display: none;">
            <form id="frmForgot" name="frmForgot" method="POST">
                <div class="form-group">
                    <label for="remember"> Enter your e-mail address below to reset your password.</label>
                    <input type="email" name="forgot_username" id="forgot_username" value="%EMAIL%" class="form-control" placeholder="Email Id"/>
                    <div class="form-group text-center">
                        <input type="submit" name="forgot-submit" id="forgot-submit" class="form-control btn btn-login" value="Submit" title="Submit">
                    </div>
                    <a href="javascript:void(0);" id="go_back_btn" class="forgot-password" title="Back"> Back</a>
                </div>
            </form>
        </div>
    </div>
</li>
<li><a href="{SITE_URL}registration" title="Signup"><h5>SignUp</h5></a></li>