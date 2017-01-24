<?php
/**
 *   @copyright Copyright (c) 2015 Quality Unit s.r.o.
 *   @author Martin Pullmann
 *   @package WpLiveAgentPlugin
 *   @version 1.0.0
 *
 *   Licensed under GPL2
 */

class Qualityunit_Liveagent_Helper_Connect {

    private function connect($initUrl) {
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, $initUrl);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
        curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false);
        $curl_response=curl_exec($ch);

        if ($curl_response === false) {
            $info = curl_error($ch);
            curl_close($ch);
            throw new Qualityunit_Liveagent_Exception_Base('Error connecting to the account ('.$initUrl.'). Additional info: ' . $info);
        }
        $info = curl_getinfo($ch);
        curl_close($ch);
        $decodedResponse = json_decode($curl_response);
        if (!isset($decodedResponse->response)) {
            if (($info['http_code'] == 301) || ($info['http_code'] == 302)) {
                throw new Qualityunit_Liveagent_Exception_Base('Connection failed. The request has been redirected to '.$info['redirect_url']);
            }
            throw new Qualityunit_Liveagent_Exception_Base('Connection failed with error '.$info['http_code'].'. Please try again...');
        }
        if ($info['http_code'] != 200) {
            throw new Qualityunit_Liveagent_Exception_Base('Connection failed with error "' . $decodedResponse->response->errormessage . '"');
        }
        return $decodedResponse->response;
    }

    private function connectPost($initUrl, $post) {
        $postParams = '';
        foreach ($post as $key => $value) {
            $postParams .= $key . '='. urlencode($value) .'&';
        }
        $postParams = substr($postParams, 0, -1);
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, $initUrl);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postParams);
        curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false);
        $curl_response=curl_exec($ch);
        if ($curl_response === false) {
            $info = curl_error($ch);
            curl_close($ch);
            throw new Qualityunit_Liveagent_Exception_Base('Error connecting to the account ('.$initUrl.'). Additional info: ' . $info);
        }
        $info = curl_getinfo($ch);
        curl_close($ch);
        $decodedResponse = json_decode($curl_response);
        if (!isset($decodedResponse->response)) {
            throw new Qualityunit_Liveagent_Exception_Base('Connection failed. Please try again...');
        }
        if ($info['http_code'] != 200) {
            throw new Qualityunit_Liveagent_Exception_Base('Connection failed with error "' . $decodedResponse->response->errormessage . '"');
        }
        return $decodedResponse->response;
    }

    public function ping($url, $apikey) {
        if (empty($url) || empty($apikey)) {
            throw new Qualityunit_Liveagent_Exception_ConnectProblem('Please, fill in all credentials!');
        }
        if (substr($url, -1) != '/') $url .= '/';
        try {
            $this->connect($url . 'api/application/status?apikey=' . $apikey);
        }
        catch (Qualityunit_Liveagent_Exception_Base $e) {
            throw new Qualityunit_Liveagent_Exception_ConnectProblem($e->getMessage());
        }
        return;
    }

    public function createWidget($url, $params) {
        if (empty($url) || empty($params)) {
            throw new Qualityunit_Liveagent_Exception_Base('Please, fill in all parameters!');
        }
        if (substr($url, -1) != '/') $url .= '/';
        try {
            $response = $this->connectPost($url . 'api/widgets', $params);
        }
        catch (Qualityunit_Liveagent_Exception_Base $e) {
            throw new Qualityunit_Liveagent_Exception_ConnectFailed($e->getMessage());
        }
        return array(0 => $response);
    }

	public function connectWithLA($url, $email, $apikey) { // authToken
	    if (empty($url) || empty($apikey)) {
	        throw new Qualityunit_Liveagent_Exception_Base('Please, fill in all credentials!');
	    }
	    if (substr($url, -1) != '/') $url .= '/';
        try {
            $response = $this->connect($url . 'api/agents/' . urlencode($email) . '?apikey=' . $apikey);
        }
        catch (Qualityunit_Liveagent_Exception_Base $e) {
           throw new Qualityunit_Liveagent_Exception_ConnectFailed($e->getMessage());
        }
	    return $response;
	}

    public function getWidgets($url, $apikey) {
	    if (empty($url) || empty($apikey)) {
	        throw new Qualityunit_Liveagent_Exception_Base('Please, fill in all credentials!');
	    }
	    if (substr($url, -1) != '/') $url .= '/';
      try {
          $response = $this->connect($url . 'api/widgets?apikey=' . $apikey . '&rtype=C'); // chat widgets only
      }
      catch (Qualityunit_Liveagent_Exception_Base $e) {
         throw new Qualityunit_Liveagent_Exception_ConnectFailed($e->getMessage());
      }
	    return $response->widgets;
	}

	public function getOverview($url, $apikey) {
	    if (empty($url) || empty($apikey)) {
	        throw new Qualityunit_Liveagent_Exception_Base('Please, fill in all credentials!');
	    }
	    if (substr($url, -1) != '/') $url .= '/';
	    try {
	        $response = $this->connect($url . 'api/chats/overview?apikey=' . $apikey);
	    }
	    catch (Qualityunit_Liveagent_Exception_Base $e) {
	        throw new Qualityunit_Liveagent_Exception_ConnectFailed($e->getMessage());
	    }
	    return $response->chatsOverview[0];
	}
}