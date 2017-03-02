<div class="common-bg">
    <div class="heading container category">
        <div class="col-lg-4">
            <div class="border"></div>
        </div>
        <div class="col-lg-4">
            <h1>Product Deals</h1>
        </div>
        <div class="col-lg-4">
            <div class="border"></div>
        </div>
    </div>
</div>

<div class="main">
    <div class="container">
        <div class="heading-btn">
            <h2>Select Product</h2>
            <a class="btn btn-default blue-btn right-btn" href="{SITE_URL}orderCart" title="View Cart"> <i class="fa fa-shopping-cart" aria-hidden="true"></i> View Cart(<span id="cartCount">%CART_COUNT%</span>)</a>
        </div>
        <div class="product-filter">
          <form name="searchDeals" action="{SITE_URL}searchDeals" method="post">
           <div class="search-box">
                    <div class="form-inline full-width-text">
                        <div class="form-group search-btm">
	                        <input type="text" name="searchText" placeholder="Search by keyword" class="form-control order-form" value="%SEARCH_TEXT%">
                            <button class="btn search-filed btn-link" type="submit"> <i class="fa fa-search" aria-hidden="true"></i></button>
	                    </div>
                        <div class="form-group">
	                    <select class="gender order-form" name="cateId" id="cateId" onchange="this.form.submit()">
	                        <option value="">Filter by category</option>
	                        %CATE_OPTION%
	                    </select>
	                </div>
                        <div class="form-group">
	                    <select class="gender order-form" name="subCateId" id="subCateId" onchange="this.form.submit()">
	                        <option value="">Filter by subcategory</option>
	                        %SUBCATE_OPTION%
	                    </select>
	                </div>
                    </div>
                </div>
            </form>
        </div>
            <!--<div class="form-inline">
            	<form name="searchDeals" action="{SITE_URL}searchDeals" method="post">
	                <div class="search-box">
	                    <div class="form-inline full-width-text">
                        <div class="form-group search-btm">
	                        <input type="text" name="searchText" id="" placeholder="Search by keyword" class="form-control order-form" value="%SEARCH_TEXT%">
                            <button class="btn search-filed btn-link" type="submit"> <i class="fa fa-search" aria-hidden="true"></i></button>
	                    </div>
	                </div>
	                <div class="form-group">
	                    <select class="gender order-form" name="cateId" id="cateId" onchange="this.form.submit()">
	                        <option value="">Filter by category</option>
	                        %CATE_OPTION%
	                    </select>
	                </div>
	                <div class="form-group">
	                    <select class="gender order-form" name="subCateId" id="subCateId" onchange="this.form.submit()">
	                        <option value="">Filter by subcategory</option>
	                        %SUBCATE_OPTION%
	                    </select>
	                </div>
            	</form>
                
            </div>-->
        
        <div class="list">
        	%DEAL_LIST%
        	%PAGINATION%
    	</div>
            
        </div>
    </div>
<!--</div>-->

<script type="text/javascript">
    $(document).ready(function(){

        //For paging
        $(document).on("click",".buttonPage",function(){
          var searchText = $('#searchText').val();
          var cateId = $('#cateId').val();
          var subCateId = $('#subCateId').val();
          var page = $(this).data("page");
          $.ajax({
                type: 'POST',
                url: '<?php echo SITE_MOD; ?>deals-nct/ajax.deals.php',
                data: {searchText: searchText,cateId: cateId,subCateId: subCateId,page: page,action:'paging'},
                success: function(data) {
                    $('.list').html(data);
                    $(window).scrollTop(0);
                }
            });
        });


        //For add to cart
        $(document).on("click",".add-cart-btn",function(){ 
          var dealId = $(this).data("id");
          
          $.ajax({
                type: 'POST',
                url: '<?php echo SITE_MOD; ?>deals-nct/ajax.deals.php',
                data: {dealId: dealId,action:'add-to-cart'},
                dataType : 'json',
                success: function(data) {
                    if(data.status == 's')
                    {
                        $('#cartCount').text(data.cartProduct);
                        toastr["success"]("Added successfully");
                    }
                    else if(data.status == 'a')
                    {
                        toastr["error"]("Already in cart");
                    }
                    else if(data.status == 'n')
                    {
                        toastr["error"]("Please login to continue");
                        $('.login-dropdown').show();
                    }
                    
                    $(window).scrollTop(0);
                }
            });
        });
    });
</script>