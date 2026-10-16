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

/**
 * 8. Comprehensive File Upload Security Shield
 */

// A. Filter allowed MIME types to remove dangerous/executable types
add_filter( 'upload_mimes', function ( $mimes ) {
    $blocked = array(
        'html|htm',
        'js',
        'php',
        'phtml',
        'php3',
        'php4',
        'php5',
        'php7',
        'php8',
        'phar',
        'sh',
        'py',
        'pl',
        'cgi',
        'exe',
        'bat',
        'cmd',
        'bin',
        'dll',
        'msi',
    );
    foreach ( $blocked as $ext ) {
        unset( $mimes[ $ext ] );
    }
    return $mimes;
}, 999 );

// B. Deep Pre-Upload Validation (Double extensions, real image verification, SVG XSS inspection)
add_filter( 'wp_handle_upload_prefilter', 'adamas_validate_file_upload' );
add_filter( 'wp_handle_sideload_prefilter', 'adamas_validate_file_upload' );

function adamas_validate_file_upload( $file ) {
    if ( ! empty( $file['error'] ) ) {
        return $file;
    }

    $filename = isset( $file['name'] ) ? (string) $file['name'] : '';
    $tmp_path = isset( $file['tmp_name'] ) ? (string) $file['tmp_name'] : '';

    if ( empty( $filename ) ) {
        return $file;
    }

    // 1. Check for null bytes or control characters
    if ( strpos( $filename, "\0" ) !== false || preg_match( '/[\x00-\x1F\x7F]/', $filename ) ) {
        $file['error'] = __( 'Security Alert: File name contains invalid or hidden characters.', 'default' );
        return $file;
    }

    // 2. Reject double extensions targeting script execution (e.g., shell.php.jpg or image.phtml.png)
    $dangerous_patterns = '/\.(php[0-9]?|phtml|phtm|phar|pl|py|cgi|sh|bash|exe|bat|cmd|msi|vbs|js|html|htm)\./i';
    if ( preg_match( $dangerous_patterns, $filename ) ) {
        $file['error'] = __( 'Security Alert: Multi-extension files containing executable scripts are strictly prohibited.', 'default' );
        return $file;
    }

    // 3. Inspect final extension
    $ext = strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) );
    $executable_exts = array(
        'php', 'phtml', 'php3', 'php4', 'php5', 'php7', 'php8', 'phps', 'pht', 'phtm',
        'phar', 'pl', 'py', 'cgi', 'sh', 'bash', 'exe', 'bat', 'cmd', 'msi', 'vbs',
        'html', 'htm', 'shtml', 'svgz'
    );
    if ( in_array( $ext, $executable_exts, true ) ) {
        $file['error'] = __( 'Security Alert: Direct upload of executable or script formats is forbidden.', 'default' );
        return $file;
    }

    // 4. Image Real-Format Verification (Anti-Polyglot / Disguised scripts)
    $image_exts = array( 'jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp' );
    if ( in_array( $ext, $image_exts, true ) && ! empty( $tmp_path ) && file_exists( $tmp_path ) ) {
        $image_info = @getimagesize( $tmp_path );
        if ( false === $image_info || empty( $image_info[0] ) || empty( $image_info[1] ) ) {
            $file['error'] = __( 'Security Alert: The uploaded image is invalid or corrupted.', 'default' );
            return $file;
        }
    }

    // 5. Deep SVG Sanitization & Malicious Script Scan (Stored XSS Protection)
    if ( 'svg' === $ext && ! empty( $tmp_path ) && file_exists( $tmp_path ) ) {
        $svg_content = file_get_contents( $tmp_path );
        if ( false !== $svg_content ) {
            $xss_patterns = array(
                '/<script/i',
                '/javascript\s*:/i',
                '/data\s*:\s*text\/html/i',
                '/onload\s*=/i',
                '/onerror\s*=/i',
                '/onclick\s*=/i',
                '/onmouseover\s*=/i',
                '/onfocus\s*=/i',
                '/<foreignObject/i',
                '/<iframe/i',
                '/<embed/i',
                '/<object/i',
            );

            foreach ( $xss_patterns as $pattern ) {
                if ( preg_match( $pattern, $svg_content ) ) {
                    $file['error'] = __( 'Security Alert: SVG file contains insecure elements or embedded scripts.', 'default' );
                    return $file;
                }
            }
        }
    }

    return $file;
}

// C. Sanitize uploaded file names to avoid directory traversal or special character tricks
add_filter( 'sanitize_file_name', function ( $filename ) {
    return preg_replace( '/\.{2,}/', '.', $filename );
}, 10, 1 );

