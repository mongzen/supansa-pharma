<?php defined('C5_EXECUTE') or die("Access Denied.");
$pg = Core::make('Application\Controller\PageType\PageGlobal');
$pg->autoRedirect();
$ux = new User();
$page = Page::getCurrentPage();
$pt = $c->getPageTemplateObject();
$template = null;
if (is_object($pt)) {
    $template =  $pt->getPageTemplateHandle();
}
$site = Config::get('concrete.site');
$thumb = $page->getAttribute('thumbnail');
$image_banner = $c->getAttribute('image_banner');
is_object($thumb) ? $thumb = $thumb->getURL() : $thumb = '/application/themes/mongzen/assets/images/og/og-image.jpg';
is_object($image_banner) ? $image_banner = $image_banner->getURL() : $image_banner = '';

$meta_title = $page->getAttribute('meta_title');
$meta_title = $meta_title != '' ? $meta_title : $page->getCollectionName() . ' - ' . $site;

$meta_desc = $page->getAttribute('meta_description');
$meta_desc = $meta_desc != '' ? $meta_desc : $page->getCollectionDescription();

$lang = $pg->getLanguages();
$langarea = $lang['area'];
$langpath = $lang['path'];

$_body = '';
$_class = [];

if ($c->isEditMode()) {
    $_class[] = 'mode-edit';
}
if (!$c->isEditMode()) {
    $_class[] = 'mode-view';
}
if ($ux->checkLogin()) {
    $_class[] = 'mode-login';
}

if (count($_class) > 0) {
    $_body = implode(' ', $_class);
}

define('SITE', $site);
define('THEMEDIR', $this->getThemePath());
define('PAGETYPE', $page->getCollectionTypeHandle());
define('PAGETEMPLATE', $template);
define('CHECKLOGIN', $ux->checkLogin());
define('PAGENAME', $page->getCollectionName());
define('PAGEDESC', $page->getCollectionDescription());
define('PAGETHUMB', $thumb);
define('PAGEIMAGEBANNER', $image_banner);
define('PAGEID', $page->getCollectionID());
define('PAGEPATH', $page->getCollectionLink());
define('EDITMODE', $c->isEditMode());
define('METATITLE', $meta_title);
define('METADESC', $meta_desc);
define('LANGAREA', $langarea);
define('LANGPATH', $langpath);
define('BODYCLASS', $_body);