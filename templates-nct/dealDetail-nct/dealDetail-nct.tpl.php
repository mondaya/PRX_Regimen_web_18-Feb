<div class="common-bg">
    <div class="heading container category">
        <div class="col-lg-3">
            <div class="border"></div>
        </div>
        <div class="col-lg-6">
            <h1>%PRODUCT_NM%</h1>
        </div>
        <div class="col-lg-3">
            <div class="border"></div>
        </div>
    </div>
</div>

<div class="main">
    <div class="container">
        <div class="heading-btn">
            <h2>Product Details</h2>
            <a href="#">
                <img id="share_button" class="right-btn" src="{SITE_IMG}share-fb.jpg" alt="fb-share" width="125px" height="50px" title="Share">
            </a>
        </div>

        <div class="product-details">
            <div class="product-slider">
                <div class="carousel fade-carousel slide" data-ride="carousel" data-interval="4000" id="bs-carousel">

                    <!-- Wrapper for slides -->
                    <div class="carousel-inner">
                        %IMAGE_SLIDER%
                    </div>
                    
                </div>
                <!--.container-->
            </div>

            <div class="my-profile product-info">
                <label>Name :</label>
                <span>%PRODUCT_NM%</span>
                <label>Category : </label>
                <span>%CATE_NM%</span>
                <label>Sub Category :</label>
                <span>%SUB_CATE%</span>
                <div class="clearfix"></div>
                <label>Posted Date :</label>
                <span>%POSTED_DATE%</span>
                <div class="clearfix"></div>
                <label>Weight :</label>
                <span>%WEIGHT% Kg</span>
                <div class="clearfix"></div>
                <label>Price :</label>
                <span>%DISCOUNT_PRICE% &nbsp; <div class="grey">%ACTUAL_PRICE%</div></span>
                <div class="clearfix"></div>
                <label>Discount :</label>
                <span>%DIS_PER%</span>
                <div class="clearfix"></div>
                <label>Available Quantity : </label>
                <span>%QUANTITY%</span>
                <div class="clearfix"></div>
                
                
                <form action="{SITE_URL}add-to-cart/%DEAL_ID%" method="post">
                    <label>Select Quantity : </label>
                    <span>%QUI_OPTION%</span>
                    <input type="hidden" name="dealId" value="%DEAL_ID%">
                    <div class="clearfix"></div>
                    <input class="btn btn-default blue-btn add-cart-btn" type="submit" name="submit" value="Add to cart">
                </form>
            </div>
        </div>
        <div class="my-profile product-cntent">
            <label>Description : </label>
            <span>%DESC%</span>
        </div>
        <div class="black-border"></div>

        <div class="heading-btn">
            <h2>Similar Product Deals</h2>
        </div>
        <div class="product-category">
            %SIMILAR_DEALS%
        </div>
        <div class="black-border"></div>
    </div>
</div>

<div id="fb-root"></div>
<script>
    window.fbAsyncInit = function() {
        FB.init({appId: 'FB_APP_ID', status: true, cookie: true,
        xfbml: true});
        };
        (function() {
        var e = document.createElement('script'); e.async = true;
        e.src = document.location.protocol +
        '//connect.facebook.net/en_US/all.js';
        document.getElementById('fb-root').appendChild(e);
    }());
</script>

<script type="text/javascript">
$(document).ready(function(){
    $('#share_button').click(function(e){
        e.preventDefault();
        FB.ui(
        {
            method: 'feed',
            name: '%PRODUCT_NM%',
            link: '{SITE_URL}product/%DEAL_ID%',
            picture: '%PRODUCT_IMG%',
            caption: '%PRODUCT_NM%'
        });
    });
});
</script>

