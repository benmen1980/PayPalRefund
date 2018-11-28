<?php
/**
 * Send HTTP POST Request
 *
 * @param     string     The API method name
 * @param     string     The POST Message fields in &name=value pair format
 * @return     array     Parsed HTTP Response body
 */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
function PPHttpPost($methodName_, $nvpStr_, $env) {

 // Set up your API credentials, PayPal end point, and API version.
 
 // Eilat
 $API_UserName = urlencode('');
 $API_Password = urlencode('');
 $API_Signature = urlencode('');

 
// if("sandbox" === $env)
//$API_Endpoint = "https://api-3t.$env.paypal.com/nvp";
 //else
 	$API_Endpoint = "https://api-3t.paypal.com/nvp";
	
 $version = urlencode('119');

 // Set the curl parameters.
 $ch = curl_init();
 curl_setopt($ch, CURLOPT_URL, $API_Endpoint);
 curl_setopt($ch, CURLOPT_VERBOSE, 1);
// Turn off the server and peer verification (TrustManager Concept).
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); 
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type:application/json'));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPGET, 1);
 
 
 // Set the API operation, version, and API signature in the request.
 
 $nvpreq = "METHOD=$methodName_&VERSION=$version&PWD=$API_Password&USER=$API_UserName&SIGNATURE=$API_Signature$nvpStr_";
 
 //echo "-----".$nvpreq;
 // Set the request as a POST FIELD for curl.
 curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);

 // Get response from the server.
 $httpResponse = curl_exec($ch);
 
 if(!$httpResponse) {
 	exit("$methodName_ failed: ".curl_error($ch).'('.curl_errno($ch).')');
 }
 
 // Extract the response details.
 $httpResponseAr = explode("&", $httpResponse);

 $httpParsedResponseAr = array();
 foreach ($httpResponseAr as $i => $value) {
	 $tmpAr = explode("=", $value);
	 if(sizeof($tmpAr) > 1) {
	 	$httpParsedResponseAr[$tmpAr[0]] = $tmpAr[1];
	 }
 }

 if((0 == sizeof($httpParsedResponseAr)) || !array_key_exists('ACK', $httpParsedResponseAr)) {
 	exit("Invalid HTTP Response for POST request($nvpreq) to $API_Endpoint.");
 }
curl_close($ch);	
 return $httpParsedResponseAr;
}

// Set request-specific fields.
$transactionID = $_GET['txn_id'];
$refundType = urlencode('Partial');  // or 'Partial'
$amount = $_GET['amnt'];                          // required if Partial.
$memo ="Test amnt";                            // required if Partial.
$currencyID = urlencode('ILS');   // or other currency ('GBP', 'EUR', 'JPY', 'CAD', 'AUD')

// Add request-specific fields to the request string.
$nvpStr = "&TRANSACTIONID=$transactionID&REFUNDTYPE=$refundType&CURRENCYCODE=$currencyID";

if(isset($memo)) {
	$nvpStr .= "&NOTE=$memo";
}

if(strcasecmp($refundType, 'Partial') == 0) {
	if(!isset($amount)) {
		exit('Partial Refund Amount is not specified.');
	} else {
		$nvpStr = $nvpStr."&AMT=$amount";
	}
	
	if(!isset($memo)) {
		exit('Partial Refund Memo is not specified.');
	}
}

// Execute the API operation; see the PPHttpPost function above.

$env = '';
//echo ">>>>>>>>>>>".$nvpStr;

$httpParsedResponseAr = PPHttpPost('RefundTransaction',$nvpStr,$env);

if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) {
	exit('<h1>Priority PayPal API message: <br> Refund Completed Successfully: '.print_r( $httpParsedResponseAr, true).'</h1>');
} else  {
	exit('<h1>Priority PayPal API message: <br> RefundTransaction failed: ' . print_r(urldecode($httpParsedResponseAr["L_LONGMESSAGE0"]), true).'</h1><br>'.print_r( $httpParsedResponseAr, true));
}
?>
