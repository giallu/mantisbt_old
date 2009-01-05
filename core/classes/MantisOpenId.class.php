<?php
# Mantis - a php based bugtracking system

# Mantis is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 2 of the License, or
# (at your option) any later version.
#
# Mantis is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with Mantis.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @copyright Copyright (C) 2002 - 2009  Mantis Team   - mantisbt-dev@lists.sourceforge.net
 * @link http://www.mantisbt.org
 * @package CoreAPI
 * @subpackage OpenId
 */

/**
 * A class that handles functionality related to Open ID support.
 */
class MantisOpenId
{
	/**
	 * Checks if Open ID support is enabled.  This is true if the enabled flag
	 * is set to ON and both API / site names are set.
	 * 
	 * @returns true: enabled, false: otherwise.
	 */
	public static function isEnabled() {
		if ( config_get( 'openid_enabled' ) != ON ) {
			return false;
		}
		
		if ( is_blank( config_get( 'openid_api_key' ) ) || is_blank( config_get( 'openid_site_name' ) ) ) {
			return false;
		}
		
		return true;		
	}

	/**
	 * Gets a string with the javascript that needs to be added at the bottom of the pages
	 * that will have a open id login.
	 * 
	 * @returns the script to be added at the bottom of the page before the closing body flag.
	 */
	public static function getLoginScript() {
		$t_script = '<!-- open id login -->' . "\n";
		$t_script .= '<script src="https://rpxnow.com/openid/v2/widget" type="text/javascript"></script>' . "\n";
		$t_script .= '<script type="text/javascript">' . "\n";
	  	$t_script .= '	RPXNOW.token_url = "' . MantisOpenId::getTokenUrl( false ) . '"' . "\n";
	 	$t_script .= '	RPXNOW.realm = "' . MantisOpenId::getSiteName() . '"' . "\n";
	  	$t_script .= '	RPXNOW.overlay = true' . "\n";
		$t_script .= '</script>' . "\n";
		$t_script .= '<!-- end of open id login -->' . "\n";
	
		return $t_script;
	}
	
	/**
	 * Gets a string with a Login link.  When clicked this link will activate the RpxNow javascript widget.
	 * 
	 * @param string html contents within the link (img tag or text).
	 * @returns string with the login link html.
	 */
	public static function getSignInLink( $p_contents ) {
		$t_link = '<a class="rpxnow" onclick="return false;" href="https://';
		$t_link .= MantisOpenId::getSiteName();
		$t_link .= '.rpxnow.com/openid/v2/signin?token_url=';
		$t_link .= MantisOpenId::getTokenUrl();
		$t_link .= '">';
		$t_link .= $p_contents;
		$t_link .= '</a>';

		return $t_link;
	}

	/**
	 * Gets the URL that the Open Id should call with the authentication token
	 * after the user signs in.
	 * 
	 * @param bool $p_url_encode - true: encode the url, false: return as is.
	 * @returns the url to send.
	 */
	private static function getTokenUrl( $p_url_encode = true ) {
		$t_token_url = config_get( 'path' ) . 'openid_login.php';

		if ( $p_url_encode ) {
			$t_token_url = urlencode( $t_token_url );
		}

		return $t_token_url;
	}
	
	/**
	 * Gets the name of the site as registered on RpxNow.
	 * 
	 * @returns the site name.
	 */
	private static function getSiteName() {
		return config_get( 'openid_site_name' );
	}
}

/**
 * Exception class specific to exceptions thrown by RPX class.
 */
class APIException extends Exception
{
}

/**
 * A class that interacts with the RpxNow service.  This is based on
 * the PHP sample supplied on their website, but changes to use
 * SimpleXml rather than taking a dependency on XML DOM.
 * 
 * This class was also modified to use the $g_openid_ca_bundle
 * configuration option to provide curl with the certificates
 * bundle.
 */
class RPX {
    var $api_key = null;
    var $base_url = null;
    var $format = "xml";
    var $response_body = "";

    function RPX($api_key, $base_url) {
        while ($base_url[strlen($base_url) - 1] == "/") {
            $base_url = substr($base_url, 0, strlen($base_url) - 1);
        }

        $this->api_key = $api_key;
        $this->base_url = $base_url;
    }

    /*
     * Performs the 'auth_info' API call to retrieve information about
     * an OpenID authentication response.  You'll need to inspect the
     * resulting DOMDocument to get information about the response.
     * See the API documentation for details.
     *
     * https://rpxnow.com/docs
     */
    public function auth_info($token) {
        return $this->apiCall("auth_info", array("token" => $token));
    }

    /*
     * Returns an array of identifier mappings for the specified
     * primary key.
     */
    public function mappings($primary_key) {
        $api_response = $this->apiCall(
             "mappings", array("primaryKey" => $primary_key));

        $status = $this->_getMessageStatus( $api_response );

        if ( $status != 'ok' ) {
            throw new APIException(
              sprintf("API status was not 'ok', got '%s' instead", $status));
        }
        
        $t_identifiers = $api_response->identifiers;

        return $t_identifiers;
    }

    /*
     * Maps an identifier to a primary key from your application.
     * Returns null.
     */
    public function map($identifier, $primary_key) {
        $this->apiCall("map", array("primaryKey" => $primary_key,
                                    "identifier" => $identifier));
    }

    /*
     * Removes a mapping for an identifier and primary key.  Returns
     * null.
     */
    public function unmap($identifier, $primary_key) {
        $this->apiCall("unmap", array(
            "primaryKey" => $primary_key,
            "identifier" => $identifier));
    }

    /*
     * Performs an API call using the specified name and arguments
     * array.  Automatically adds your API key to the request and
     * requests an XML response.  Returns a DOMDocument or raises
     * APIException.
     */
    private function apiCall($method_name, $partial_query) {
        $partial_query["format"] = $this->format;
        $partial_query["apiKey"] = $this->api_key;

        $query_str = "";
        foreach ($partial_query as $k => $v) {
            if (strlen($query_str) > 0) {
                $query_str .= "&";
            }

            $query_str .= urlencode($k);
            $query_str .= "=";
            $query_str .= urlencode($v);
        }

        $url = $this->base_url . "/api/v2/" . $method_name;
        $response_body = $this->_post($url, $query_str);

        $api_response = $this->_parse($response_body);
        $status = $this->_getMessageStatus($api_response);

        if ($status != 'ok') {
            throw new APIException(
              sprintf("API status was not 'ok', got '%s' instead", $status));
        }

        return $api_response;
    }

    private function _getMessageStatus($parsed_response) {
        $t_attributes = $parsed_response->attributes();
        return $t_attributes['stat']; 
    }

    private function _resetPostData() {
        $this->response_data = "";
    }

    private function _writeResponseData($curl_handle, $raw) {
        $this->response_data .= $raw;
        return strlen($raw);
    }

    private function _post($url, $post_data) {
        $this->_resetPostData();

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_WRITEFUNCTION,
                    array(&$this, "_writeResponseData"));

        $ca = config_get( 'openid_ca_bundle' );
        if ($ca != '') {
            curl_setopt($curl, CURLOPT_CAINFO, $ca); // Set the location of the CA-bundle
        }
        curl_exec($curl);

        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if (!$code) {
            throw new APIException(
              sprintf("Error performing HTTP request: %s", curl_error($curl)));
        }

        $response_body = $this->response_data;
        $this->_resetPostData();
        curl_close($curl);

        return $response_body;
    }

    private function _parse($raw) {
    	$doc = simplexml_load_string( $raw );
    	return $doc;
    }
}
