<tr>
    <td>
        <a href="{SITE_URL}product/%ID%" class="checkout-img" title="%PRODUCT_NM%"><img src="%IMG%" alt="checkout-img" style="height:80px; width:80px;"/></a>
    </td>
    <td><a href="{SITE_URL}product/%ID%" title="%PRODUCT_NM%">%PRODUCT_NM%</a></td>
    <td>%WEIGHT%</td>
    <td class="blue">%CURR_SIGN%%PRODUCT_PRICE%</td>
    <td><input type="number" value="%QUANTITY%" min="1" max="%MAX_QUANTITY%" id="changeQuantity_%CART_ID%" data-id="%CART_ID%" class="changeQuantity"></td>
    <td class="blue">%CURR_SIGN%%TOTAL_PRODUCT_PRICE%</td>
    <td>
        <!-- <div class="edit-delete delete-btn"> <a href="#"><i class="fa fa-trash-o" aria-hidden="true"></i></a> </div> -->
        <a href="{SITE_URL}deleteCart/%CART_ID%/%ID%" id="deleteCart" data-id="%CART_ID%"><i aria-hidden="true" class="fa fa-trash-o" title="Delete"></i></a>
    </td>
</tr>