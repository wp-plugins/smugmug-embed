<?php

    add_filter( 'media_upload_tabs', 'SME_Embed_tab' );
    function SME_Embed_tab( $tabs ) {
        $newtab = array( 'SME_insert_tab' => __( 'Embed from SmugMug', 'insertgmap' ) );
        return array_merge( $tabs, $newtab );
    }

   add_action( 'media_upload_SME_insert_tab', 'SME_media_embed_tab' );
    function SME_media_embed_tab() {
        global $errors;

        return wp_iframe( 'SME_media_embed_form', $errors );
    }



    function SME_media_embed_form() {
       global $SME_api,$SME_Settings,$SME_api_progress, $SME_smugmugembed_api;
        if ( $SME_api_progress!= 4 ) {
            echo '<div id="message" class="error"><p><strong>SmugMug Embed needs to be authorized before use.  To start the process, please <a href="../wp-admin/options-general.php?page=smugmugembed-settings" title="authorize SmugMug Embed">click here</a></strong></p></div>';
            return;
        }

            $galleries = get_transient('smugmugembed_galleries');
            if (!$galleries){
                $SME_api->setToken( "id={$SME_smugmugembed_api['api']['id']}", "Secret={$SME_smugmugembed_api['api']['Secret']}" );
                $galleries = $SME_api->albums_get('Extras=Passworded', 'NickName=' . $SME_smugmugembed_api[ 'api' ][ 'NickName' ] );
    	        array_sort_by_column($galleries,'Title');          
    	        set_transient( 'smugmugembed_galleries', $galleries, 3600 ); 

    	    }
            $SME_smug_gals = $SME_Settings['availableGalleries'];
            $SME_smug_sizes = $SME_Settings['availableSizes'];
            $SME_smug_response = $SME_Settings['availableClickResponses'];
            $SME_default_size = $SME_Settings['defaultSize'];
            $SME_default_response = $SME_Settings['clickResponse'];
            $SME_caption = $SME_Settings['caption'];
            $SME_keywords= $SME_Settings['keywords'];
            $SME_fileName = $SME_Settings['imageName'];
                        ?>
            <div class="SME_attachments-browser">
            	<div class="SME_media-toolbar">
            		<div class="SME_media-toolbar-secondary">
            		  <form method="POST" action="" id="SME_ChooseGalleryForm">
            		  <input type="hidden" name="action" value="SME_getImagesFromGallery"/>
            			<select name="SME_ChosenGallery" onchange=ajaxSubmit();>
            			<option value="">Select Gallery</option>
            			<?php
				$foundLock="false";

           			foreach ( $galleries as $gallery => $galleryvalue ) {
              				if ( empty($SME_smug_gals)|| in_array($galleryvalue[ 'id' ], $SME_smug_gals )) {
              				        $locked = ($galleryvalue['Passworded']=="1")?"true":"false";
               					?><option value='<?php echo $galleryvalue['id'] ?>|<?php echo $galleryvalue['Key'] ?>|<?php echo $locked?>'><?php echo $galleryvalue['Title'],($locked=="true" ? " (Thumbnail only)" : "") ?></option>
               					<?php 
               				}
             			}
             			?></select>
             			<input type=hidden id="defaultSize" value="<?php echo $SME_default_size; ?>"/>
             			<input type=hidden id="defaultLink" value="<?php echo $SME_default_response; ?>"/>             			
             		 </form>
             		</div>
             	</div>
             	<ul id="SME_imageViewer" class="SME_attachments SME_ui-sortable SME_ui-sortable-disabled">

             	</ul>
             	<form id="insertForm" action="" method="post">
             	<div class="SME_media-sidebar">
             		<div class="SME_attachment-details">
             		<h3 class="hndle">Image Settings</h3>
             		<label class="setting" data-setting="border">
      				<span>Border</span>
				<select class="border" data-user-setting="border" name="border" id="border" data-setting="border">
					<option value="Yes" selected="">Yes</option>
					<option value="">No</option>
				</select>
    			</label>
             		<label class="setting" data-setting="title">
             			<span>
      					Title
    				</span>
    				<?php
    				echo '<input id="title" name="title" type="text" value="',($SME_fileName==1) ? "{SMUGMUGFILENAME}" : "" ,'"></input>';
    				?>
    			</label>
             		<label class="setting caption">
				<span>Caption</span>
				<textarea id='caption' name='caption' data-setting="caption"><?php 
				echo '',($SME_owner==1) ? "{SmugMugOwner}" : "" ,' ',($SME_caption==1) ? "{SMUGMUGCAPTION}" : "" ,'</textarea>';
				?>

			</label>
			<label class="setting alt-text">
				<span>Alt Text</span>
				<?php
    				echo '<input id="alt" name="alt"  type="text" data-setting="alt" value="',($SME_keywords==1) ? "{SMUGMUGKEYWORDS}" : "" ,' ',($SME_caption==1) ? "{SMUGMUGCAPTION}" : "" ,'"/>';
    				?>

			</label>
	
			<div class="setting align">
				<span>Alignment</span>
				<select class="alignment" data-user-setting="align" name="align" id="align" data-setting="align">
					<option value="left" selected="">Left</option>
					<option value="center">Center</option>
					<option value="right">Right</option>
					<option value="none">None</option>
				</select>
			</div>
	
			<div class="setting link-to">
				<span>Link To</span>
				<select class="link-to" data-user-setting="urlbutton" name="link" id="link" data-setting="link">
				<?php
				   if ($SME_smug_response) {
				     foreach ($SME_smug_response as $SME_response => $SME_responseValue) {
				         if ($SME_responseValue)
				          echo '<option ', (($SME_response == $SME_default_response) ?  "selected" : "") ,' value="'.$SME_response.'">'.$SME_response.'</option>';
				     }
				   }
				 ?>    
				</select>
			</div>
			<label class="setting">
				<span>
      					Size
    				</span>
    				<select class="size" data-user-setting="imgsize" data-setting="size" id="size" name="size">
				<?php  
				   if ($SME_smug_sizes) {    				
    					foreach ($SME_smug_sizes as $SME_size => $SME_sizeValue) {
				         if ($SME_sizeValue)
				          echo '<option ', (($SME_size == $SME_default_size) ?  "selected" : "") ,' value="'.$SME_size.'">'.$SME_size.'</option>';
				     }
				   }
				 ?>
    				</select>
    			</label>
             		</div>
             	</div>
            </div>
            <div class="SME_media-frame-toolbar">
                <div class="SME_media-toolbar">
                    <div class="SME_media-selection">
                       <span id="SME_Count" class="SME_count"></span>
                       <span id="SME_Clear" style="display:none;"><a class="SME_clear-selection" href="#" onclick="SME_clearAll()">Clear</a></span>
                    </div>
                    <div  id="SME_PreviewHolder-palette">
                      <ul id="SME_PreviewHolder" class="SME_attachment-preview SME_ui-sortable-disabled"></ul>
                    </div>

                </div>
                    <div class="SME_media-toolbar-primary">

		      <a id="SME_insert" class="button media-button button-primary button-large media-button-insert" href="#" disabled="disabled" onclick="ajaxInsert()";>
		           Insert into <?php echo is_single()?"post":"page" ?>
		      </a>
		      <input type=hidden name="action" value="SME_sendImagesToEditor" />
		      <input type=hidden name="selectedImages" value="" />
		    </div>
            </div>
            </form>
            <div id="SME_hiddenDiv" style="height:0px;width:0px;"></div>
               <?php
        }
       

function SME_getImagesFromGallery() {
     global $SME_api,$SME_Settings,$SME_api_progress, $SME_smugmugembed_api;

     $SME_ChosenGallery=explode("|",$_POST['SME_ChosenGallery']);

     $SME_api->setToken( "id={$SME_smugmugembed_api['api']['id']}", "Secret={$SME_smugmugembed_api['api']['Secret']}" );
     $SME_images = get_transient('smugmugembed_images_'. $SME_ChosenGallery[0] .'_'.$SME_ChosenGallery[1]);
     if (!$SME_images){
        $SME_images= $SME_api->images_get('AlbumID='. $SME_ChosenGallery[0],'AlbumKey='.$SME_ChosenGallery[1],'Heavy=1,Extras=ThumbURL,SmallURL,MediumURL,LargeURL' );
        $SME_images = ($SME_api->APIVer == "1.2.2" ) ? $SME_images[ 'Images' ] : $SME_images;  
        //we need this in another function so set it to session
        //we cant use globals here because the way the function is called
        //from admin_ajax.php...we may try this with transients in a future release
        set_transient('smugmugembed_images_'. $SME_ChosenGallery[0] .'_'.$SME_ChosenGallery[1],$SME_images,3600);
     }
     $_SESSION['SME_images']=$SME_images;     
     foreach ($SME_images as $image =>$imageValue) {
      echo '<li class="SME_attachment SME_save-ready" >';
      echo '<div id="'.$imageValue['id'].'"  class="SME_attachment-preview" onclick=SME_ChangeState(this)  >';
      echo '<div class="SME_thumbnail">';
      echo '<div class="SME_centered">';
      echo "<img ' src='".$imageValue['ThumbURL']."' />";
      echo '</div></div>';
      echo '<a class="uncheck"><div class="media-modal-icon"></div></a></div></li>';
     }
     echo "<input type=hidden id='locked' value='".$SME_ChosenGallery[2]."' />";
     exit;
}        
add_action('wp_ajax_SME_getImagesFromGallery', 'SME_getImagesFromGallery');
function SME_array_multi_search($needle,$haystack){
foreach($haystack as $key=>$data){

if(in_array($needle,$data))
return $key;
}
}

function SME_translateMeta($meta,$image) {

   if (strpos($meta,'{SMUGMUGKEYWORDS}')!==false) $meta = str_replace("{SMUGMUGKEYWORDS}",$image['Keywords'],$meta);
   if (strpos($meta,'{SMUGMUGFILENAME}')!==false) $meta = str_replace("{SMUGMUGFILENAME}",$image['FileName'],$meta);
   if (strpos($meta,'{SMUGMUGCAPTION}')!==false)  $meta = str_replace("{SMUGMUGCAPTION}",$image['Caption'],$meta);      
   return $meta;
}
function SME_sendImagesToEditor() {
     $SME_images=$_SESSION['SME_images'];
     $SME_selectedImages = explode(",",$_POST["selectedImages"]);
     $SME_link=$_POST['link'];
     $SME_size=$_POST['size'];
     $SME_align=$_POST['align'];  
     $SME_border=$_POST['border'];  
     if (!empty($SME_border)) $SME_border = "style='border:1px solid black'";
      
     foreach ($SME_selectedImages as $SME_selectedImage =>$SME_selectedImageValue) {
        
        $image = $SME_images[SME_array_multi_search($SME_selectedImageValue,$SME_images)];
        $SME_alt=SME_translateMeta($_POST['alt'],$image);
        $SME_caption=SME_translateMeta($_POST['caption'],$image);
        $SME_title=SME_translateMeta($_POST['title'],$image);         
        switch ($SME_size)
        {
           case "Thumbnail":
                $imageUrl=$image['ThumbURL'];
        	break;
           case "Medium":
                $imageUrl=$image['MediumURL'];
        	break;
           case "Large":
                $imageUrl=$image['LargeURL'];
        	break;
           default:
                $imageUrl=$image['SmallURL'];
        	break;      
        }  

        switch ($SME_align)
        {
           case "left":
       		$float="alignleft";
        	break;
           case "center":
       		$float="aligncenter";
        	break;
           case "right":
       		$float="alignright";
        	break;
           default:
       		$float="alignnone";
        	break;      
        }  	          	        
 	list($SME_width, $SME_height, $SME_type, $SME_attr) = getimagesize($imageUrl);        
        $html = "<img width='".$SME_width."' src='".$imageUrl."' class='".$float."' alt='".$SME_alt."' title='".$SME_title."' $SME_border/>";        	
        switch ($SME_link)
        {
        ///this is not working yet so we took it out of release 1
           case "Shopping":
               // $imageLink= 'http://' . $SME_api[ 'api' ][ 'NickName' ] . '.smugmug.com/buy/' . $image['album']['id']. '_' . $image['album']['key'] . '/' . $image[ 'id' ] . '_' . $image[ 'Key' ];

        	break;
           case "Lightbox":
                $imageLink= $image['LightboxURL'];

        	break;
           case "Large":
                $imageLink= $image['LargeURL'];
        	break;
           default:
           	$imageLink="";
        	break;      
        }  
        if ($imageLink!="")
            $html="<a href='".$imageLink."'>".$html."</a>";
        if (!empty($SME_caption)){
            $html="[caption width='".$SME_width."' align='".$float."' id='']".$html.$SME_caption."[/caption]";      
            }
        $out.=$html;      
     }
     media_send_to_editor($out);
     exit;
}
function array_sort_by_column(&$arr, $col, $dir = SORT_ASC) {
    $sort_col = array();
    foreach ($arr as $key=> $row) {
        $sort_col[$key] = $row[$col];
    }

    array_multisort($sort_col, $dir, $arr);
}

add_action('wp_ajax_SME_sendImagesToEditor', 'SME_sendImagesToEditor');