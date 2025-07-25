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

	<link rel="stylesheet" href="<?php echo $view->getThemePath() ?>/assets/libs/swiper-10.2.0/swiper-bundle.min.css">
	<link rel="stylesheet" href="<?php echo $view->getThemePath() ?>/assets/css/global.css?v=<?php echo time(); ?>">

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