<?php
/*
Plugin Name: SmugMug Embed
Plugin URI: http://www.wicklundphotography.com/smugmugembed-wordpress-plugin
Description: Embeds images from a users SmugMug account into a post or page
Author: Tracy Wicklund	
Version: 0.91
Author URI: http://www.wicklundphrography.com/
*/

/*  Copyright 2013  Tracy Wicklund  (email : tracy@wicklundphotography.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

//start a session
add_action('init', 'myStartSession', 1);
add_action('wp_logout', 'myEndSession');
add_action('wp_login', 'myEndSession');

function myStartSession() {
    if(!session_id()) {
        session_start();
    }
}

function myEndSession() {
    session_destroy ();
}


require_once( dirname( __FILE__ ) . '/includes/SmugMugEmbedSettings.php' );
require_once( dirname( __FILE__ ) . '/includes/lib/phpSmug/phpSmug.php' );
require_once( dirname( __FILE__ ) . '/includes/SME-smugmugembed_shortcode.php' );
//Create SmugMug API Class
    $SME_api   = new SME_phpSmug( "APIKey=hLIFIsmrKd7lITN7j22SNggj4ITyFl1s", "AppName=SmugMug Embed", "OAuthSecret=e3f56a2abe72165029bef82799945c8b" );

    /*-----------------------------------------------------------------------------------*/
    /* Call register settings function */
    /*-----------------------------------------------------------------------------------*/

    function SME_smugmugembed_settings() {

        register_setting( 'SME_smugmugembed_api_group', 'SME_smugmugembed_api' );
        register_setting( 'SME_smugmugembed_api_group', 'SME_api_progress' );
        register_setting( 'SME_smugmugembed_settings_group', 'SME_Settings' );        
      }

    add_action( 'admin_init', 'SME_smugmugembed_settings' );


//define settings and functions
    $SME_smugmugembed_api      = get_option( 'SME_smugmugembed_api' );
    //get the SmugMug auth flag
    $SME_api_progress =  get_option('SME_api_progress');
    //get the array of main settings
    $SME_Settings = get_option( 'SME_Settings');

    

    add_action( 'admin_enqueue_scripts', 'SME_admin_scripts' );

    function SME_admin_scripts() {
        wp_register_style( 'SME_EmbedStyle', plugins_url( '/includes/css/style.css', __FILE__ ) );
        wp_register_script( 'SME_JavaScript', plugins_url( '/includes/SME_SmugMugEmbed.js', __FILE__ ));
        wp_enqueue_script(  'SME_JavaScript' );
        wp_enqueue_style( 'SME_EmbedStyle' );
    }
//These methods will show the Authorize message from the admin panel if 
//smugmug has not authorized SmugMugEmbed

  function showSMEAuthorizeMessage() {
        global $SME_api_progress;
        if ( $SME_api_progress!= 4 ) {
            echo '<div id="message" class="error"><p><strong>SmugMug Responsive Slider needs to be authorized before use.  To start the process, please <a href="/wp-admin/options-general.php?page=smugmugembed-settings" title="authorize SmugMug Embed">click here</a></strong></p></div>';
        }
    }
    
    function showSMEAdminMessages() {
        showMessage( "SmugMug Embed needs to be authorized before it will work.", true );
    }

    add_action( 'admin_notices', 'showSMEAdminMessages' );

    /*-----------------------------------------------------------------------------------*/
    /* Activation Hook.  Check WP Version */
    /*-----------------------------------------------------------------------------------*/

    register_activation_hook( __FILE__, 'SME_Activivation_init' );

    function SME_Activivation_init() {
        global $wp_version;

        if ( version_compare( $wp_version, "3.2", "<" ) ) {

            deactivate_plugins( basename( __file__ ) );
            wp_die( "This plugin requires WordPress version 3.2 or higher." );
        }

        add_option(
            'SME_Settings', array(
                                    'availableGalleries'      => array(''),
                                    'availableSizes'          => array(''),
                                    'availableClickResponses'          => array(''),                                    
                                    'clickResponse'        => 0,
                                    'caption'      => 0,
                                    'keywords'         => 0,
                                    'imageName'       => 0,
                                    'defaultSize'     => 0
                               )
        );
    }    
?>
