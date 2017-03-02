<div class="common-bg">
    <div class="heading container">
        <div class="col-lg-3">

            <div class="border">
            </div>
        </div>
        <div class="col-lg-6">
            <div class="row">
                <h1>Place Your Custom Order</h1></div>
        </div>
        <div class="col-lg-3">

            <div class="border"></div>

        </div>
    </div>
</div>

<div class="main">
    <div class="container">
        <form method="post" action="" name="order" id="order">
            <div class="heading-btn">
                <h2>Custom Order </h2>
                <input type="submit" class="btn btn-default blue-btn right-btn" value="Save" name="orderSubmit" title="Save">
            </div>
            <div class="place-order">
                <table class="table  table-hover" id="tab_logic">
                    <thead>
                        <tr>
                            <th class="text-center">
                                No.
                            </th>
                            <th class="text-center">
                                Product Name
                            </th>
                            <th class="text-center">
                                Product URL
                            </th>
                            <th class="text-center">
                                Price(<?php echo SITE_CURR; ?>)
                            </th>
                            <th class="text-center">
                                Quantity
                            </th>
                            <th class="text-center">
                                Size
                            </th>
                            <th class="text-center">
                                Color
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr id='addr0'>
                            <td>
                                1
                            </td>
                            <td>
                                <input type="text" name='productName[]' class="form-control" />
                            </td>
                            <td>
                                <input type="text" name='productUrl[]' class="form-control" />
                            </td>
                            <td class="number">
                                <input type="text" name='productPrice[]' class="form-control" />
                            </td>
                            <td class="number">
                                <input type="text" name='quantity[]' class="form-control" />
                            </td>
                            <td class="number">
                                <input type="text" name='size[]' class="form-control" />
                            </td>
                            <td class="number">
                                <input type="text" name='color[]' class="form-control" />
                            </td>
                        </tr>

                        <tr id='addr1'></tr>
                    </tbody>
                </table>
                <div class="add-more">

                    <a id="add_row" class="btn btn-default blue-btn" title="Add More">Add More</a><a id='delete_row' class="blue-btn btn btn-default" title="Delete">Delete</a></div>
            </div>
        </form>

    </div>
</div>

<script>
    $(document).ready(function() {

        //For validation
        $("#order").validate({
            errorClass: 'help-block',
            errorElement: 'label',
            rules: {
                'productName[]': { required: true },
                'productUrl[]': { required: true, url:true },
                'productPrice[]': { required: true, number: true },
                'quantity[]': { required: true, number: true },
                'size[]': { required: true},
                'color[]': { required: true}
            },
            messages: {
                'productName[]': {
                    required: "Enter Name"
                },
                'productUrl[]': {
                    required: "Enter Url",
                    url: "Invalid url"
                },
                'productPrice[]': {
                    required: "Enter Price",
                    number: "Number only"

                },
                'quantity[]': {
                    required: "Enter Quantity",
                    number: "Number only"

                },
                'size[]': {
                    required: "Enter Size"

                },
                'color[]': {
                    required: "Enter Color"

                }
            },
            errorPlacement: function (error, element) {
                error.insertAfter(element);
            }
        });

        //For add more order
        var i = 1;
        $("#add_row").click(function() {
        $('#addr' + i).html("<td>" + (i + 1) + "</td><td><input name='productName[]' id='productName_"+i+"' type='text' class='form-control input-md'  /> </td><td><input  name='productUrl[]' id='productUrl"+i+"' type='text' class='form-control input-md'></td><td><input name = 'productPrice[]' id = 'productPrice"+i+"' type = 'text' class = 'form-control input-md' ></td><td><input name = 'quantity[]' id = 'quantity"+i+"' type = 'text'class = 'form-control input-md' ></td><td><input name = 'size[]' id = 'size"+i+"' type = 'text' class = 'form-control input-md' ></td><td><input name = 'color[]' id = 'color"+i+"' type = 'text'class = 'form-control input-md' ></td>");

            $('#tab_logic').append('<tr id="addr' + (i + 1) + '"></tr>');
            i++;
        });
        $("#delete_row").click(function() {
            if (i > 1) {
                $("#addr" + (i - 1)).html('');
                i--;
            }
        });

    });

</script>