<?php

include("simple_html_dom.php");
$html = file_get_html(base64_decode($_REQUEST['html']));
//echo $html;exit; 

$i=0;
foreach($html->find('tr.cart_item') as $row) {
    $data['product'][$i]['name'] = trim($row->find('td',2)->plaintext);
    $data['product'][$i]['price'] = trim($row->find('td',3)->plaintext);
    $data['product'][$i]['total'] = trim($row->find('td',5)->plaintext);

    $nodes = $html->find("input[type=number]");

	foreach ($nodes as $node) {
	    $data['product'][$i]['quantity'] = $node->value;
	}

    $i++;
}
echo json_encode($data);

?>