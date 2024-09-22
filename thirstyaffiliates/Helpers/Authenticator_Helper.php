<?php
namespace ThirstyAffiliates\Helpers;

if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Authenticator_Helper {

  const OPTION_KEY_AUTH_ACCOUNT_EMAIL      = "ta_authenticator_account_email";
  const OPTION_KEY_AUTH_ACCOUNT_SECRET     = "ta_authenticator_secret_token";
  const OPTION_KEY_AUTH_ACCOUNT_SITE_UUID  = "ta_authenticator_site_uuid";
  const OPTION_KEY_AUTH_ACCOUNT_USER_UUID  = "ta_authenticator_user_uuid"; 

  /**
   * Assembles a URL for connecting to our Authenticator service.
   *
   * @access public
   * @param string $return_url The URL to return back to after being successfully authenticated.
   * @param array $additional_params Extra parameters to include in the URL for authentication.
   * @return string The assembled URL.
   */
  public static function get_auth_connect_url($return_url, $additional_params = array()) {
    $connect_params = array(
      'return_url' => urlencode(add_query_arg('ta-connect', 'true', $return_url)),
      'nonce'      => wp_create_nonce('ta-connect')
    );

    $site_uuid = get_option('ta_authenticator_site_uuid');

    if($site_uuid) {
      $connect_params['site_uuid'] = $site_uuid;
    }

    if(!empty($additional_params)) {
      $connect_params = array_merge($connect_params, $additional_params);
    }

    return add_query_arg($connect_params, TA_AUTH_SERVICE_URL . '/connect/thirstyaffiliates');
  }

  /**
   * Creates a Base64 encoded string so that it can be passed within URLs without any URL encoding.
   *
   * @access public
   * @param string $value The string to encode.
   * @return string The Base64 encoded string.
   */
  public static function base64url_encode($value) {
    return rtrim( strtr( base64_encode( $value ), '+/', '-_' ), '=' );
  }

  /**
   * Returns header data to be used with JWT and wp_remote_request.
   *
   * @access public
   * @param string $jwt The JWT to use in the Authorization header.
   * @param string $domain The domain to use in the Host header.
   * @return array The processed header data.
  */
  public static function jwt_header($jwt, $domain) {
    return array(
      'Authorization' => 'Bearer ' . $jwt,
      'Accept'        => 'application/json;ver=1.0',
      'Content-Type'  => 'application/json; charset=UTF-8',
      'Host'          => $domain
    );
  }

  /**
   * Generates a JWT (JSON Web Token), signed by the stored secret token.
   *
   * @access public
   * @param array $payload Payload data.
   * @param string $secret Used to sign the JWT.
   * @return string The generated JWT.
   */
  public static function generate_jwt($payload, $secret = false) {
    if($secret === false) {
      $secret = get_option('ta_authenticator_secret_token');
    }

    // Create token header.
    $header = array(
      'typ' => 'JWT',
      'alg' => 'HS256'
    );

    $header = json_encode($header);
    $header = Authenticator_Helper::base64url_encode($header);

    // Create token payload.
    $payload = json_encode($payload);
    $payload = Authenticator_Helper::base64url_encode($payload);

    // Create Signature Hash.
    $signature = hash_hmac('sha256', "{$header}.{$payload}", $secret);
    $signature = json_encode($signature);
    $signature = Authenticator_Helper::base64url_encode($signature);

    // Create JWT.
    $jwt = "{$header}.{$payload}.{$signature}";
    return $jwt;
  }
}
