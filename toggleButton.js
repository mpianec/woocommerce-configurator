jQuery(document).ready(function($){
	var clicked=0;
	var arr=[];
	$(document).on("click",".amondi-button",function(event){				
			btn=event.target;
			btnid=event.target.id;
			split=btnid.split("-");
			var ids="imgs-"+split[1];
			var prod_image=document.getElementById(ids);
			var label=document.getElementById("label-"+split[1]);	
			var part=$(".amondi-price .woocommerce-Price-amount")
			if(part.length==1){
				var product_price=part[0].textContent.replace(/[^\d.,]/g,'');
				var currency=part[0].textContent.replace(/[.,\s0-9]/g,'');
			}
			else{
				var product_price=part[1].textContent.replace(/[^\d.,]/g,'');
				var currency=part[1].textContent.replace(/[.,\s0-9]/g,'');
			}
			var prod_price_separator=product_price.replace(".","");	
			var prod_price_float=prod_price_separator.replace(",",".");
			var new_price=0;
			var price=btn.getAttribute("data-price");
			var new_price_float=0;
			if(arr[split[1]]==0 || arr[split[1]]==null){	
			    new_price=parseFloat(prod_price_float)+parseFloat(price);		
				new_price_float=new_price.toFixed(2);
				if(part.length==1){
					part[0].innerHTML=currency+parseFloat(new_price_float).toLocaleString(undefined,{minimumFractionDigits: 2, maximumFractionDigits: 2});
				}
				else{
					part[1].innerHTML=currency+parseFloat(new_price_float).toLocaleString(undefined,{minimumFractionDigits: 2, maximumFractionDigits: 2});
				}
				label.style.backgroundColor="grey";
				label.style.color="";
				prod_image.style.display="block";
				arr[split[1]]=1;
				}
			else if(arr[split[1]]==1){	
				new_price=parseFloat(prod_price_float)-parseFloat(price);
				new_price_float=new_price.toFixed(2);
				if(part.length==1){
					part[0].innerHTML=currency+parseFloat(new_price_float).toLocaleString(undefined,{minimumFractionDigits: 2, maximumFractionDigits: 2});
				}
				else{
					part[1].innerHTML=currency+parseFloat(new_price_float).toLocaleString(undefined,{minimumFractionDigits: 2, maximumFractionDigits: 2});
				}
				label.style.backgroundColor="";
				label.style.color="";
				prod_image.style.display="none";
				arr[split[1]]=0;
			}
	});
});