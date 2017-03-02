<?php
/*	@ Scrap cart from store function
	@ by:NTC 29112016
*/
include("../api/simple_html_dom.php");

function scraphtml($storeLink,$htmldata)
{
	$html = file_get_html($htmldata);
	if(trim($storeLink)=='http://www.intox-detox.com')
	{

		$i=0;
		$product =array();

		foreach($html->find('tr.cart_item ') as $row) 
		{
			$image =$row->find('td',1)->children(0)->children(0)->src;
			$product[$i]['image']=$image;
			$product[$i]['name']= trim($row->find('td',2)->plaintext);
			$product[$i]['price'] = getvalue(trim($row->find('td',3)->plaintext));
		    $product[$i]['total'] = getvalue(trim($row->find('td',5)->plaintext));
		    
		    $nodes = $html->find("input[type=number]");
			$product[$i]['quantity'] = $nodes[$i]->value;
		    $i++;
		}
		$data['product'] =$product;
		foreach($html->find('tr.cart-subtotal') as $row);
		{
			$subtotal =$row->find('td')[0];	
			$subtotal= $subtotal->plaintext;
		}
		 $subtotal=  getvalue($subtotal);
		 $data['subtotal'] =$subtotal;
		 $data['tax'] ="0.0";
		foreach($html->find('tr.cart-discount') as $row);
		{
			$discount =$row->find('td')[0];	
			$discount= $discount->plaintext;
		}
		 $discount= getvalue($discount);
		 $data['discount'] =$discount==$subtotal?0.0:$discount;;

		foreach($html->find('tr.shipping') as $row);
		{
			$shipping =$row->find('td')[0];	
			$shipping= trim($shipping->plaintext);
		}
		 $shipping= getvalue($shipping);
		 $data['shipping'] =$shipping;

		foreach($html->find('tr.order-total') as $row);
		{
			$total =$row->find('td')[0];	
			$total= trim($total->plaintext);
		}
		$total =getvalue($total);
		$data['grandtotal'] =$total;
		return $data;
	}else if($storeLink=='http://www.brandnutritionusa.com')
	{
		$i=0;
		$product =array();

		foreach($html->find('tr.cart_item ') as $row) 
		{
			$image =$row->find('td',1)->children(0)->children(0)->src;
			$product[$i]['image']=$image;
			$product[$i]['name']= trim($row->find('td',2)->plaintext);
			$product[$i]['price'] =substr(trim($row->find('td',3)->plaintext),5);
		    $product[$i]['total'] = substr(trim($row->find('td',5)->plaintext),5);
		    
		    $nodes = $html->find("input[type=number]");
			$product[$i]['quantity'] = $nodes[$i]->value;
		
		    $i++;
		}
		$data['product'] =$product;
		foreach($html->find('tr.cart-subtotal') as $row);
		{
			$subtotal =$row->find('td')[0];	
			$subtotal= $subtotal->plaintext;
		}
		 $subtotal= substr($subtotal,5);
		 $data['subtotal'] =$subtotal;
		 $data['tax'] ="0.0";
		 $discount="0.0";
		 if(gettype($html->find('tr.cart-discount'))=='object')
		 {
			foreach($html->find('tr.cart-discount') as $row);
			{
				$discount =$row->find('td')[0];	
				$discount= substr($discount->plaintext,5);
			}
		}
		 $discount=$discount;
		 $data['discount'] =$discount==$subtotal?0.0:$discount;;

		foreach($html->find('tr.shipping') as $row);
		{
			$shipping =$html->find('option[selected=selected]')[0]->plaintext; 
		}
		 $shipping=$shipping;
		 $data['shipping'] =substr($shipping,strpos($shipping,'&#036;')+6);

		foreach($html->find('tr.order-total') as $row);
		{
			$total =$row->find('td')[0];	
			$total= substr($total->plaintext,5);
		}
		$total =$total;
		$data['grandtotal'] =$total;
		return $data;
	
	}else if($storeLink=='http://www.naturessources.com')
	{
		$i=0;
		$product =array();

		foreach($html->find('tr.ec_cartitem_row ') as $row) 
		{
			$nodes = $html->find("input[type=number]");
			$image =$row->find('td',1)->children(0)->src;
			$product[$i]['image']=$image;
			$product[$i]['name']= trim($row->find('td',2)->plaintext);
			$product[$i]['price'] =substr(trim($row->find('td',3)->plaintext),1);
			$product[$i]['quantity'] = $nodes[$i]->value;
		    $product[$i]['total'] = $product[$i]['quantity'] * $product[$i]['price'] ;
		    $i++;
		}
		$data['product'] =$product;
		$data['subtotal']=substr($html->find('div[id=ec_cart_subtotal]')[0]->plaintext,1);
		$data['tax']=substr($html->find('div[id=ec_cart_tax]')[0]->plaintext,1);
		$data['discount']=substr($html->find('div[id=ec_cart_discount]')[0]->plaintext,2);
		$data['shipping']=substr($html->find('div[id=ec_cart_shipping]')[0]->plaintext,1);
		$data['grandtotal']=substr($html->find('div[id=ec_cart_total]')[0]->plaintext,1);

		return $data;
	}else if($storeLink=='http://www.natrenpro.com')
	{

		$product =array();
		$rows =$html->find('div.basket-product-row');
		for($index=0;$index<count($rows);$index++)
		{
			$image='https://www.natren.com/mm5/'.$rows[$index]->children(0)->children(0)->src;
			$name =$rows[$index]->children(1)->children(0)->plaintext;
			$qty =$rows[$index]->find("input[type=tel]")[0]->value;
			$price =substr($rows[$index]->children(4)->children(0)->plaintext,1);
			$product[$index]['image']=$image;
			$product[$index]['name']=$name;
			$product[$index]['quantity']=$qty;
			$product[$index]['price']=$price;
			$product[$index]['total']=$price * $qty;
			$total+=$product[$i]['total'];
		}
		$data['product'] =$product;
		$data['subtotal'] =$total;
		$data['tax']="0.0";
		$data['discount'] ="0.0";
		$data['shipping'] ="0.0";
		$data['grandtotal'] =$total;
		return $data;
	}else if($storeLink=='http://www.amfpharma.com')
	{
		$i=0;
		$product =array();
		$tbody=$html->find('div.cart-info')[0]->children(0)->children(1);
		$rows =$tbody->find('tr');
			foreach ($rows as $row) {
				$image =$row->find('td',0)->children(0)->children(0)->src;
				$product[$i]['image']=$image;
				$product[$i]['name']= trim($row->find('td',1)->plaintext);
				$product[$i]['price'] = substr(trim($row->find('td',4)->plaintext),1);
			    $product[$i]['total'] = substr(trim($row->find('td',5)->plaintext),1);
			    
			    $nodes = $html->find("input[type=text]");
				$product[$i]['quantity'] = $nodes[$i]->value;
		    $i++;
			}
		$data['product']=$product;
		$tbody=$html->find('div.cart-total')[0]->children(0);
		$subtotal =$tbody->find('tr')[0]->find('td',1)->plaintext;
		$data['subtotal']=substr($subtotal,1);

		$data['shipping'] ="0.0";
		$data['tax'] ="0.0";
		$data['discount'] ="0.0";

		$tbody=$html->find('div.cart-total')[0]->children(0);
		$grandtotal =$tbody->find('tr')[1]->find('td',1)->plaintext;
		$data['grandtotal']=substr($grandtotal,1);

		return $data;
	}else if($storeLink=='http://www.p-73.com')
	{
		$i=0;
		$product =array();

		foreach($html->find('tr.cart_item ') as $row) 
		{
			$image =$row->find('td',1)->children(0)->children(0)->src;
			$product[$i]['image']=$image;
			$product[$i]['name']= trim($row->find('td',2)->plaintext);
			$product[$i]['price'] = getvalue(trim($row->find('td',3)->plaintext));
		    $product[$i]['total'] = getvalue(trim($row->find('td',5)->plaintext));
		    
		    $nodes = $html->find("input[type=number]");
			$product[$i]['quantity'] = $nodes[$i]->value;
		    $i++;
		}
		$data['product'] =$product;
		foreach($html->find('tr.cart-subtotal') as $row);
		{
			$subtotal =$row->find('td')[0];	
			$subtotal= $subtotal->plaintext;
		}
		 $subtotal=  getvalue($subtotal);
		 $data['subtotal'] =$subtotal;
		 $data['tax'] ="0.0";
		foreach($html->find('tr.cart-discount') as $row);
		{
			$discount =$row->find('td')[0];	
			$discount= $discount->plaintext;
		}
		 $discount= getvalue($discount);
		 $data['discount'] =$discount==$subtotal?0.0:$discount;;

		foreach($html->find('tr.shipping') as $row);
		{
			$shipping =$row->find('td')[0];	
			$shipping= trim($shipping->plaintext);
		}
		 $shipping= getvalue($shipping);
		 $data['shipping'] =$shipping;

		foreach($html->find('tr.order-total') as $row);
		{
			$total =$row->find('td')[0];	
			$total= trim($total->plaintext);
		}
		$total =getvalue($total);
		$data['grandtotal'] =$total;
		return $data;
	}else if($storeLink=='http://www.naturade.com')
	{
		$i=0;
		$product =array();

		foreach($html->find('tr.cart_item ') as $row) 
		{
			$image =$row->find('td',1)->children(0)->children(0)->src;
			$product[$i]['image']=$image;
			$product[$i]['name']= trim($row->find('td',2)->plaintext);
			$product[$i]['price'] = getvalue(trim($row->find('td',3)->plaintext));
		    $product[$i]['total'] = getvalue(trim($row->find('td',5)->plaintext));
		    
		    $nodes = $html->find("input[type=number]");
			$product[$i]['quantity'] = $nodes[$i]->value;
		    $i++;
		}
		$data['product'] =$product;
		foreach($html->find('tr.cart-subtotal') as $row);
		{
			$subtotal =$row->find('td')[0];	
			$subtotal= $subtotal->plaintext;
		}
		 $subtotal=  getvalue($subtotal);
		 $data['subtotal'] =$subtotal;
		 $data['tax'] ="0.0";
		foreach($html->find('tr.cart-discount') as $row);
		{
			$discount =$row->find('td')[0];	
			$discount= $discount->plaintext;
		}
		 $discount= getvalue($discount);

		 $data['discount'] =$discount==$subtotal?0.0:$discount;

		foreach($html->find('tr.shipping') as $row);
		{
			$shipping =$row->find('td')[0];	
			$shipping= trim($shipping->plaintext);
		}
		 $shipping= getvalue($shipping);
		 $data['shipping'] =$shipping;
		 $total="0.0";
		foreach($html->find('tr.order-total') as $row);
		{
			$total =$row->find('td')[0];	
			$total= trim($total->plaintext);
		}
		$total =getvalue($total);
		$data['grandtotal'] =$total;
		return $data;
	}else if($storeLink=='http://www.mushroomscience.com')
	{
		$i=0;
		$product =array();

		foreach($html->find('tr.cart_item ') as $row) 
		{
			$image =$row->find('td',1)->children(0)->children(0)->src;
			$product[$i]['image']=$image;
			$product[$i]['name']= trim($row->find('td',2)->plaintext);
			$product[$i]['price'] = getvalue(trim($row->find('td',3)->plaintext));
		    $product[$i]['total'] = getvalue(trim($row->find('td',5)->plaintext));
		    
		    $nodes = $row->find('option[selected=""]');
			$product[$i]['quantity'] = $nodes[0]->plaintext;
		    $i++;
		}
		$data['product'] =$product;
		foreach($html->find('tr.cart-subtotal') as $row);
		{
			$subtotal =$row->find('td')[0];	
			$subtotal= $subtotal->plaintext;
		}
		 $subtotal=  getvalue($subtotal);
		 $data['subtotal'] =$subtotal;
		 $data['tax'] ="0.0";
		foreach($html->find('tr.cart-discount') as $row);
		{
			$discount =$row->find('td')[0];	
			$discount= $discount->plaintext;
		}
		 $discount= getvalue($discount);

		 $data['discount'] =$discount==$subtotal?0.0:$discount;

		foreach($html->find('tr.shipping') as $row);
		{
			$shipping =$row->find('td')[0];	
			$shipping= trim($shipping->plaintext);
		}
		 $shipping= getvalue($shipping);
		 $data['shipping'] =$shipping;
		 $total="0.0";
		foreach($html->find('tr.order-total') as $row);
		{
			$total =$row->find('td')[0];	
			$total= trim($total->plaintext);
		}
		$total =getvalue($total);
		$data['grandtotal'] =$total;
		return $data;
	}else if($storeLink=='http://www.natrol.com')
	{

		$i=0;
		$product =array();
		foreach ($html->find('table.ShoppingCartItem') as $rows){
			$row =$rows->find('tr')[0];
			
			$product[$i]['image']="";
			$product[$i]['name']= str_replace(array("\t","\r","\n","\u00ae")," ",trim($row->find('td',0)->plaintext));
			$nodes = $row->find("input[type=text]");
			$product[$i]['quantity'] = $nodes[0]->value;
			$total = substr(trim($row->find('td.cartProductSubtotal',0)->plaintext),1);
		   
			$product[$i]['price'] = $total / $product[$i]['quantity'];
			$product[$i]['total'] =$total;
		    
		 	 $i++;
		}
		  $data['product'] =$product;
		  $subtotal =trim($html->find('span#ctl00_PageContent_ctrlCartSummary_lblSubTotalNoDiscount')[0]->plaintext);
		  $data['subtotal']=substr($subtotal,1);
		  $data['tax']="0.0";
		  $data['discount']="0.0";
		  $data['grandtotal']=substr($subtotal,1);
		return $data;
	}else if($storeLink=='http://www.rainbowlight.com')
	{
		$i=0;
		$product =array();
		
		foreach($html->find('tr.odd') as $row) 
		{ 
			$image =$row->find('td',0)->children(0)->children(0)->src;
			$product[$i]['image']=$image;
			$product[$i]['name']= trim($row->find('td',1)->plaintext);
			$product[$i]['price'] = substr(trim($row->find('td',2)->plaintext),1);
		    $product[$i]['total'] = substr(trim($row->find('td',4)->plaintext),1);
		    
		  	$nodes = $row->find("input[type=text]");
			$product[$i]['quantity'] = $nodes[0]->value;
		    $i++;
		   
		}
		$j=0;
		foreach($html->find('tr.even') as $row) 
		{
			$image =$row->find('td',0)->children(0)->children(0)->src;
			$product[$i]['image']=$image;
			$product[$i]['name']= trim($row->find('td',1)->plaintext);
			$product[$i]['price'] = substr(trim($row->find('td',2)->plaintext),1);
		    $product[$i]['total'] = substr(trim($row->find('td',4)->plaintext),1);
		    
		   	$nodes = $row->find("input[type=text]");
			$product[$i]['quantity'] = $nodes[0]->value;
		    $i++;
		    
		}
		$data['product'] =$product;

		$subtotal=$html->find('#shopping-cart-totals-table')[0];
		$subtotal =substr(trim($subtotal->find('td')[3]->plaintext),1);
		$data['subtotal'] =$subtotal;

		$shipping=$html->find('#shopping-cart-totals-table')[0];
		$shipping =substr(trim($shipping->find('td')[5]->plaintext),1);
		$data['shipping'] =$shipping;

		$data['tax'] ="0.0";
		$data['discount'] ="0.0";

		$grandtotal=$html->find('#shopping-cart-totals-table')[0];
		$grandtotal =substr(trim($grandtotal->find('td')[1]->plaintext),1);
		$data['grandtotal'] =$grandtotal;
		
		return $data;
	}else if($storeLink=='http://www.acgrace.com')
	{

		$i=0;
		$product =array();
		$rows =$html->find('ul.CartList')[0];
		$subtotal=0.0;
		for($index=0;$index<count($rows->find('div.ProductImage'));$index++)
		{
			$qty =$rows->find('option[selected=""]')[$index]->plaintext;
			$price =substr(trim($rows->find('em.ProductPrice')[$index]->plaintext),1);
			$image= $rows->find('div.ProductImage')[$index]->children(0)->children(0)->src;
			$product[$index]['image']=$image;
			$product[$index]['name']= str_replace(array("\t","\r","\n")," ",trim($rows->find('div.ProductDetails')[$index]->children(0)->children(0)->plaintext));
			$product[$index]['price'] = $price;
		    $product[$index]['total'] = $qty*$price;
		    $product[$index]['quantity'] =$qty;
		    $subtotal +=$product[$index]['total'];
		
		}
		
		$data['product'] =$product;
		$data['subtotal'] =$subtotal;
		$data['shipping'] ="0.00";
		$data['discount'] ="0.00";
		$data['tax'] ="0.0";
		$data['grandtotal'] =$subtotal;
		
		return $data;
	}else
	{
		return $data;
	}
}
//helper function for string mixed data to retive numeric value
function getvalue($src)
{
	$t =explode(' ', $src);
	$val=0.0;
	foreach ($t as  $value) {
			if(floatval($value)>0)
			{
				$val =$value;
			}
	}
	return $val;
}