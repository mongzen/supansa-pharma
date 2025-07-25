<?php
defined('C5_EXECUTE') or die("Access Denied.");
$this->inc('elements/header.php');
?>

<?php
$content = new Area('Content: ' . LANGAREA);
$content->display($c);
?>

<?php $this->inc('elements/footer.php'); ?>