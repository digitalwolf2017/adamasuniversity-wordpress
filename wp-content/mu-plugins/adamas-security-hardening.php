<?php
/**
 * Plugin Name: Adamas Security Hardening (Must-Use)
 * Description: Hardens WordPress core against username enumeration, version disclosure, brute-force attacks, and form spam.
 * Version: 1.1.0
 * Author: Adamas University IT / Security
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Helper: Retrieve client IP securely
 */
function adamas_get_client_ip() {
    $ip = '';
    if ( ! empty( $_SERVER['HTTP_CF_CONNECTING_IP'] ) ) {
        $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
    } elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
        $parts = explode( ',', $_SERVER['HTTP_X_FORWARDED_FOR'] );
        $ip = trim( $parts[0] );
    } elseif ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return filter_var( $ip, FILTER_VALIDATE_IP ) ? $ip : '0.0.0.0';
}

/**
 * 1. Block Author Scanning / Username Enumeration via Query String (/?author=N)
 */
add_action( 'template_redirect', function () {
    if ( ! is_admin() && ( isset( $_GET['author'] ) || is_author() ) ) {
        wp_safe_redirect( home_url(), 301 );
        exit;
    }
} );

/**
 * 2. Restrict WP REST API User Endpoints for Unauthenticated Requests
 * Prevents bots from querying /wp-json/wp/v2/users to extract user accounts.
 */
add_filter( 'rest_endpoints', function ( $endpoints ) {
    if ( ! is_user_logged_in() ) {
        if ( isset( $endpoints['/wp/v2/users'] ) ) {
            unset( $endpoints['/wp/v2/users'] );
        }
        if ( isset( $endpoints['/wp/v2/users/(?P<id>[\d]+)'] ) ) {
            unset( $endpoints['/wp/v2/users/(?P<id>[\d]+)'] );
        }
    }
    return $endpoints;
} );

/**
 * 3. Disable WordPress Version Disclosure
 * Removes version numbers from <head>, RSS feeds, and script/style tags.
 */
remove_action( 'wp_head', 'wp_generator' );
add_filter( 'the_generator', '__return_empty_string' );

add_filter( 'style_loader_src', function ( $src ) {
    if ( strpos( $src, 'ver=' . get_bloginfo( 'version' ) ) ) {
        $src = remove_query_arg( 'ver', $src );
    }
    return $src;
}, 9999 );

add_filter( 'script_loader_src', function ( $src ) {
    if ( strpos( $src, 'ver=' . get_bloginfo( 'version' ) ) ) {
        $src = remove_query_arg( 'ver', $src );
    }
    return $src;
}, 9999 );

/**
 * 4. Disable XML-RPC Services (Defense-in-Depth)
 */
add_filter( 'xmlrpc_enabled', '__return_false' );
add_filter( 'xmlrpc_methods', function () {
    return array();
} );

/**
 * 5. Generic Login Error Messages
 * Prevents attackers from guessing valid usernames based on login error responses.
 */
add_filter( 'login_errors', function () {
    return __( '<strong>Error:</strong> Invalid login credentials.', 'default' );
} );

/**
 * 6. Login Brute Force Protection & Rate Limiting
 * Locks out an IP address after 5 failed login attempts for 15 minutes.
 */
add_filter( 'authenticate', function ( $user, $username, $password ) {
    if ( empty( $username ) || empty( $password ) ) {
        return $user;
    }

    $ip = adamas_get_client_ip();
    $transient_key = 'au_lockout_' . md5( $ip );
    $lockout_until = get_transient( $transient_key );

    if ( $lockout_until && time() < $lockout_until ) {
        $remaining = ceil( ( $lockout_until - time() ) / 60 );
        return new WP_Error(
            'too_many_retries',
            sprintf( __( '<strong>Security Lockout:</strong> Too many failed login attempts. Please wait %d minute(s) before trying again.', 'default' ), $remaining )
        );
    }

    return $user;
}, 20, 3 );

add_action( 'wp_login_failed', function ( $username ) {
    $ip = adamas_get_client_ip();
    $attempts_key = 'au_attempts_' . md5( $ip );
    $lockout_key  = 'au_lockout_' . md5( $ip );

    $attempts = (int) get_transient( $attempts_key );
    $attempts++;

    if ( $attempts >= 5 ) {
        // Lock out for 15 minutes (900 seconds)
        set_transient( $lockout_key, time() + 900, 900 );
        delete_transient( $attempts_key );
    } else {
        // Track attempts in a 15-minute sliding window
        set_transient( $attempts_key, $attempts, 900 );
    }
} );

add_action( 'wp_login', function ( $user_login, $user ) {
    $ip = adamas_get_client_ip();
    delete_transient( 'au_attempts_' . md5( $ip ) );
    delete_transient( 'au_lockout_' . md5( $ip ) );
}, 10, 2 );

/**
 * 7. Contact Form 7 Honeypot & Anti-Spam Shield
 * Automatically traps bots attempting automated form spam submissions.
 */
add_filter( 'wpcf7_form_elements', function ( $elements ) {
    $honeypot = '<span class="wpcf7-form-control-wrap au_hp_field" style="display:none !important; visibility:hidden !important; position:absolute; left:-9999px;" aria-hidden="true">'
              . '<input type="text" name="adamas_hp_trap" value="" tabindex="-1" autocomplete="off" />'
              . '</span>';
    return $elements . $honeypot;
} );

add_filter( 'wpcf7_spam', function ( $spam ) {
    if ( $spam ) {
        return $spam;
    }
    if ( ! empty( $_POST['adamas_hp_trap'] ) ) {
        return true;
    }
    return $spam;
} );
