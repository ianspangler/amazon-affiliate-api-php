<?php


namespace App\Services;

/*
* Class to retrieve product data from Amazon affiliate program (product advertising) API 
* Author: Ian Spangler
* Date: 11/3/2015
*/

use App\lib\vendor\aws\AWSSignedRequest;


class AmazonAssocHelper {

	//AMAZON AFFILIATE PROGRAM ACCESS -- sign into affiliate-program.amazon.com
	const AMZ_ASSOC_TAG = 'your-affiliate-id';

	//AWS credentials -- sign into aws.amazon.com
	const AMZ_ASSOC_ACCESSKEY = 'YOUR_ACCESS_KEY';  
	const AMZ_ASSOC_SECRETKEY = 'YOUR_SECRET_KEY';  

	//Set the values for some of the search parameters
	private static $operation = "ItemSearch";
	private static $version = "2013-08-01";
	private static $response_group = "Small,Images,OfferSummary";

	protected function __construct() {

	}

	/**
	* Fetches relevant product data in product API from keyphrase and category
	* returns: array of data from the top result node
	*/
	public static function fetch_product_data($keyphrase, $category) {
	
		$result_xml = self::get_search_results($keyphrase, $category);
		
		//return an array containing the item name, link, image, and price
		return self::get_top_result_data($result_xml);
	
	}


	/**
	* Runs search with signed request on product API using keyphrase and search index
	* returns: XML object
	*/
	private static function get_search_results($keyphrase, $search_index) {
		
		//Define the request
		$params = array("SearchIndex"=>$search_index, //the category
						"Title"=>$keyphrase, 
						"Operation"=>self::$operation,
						"ResponseGroup"=>self::$response_group);
		
		$request = AWSSignedRequest::get_signed_request('com', $params, self::AMZ_ASSOC_ACCESSKEY, self::AMZ_ASSOC_SECRETKEY, self::AMZ_ASSOC_TAG, self::$version);

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
	private static function get_top_result_data($xml) {

		if (!empty(self::handle_errors($xml))) {
			return array('error'=>self::handle_errors($xml));
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
	private static function handle_errors($xml) {

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
