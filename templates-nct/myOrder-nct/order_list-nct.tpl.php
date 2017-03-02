<tr>
    <td><a href="%SITE_URL%order/%ID%" title="%ORDER_ID%">%ORDER_ID%</a></td>
    <td>%DATE%</td>
    <td><a href="%SITE_URL%product/%PRODUCT_ID%" title="%PRODUCT_NM%">%PRODUCT_NM%</a></td>
    <td><a href="%SITE_URL%transaction/%TRANSACTIONID%" title="%TRANSACTIONID%">%TRANSACTIONID%</a></td>
    <td>%CURR_SIGN%%PRICE%</td>
    <td>%STATUS%</td>
    <td>
        <div class="edit-delete delete-btn"> 
        	<a href="%SITE_URL%deleteOrder/%ID%" id="deleteOrder" data-id="%ID%" title="Delete"><i aria-hidden="true" class="fa fa-trash-o"></i></a>
        </div>
    </td>
</tr>