<?php
$config['doc_root']   = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https" : "http");
$ishttp = $config['doc_root'];
$config['doc_root']   .= "://".$_SERVER['HTTP_HOST'];
$config['doc_root']   .= str_replace('index.php', '', isset($_SERVER['DOCUMENT_URI'])? $_SERVER['DOCUMENT_URI'] : $_SERVER['PHP_SELF']);

if($_SERVER['HTTP_HOST']=='localhost')
{
	$config['data_dir']    = $_SERVER['DOCUMENT_ROOT'] . '/data-bkd/';
	$config['attach_dir']  = $_SERVER['DOCUMENT_ROOT'] . '/data-file-bkd/';
	$config['img_baseurl'] = $config['doc_root'];

}else{	// LIVE
	$config['data_dir']    = '/var/www/html/data-bkd/';
	$config['attach_dir']  = '/var/www/html/data-file-bkd/';
	$config['img_baseurl'] = $ishttp ."://".$_SERVER['HTTP_HOST'] .'/';
}

// ----- Upload Images dir -----
$config['images_dir']           = $config['data_dir'] . 'images/';
$config['product_images_dir']   = $config['images_dir'] . 'product/';
$config['category_images_dir']  = $config['images_dir'] . 'category/';
$config['pages_images_dir']     = $config['images_dir'] . 'pages/';
$config['logo_dir']             = $config['images_dir'] . 'logo/';
$config['pendana_images_dir']   = $config['images_dir'] . 'pendana/';


// echo $config['pages_images_dir'];
// ----- DATA URL -----
$config['images_posts_uri']     = $config['img_baseurl'] . "images-data/";
$config['images_product_uri']   = $config['images_posts_uri'] . "product/";
$config['images_category_uri']  = $config['images_posts_uri'] . "category/";
$config['images_pages_uri']     = $config['images_posts_uri'] . "pages/";
$config['images_logo_uri']      = $config['images_posts_uri'] . "logo/";
$config['images_member_uri']      = "https://www.bkdana.com/images-data/member/";

$config['template_uri']        = $config['doc_root'] .'static/';
$config['images_uri']          = $config['doc_root'] .'static/images/';

$config['mail_username']      = 'bkdanafinansial@gmail.com';
$config['mail_password']      = 'master177';

$config['pendana_intern_userid']   = '5';
$config['pendana_intern_memberid'] = '5';

$config['bkd_telp']           = '+62 21 83784354';
$config['bkd_email']          = 'cs@bkdana.id';
?>