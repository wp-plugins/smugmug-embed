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
	function SME_EnsureCanReachSmugMug() {
       global $SME_api,$SME_Settings,$SME_api_progress, $SME_smugmugembed_api;
	   
	   $SME_api->setToken( "id={$SME_smugmugembed_api['api']['id']}", "Secret={$SME_smugmugembed_api['api']['Secret']}" );
	   
	 //  $SME_api->service_ping("Pretty=true");

	try {
		} catch(Exception $e) {
				echo '<div id="message" class="error"><p><strong>There seems to be a problem reaching SmugMug.com.<br/>Perhaps there is a problem with the internet connection...<br/>Please try again later.</strong></p></div>';
				die;
		}
		return;
	}

    function SME_media_embed_form() {
       global $SME_api,$SME_Settings,$SME_api_progress, $SME_smugmugembed_api;
	   $SME_api->setToken( "id={$SME_smugmugembed_api['api']['id']}", "Secret={$SME_smugmugembed_api['api']['Secret']}" );
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
            if (!empty ( $SME_Settings['availableGalleries'])) $SME_smug_gals = $SME_Settings['availableGalleries'];
            $SME_smug_sizes = $SME_Settings['availableSizes'];
            $SME_smug_response = $SME_Settings['availableClickResponses'];
            $SME_default_size = $SME_Settings['defaultSize'];
            $SME_default_align = $SME_Settings['defaultAlign'];
            $SME_default_response = $SME_Settings['clickResponse'];
            $SME_caption = $SME_Settings['caption'];
            $SME_keywords= $SME_Settings['keywords'];
            $SME_fileName = $SME_Settings['imageName'];
            $SME_defaultNewWindow = $SME_Settings['newWindow'];

                        ?>
            <div class="SME_attachments-browser">
            	<div class="SME_media-toolbar">
            		<div class="SME_media-toolbar-secondary">
            		  <form method="POST" action="" id="SME_ChooseGalleryForm">
            		  <input type="hidden" name="action" value="SME_getImagesFromGallery"/>
            			<select id="SME_ChosenGallery" name="SME_ChosenGallery" onchange=ajaxSubmit();>
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
             			?></select>	<span id="SME_spinner" style="display: none;float:none;" class="spinner" ></span>


             			<input type=hidden id="defaultSize" value="<?php echo $SME_default_size; ?>"/>
             			<input type=hidden id="defaultLink" value="<?php echo $SME_default_response; ?>"/>             			
             		 </form>
<form method="POST" action="" id="SME_ClearTransientForm">
            		  <input type="hidden" name="action" value="SME_ClearImageGalleryTransient"/>
<a href="javascript:SME_ClearTransient();" name="SME_ClearTransient" name="SME_ClearTransient" class="SME_ClearCache">Clear Image Cache</a>
</form>
             		</div>
             	</div>
             	<ul id="SME_imageViewer" class="SME_attachments SME_ui-sortable SME_ui-sortable-disabled">

             	</ul>
             	<form id="insertForm" action="" method="post">
<input type=hidden id="chosenGallery" name="chosenGallery" />
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
				echo '',($SME_caption==1) ? "{SMUGMUGCAPTION}" : "" ,'</textarea>';
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
					<option value="left" <?php if ($SME_default_align=="Left") echo "selected"; ?>">Left</option>
					<option value="center" <?php if ($SME_default_align=="Center") echo "selected"; ?>>Center</option>
					<option value="right" <?php if ($SME_default_align=="Right") echo "selected"; ?>>Right</option>
					<option value="none">None</option>
				</select>
			</div>
	
			<div class="setting link-to">
				<span>Link To</span>
				<select class="link-to" data-user-setting="urlbutton" name="link" id="link" data-setting="link">
				<?php
				   if ($SME_smug_response) {
				     foreach ($SME_smug_response as $SME_response => $SME_responseValue) {
 					 $SME_responseText=$SME_response;
  					 if ($SME_responseText=="URL")$SME_responseText="SmugMug Gallery";
  					 if ($SME_responseText=="Lightbox")$SME_responseText="SmugMug Lighbox";
				         if ($SME_responseValue)
				          echo '<option ', (($SME_response == $SME_default_response) ?  "selected" : "") ,' value="'.$SME_response.'">'.$SME_responseText.'</option>';
				     }
				   }
				 ?>    
				</select>
			</div>
			<div class="setting newWindow">
				<span>New Window?</span>
				<span><input type="checkbox" style="float:left;width:auto;" name="newWindow" value="Yes" <?php echo '',($SME_defaultNewWindow=="Yes") ? "checked" : "" ,' '; ?> class="link-to"  data-user-setting="new window" name="newWindow" id="newWindow" data-setting="newWindow"/></span>
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
             		<div class="SME_Slider">
							<div class="setting ">
								<span style="margin-left: 90px;text-align: left;"> 
								<input type="radio"  onClick="SME_toggleSliderSettings();" checked="checked" style="float:left;width:auto;margin:0px 4px 0px 0px;" name="SME_newSlider"  value="images" id="ImageOnly">Image(s) Only</INPUT><br />
								<input type="radio"  onClick="SME_toggleSliderSettings();"  style="float:left;width:auto;margin:0px 4px 0px 0px;" name="SME_newSlider"   value="gallery"  id="GalleryOnly" >Gallery</INPUT><br /></span>
								<div id=SME_GallerySettings name=SME_GallerySettings style="display:none">
									<label class="setting">
									<span>Columns</span>
									<select name=SME_GalleryColumns  data-user-setting="SME_GalleryColumns" data-setting="SME_GalleryColumns" >
										<option value=1>1</option>
										<option value=2>2</option>
										<option value=3 selected>3</option>
										<option value=4>4</option>
										<option value=5>5</option>
										<option value=6>6</option>
										<option value=7>7</option>
										<option value=8>8</option>
										<option value=9>9</option>
										</select>
									</label>
									</div>
								
							</div>
					</div>
             	</div>
            </div>
            <div class="SME_media-frame-toolbar">
                <div class="SME_media-toolbar">
                    <div class="SME_media-selection">
					<span style="position: absolute;"><a class="SME_clear-selection" href="#" onclick="SME_selectAll()">Select All</a></span>
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
	  if (strlen($SME_ChosenGallery[0]) <1) exit;
     $SME_api->setToken( "id={$SME_smugmugembed_api['api']['id']}", "Secret={$SME_smugmugembed_api['api']['Secret']}" );
     $SME_images = get_transient('smugmugembed_images_'. $SME_ChosenGallery[0] .'_'.$SME_ChosenGallery[1]);
     if (!$SME_images){
        $SME_images= $SME_api->images_get('AlbumID='. $SME_ChosenGallery[0],'AlbumKey='.$SME_ChosenGallery[1],'Heavy=1,Extras=ThumbURL,SmallURL,MediumURL,LargeURL,XLargeURL,X2LargeURL,X2LargeURL' );
        $SME_images = ($SME_api->APIVer == "1.2.2" ) ? $SME_images[ 'Images' ] : $SME_images;  
        //we need this in another function so set it to session
        //we cant use globals here because the way the function is called
        //from admin_ajax.php...we may try this with transients in a future release
        set_transient('smugmugembed_images_'. $SME_ChosenGallery[0] .'_'.$SME_ChosenGallery[1],$SME_images,3600);
     }
     $_SESSION['SME_images']=$SME_images; 
     foreach ($SME_images as $image =>$imageValue) {
      echo '<li class="SME_attachment SME_save-ready" >';
      echo '<div id="'.$imageValue['id'].'" name="SME_imageDiv" class="SME_attachment-preview" onclick=SME_ChangeState(this)  >';
      echo '<div class="SME_thumbnail">';
      echo '<div class="SME_centered"  >';
      echo "<img  src='".$imageValue['ThumbURL']."' />";
      echo '</div></div>';
      echo '<a class="uncheck"><div class="media-modal-icon"></div></a></div></li>';
	 }
     echo "<input type=hidden id='locked' value='".$SME_ChosenGallery[2]."' />";
     exit;
}        
add_action('wp_ajax_SME_getImagesFromGallery', 'SME_getImagesFromGallery');
function SME_ClearImageGalleryTransient() {
	  global $SME_api,$SME_Settings,$SME_api_progress, $SME_smugmugembed_api;
	  	   //SME_EnsureCanReachSmugMug();

			if ( $SME_api_progress!= 4 ) {
				echo '<div id="message" class="error"><p><strong>SmugMug Embed needs to be authorized before use.  To start the process, please <a href="../wp-admin/options-general.php?page=smugmugembed-settings" title="authorize SmugMug Embed">click here</a></strong></p></div>';
				return;
			}
	$galleries = get_transient('smugmugembed_galleries');
				if (!$galleries){
					$SME_api->setToken( "id={$SME_smugmugembed_api['api']['id']}", "Secret={$SME_smugmugembed_api['api']['Secret']}" );
					$galleries = $SME_api->albums_get('Extras=Passworded', 'NickName=' . $SME_smugmugembed_api[ 'api' ][ 'NickName' ] );
					array_sort_by_column($galleries,'Title');
		}
	foreach ( $galleries as $gallery => $galleryvalue ) {
		delete_transient('smugmugembed_images_'. $galleryvalue['id'] .'_'.$galleryvalue['Key']);
		 }
		delete_transient("smugmugembed_galleries");
	  	$galleries = get_transient('smugmugembed_galleries');
				if (!$galleries){
					$SME_api->setToken( "id={$SME_smugmugembed_api['api']['id']}", "Secret={$SME_smugmugembed_api['api']['Secret']}" );
					$galleries = $SME_api->albums_get('Extras=Passworded', 'NickName=' . $SME_smugmugembed_api[ 'api' ][ 'NickName' ] );
					array_sort_by_column($galleries,'Title');
	    	        set_transient( 'smugmugembed_galleries', $galleries, 3600 ); 

		}

}
add_action('wp_ajax_SME_ClearImageGalleryTransient', 'SME_ClearImageGalleryTransient');
function SME_array_multi_search($needle,$haystack){
global $SME_api;
foreach($haystack as $key=>$data){

if($data['id']==$needle) {
return $key;
}
}
}

function SME_translateMeta($meta,$image) {

   if (strpos($meta,'{SMUGMUGKEYWORDS}')!==false) $meta = str_replace("{SMUGMUGKEYWORDS}",$image['Keywords'],$meta);
   if (strpos($meta,'{SMUGMUGFILENAME}')!==false) $meta = str_replace("{SMUGMUGFILENAME}",$image['FileName'],$meta);
   if (strpos($meta,'{SMUGMUGCAPTION}')!==false)  $meta = str_replace("{SMUGMUGCAPTION}",$image['Caption'],$meta);      
   return $meta;
}
function SME_sendImagesToEditor() {
     global $SME_api,$SME_Settings,$SME_api_progress, $SME_smugmugembed_api;
	 $SME_insertType=$_POST['SME_newSlider'];

     $SME_images=$_SESSION['SME_images'];
     $SME_selectedImages = explode(",",$_POST["selectedImages"]);
     $SME_link=$_POST['link'];
     $SME_size=$_POST['size'];
     $SME_align=$_POST['align'];  
     $SME_border=$_POST['border'];  
     $SME_Cols=$_POST['SME_GalleryColumns'];  
	 
     $SME_ChosenGallery=explode("|",$_POST['chosenGallery']);

     $SME_newWin=$_POST['newWindow']; 
	$out = "";		
	 
	 if ($SME_insertType == "gallery") {
	      foreach ($SME_selectedImages as $SME_selectedImage =>$SME_selectedImageValue) {
			$image = $SME_images[SME_array_multi_search($SME_selectedImageValue,$SME_images)];
			 switch ($SME_size)
			{
           case "Thumbnail":
                $imageUrl='ThumbURL';
        	break;
           case "Medium":
                $imageUrl='MediumURL';
        	break;
           case "Large":
                $imageUrl='LargeURL';
        	break;
           case "XLarge":
                $imageUrl='XLargeURL';
        	break;	
           case "2XLarge":
                $imageUrl='X2LargeURL';
        	break;
           case "3XLarge":
                $imageUrl='X3LargeURL';
        	break;			
           default:
                $imageUrl='ThumbURL';
        	break;      
        }  

			$imageList .= $image['Key'].",";
			
	 	            $out = "[SME_gallery ids='".trim($imageList,",")."' size='".$imageUrl."' columns='".$SME_Cols."' caption='".$_POST['caption']."' link='".$SME_link."' new='".$SME_newWin."']";
			}
                media_send_to_editor($out);
				
	  return;
	  } else if ($SME_insertType == "slider") {
	      foreach ($SME_selectedImages as $SME_selectedImage =>$SME_selectedImageValue) {
			$image = $SME_images[SME_array_multi_search($SME_selectedImageValue,$SME_images)];
			 switch ($SME_size)
			{
           case "Thumbnail":
                $imageUrl='ThumbURL';
        	break;
           case "Medium":
                $imageUrl='MediumURL';
        	break;
           case "Large":
                $imageUrl='LargeURL';
        	break;
           case "XLarge":
                $imageUrl='XLargeURL';
        	break;	
           case "2XLarge":
                $imageUrl='X2LargeURL';
        	break;
           case "3XLarge":
                $imageUrl='X3LargeURL';
        	break;			
           default:
                $imageUrl='ThumbURL';
        	break;      
        }  

			$imageList .= $image['Key'].",";
			
	 	            $out = "[SME_slider ids='".trim($imageList,",")."' size='".$imageUrl."'  link='".$SME_link."' new='".$SME_newWin."']";
			}
                media_send_to_editor($out);
				
	  return;
	  }
	  if (!empty($SME_border)) $SME_border = "style='border:1px solid black'";

     foreach ($SME_selectedImages as $SME_selectedImage =>$SME_selectedImageValue) {
	 $image = $SME_images[SME_array_multi_search($SME_selectedImageValue,$SME_images)];
		  if ($image['Hidden']=="1") {
		  continue;
		  }
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
           case "XLarge":
                $imageUrl=$image['XLargeURL'];
        	break;	
           case "2XLarge":
                $imageUrl=$image['X2LargeURL'];
        	break;
           case "3XLarge":
                $imageUrl=$image['X3LargeURL'];
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
try {
	list($SME_width, $SME_height, $SME_type, $SME_attr) = getimagesize($imageUrl);   
} catch (Exception $e) {
continue;
}
        $html = "<img width='".$SME_width."' src='".$imageUrl."' class='".$float."' alt='".$SME_alt."' title='".$SME_title."' $SME_border/>";        	
        switch ($SME_link)
        {
        ///this is not working yet so we took it out of release 1
		//working as of 1.4
           case "Shopping":
                $imageLink= 'http://' .$SME_smugmugembed_api[ 'api' ][ 'NickName' ] . '.smugmug.com/buy/' . $SME_ChosenGallery[0]. '_' . $SME_ChosenGallery[1] . '/' . $image[ 'id' ] . '_' . $image[ 'Key' ];

        	break;
           case "Lightbox":
                $imageLink= $image['LightboxURL'];

        	break;
           case "Large":
                $imageLink= $image['LargeURL'];
        	break;
           case "URL":
                $imageLink= $image['URL'];
        	break;
           default:
           	$imageLink="";
        	break;      
        }  
        if ($imageLink!=""){
            $newWindow="";
            if ($SME_newWin=="Yes") $newWindow="target=_blank ";
            $html="<a href='".$imageLink."' ".$newWindow." >".$html."</a>";
         }
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

//code for gallery
function SME_custom_sanitize_html_class( $sanitized, $class, $fallback) {
 switch ($class)
        {
		   case "X3LargeURL":
			$output = "full";	
			break;			
           case "X2LargeURL":
		   case "LargeURL":
			$output = "large";
			break;						
		   case "MediumURL":
		   case "SmallURL":
		    $output = "medium";
			break;						
		   case "ThumbURL":
		      $output="thumbnail";
			break;						  
		   default:
		     $output=$sanitized;
			break;						 
		}
return $output;
}

add_filter('sanitize_html_class', 'SME_custom_sanitize_html_class', 1,3);

//function SME_custom_gallery_shortcode($str, $attr) {
function SME_custom_gallery_shortcode( $attr) {
     global $SME_api,$SME_Settings,$SME_api_progress, $SME_smugmugembed_api;
   //NEW CODE IN HERE///////////////////////////////
$html5 = current_theme_supports( 'html5', 'gallery' );
	$atts = shortcode_atts( array(
		'order'      => 'ASC',
		'orderby'    => 'menu_order ID',
		'id'         => 0,
		'itemtag'    => $html5 ? 'figure'     : 'dl',
		'icontag'    => $html5 ? 'div'        : 'dt',
		'captiontag' => $html5 ? 'figcaption' : 'dd',
		'columns'    => 3,
		'size'       => 'thumbnail',
		'include'    => '',
		'exclude'    => '',
		'link'       => '',
		'caption'       => '',
		'ids'       => '',
		'new'       => ''
	), $attr, 'SME_gallery' );
	if (empty ( $atts['include'] )) $atts['include'] = $atts['ids'];


	$id = intval( $atts['id'] );


		$attachments = array();

$images = explode(",",$atts['include']);

	foreach ( $images as $key => $imageKey ) {
      $SME_api->setToken("id={$SME_smugmugembed_api['api']['id']}", "Secret={$SME_smugmugembed_api['api']['Secret']}" );
	  $image = $SME_api->images_getInfo("ImageKey={$imageKey}",'Heavy=1' );
	  $attachments[count($attachments)] = $image;
}

	if ( empty( $attachments ) ) {
		return '';
	}



	$itemtag = tag_escape( $atts['itemtag'] );
	$captiontag = tag_escape( $atts['captiontag'] );
	$icontag = tag_escape( $atts['icontag'] );
	$valid_tags = wp_kses_allowed_html( 'post' );
	if ( ! isset( $valid_tags[ $itemtag ] ) ) {
		$itemtag = 'dl';
	}
	if ( ! isset( $valid_tags[ $captiontag ] ) ) {
		$captiontag = 'dd';
	}
	if ( ! isset( $valid_tags[ $icontag ] ) ) {
		$icontag = 'dt';
	}

	$columns = intval( $atts['columns'] );
	$itemwidth = $columns > 0 ? floor(100/$columns) : 100;
	$float = is_rtl() ? 'right' : 'left';

	$selector = "gallery-{$instance}";

	$gallery_style = '';

	/**
	 * Filter whether to print default gallery styles.
	 *
	 * @since 3.1.0
	 *
	 * @param bool $print Whether to print default gallery styles.
	 *                    Defaults to false if the theme supports HTML5 galleries.
	 *                    Otherwise, defaults to true.
	 */
	if ( apply_filters( 'use_default_gallery_style', ! $html5 ) ) {
		$gallery_style = "
		<style type='text/css'>
			#{$selector} {
				margin: auto;
			}
			#{$selector} .gallery-item {
				float: {$float};
				margin-top: 10px;
				text-align: center;
				width: {$itemwidth}%;
			}
			#{$selector} img {
				border: 2px solid #cfcfcf;
			}
			#{$selector} .gallery-caption {
				margin-left: 0;
			}
			/* see gallery_shortcode() in wp-includes/media.php */
		</style>\n\t\t";
	}
    
	$size_class = sanitize_html_class( $atts['size'] );
	$gallery_div = "<div id='$selector' class='gallery galleryid-{$id} gallery-columns-{$columns} gallery-size-{$size_class}'>";

	/**
	 * Filter the default gallery shortcode CSS styles.
	 *
	 * @since 2.5.0
	 *
	 * @param string $gallery_style Default CSS styles and opening HTML div container
	 *                              for the gallery shortcode output.
	 */
	$output = apply_filters( 'gallery_style', $gallery_style . $gallery_div );

	$i = 0;
	foreach ( $attachments as $id => $attachment ) {
		$image_meta  = $attachment;

		$image_output ="<img class='attachment-".$size_class."'  alt='".$image_meta['FileName']."' src='". $attachment[$atts['size']]."'></img>";
        switch ($atts['link'])
        {
        ///this is not working yet so we took it out of release 1
		//working as of 1.4
           case "Shopping":
                $imageLink= 'http://' .$SME_smugmugembed_api[ 'api' ][ 'NickName' ] . '.smugmug.com/buy/' . $attachment[ 'Album' ]['id'] . '_' . $attachment[ 'Album' ]['Key'] . '/' . $attachment[ 'id' ] . '_' . $attachment[ 'Key' ];
        	break;
           case "Lightbox":
                $imageLink= $attachment['LightboxURL'];

        	break;
           case "Large":
                $imageLink= $attachment['LargeURL'];
        	break;
           case "URL":
                $imageLink= $attachment['URL'];
        	break;
           default:
           	$imageLink="";
        	break;      
        }  
        if ($imageLink!=""){
            $newWindow="";
            if ($atts['new'] =="Yes") $newWindow="target=_blank ";
            $html="<a href='".$imageLink."' ".$newWindow." >".$image_output."</a>";
			$image_output=$html;
			
         }
		//	$image_output = wp_get_attachment_link( $id, $atts['size'], true, false, false, $attr );
		 $SME_caption=SME_translateMeta($atts['caption'],$attachment);

		$orientation = '';
		if ( isset( $image_meta['Height'], $image_meta['Width'] ) ) {
			$orientation = ( $image_meta['Height'] > $image_meta['Width'] ) ? 'portrait' : 'landscape';
		}
		$output .= "<{$itemtag} class='gallery-item'>";
		$output .= "
			<{$icontag} class='gallery-icon {$orientation}'>
				$image_output
			</{$icontag}>";
		if ( $captiontag && trim($SME_caption) ) {
			$output .= "
				<{$captiontag} class='wp-caption-text gallery-caption' id='$selector-$id'>
				" . wptexturize(trim($SME_caption)) . "
				</{$captiontag}>";
		}
		$output .= "</{$itemtag}>";
		if ( ! $html5 && $columns > 0 && ++$i % $columns == 0 ) {
			$output .= '<br style="clear: both" />';
		}
	}

	if ( ! $html5 && $columns > 0 && $i % $columns !== 0 ) {
		$output .= "
			<br style='clear: both' />";
	}

	$output .= "
		</div>\n";
	return $output;
}

add_shortcode('SME_gallery', 'SME_custom_gallery_shortcode');
