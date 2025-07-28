<?php defined('C5_EXECUTE') or die("Access Denied.");
$this->inc('elements/function.php'); ?>
<!DOCTYPE html>
<html lang="<?php echo Localization::activeLanguage() ?>">

<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta property="og:type" content="website" />
	<meta property="og:title" content="<?php echo METATITLE; ?>" />
	<meta property="og:description" content="<?php echo METADESC; ?>" />
	<meta property="og:site_name" content="<?php echo SITE; ?>" />
	<meta property="og:url" content="<?php echo PAGEPATH; ?>" />
	<meta property="og:image" content="<?php echo PAGETHUMB; ?>" />
	<meta property="og:image:alt" content="<?php echo METATITLE; ?>" />

	<link rel="stylesheet" href="<?php echo $view->getThemePath() ?>/assets/libs/bootstrap/bootstrap-icons.css">
	<link rel="stylesheet" href="<?php echo $view->getThemePath() ?>/assets/libs/bootstrap/bootstrap.min.css">
	<link rel="stylesheet" href="<?php echo $view->getThemePath() ?>/assets/libs/swiper-10.2.0/swiper-bundle.min.css">
	<link rel="stylesheet" href="<?php echo $view->getThemePath() ?>/assets/css/global.css?v=<?php echo time(); ?>">

	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link
		href="https://fonts.googleapis.com/css2?family=Sarabun:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800&display=swap"
		rel="stylesheet">

	<?php
  if (PAGETEMPLATE) {
    $pagetypecss = $_SERVER['DOCUMENT_ROOT'] . $view->getThemePath() . '/assets/css/pages/' . PAGETEMPLATE . '.css';
    if (file_exists($pagetypecss)) {
  ?>
	<link rel="stylesheet" type="text/css"
		href="<?php echo $view->getThemePath() ?>/assets/css/pages/<?php echo PAGETEMPLATE; ?>.css?v=<?php echo time(); ?>">
	<?php
    }
  }

  View::element('header_required', [
    'pageTitle' => isset($pageTitle) ? $pageTitle : '',
    'pageDescription' => isset($pageDescription) ? $pageDescription : '',
    'pageMetaKeywords' => isset($pageMetaKeywords) ? $pageMetaKeywords : ''
  ]);
  ?>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
	<script>
	if (navigator.userAgent.match(/IEMobile\/10\.0/)) {
		var msViewportStyle = document.createElement('style');
		msViewportStyle.appendChild(
			document.createTextNode(
				'@-ms-viewport{width:auto!important}'
			)
		);
		document.querySelector('head').appendChild(msViewportStyle);
	}
	</script>
</head>

<body class="<?php echo BODYCLASS; ?>">
	<div class="<?php echo $c->getPageWrapperClass() ?>">