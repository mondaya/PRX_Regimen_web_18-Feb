<div class="main order-main">
    <div class="container">
        <div class="order-list">
            <h2 class="title-border">My Orders</h2>
            <form name="costomOrder" action="" method="post">
                <div class="search-box">
                    <div class="form-inline full-width-text">
                        <div class="form-group search-btm">
                            <input type="text" name="searchText" id="" placeholder="Search by keyword" class="form-control order-form" value="%SEARCH_TEXT%">
                            <button class="btn search-filed btn-link" type="submit"> <i class="fa fa-search" aria-hidden="true"></i></button>
                        </div>
                        <div class="form-group pos-rel">
                            <input type="text" placeholder="Filter by date" name="date" class="gender form-control order-form" id="datepicker" onchange="this.form.submit()" value="%DATE%">
                            <i class="fa fa-calendar" aria-hidden="true"></i>
                        </div>
                        <div class="form-group">
                            <select class="gender order-form" name="status" id="status" onchange="this.form.submit()">
                                <option value="">Filter by status</option>
                                <option value="d" %SELECTED_D%>Delivered</option>
                                <option value="s" %SELECTED_S%>Shipped</option>
                                <option value="p" %SELECTED_P%>Pending</option>
                                <option value="r" %SELECTED_R%>Returned</option>
                            </select>
                        </div>
                    </div>
                </div>
            </form>
            <div class="table-responsive place-order">
                <table class="table custom-order">
                    <thead>
                        <tr>
                          <th>Order Id</th>
                          <th>Purchase date</th>
                          <th>Product name</th>
                          <th>Transaction ID</th>
                          <th>total amount</th>
                          <th>Status</th>
                          <th>delete</th>
                        </tr>
                    </thead>
                    
                    <tbody class="data">
                        %ORDER_LIST%
                    </tbody>

                </table>
            </div>
            
            <div class="paging">
            %PAGINATION%
            </div>
        
        </div>
        <div class="clearfix"></div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){

        $("#datepicker").datepicker();

        //For paging
        $(document).on("click",".buttonPage",function(){
          var searchText = $('#searchText').val();
          var date = $('#datepicker').val();
          var status = $('#status').val();
          var page = $(this).data("page");
          $.ajax({
                type: 'POST',
                url: '<?php echo SITE_MOD; ?>myOrder-nct/ajax.myOrder.php',
                data: {searchText: searchText,date: date,status: status,page: page,action:'paging'},
                dataType : 'json',
                success: function(data) {
                    $('.data').html(data.orderList);
                    $('.paging').html(data.paging);
                    $(window).scrollTop(0);
                }
            });
        });

        $(document).on("click","#deleteOrder",function(){
            var id = $(this).data("id");
            //alert(id);

            if (confirm('Are you sure to delete this order?')) {
                return true;
            }else{
                return false;
            }

        });            

    });
</script>