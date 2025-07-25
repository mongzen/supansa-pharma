<?php
defined('C5_EXECUTE') or die("Access Denied.");

$this->inc('elements/header.php');

use Concrete\Core\Page\PageList;
use Concrete\Core\Attribute\Key\CollectionKey;
use Concrete\Core\Tree\Node\Node as TreeNode;
use \Concrete\Core\Tree\Type\Topic as TopicTree;
use \Concrete\Core\Tree\Node\Type\Category as TreeNodeCategory;


/** @var \Concrete\Core\Utility\Service\Text $th */
$th = Core::make('helper/text');
/** @var \Concrete\Core\Localization\Service\Date $dh */
$dh = Core::make('helper/date');

?>

<article class="main-article">
	<?php
	$a = new Area('Content');
	$a->display($c);
	?>
</article>

<?php
$this->inc('elements/footer.php');
