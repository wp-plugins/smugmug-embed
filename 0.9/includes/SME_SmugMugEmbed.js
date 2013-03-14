var selectedImages = [];

/////adds images from smugmug

function ajaxSubmit(){
	SME_clearAll();
	var SME_ChooseGalleryForm = document.getElementById("SME_ChooseGalleryForm");
	SME_ChooseGalleryForm= jQuery(SME_ChooseGalleryForm).serialize();
	jQuery.ajax({
	type:"POST",
	url: "/wp-admin/admin-ajax.php",
	data: SME_ChooseGalleryForm,
	success:function(data){
	jQuery("#SME_imageViewer").html(data);
	if (jQuery("#locked").val()=="true") {
	    jQuery("#size option[value=Thumbnail]").attr("selected","selected")
	    		.siblings().attr("disabled","disabled");
	    jQuery("#link option[value=None]").attr("selected","selected")
	    		.siblings().attr("disabled","disabled");	    		
	} else {
		jQuery("#size option").removeAttr("disabled","disabled");
		jQuery("#size option[value="+jQuery("#defaultSize").val() +"]").attr("selected","selected");
		jQuery("#link option").removeAttr("disabled","disabled");
		jQuery("#link option[value="+jQuery("#defaultLink").val() +"]").attr("selected","selected");		
	}
	}
	});
exit;
}
////selects or deselects an image
function SME_ChangeState(el) {

	 var imgId=  el.id;
	 var checkEl =  jQuery(el).children("a");	 
	 if (jQuery(el).parent().hasClass('selected')){
	   SME_removeSelect(el,checkEl);
           SME_updateCount("remove",imgId);
           SME_removeFromPreview(el);
	 } else {
	    jQuery(el).parent().addClass('selected');
	    jQuery(checkEl).removeClass('uncheck').addClass('check');
	    SME_updateCount("add",imgId);
	    SME_addToPreview(jQuery(el),imgId);
	 }
}
function SME_removeSelect(el,checkEl) {
            jQuery(el).parent().removeClass('selected');
	    jQuery(checkEl).removeClass('check').addClass('uncheck');
}
////add selected image to array and updates count
function SME_updateCount(action,img) {
	if (action=="remove") {
	   //if (jQuery.inArray(img, selectedImages))
	        selectedImages.splice( jQuery.inArray(img, selectedImages), 1 );
	}
	else if (action=="add") {
		   // if (!jQuery.inArray(img, selectedImages))
		        selectedImages.push(img);
	}
	if (selectedImages.length >0){
   		document.getElementById("SME_Count").innerHTML= selectedImages.length +" selected";
   		document.getElementById("SME_Clear").style.display="block";
   		jQuery("#SME_insert").removeAttr("disabled");
   	} else {
   		document.getElementById("SME_Count").innerHTML="";   	
   		document.getElementById("SME_Clear").style.display="none";
   		jQuery("#SME_insert").attr("disabled","disabled");
   	}
}
function SME_clearAll() {
	for (var i=0;i<selectedImages.length;i++) {
 	    var checkEl = document.getElementById(selectedImages[i]);
 	    jQuery(checkEl).parent().removeClass('selected');
	    jQuery(checkEl).children("a").removeClass('check').addClass('uncheck');
	}
	selectedImages = [];
	document.getElementById("SME_Count").innerHTML="";   	
   	document.getElementById("SME_Clear").style.display="none";
	jQuery("#SME_PreviewHolder").empty();
   	jQuery("#SME_insert").attr("disabled","disabled");	
}

function SME_addToPreview(el,imgId) {

	var previewHolder = document.getElementById("SME_PreviewHolder");
	jQuery("#SME_PreviewHolder").append('<li id="li'+imgId+'" class="SME_attachment-preview" onclick="SME_removeFromPreview(this)"></li>');
	jQuery(el).children("div").clone().appendTo("#li"+imgId);
	
}


function ajaxInsert(){
	jQuery('input[name=selectedImages]').val(selectedImages);
	var SME_insertForm = document.getElementById("insertForm");
	SME_insertForm = jQuery(SME_insertForm).serialize();
	jQuery.ajax({
	type:"POST",
	url: "/wp-admin/admin-ajax.php",
	data:SME_insertForm,
	success:function(data){
	jQuery("#SME_hiddenDiv").html(data);
	}
	});
}

function SME_removeFromPreview(el) {
        if (el.id.substr(0,2)!="li") el=document.getElementById("li"+el.id);

	var imgId = el.id.substring(2);
	var imgEl = jQuery("#"+imgId);
	 var checkEl =  jQuery(imgEl).children("a");	 
	 if (jQuery(imgEl).parent().hasClass('selected')){
             SME_removeSelect(imgEl,checkEl);
             SME_updateCount("remove",imgId);
         }
	
       jQuery(el).remove();
       } 