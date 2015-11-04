<?php

	namespace App\Services;

	require_once getcwd() . '/App/app_global.php';

	//$amzAssocHelper = new AmazonAssocHelper();
	//$product_data = $amzAssocHelper->fetch_product_data("Envato Audiojungle TPU Case", "Electronics");

	/**
	*	Call the Product API by passing in the name of the product and the shopping category
	*	("Books", "Movies", and "VideoGames" are examples -- see API documentation for full list)
	*/
	$product_data = AmazonAssocHelper::fetch_product_data("Jurassic World", "Movies");

	//print_r($product_data);
	//exit;

?>
<!doctype html>
<html>
	<head>
		<title>Tuts+ Demo: Amazon Product API</title>
		<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
		<link rel="stylesheet" href="css/reset.css" />
		<link rel="stylesheet" href="css/style.css" />
	</head>
	<body>
		<?php if (!empty($product_data) && array_key_exists("error", $product_data) != 1): ?>
			<div class="affiliate_wrap">
				
				<a href="<?php echo $product_data['link']; ?>" target="_blank">
					<img src="<?php echo $product_data['image']; ?>" alt="<?php echo $product_data['title']; ?>" />
				</a>
				<h2>
					<a href="<?php echo $product_data['link']; ?>" target="_blank"><?php echo $product_data['title']; ?></a>
				</h2>
				<p><em><?php echo $product_data['price']; ?></em> on Amazon</p>
				<a class="button" href="<?php echo $product_data['link']; ?>" target="_blank">Buy Now</a>
			</div>

		<?php endif ?>
	</body>
</html>