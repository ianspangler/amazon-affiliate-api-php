<?php


namespace App\Services;

/*
* Class to retrieve product data from Amazon affiliate program (product advertising) API 
*/

use App\lib\vendor\aws\AWSSignedRequest;


class AmazonAssocHelper {
	
	//AMAZON AFFILIATE PROGRAM ACCESS -- sign into affiliate-program.amazon.com
	const AMZ_ASSOC_TAG = 'envtut-20';
	const AMZ_ASSOC_ACCESSKEY = 'AKIAIQFKK7RJMRNMFNWA';  
	const AMZ_ASSOC_SECRETKEY = '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'; 


	//Set the values for some of the parameters
	private $operation = "ItemSearch";
	private $version = "2013-08-01";
	private $response_group = "Small,Images,OfferSummary";

	function __construct() {

	}

	/**
	* Fetches relevant product link from product API from keyphrase and category
	* returns: array of data from the top result node
	*/
	public function fetch_product_data($keyphrase, $search_index) {
		
		$result_xml = $this->get_search_results($keyphrase, $search_index);
		
		//return an array containing the item name, link, image, and price
		return $this->get_top_result_data($result_xml);
	
	}


	/**
	* Runs search with signed request on product API using keyphrase and category name
	* returns: parsed XML object
	*/
	private function get_search_results($keyphrase, $search_index) {
		
		//Define the request
		$params = array("SearchIndex"=>$search_index, //the category
						"Title"=>$keyphrase, 
						"Operation"=>$this->operation,
						"ResponseGroup"=>$this->response_group);
		

		$request = AWSSignedRequest::get_signed_request('com', $params, self::AMZ_ASSOC_ACCESSKEY, self::AMZ_ASSOC_SECRETKEY, self::AMZ_ASSOC_TAG, $this->version);

		$response = file_get_contents($request);
		/*header('Content-type: application/xml');
		echo $response;
		exit;*/
		return simplexml_load_string($response);
		
	}


	/**
	* Parses top result node, and its attributes, from XML object
	* returns: array of product data
	*/
	private function get_top_result_data($xml) {

		if (!empty($this->handle_errors($xml))) {
			return array('error'=>$this->handle_errors($xml));
		}

		//get the first result node
		$first_item = $xml->Items[0]->Item; 
		$item_title = $first_item->ItemAttributes->Title;
		$item_link = $first_item->DetailPageURL;
		$item_image = $first_item->LargeImage->URL;
		$item_price = $first_item->OfferSummary->LowestNewPrice->FormattedPrice;

		return array( 'title'=>(string)$item_title,
					  'link'=>(string)$item_link, 
					  'image'=>(string)$item_image, 
					  'price'=>(string)$item_price );

		

	}


	/**
	* Checks for errors in the request/ result
	* returns: array with message(s) describing the "error"
	*/
	private function handle_errors($xml) {

		$errors_arr = array();

		//process errors in request
		foreach ($xml->OperationRequest->Errors->Error as $error) {

		   	error_log("Error code: " . $error->Code . "\r\n");
		  	error_log($error->Message . "\r\n");
		  	error_log("\r\n");

		  	array_push($errors_arr, (string)$error->Message);
		}

		//check for invalid category, no matches, or other search error
		foreach ($xml->Items->Request->Errors->Error as $error) {
			
			error_log("Error code: " . $error->Code . "\r\n");
		  	error_log($error->Message . "\r\n");
		  	error_log("\r\n");

			array_push($errors_arr, (string)$error->Message);
		}
		

		return $errors_arr;

	}
	

}
