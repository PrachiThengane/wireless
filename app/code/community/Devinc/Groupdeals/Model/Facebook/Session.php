<?php
/**
 * Facebook session model
 * 
 * @author     Ivan Weiler <ivan.weiler@gmail.com>
 */
class Devinc_Groupdeals_Model_Facebook_Session extends Varien_Object
{
	private $_client;

	public function __construct() {
        if ($this->getCookie()) {
            $data = array();
            $data = $this->getCookie();
            $this->setData($data);
        }
    }
 
    public function isConnected() {
        if (!$this->validate()) {
            return false;
        }
        return true;
    }
 
    public function validate() {
        return $this->$_valid_signature;
    }
         
    public function getCookie()
    {
    	return $this->get_new_facebook_cookie(Mage::getSingleton('groupdeals/facebook_config')->getApiKey(), Mage::getSingleton('groupdeals/facebook_config')->getSecret());
    }
	     
	public function getClient()
	{
		if(is_null($this->_client)) {
			$this->_client = Mage::getModel('groupdeals/facebook_client',array(
									Mage::getSingleton('groupdeals/facebook_config')->getApiKey(),
									Mage::getSingleton('groupdeals/facebook_config')->getSecret(),
									$this
							));
		}
		return $this->_client;
	}
	
	function parse_signed_request($signed_request, $secret) {
		list($encoded_sig, $payload) = explode('.', $signed_request, 2);
	
		// decode the data
		$sig = $this->base64_url_decode($encoded_sig);
		$data = json_decode($this->base64_url_decode($payload), true);
		$data['sig'] = $sig;
		
		if (strtoupper($data['algorithm']) !== 'HMAC-SHA256') {
			//error_log('Unknown algorithm. Expected HMAC-SHA256');
			return null;
		}
	
		// check sig
		$expected_sig = hash_hmac('sha256', $payload, $secret, $raw = true);
		if ($sig !== $expected_sig) {
			$this->_valid_signature = false;
			//error_log('Bad Signed JSON signature!');
			return null;
		}
	
		$this->_valid_signature = true;
		return $data;
	}
	
	function base64_url_decode($input) {
		return base64_decode(strtr($input, '-_', '+/'));
	}
	
	function get_new_facebook_cookie($app_id, $app_secret) {
		$signed_request = $this->parse_signed_request(Mage::app()->getRequest()->getCookie('fbsr_' . $app_id), $app_secret);
		// $signed_request should now have most of the old elements
		$signed_request['uid'] = $signed_request['user_id']; // for compatibility
		if (!is_null($signed_request)) {
			// the cookie is valid/signed correctly
			// lets change “code” into an “access_token”
			$access_token_response = file_get_contents("https://graph.facebook.com/oauth/access_token?client_id=$app_id&redirect_uri=&client_secret=$app_secret&code=" . $signed_request['code']);
			parse_str($access_token_response);
			$signed_request['access_token'] = $access_token;
			$signed_request['expires'] = time() + $expires;
		}
		return $signed_request;
	}
	
}