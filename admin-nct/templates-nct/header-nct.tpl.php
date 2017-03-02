<noscript>
<div style="background-color:#F90; border:#666; font-size:22px; padding:15px; text-align:center"><strong>Please enable your javascript to get better performance.</strong></div>
</noscript>
<div class="modal fade" id="myModal_autocomplete" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
              <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn default" data-dismiss="modal">Close</button>
			</div>
        </div>
    </div>
</div>
<div class="header navbar navbar-fixed-top custom_navbar">
	<div class="header-inner">
		<!-- BEGIN LOGO -->
		<a class="navbar-brand custom_navbar-brand" href="<?php echo SITE_URL; ?>" title="<?php echo SITE_NM; ?>" target="_blank">
        	<!--<img src="<?php echo SITE_IMG; ?>final-02.png" />-->
            <div class="custom" style="margin-top:11px;margin-left:20px;">
			<?php echo SITE_NM; ?>
            </div>
		</a>
		<!-- END LOGO -->
		<!-- BEGIN RESPONSIVE MENU TOGGLER -->
		<a href="javascript:;" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
			<img src="<?php echo SITE_ADM_IMG ?>menu-toggler.png" alt=""/>
		</a>
		<!-- END RESPONSIVE MENU TOGGLER -->
		<!-- BEGIN TOP NAVIGATION MENU -->
		<ul class="nav navbar-nav pull-right">

			<!-- BEGIN USER LOGIN DROPDOWN -->
			<li><span class="username">Last Login</span> <br/><span class="username"><?php echo ucfirst($_SESSION["last_login"]); ?></span></li>

			<li class="dropdown user">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
					<img alt="" src="<?php echo SITE_ADM_IMG ?>avatar.png" width="28"/>
					<span class="username">
						 Hi, <?php echo ucfirst($_SESSION['uName']); ?>
					</span>
                   <i class="fa fa-angle-down"></i>
				</a>
				<ul class="dropdown-menu">

					<li>
						<a href="javascript:;" id="trigger_fullscreen">
							<i class="fa fa-arrows"></i> Full Screen
						</a>
					</li>

					<li>
						<a href="<?php echo SITE_ADM_INC;?>logout-nct.php">
							<i class="fa fa-key"></i> Log Out
						</a>
					</li>
				</ul>
			</li>
			<!-- END USER LOGIN DROPDOWN -->
		</ul>
		<!-- END TOP NAVIGATION MENU -->
	</div>
</div>
<div class="clearfix"></div>


<div class="modal fade in" id="Edit_Profile1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">

    <div class="modal-content" style="width:800px;">
      <div class="modal-header_1">
       <!--  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button> -->
        <h4 class="modal-title_1 text-center blue-text" id="myModalLabel">Crop Image</h4>
      </div>
      <div class="modal-body_1">
          <div class="edit-profile-block">

<div class="container2"  id="crop-avatar">

 <!-- <div class="container" id="crop-avatar"> -->
 <!-- Current avatar -->
    <!--<div class="avatar-view" title="Change the avatar">
      <!-- <img src="img/picture.jpg" alt="Avatar">-->
    <!-- </div> -->

    <!-- Cropping modal -->
          <form class="avatar-form" action="crop.php" enctype="multipart/form-data" method="post" name="avtar_form" id="avtar_form">
            <!-- <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title" id="avatar-modal-label">Change Avatar</h4>
            </div> -->
            <div class="modal-body_1">
              <div class="avatar-body">

                <!-- Upload image and data -->
                <div class="avatar-upload">
                  <input type="hidden" class="avatar-src" name="avatar_src">
                  <input type="hidden" class="avatar-data" name="avatar_data">
                  <input type="hidden"  name="which_types" id="which_types">

                  <label for="avatarInput">Local upload</label>
                  <input type="file" class="avatar-input" id="avatarInput" name="avatar_file">
                </div>

                <!-- Crop and preview -->
                <div class="row">
                  <div class="col-md-12">
                    <div class="avatar-wrapper"></div>
                  </div>

                </div>

                <div class="row avatar-btns">
                  <div class="col-md-9">

                  </div>
                 <!--  <div class="col-md-3">
                   <div id="hidden_image_id">&nbsp;  </div>
                   <button type="button" class="btn btn-primary btn-block avatar-save" onclick="return showdata();">Done</button>
                 </div> -->

                  <div class="col-md-3">
                    <div id="hidden_image_id" style="display:none;"></div>
                    <button type="button" style="float:left; width:50%" class="btn btn-primary btn-block avatar-save" onclick="return showdata();">Done</button>
                      <button type="button" style="float:right" class="btn btn-default" data-dismiss="modal" id="close_popup">Cancel</button>
                  </div>

                </div>
              </div>
            </div>
            <!-- <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div> -->
          </form>
        <!-- /.modal -->

    </div>
        </div>
      </div>
      <!-- <div class="modal-footer text-center">
       <button type="button" class="btn btn-primary">Save changes</button>
        <button type="button" class="btn btn-default" data-dismiss="modal" id="close_popup">Cancel</button>
      </div> -->
    </div>
  </div>
</div>
