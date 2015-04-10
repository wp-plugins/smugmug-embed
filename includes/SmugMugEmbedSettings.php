<?php
    /**
     * User: twicklund
     * Date: 2/1/2013
       ---Credit given to mgyura for the original code in his SmugSlider plugin
     */


    /*-----------------------------------------------------------------------------------*/
    /* Settings and oAuth approval for SmugMug Embed */
    /*-----------------------------------------------------------------------------------*/

    function SME_smugmugembed_option_settings() {
        global $SME_api, $SME_smugmugembed_api, $SME_api_progress,$SME_Settings, $SME_Settings;
       

		echo '<div class="wrap">';
        echo '<h2>SmugMug Embed Settings</h2>';
   /*-----------------------------------------------------------------------------------*/
        /* oAuth process start at the bottom of the page with the last else  */
        /* Now that we have the OAUth credentials we can make a settings page  */
        /* First step is to allow users to filter categories  */
        /*-----------------------------------------------------------------------------------*/

        if ( $SME_api_progress == 4 ) {
            try {
                $SME_api->setToken( "id={$SME_smugmugembed_api['api']['id']}", "Secret={$SME_smugmugembed_api['api']['Secret']}" );
                $galleries = $SME_api->albums_get( 'Extras=Passworded','NickName=' . $SME_smugmugembed_api[ 'api' ][ 'NickName' ] );
                 if (!empty ($SME_Settings['availableGalleries']) ) $SME_smug_cats = $SME_Settings['availableGalleries'];
			  $SME_smug_available_sizes = $SME_Settings['availableSizes'];
               $SME_smug_available_clicks = $SME_Settings['availableClickResponses'];
                ?>
				<form method="post" action="options.php">
				 <?php settings_fields( 'SME_smugmugembed_settings_group' ); ?>
                <h3>SmugMug Galleries</h3>
                <table class="form-table">
                    <tr valign="top">
                        <td>
                            <?php

                            echo '<p><b>Leave all unchecked to use every gallery OR select individual galleries to use below</b><br/>*<em>indicates password protected</em></p>';
                            echo '<div class="SMEsmug_cats"><h4>Available Galleries</h4>';
                            foreach ( $galleries as $gallery => $galleryvalue ) {

                            ?>
                                    <div class=SMEsmug_checkbox">
                                        <input type="checkbox" name="SME_Settings[availableGalleries][<?php echo $galleryvalue[ 'id' ] ?>]" id="<?php echo $galleryvalue[ 'id' ] ?>" value="<?php echo $galleryvalue[ 'id' ] ?>" <?php if ( isset( $SME_smug_cats[ $galleryvalue[ 'id' ] ] ) ) {
                                            echo 'checked="checked"';
                                        } ?> />
                                        <label <?php echo ($galleryvalue[ 'Passworded' ] ? 'style="font-style:italic;"' :'') ;?> for="<?php echo $galleryvalue[ 'id' ] ?>">
                                            <?php 
                                                  echo $galleryvalue[ 'Title' ]

                                            ?>
                                        </label>
                                    </div>
                            <?php
                            }
                            ?>

                        </td>
                    </tr>
                </table>
                <p class="submit">
                    <input type="submit" class="button-primary" value="Save All" />
                </p>
				<?php


                /*-----------------------------------------------------------------------------------*/
                /* Create settings for the image  */
                /*-----------------------------------------------------------------------------------*/

                echo '<h3>Image Options</h3>';
                ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">What sizes should be available to the author when inserting an image? </th>
                        <td>
                            <input type="checkbox" id="sizes[thumbnail]" name="SME_Settings[availableSizes][Thumbnail]"  <?php if ( isset( $SME_smug_available_sizes['Thumbnail'] )) {
                                            echo 'checked="checked"';
                                        } ?> /> Thumbnail<br />
                            <input type="checkbox"  id="sizes[small]" name="SME_Settings[availableSizes][Small]"   <?php if ( isset( $SME_smug_available_sizes['Small'] ) ) {
                                            echo 'checked="checked"';
                                        } ?> /> Small<br />
                            <input type="checkbox"  id="sizes[medium]"  name="SME_Settings[availableSizes][Medium]"  <?php if ( isset( $SME_smug_available_sizes['Medium'] ) ) {
                                            echo 'checked="checked"';
                                        } ?> /> Medium<br />
                            <input type="checkbox"  id="sizes[large]" name="SME_Settings[availableSizes][Large]"   <?php if ( isset( $SME_smug_available_sizes['Large'] ) ) {
                                            echo 'checked="checked"';
                                        } ?> /> Large<br /> 
                            <input type="checkbox"  id="sizes[xlarge]" name="SME_Settings[availableSizes][XLarge]"   <?php if ( isset( $SME_smug_available_sizes['XLarge'] ) ) {
                                            echo 'checked="checked"';
                                        } ?> /> XLarge<br />   
                            <input type="checkbox"  id="sizes[2xlarge]" name="SME_Settings[availableSizes][2XLarge]"   <?php if ( isset( $SME_smug_available_sizes['2XLarge'] ) ) {
                                            echo 'checked="checked"';
                                        } ?> /> 2XLarge<br />  		
                            <input type="checkbox"  id="sizes[3xlarge]" name="SME_Settings[availableSizes][3XLarge]"   <?php if ( isset( $SME_smug_available_sizes['3XLarge'] ) ) {
                                            echo 'checked="checked"';
                                        } ?> /> 3XLarge<br />  											
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">What should be the default size?  </th>
                        <td>
                            <select name="SME_Settings[defaultSize]">
                                <option value="Thumbnail" <?php selected($SME_Settings[ 'defaultSize' ], 'Thumbnail' ); ?>> Thumbnail</option>                            
                                <option value="Small" <?php selected($SME_Settings[ 'defaultSize' ], 'Small' ); ?>> Small</option>
                                <option value="Medium" <?php selected($SME_Settings[ 'defaultSize' ], 'Medium' ); ?>> Medium</option>
                                <option value="Large" <?php selected($SME_Settings[ 'defaultSize' ], 'Large' ); ?>> Large</option>  
                                <option value="XLarge" <?php selected($SME_Settings[ 'defaultSize' ], 'XLarge' ); ?>> XLarge</option>                                
                                <option value="2XLarge" <?php selected($SME_Settings[ 'defaultSize' ], '2XLarge' ); ?>> 2XLarge</option>                                
                                <option value="3XLarge" <?php selected($SME_Settings[ 'defaultSize' ], '3XLarge' ); ?>> 3XLarge</option>                                
								</select>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">What should be the default alignment?  </th>
                        <td>
                            <select name="SME_Settings[defaultAlign]">
                                <option value="Left" <?php selected($SME_Settings[ 'defaultAlign' ], 'Left' ); ?>> Left</option>                            
                                <option value="Center" <?php selected($SME_Settings[ 'defaultAlign' ], 'Center' ); ?>> Center</option>
                                <option value="Right" <?php selected($SME_Settings[ 'defaultAlign' ], 'Right' ); ?>> Right</option>
    			    </select>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">What options for click responses should be available to the author when inserting an image?  </th>
                        <td>
                             <input type="checkbox" name="SME_Settings[availableClickResponses][None]"  <?php if ( isset( $SME_smug_available_clicks['None'] ) ) {
                                            echo 'checked="checked"';
                                        } ?> /> No Link<br/>
                             <input type="checkbox" name="SME_Settings[availableClickResponses][Large]"  <?php if ( isset( $SME_smug_available_clicks['Large'] ) ) {
                                            echo 'checked="checked"';
                                        } ?> /> Large Image<br/>
                          <input type="checkbox" name="SME_Settings[availableClickResponses][Shopping]"  <?php if ( isset( $SME_smug_available_clicks['Shopping'] ) ) {
                                            echo 'checked="checked"';
                                        } ?> />SmugMug Shopping Cart<br/>
                           <input type="checkbox" name="SME_Settings[availableClickResponses][URL]"  <?php if ( isset( $SME_smug_available_clicks['URL'] ) ) {
                                            echo 'checked="checked"';
                                        } ?> /> SmugMug Gallery<br/> 
                             <input type="checkbox" name="SME_Settings[availableClickResponses][Lightbox]"  <?php if ( isset( $SME_smug_available_clicks['Lightbox'] ) ) {
                                            echo 'checked="checked"';
                                        } ?> />SmugMug Lightbox<br/>                                                                                                                   
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">What should be the default click response?  </th>
                        <td>
                            <select name="SME_Settings[clickResponse]">
                                <option value="None" <?php selected( $SME_Settings[ 'clickResponse' ], 'None' ); ?>>No Link</option>                            
                                <option value="Large" <?php selected( $SME_Settings[ 'clickResponse' ], 'Large' ); ?>>Large Image</option>
                             <option value="Shopping" <?php selected( $SME_Settings[ 'clickResponse' ], 'Shopping' ); ?>>SmugMug Shopping Cart</option>
                                <option value="URL" <?php selected( $SME_Settings[ 'clickResponse' ], 'URL' ); ?>>SmugMug Gallery</option>
                                <option value="Lightbox" <?php selected( $SME_Settings[ 'clickResponse' ], 'Lightbox' ); ?>>SmugMug Lightbox</option>                                
                            </select>
                        </td>
                    </tr>  
			<tr valign="top">
                        <th scope="row">Default for "Open in new window"?  </th>
                        <td>
                            <select name="SME_Settings[newWindow]">
                                <option value="Yes" <?php selected( $SME_Settings[ 'newWindow' ], 'Yes' ); ?>>Yes</option>                            
                                <option value="No" <?php selected( $SME_Settings[ 'newWindow' ], 'No' ); ?>>No</option>
                            </select>
                        </td>
                    </tr>                    
                    <tr valign="top">
                        <th scope="row">Default value for "Show Keywords":</th>
                        <td>
                            <select name="SME_Settings[keywords]">
                                <option value="1" <?php selected( $SME_Settings[ 'keywords' ], '1' ); ?>> True </option>
                                <option value="0" <?php selected( $SME_Settings[ 'keywords' ], '0' ); ?>> False </option>
                            </select>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Default value for "Show Caption":</th>
                        <td>
                            <select name="SME_Settings[caption]">
                                <option value="1" <?php selected( $SME_Settings[ 'caption' ], '1' ); ?>> True </option>
                                <option value="0" <?php selected( $SME_Settings[ 'caption' ], '0' ); ?>> False </option>
                            </select>
                        </td>
                    </tr>     
                    <tr valign="top">
                        <th scope="row">Default value for "Show Image Name":</th>
                        <td>
                            <select name="SME_Settings[imageName]">
                                <option value="1" <?php selected( $SME_Settings[ 'imageName' ], '1' ); ?>> True </option>
                                <option value="0" <?php selected( $SME_Settings[ 'imageName' ], '0' ); ?>> False </option>
                            </select>
                        </td>
                    </tr>                                      
 
                </table>
                <p class="submit">
                    <input type="submit" class="button-primary" value="Save All" />
                </p>
				</form>
            <?php


            } catch ( Exception $e ) {
                echo "{$e->getMessage()} (Error Code: {$e->getCode()})";
            }


?>
			<hr />
        <div class="SMEsmug_reset">
            <p>If you want to reset all available options, click this button</p>
            <form method="post" action="options.php">
                <?php settings_fields( 'SME_smugmugembed_settings_group' ); ?>
                <input type="hidden" name="SME_Settings[availableGalleries]" value="" />
                <input type="hidden" name="SME_Settings[availableClickResponses]" value="None" />
                <input type="hidden" name="SME_Settings[clickResponse]" value="0" />
                <input type="hidden" name="SME_Settings[caption]" value="0" />
                <input type="hidden" name="SME_Settings[keywords]" value="0" />
                <input type="hidden" name="SME_Settings[imageName]" value="0" />
                <input type="hidden" name="SME_Settings[defaultSize]" value="0" />
                <input type="hidden" name="SME_Settings[defaultAlign]" value="0" />
                <p class="submit">
                    <input type="submit" class="button-secondary" value="Reset Options" />
                </p>
            </form>
        </div>
 
        <div class="SMEsmug_reset">
            <p>If you want to link to a different SmugMug account, or have an error with the current SmugMug Embed authorization, click this button</p>
            <form method="post" action="options.php">
                <?php settings_fields( 'SME_smugmugembed_api_group' ); ?>
                <input type="hidden" name="SME_smugmugembed_api" value="" />
                <input type="hidden" name="SME_api_progress" value="" />
                <p class="submit">
                    <input type="submit" class="button-secondary" value="Delete SmugMug Authorization" />
                </p>
            </form>
        </div>
        <hr class="SMEClear" />

        <?php
        }
		

        /*-----------------------------------------------------------------------------------*/
        /* Step 3 in API OAuth approval */
        /* Grab the OAuth credentials and save them to the options array for later use  */
        /* Set the progress option to "4" */
        /*-----------------------------------------------------------------------------------*/

        elseif ( $SME_api_progress == 3 ) {

            //Use the Request token obtained to get an access token
            $SME_api->setToken( "id={$SME_smugmugembed_api['temp']['id']}", "Secret={$SME_smugmugembed_api['temp']['key']}" );
            $token = $SME_api->auth_getAccessToken();
            ?>

        <p>This page will automatically refresh in 5 seconds.  If it does not, click the below button.</p>
        <form method="post" action="options.php">
            <?php settings_fields( 'SME_smugmugembed_api_group' ); ?>
            <input type="hidden" name="SME_smugmugembed_api[temp][id]" value="" />
            <input type="hidden" name="SME_smugmugembed_api[temp][key]" value="" />
            <input type="hidden" name="SME_smugmugembed_api[api][id]" value="<?php echo $token[ 'Token' ][ 'id' ]; ?>" />
            <input type="hidden" name="SME_smugmugembed_api[api][Secret]" value="<?php echo $token[ 'Token' ][ 'Secret' ]; ?>" />
            <input type="hidden" name="SME_smugmugembed_api[api][Access]" value="<?php echo $token[ 'Token' ][ 'Access' ]; ?>" />
            <input type="hidden" name="SME_smugmugembed_api[api][Permissions]" value="<?php echo $token[ 'Token' ][ 'Permissions' ]; ?>" />
            <input type="hidden" name="SME_smugmugembed_api[api][User]" value="<?php echo $token[ 'User' ][ 'id' ]; ?>" />
            <input type="hidden" name="SME_smugmugembed_api[api][DisplayName]" value="<?php echo $token[ 'User' ][ 'DisplayName' ]; ?>" />
            <input type="hidden" name="SME_smugmugembed_api[api][NickName]" value="<?php echo $token[ 'User' ][ 'NickName' ]; ?>" />
            <input type="hidden" name="SME_smugmugembed_api[api][URL]" value="<?php echo $token[ 'User' ][ 'URL' ]; ?>" />
            <input type="hidden" name="SME_smugmugembed_api[api][AccountStatus]" value="<?php echo $token[ 'User' ][ 'AccountStatus' ]; ?>" />
            <input type="hidden" name="SME_smugmugembed_api[api][AccountType]" value="<?php echo $token[ 'User' ][ 'AccountType' ]; ?>" />
            <input type="hidden" name="SME_smugmugembed_api[api][FileSizeLimit]" value="<?php echo $token[ 'User' ][ 'FileSizeLimit' ]; ?>" />
            <input type="hidden" name="SME_smugmugembed_api[api][SmugVault]" value="<?php echo $token[ 'User' ][ 'SmugVault' ]; ?>" />
            <input type="hidden" name="SME_api_progress" value="4" />
            <p class="submit">
                <input type="submit" class="button-primary" id="formButton" value="Got the key" />
            </p>
        </form>


        <script language="javascript">
            document.getElementById("formButton").click();
        </script>


        <hr />
        <div class="SMEsmug_reset">
            <p>If there was an error in the approval process, click this button to restart approval</p>
            <form method="post" action="options.php">
                <?php settings_fields( 'SME_smugmugembed_api_group' ); ?>
                <input type="hidden" name="SME_smugmugembed_api" value="" />
                <input type="hidden" name="SME_api_progress" value="" />
                <p class="submit">
                    <input type="submit" class="button-secondary" value="Delete SmugMug Authorization" />
                </p>
            </form>
        </div>
        <hr class="SMEClear" />


        <?php

        }

        /*-----------------------------------------------------------------------------------*/
        /* Step 2 in API OAuth approval */
        /* Using the temp ID and Key go out to SmugMug and request approval */
        /* Need to save these to options becuase WP clears all $_SESSION[]  */
        /* Set the progress option to "3" */
        /*-----------------------------------------------------------------------------------*/

        elseif ( $SME_api_progress == 2 ) {

            echo '<h2>Step 1:</h2>';
            echo '<p>Click the button below to send a request to SmugMug for approval.  A new browser tab will open up and you will be asked to log into your SmugMug account and approve access for this app.</p>';
            echo "<p><a href='https://secure.smugmug.com/services/oauth/authorize.mg?Access=Full&Permissions=Add&oauth_token=" . $SME_smugmugembed_api[ 'temp' ][ 'id' ] . "' class='button-primary'  target='_blank'>Click here to log into SmugMug to approve access</a></p>";
            echo '<h2>Step 2:</h2>';
            echo '<p>Once you have given this app permission to access your account, click the below button.  This will save the approval credentials to your WordPress database.</p>'

            ?>

        <form method="post" action="options.php">
            <?php settings_fields( 'SME_smugmugembed_api_group' ); ?>
            <table class="form-table">
                <input type="hidden" name="SME_smugmugembed_api[temp][id]" value="<?php echo $SME_smugmugembed_api[ 'temp' ][ 'id' ] ?>" />
                <input type="hidden" name="SME_smugmugembed_api[temp][key]" value="<?php echo $SME_smugmugembed_api[ 'temp' ][ 'key' ] ?>" />
                <input type="hidden" name="SME_api_progress" value="3" />
            </table>
            <p class="submit">
                <input type="submit" class="button-primary" value="Authorization Completed, let's finalize this" />
            </p>
        </form>

        <hr />
        <div class="SMEsmug_reset">
            <p>If there was an error in the approval process, click this button to restart approval</p>
            <form method="post" action="options.php">
                <?php settings_fields( 'SME_smugmugembed_api_group' ); ?>
                <input type="hidden" name="SME_smugmugembed_api" value="" />
                <input type="hidden" name="SME_api_progress" value="" />
                <p class="submit">
                    <input type="submit" class="button-secondary" value="Delete SmugMug Authorization" />
                </p>
            </form>
        </div>
        <hr class="SMEClear" />


        <?php
        }


        /*-----------------------------------------------------------------------------------*/
        /* Step 1 in API OAuth approval */
        /* Grab the temp ID and Key from SmugMug and save it in an options array */
        /* Set the progress option to "2" */
        /*-----------------------------------------------------------------------------------*/

        else {

            // Step 1: Get a Request Token
            $d = $SME_api->auth_getRequestToken();
            ?>

        <p>SmugMug Embed needs to have permission from your SmugMug account to access photos.  Click the button below to start the approval process.  </p>
        <form method="post" action="options.php">
            <?php settings_fields( 'SME_smugmugembed_api_group' ); ?>
            <input type="hidden" name="SME_smugmugembed_api[temp][id]" value="<?php echo $d[ 'Token' ][ 'id' ]; ?>" />
            <input type="hidden" name="SME_smugmugembed_api[temp][key]" value="<?php echo $d[ 'Token' ][ 'Secret' ]; ?>" />
            <input type="hidden" name="SME_api_progress" value="2" />
            <p class="submit">
                <input type="submit" class="button-primary" value="Start Activation With SmugMug" />
            </p>
        </form>
        <?php
        }

        echo '</div>';
		
    }
    /*-----------------------------------------------------------------------------------*/
    /* Create settings menu for our functions */
    /*-----------------------------------------------------------------------------------*/

    function SME_SmugMugEmbed_settings_menu() {
        add_submenu_page( 'options-general.php', 'SmugMug Embed', 'SmugMug Embed', 'edit_posts', 'smugmugembed-settings', 'SME_smugmugembed_option_settings' );
    }

    add_action( 'admin_menu', 'SME_SmugMugEmbed_settings_menu' );

?>