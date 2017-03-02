<div class="slider">
    <div class="carousel fade-carousel slide" data-ride="carousel" data-interval="4000" id="bs-carousel">
        <ol class="carousel-indicators">
            %INDICATORS%
        </ol>
        <div class="carousel-inner">
            %SLIDER%
        </div>
    </div>
</div>

<div class="container">
    <div class="supported-brands-slider">
        <div class="form-group">
            <div class="icon-addon addon-md">
                
                <form action="{SITE_URL}searchDeals" method="post">
                    <input type="text" class="form-control" name="searchText" id="searchText" placeholder="Search by keyword">
                </form>
                
                <label for="search" class="fa fa-search" aria-hidden="true" rel="tooltip" title="search"></label>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="supported-brands-slider">
        <a><h4 title="Supported Brands">Supported Brands</h4></a>

        <div class="col-lg-10">
            <div id="Carousel" class="carousel slide">

                <!-- Carousel items -->
                <div class="carousel-inner brand-inner">
                    %STORES%
                </div>
                <!--.carousel-inner-->
            </div>
            <!--.Carousel-->

        </div>
    </div>
</div>

<div class="container">
    %TOP_CATEGORY%

    <div id="cssmenu">
        <ul>
            <li class="step-1">
                    <h4>%STEP1TITLE%</h4>
                
                <ul>
                    <li class="step-info">
                            %STEP1DESC%
                    </li>
                </ul>
            </li>

            <li class="step-2">
                    <h4>%STEP2TITLE%</h4>
                
                <ul>
                    <li class="step-info">
                            %STEP2DESC%
                    </li>
                </ul>
            </li>
            <li class="step-3">
                    <h4>%STEP3TITLE%</h4>
                
                <ul>
                    <li class="step-info">
                            %STEP3DESC%
                    </li>
                </ul>
            </li>
            <li class="download-app">
                <div class="download-from">
                    <h4>Download app from</h4>
                    <div class="android">
                        <a href="{GOOGLE_STORE}" title="Google Play" target="_blank"><img src="{SITE_IMG}Android1.png" width="50px" height="50px" alt="Android" >Google Play</a>
                    </div>
                    <div class="apple">
                        <a href="{APPLE_STORE}" title="Apple Store" target="_blank"><img src="{SITE_IMG}Apple-icon1.png" width="50px" height="50px" alt="Apple" >Apple Store</a>
                    </div>
                </div>
                <div class="video-url"><a data-toggle="modal" data-target="#video" title="Introductory Video">Introductory Video</a></div>
            </li>
        </ul>
    </div>


    <!-- <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
                <ol class="carousel-indicators">
            %BANNER_INDICATORS%
        </ol>
        <div class="carousel-inner product-inner">
            %BANNER_SLIDER%
        </div>
        <a class="left carousel-control" href="#carousel-example-generic" data-slide="prev">
            <span class="left-arrow"><i class="fa fa-angle-left" aria-hidden="true"></i>
            </span>
        </a>
        <a class="right carousel-control" href="#carousel-example-generic" data-slide="next">
            <span class="right-arrow"><i class="fa fa-angle-right" aria-hidden="true"></i>
            </span>
        </a>
    </div> -->


    <div class="slider-url">
       <div id="carousel-example-generic" class="carousel slide">
          <!-- Carousel items -->
          <div class="carousel-inner">
             %BANNER%
          </div>
          <!--.carousel-inner-->
          <a class="left carousel-control" href="#carousel-example-generic" data-slide="prev">
          <span class="left-arrow"><i class="fa fa-angle-left" aria-hidden="true"></i>
          </span>
          </a>
          <a class="right carousel-control" href="#carousel-example-generic" data-slide="next">
          <span class="right-arrow"><i class="fa fa-angle-right" aria-hidden="true"></i>
          </span>
          </a>
       </div>
       <!--.Carousel-->
    </div>

    


    <!-- <div class="content">
        <div id="toggle-btn">
            <ul class="toggle-ul">
                <li class="has-sub">
                    <a href="javascript:void(0)" class="arrow-up">
                        <span class="slide-down">%WHYTITLE%
                        </span>
                    </a>
                    %WHYWLLNESS%

                </li>
            </ul>
        </div>
    </div> -->
    <div class="accordion">
    <h1>%WHYTITLE%</h1>
    <div class="accordion-head">
        
          <div class="arrow down"></div>

    </div>
  
    <div class="accordion-body">
        %WHYWLLNESS%
    </div>
    
</div>
</div>

<script type="text/javascript">
$('.accordion').each(function () {
        var $accordian = $(this);
        $accordian.find('.accordion-head').on('click', function () {
            $(this).removeClass('open').addClass('close');
            $accordian.find('.accordion-body').slideUp();
            if (!$(this).next().is(':visible')) {
                $(this).removeClass('close').addClass('open');
                $(this).next().slideDown();
            }
        });
    });
</script>
