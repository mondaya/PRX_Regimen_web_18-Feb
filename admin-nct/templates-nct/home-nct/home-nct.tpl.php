    <!-- BEGIN PAGE HEADER-->
<?php
    $qrySel = $this->db->pdoQuery(" SELECT * FROM  tbl_users where isActive = 'y'")->results();
    $countUser=count($qrySel);

    $qrySel = $this->db->pdoQuery(" SELECT * FROM  tbl_categories where isActive = 'y'")->results();
    $countCategory=count($qrySel);

    $qrySel = $this->db->pdoQuery(" SELECT * FROM  tbl_product_deals where isActive = 'y'")->results();
    $countProducts=count($qrySel);

    $qrySel = $this->db->pdoQuery(" SELECT * FROM  tbl_custom_orders")->results();
    $countCustomOrder=count($qrySel);
    
?>
    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN PAGE TITLE & BREADCRUMB-->
            <h3 class="page-title">
            Dashboard <small>statistics and more</small>
            </h3>
            <ul class="page-breadcrumb breadcrumb">
                <li>
                    <i class="fa fa-home"></i>
                        Home
                    <i class="fa fa-angle-right"></i>
                </li>
                <li>
                    <a href="#">
                        Dashboard
                    </a>
                </li>
                <li class="pull-right">
                    <div id="dashboard-report-range" class="dashboard-date-range tooltips" data-placement="top" data-original-title="Change dashboard date range">
                        <i class="fa fa-calendar"></i>
                        <span>
                        </span>
                        <i class="fa fa-angle-down"></i>
                    </div>
                </li>
            </ul>
            <!-- END PAGE TITLE & BREADCRUMB-->
        </div>
    </div>
    <!-- END PAGE HEADER-->
    <!-- BEGIN DASHBOARD STATS -->
    <div class="row">
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="dashboard-stat blue">
                <div class="visual">
                    <i class="fa fa-users"></i>
                </div>
                <div class="details">
                    <div class="number" id="getcustomer">
                        <?php echo $countUser; ?>
                    </div>
                    <div class="desc">
                         Users
                    </div>
                </div>
                
            </div>
            
            
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="dashboard-stat yellow">
                <div class="visual">
                    <i class="fa fa-sitemap"></i>
                </div>
                <div class="details">
                    <div class="number">
                        <?php echo $countCategory; ?>
                    </div>
                    <div class="desc">
                         Product Categories
                    </div>
                </div>
                
            </div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="dashboard-stat blue">
                <div class="visual">
                    <i class="fa fa-sitemap"></i>
                </div>
                <div class="details">
                    <div class="number">
                        <?php echo $countProducts; ?>
                    </div>
                    <div class="desc">
                         Product Deals
                    </div>
                </div>
                
            </div>
        </div>        
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="dashboard-stat green">
                <div class="visual">
                    <i class="fa fa-sitemap"></i>
                </div>
                <div class="details">
                    <div class="number">
                        <?php echo $countCustomOrder; ?>
                    </div>
                    <div class="desc">
                         Custom Orders
                    </div>
                </div>
                
            </div>
        </div>
        
        
    </div>
    <!-- END DASHBOARD STATS -->





