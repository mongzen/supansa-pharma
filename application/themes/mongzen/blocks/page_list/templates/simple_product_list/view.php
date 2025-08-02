<?php
defined('C5_EXECUTE') or die('Access Denied.');

$c = Page::getCurrentPage();

/** @var \Concrete\Core\Utility\Service\Text $th */
$th = Core::make('helper/text');
/** @var \Concrete\Core\Localization\Service\Date $dh */
$dh = Core::make('helper/date');

if (is_object($c) && $c->isEditMode() && $controller->isBlockEmpty()) {
    ?>
<div class="ccm-edit-mode-disabled-item"><?php echo t('Empty Page List Block.') ?></div>
<?php
} else {
    ?>

<div class="sample-page-list-wrapper">
	<div class="container">

		<?php if (isset($pageListTitle) && $pageListTitle) {
            ?>
		<div class="sample-page-list-header">
			<<?php echo $titleFormat; ?>><?php echo h($pageListTitle) ?></<?php echo $titleFormat; ?>>
		</div>
		<?php
        } ?>

		<?php if (isset($rssUrl) && $rssUrl) {
            ?>
		<a href="<?php echo $rssUrl ?>" target="_blank" class="sample-page-list-rss-feed">
			<i class="fas fa-rss"></i>
		</a>
		<?php
        } ?>

		<div class="sample-page-list-pages row row-cols-lg-3 g-4">

			<?php

            $includeEntryText = false;
            if (
                (isset($includeName) && $includeName)
                ||
                (isset($includeDescription) && $includeDescription)
                ||
                (isset($useButtonForLink) && $useButtonForLink)
            ) {
                $includeEntryText = true;
            }

            foreach ($pages as $page) {
                // Prepare data for each page being listed...
                $buttonClasses = 'sample-page-list-read-more';
                $entryClasses = 'sample-page-list-page-entry';
                $title = $page->getCollectionName();
                $target = '_self';
                if ($page->getCollectionPointerExternalLink() != '') {
                    $url = $page->getCollectionPointerExternalLink();
                    if ($page->openCollectionPointerExternalLinkInNewWindow()) {
                        $target = '_blank';
                    }
                } else {
                    $url = $page->getCollectionLink();
                    $target = $page->getAttribute('nav_target');
                }
                $description = $page->getCollectionDescription();
                $description = $controller->truncateSummaries ? $th->wordSafeShortText($description, $controller->truncateChars) : $description;
                $thumbnail = false;
                if ($displayThumbnail) {
                    $thumbnail = $page->getAttribute('thumbnail');
                }
                if (is_object($thumbnail) && $includeEntryText) {
                    $entryClasses = '';
                }
                $date = $dh->formatDateTime($page->getCollectionDatePublic(), true);
                // ...existing code...
            ?>
			<div class="sample-page-list-page-entry col-lg-4 d-flex justify-content-between gap-4">
				<?php if (is_object($thumbnail)) { ?>
				<div class="sample-page-list-page-entry-thumbnail"><?php
					$img = Core::make('html/image', ['f' => $thumbnail]);
					$tag = $img->getTag();
					$tag->addClass('img-fluid');
					echo $tag; ?>
				</div>
				<?php } ?>

				<?php if ($includeEntryText) { ?>
				<div class="sample-page-list-page-entry-text">

					<?php if (isset($includeName) && $includeName) { ?>
					<div class="sample-page-list-title">
						<?php if (isset($useButtonForLink) && $useButtonForLink) { ?>
						<?php echo h($title); ?>
						<?php } else { ?>
						<a href="<?php echo h($url) ?>" target="<?php echo h($target) ?>"><?php echo h($title) ?></a>
						<?php } ?>
					</div>
					<?php } ?>

					<?php if (isset($includeDescription) && $includeDescription) { ?>
					<div class="sample-page-list-description"><?php echo h($description) ?></div>
					<?php } ?>

					<?php if (isset($useButtonForLink) && $useButtonForLink) { ?>
					<div class="sample-page-list-page-entry-read-more">
						<a href="<?php echo h($url) ?>" target="<?php echo h($target) ?>"
							class="<?php echo h($buttonClasses) ?>"><?php echo h($buttonLinkText) ?></a>
					</div>
					<?php } ?>
				</div>
				<?php } // end if ($includeEntryText) ?>
			</div>
			<?php } // end foreach ?>
		</div><!-- end .sample-page-list-pages -->

		<?php if (count($pages) == 0) { ?>
		<div class="sample-page-list-no-pages"><?php echo h($noResultsMessage) ?></div>
		<?php } ?>
	</div>
</div><!-- end .sample-page-list-wrapper -->


<?php if ($showPagination) { ?>
<?php echo $pagination; ?>
<?php } ?>

<?php

} ?>