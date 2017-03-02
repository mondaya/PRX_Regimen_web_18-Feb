<nav class="panel">
    <ul>
        <li>
            <div class="openSubPanel"><a href="{SITE_URL}" title="Home"><i class="fa fa-home"></i>Home</a></div>
        </li>
        <li>
            <div class="closePanel"><a data-toggle="modal" data-target="#video" title="Video Intro"><i class="fa fa-play-circle-o"></i> Video Intro</a></div>
        </li>
        <li>
            <div class="openSubPanel"><i class="fa fa-download"></i><a href="javascript:" title="Download Apps"> Download Apps</a></div>
        </li>
        <li>
            <div class="openSubPanel"><a href="{SITE_URL}contactUs" title="Contact Us"><i class="fa fa-phone"></i> Contact Us</a>
            </div>
        </li>
    </ul>
    <ul>
        <li>
            <div class="openSubPanel">
                <a href="{SITE_URL}searchDeals" title="Product Deals">
                    <i class="fa fa-gavel"></i> Product Deals
                </a>
            </div>
        </li>
        <li>
            <div class="openSubPanel">
                <a href="{SITE_URL}stores" title="Product Brands">
                    <i class="fa fa-shopping-bag"></i> Product Brands
                </a>
            </div>
        </li>
    </ul>

    
    <ul>
        <?php if($_SESSION['sessUserId'] > 0){ ?>
        <li>
            <div class="openSubPanel"><a href="{SITE_URL}account" title="My Account"><i class="fa fa-user"></i> My Account</a>
            </div>
        </li>
        <li>
            <div class="openSubPanel"><a href="{SITE_URL}notifications" title="Notification"><i class="fa fa-bell"></i> Notification</a>
            </div>
        </li>
        <li>
            <div class="openSubPanel">
                %LOGIN_LOGOUT_BTN%
            </div>
        </li>
        
        <?php } ?>
        <li class="closePanel">
            <a href="javascript:void(0);" title="Close"><i class="fa fa-times"></i> Close</a>
        </li>
    </ul>
    
</nav>

<div class="wrapper">
    %FOOTER_DIV_START%
	%CONTAINER_BEFORE_LOGIN%
        <div class="%CONTENT_CLASS%">
            <div class="main-head %FULL_HEADER%">
                %CONTAINER_AFTER_LOGIN%
                <div class="logo">
                    <a href="{SITE_URL}" title="Home"><img src="{SITE_IMG}{SITE_LOGO}" alt="{SITE_NM}" width="100" height="50"></a>
                </div>
                <div class="menu">
                    <ul>
                        <li>
                            <img src="{SITE_IMG}paypal.png" title="Paypal" alt="payment-gateway" width="70" height="35" />
                            <img src="{SITE_IMG}MASTERCARD-LOGO.png" title="Master Card" alt="payment-gateway" width="70" height="35" />
                            <img src="{SITE_IMG}VISA-LOGO.png" title="Visa" alt="payment-gateway" width="70" height="35" />
                        </li>
                        <li>
                            <div class="dropdown">
                                <button class="btn btn-default dropdown-toggle select-currency" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                    %CUR_CURRENCY%
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu currency" aria-labelledby="dropdownMenu1">
                                    %CURRENCY_DROPDOWN%
                                </ul>
                            </div>
                        </li>
                        %LOGIN_USER%
                        <li>
                            <div class="menuTrigger">
                                <a href="javascript:void(0);" title="Menu">
                                    <h5>menu<i class="fa fa-bars"></i></h5></a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>