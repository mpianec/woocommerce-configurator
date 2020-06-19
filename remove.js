jQuery(document).ready(function($){
  var mediaUploader;
  var btn="";
  var split="";
  $(document).on("click",".amondi-remove-part",function(event){
	event.preventDefault();
	if($('.clone-form-object').length==1){
		var original=$('.clone-form-object:last')[0].childNodes;
		console.log(original);
		original[4].value="";
		var wc_txt=original[8].childNodes;
		wc_txt[3].value="";
		var wc_nbr=original[9].childNodes;
		wc_nbr[3].value="";
	}else{
		btn=event.target.id;
		if(btn.includes("remove")){
			split=btn.split("-");
			}
		var obj=document.getElementById('forma-'+split[2]);
		console.log(obj);
		obj.remove();
		}
	});
});