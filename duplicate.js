var cntr;
var split="";
(function($){
	"use strict";
	$(document).on("click","#addmore",function(){		
		var original=$('.clone-form-object:last')[0];
		var clone=original.cloneNode(true);
		cntr=$('.clone-form-object:last').attr('id');
		split=cntr.split("-");
		var updid="forma-"+ ++split[1];
		clone.id=updid;
		original.parentNode.appendChild(clone);
		var cloned=document.getElementById(updid).childNodes;
		cloned[4].name="part_details["+split[1]+"][img]";
		cloned[4].id="image-url-"+split[1];
		cloned[6].id="upload-button-"+split[1];
		var wc_txt=cloned[8].childNodes;
		wc_txt[3].name="part_details["+split[1]+"][layer]";
		var wc_nbr=cloned[9].childNodes;
		wc_nbr[3].name="part_details["+split[1]+"][part_price]";
		cloned[11].id='remove-button-'+split[1];
	});
})(jQuery);