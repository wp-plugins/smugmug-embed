<?php
    /**
     * User: twicklund
     * Date: 02/15/13
     */

    if ( !defined( 'ABSPATH' ) && !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
        exit();
    }

    else {
        delete_option( 'SME_smugmugembed_api' );
        delete_option( 'SME_api_progress' );
        delete_option( 'SME_Settings' );

        unregister_setting( 'SME_smugmugembed_api_group', 'SME_smugmugembed_api' );
        unregister_setting( 'SME_smugmugembed_api_group', 'SME_api_progress' );
        unregister_setting( 'SME_smugmugembed_settings_group', 'SME_Settings' ); 
    }