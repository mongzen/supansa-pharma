<?php
defined('C5_EXECUTE') or die("Access Denied.");

$this->inc('elements/header.php');
?>

<article>
  <?= $innerContent ?>
</article>

<?php $this->inc('elements/footer.php'); ?>