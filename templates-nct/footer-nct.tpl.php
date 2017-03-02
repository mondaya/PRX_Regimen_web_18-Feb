%FOOTER_DIV_END%
<footer>
		<div class="bottom-main container">
			<ul class="bottom-left">
				<h3>Social</h3>
				<div class="social-icon">
					<li>
						<a href="{FB_PAGE_URL}" target="_blank" title="Facebook"><img src="{SITE_IMG}facebook.png" alt="Facebook"></a>
					</li>
					<li>
						<a href="{TWIT_PAGE_URL}" target="_blank" title="Twitter"><img src="{SITE_IMG}twitter.png" alt="Twitter"></a>
					</li>
					<li>
						<a href="{GOOGLE_PLUS_URL}" target="_blank" title="Google+"><img src="{SITE_IMG}g+.png" alt="Google+"></a>
					</li>
				</div>
				<h3>Subscribe to Newsletter</h3>
				<div class="email-address">
					<form id="frmSubscribe" name="frmSubscribe" method="POST">
						<div class="email-text">
							<input type="email" name="subEmail" id="subEmail" class="form-control search-text required" placeholder="Enter Email Address" />
							<input type="submit" name="btnSubscribe" id="btnSubscribe" value="GO" class="btn btn-default search-btn" title="GO"/>
						</div>
						<div id="sub_error"></div>
					</form>
				</div>
			</ul>
			<ul class="bottom-left">
				<h3>Shopping</h3>
				%SUPPORT_DATA%
			</ul>
			<ul class="bottom-left">
				<h3>Help & Support</h3>
				%HELP_SUPPORT%
			</ul>
			<ul class="bottom-left">
				<h3>Ewallet</h3>
				%WALLET%
			</ul>
		</div>
		<div class="copyright">
			<div class="container">
				<h5>Copyright © <?php echo date('Y'); ?> {SITE_NM}. All Rights Reserved </h5>
				<a class="nct-logo" href="#"><img src="{SITE_IMG}nct-logo.png"></a>
			</div>
		</div>
	</footer>
</div>

<div class="loading" aria-label="Loading" role="img" tabindex="-1"></div>

<script src="{SITE_JS}jquery-ui.js"></script>
<link rel="stylesheet" type="text/css" href="{SITE_CSS}jquery-ui.css">


<div class="modal fade" id="video" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h2>Introductory Video</h2>
         </div>
         <div class="modal-body fav-cate">
               <?php echo VIDEO_EMBED; ?>
         </div>
      </div>
   </div>
</div>