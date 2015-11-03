<?php

	namespace App\Services;
	use App\Services\AmazonAssocHelper;

	require_once getcwd() . '/App/app_global.php';

	$amzAssocHelper = new AmazonAssocHelper();
	//$product_data = $amzAssocHelper->fetch_product_data("Envato Audiojungle TPU Case", "Electronics");
	//$product_data = $amzAssocHelper->fetch_product_data("Supergirl", "Books");
	$product_data = $amzAssocHelper->fetch_product_data("American Sniper", "Movies");

	//print_r($product_data);
	//exit;

?>
<!doctype html>
<html>
	<head>
		<title>Tuts+ Demo: Amazon Product API</title>
		<link rel="stylesheet" href="css/reset.css" />
		<link rel="stylesheet" href="css/style.css" />
	</head>
	<body>
		<?php if (!empty($product_data) && array_key_exists("error", $product_data) !== 1): ?>
			<div class="affiliate_wrap">
				
				<a href="<?php echo $product_data['link']; ?>" target="_blank"><img src="<?php echo $product_data['image']; ?>" alt="<?php echo $product_data['title']; ?>" /></a>
				<h2><a href="<?php echo $product_data['link']; ?>" target="_blank"><?php echo $product_data['title']; ?></a></h2>
				<p><em><?php echo $product_data['price']; ?></em> on Amazon</p>
				<a class="button" href="<?php echo $product_data['link']; ?>" target="_blank">Buy Now</a>
			</div>

		<?php endif ?>
	</body>
</html>