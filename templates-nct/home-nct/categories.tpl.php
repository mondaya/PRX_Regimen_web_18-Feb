<div class="category-tab">
    <ul class="nav nav-tabs" role="tablist">
        %TOP_CATEGORY%
        <li role="presentation"><a href="#AllCategories" aria-controls="home" role="tab" data-toggle="tab" title="All Categories">All Categories</a></li>
        <li role="presentation"><a href="#AllDeals" aria-controls="home" role="tab" data-toggle="tab" title="All Deals">All Deals</a></li>
    </ul>

    <div class="tab-content">
        %SUB_CATEGORIES%
        <div role="tabpanel" class="tab-pane" id="AllCategories">
            <div class="tab-head">
                <h3>All Categories</h3>
            </div>
            <div class="view-all">
                <a href="{SITE_URL}categories" title="View All">View All</a>
            </div>
            <div class="category-list">
                %ALL_CATEGORIES%
            </div>
        </div>
        <div role="tabpanel" class="tab-pane" id="AllDeals">
            <div class="tab-head">
                <h3>All Deals</h3>
            </div>
            <div class="view-all">
                <a href="{SITE_URL}searchDeals" title="View All">View All</a>
            </div>
            <div class="category-list">
                %ALL_DEALS%
            </div>
        </div>
        <div class="arrow">
            <a href="javascript:void(0);" class="box-height">
                <i class="fa fa-angle-down arrow-btn" aria-hidden="true"></i>
            </a>
        </div>
    </div>
</div>