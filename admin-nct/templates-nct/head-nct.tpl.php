<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta content="width=device-width, initial-scale=1" name="viewport"/>
<?php echo $this->metaTag; ?>
<title><?php echo $this->title;?></title>
<!-- BEGIN GLOBAL MANDATORY STYLES -->
<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css"/>
<link href="<?php echo SITE_ADM_PLUGIN; ?>font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo SITE_ADM_PLUGIN; ?>bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo SITE_ADM_PLUGIN; ?>uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
<!-- END GLOBAL MANDATORY STYLES -->
<!-- BEGIN THEME STYLES -->
<link href="<?php echo SITE_ADM_CSS; ?>style-metronic.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo SITE_ADM_CSS; ?>style.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo SITE_ADM_CSS; ?>style-responsive.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo SITE_ADM_CSS; ?>plugins.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo SITE_ADM_CSS; ?>pages/tasks.css" rel="stylesheet" type="text/css"/>
<!--<link href="<?php echo SITE_ADM_CSS; ?>themes/light.css" rel="stylesheet" type="text/css" id="style_color"/>-->
<link href="<?php echo SITE_ADM_CSS; ?>themes/blue.css" rel="stylesheet" type="text/css" id="style_color"/>
<link href="<?php echo SITE_ADM_CSS; ?>print.css" rel="stylesheet" type="text/css" media="print"/>
<link href="<?php echo SITE_ADM_CSS; ?>custom.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo SITE_ADM_PLUGIN; ?>bootstrap-toastr/toastr.min.css" rel="stylesheet" type="text/css"/>
<link rel="stylesheet" type="text/css" href="<?php echo SITE_ADM_PLUGIN; ?>bootstrap-datepicker/css/datepicker.css"/>
<link type="text/css" rel="stylesheet" href="<?php echo SITE_CSS; ?>bootstrap-multiselect.css">

<!-- END THEME STYLES -->
<link rel="shortcut icon" type="image/ico" href="<?php echo SITE_IMG.SITE_FAVICON; ?>">

<?php echo load_css($this->styles); ?>
<script src="<?php echo SITE_ADM_PLUGIN; ?>jquery-1.10.2.min.js" type="text/javascript"></script>
<script src="<?php print SITE_INC;?>javascript-nct/jquery.numeric.js" type="text/javascript" charset="utf-8"></script>

<script type="text/javascript" src="<?php echo SITE_JS; ?>bootstrap-multiselect.js"></script>
<script type="text/javascript" src="<?php echo SITE_JS; ?>bootstrap-multiselect.js"></script>


<?php echo (!empty($metaTag) ? $metaTag : ''); ?>
<script language="javascript" type="text/javascript">
 	var siteName = '<?php echo SITE_URL; ?>';
	var SITE_ADM_IMG = '<?php echo SITE_ADM_IMG; ?>';
	$(function(){
		var mBar = $('.page-sidebar-menu').find('li.sm-<?php echo $this->module;?>');
		mBar.addClass('active');
		mBar.parents('ul.sub-menu').parent('li').addClass('active');
	});



	function deleteProductImg(product_image_id) {
			var result = confirm("are you sure to delete this image ?");
			if (result) {
				$.ajax({
					type: "POST",
					url:"<?php echo SITE_ADM_MOD.$this->module ?>/ajax.<?php echo $this->module;?>.php",
					data: {"action": "deleteImg", "imageId": product_image_id},
					success: function() {
						$("#img_"+product_image_id).remove();

					}
				});
			}
	}
</script>
<?php //echo GOOGLE_ANA_CODE_COM; ?>
