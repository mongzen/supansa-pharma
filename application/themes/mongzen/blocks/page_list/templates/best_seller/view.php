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

<div class="bestSeller-page-list-wrapper">
	<div class="container">
		<?php if (isset($pageListTitle) && $pageListTitle) {
            ?>
		<div class="bestSeller-page-list-header">
			<h2><?php echo h($pageListTitle) ?></h2>
		</div>
		<?php
        } ?>

		<?php if (isset($rssUrl) && $rssUrl) {
            ?>
		<a href="<?php echo $rssUrl ?>" target="_blank" class="bestSeller-page-list-rss-feed">
			<i class="fas fa-rss"></i>
		</a>
		<?php
        } ?>

		<div class="bestSeller-page-list-pages row gap-3 d-flex justify-content-between align-items-center">

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
                $buttonClasses = 'bestSeller-page-list-read-more';
                $entryClasses = 'bestSeller-page-list-page-entry';
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
                    $entryClasses = 'bestSeller-page-list-page d-flex gap-2 col-lg-4 col-md-6 col-sm-12';
                }

                $date = $dh->formatDateTime($page->getCollectionDatePublic(), true);

                //Other useful page data...

                //$last_edited_by = $page->getVersionObject()->getVersionAuthorUserName();

                /* DISPLAY PAGE OWNER NAME
                 * $page_owner = UserInfo::getByID($page->getCollectionUserID());
                 * if (is_object($page_owner)) {
                 *     echo $page_owner->getUserDisplayName();
                 * }
                 */

                /* CUSTOM ATTRIBUTE EXAMPLES:
                 * $example_value = $page->getAttribute('example_attribute_handle', 'display');
                 *
                 * When you need the raw attribute value or object:
                 * $example_value = $page->getAttribute('example_attribute_handle');
                 */

                /* End data preparation. */

                /* The HTML from here through "endforeach" is repeated for every item in the list... */ ?>

			<div class="<?php echo $entryClasses ?>">

				<?php if ($includeEntryText) { ?>
				<div class="bestSeller-page-list-page-entry-text">

					<?php if (isset($includeName) && $includeName) {
                                ?>
					<div class="bestSeller-page-list-title">
						<?php if (isset($useButtonForLink) && $useButtonForLink) {
                                        ?>
						<?php echo h($title); ?>
						<?php

                                    } else {
                                        ?>
						<a href="<?php echo h($url) ?>" target="<?php echo h($target) ?>"><?php echo h($title) ?></a>
						<?php

                                    } ?>
					</div>
					<?php
                            } ?>

					<?php if (isset($includeDate) && $includeDate) {
                                ?>
					<div class="bestSeller-page-list-date"><?php echo h($date) ?></div>
					<?php
                            } ?>

					<?php if (isset($includeDescription) && $includeDescription) {
                                ?>
					<div class="bestSeller-page-list-description"><?php echo h($description) ?></div>
					<?php
                            } ?>

					<?php if (isset($useButtonForLink) && $useButtonForLink) {
                                ?>
					<div class="bestSeller-page-list-page-entry-read-more">
						<a href="<?php echo h($url) ?>" target="<?php echo h($target) ?>"
							class="<?php echo h($buttonClasses) ?>"><?php echo h($buttonLinkText) ?></a>
					</div>
					<?php } ?>

				</div>
				<?php } ?>

				<?php if (is_object($thumbnail)) { ?>
				<div class="bestSeller-page-list-page-entry-thumbnail">
					<?php
                    $img = Core::make('html/image', ['f' => $thumbnail]);
                    $tag = $img->getTag();
                    $tag->addClass('img-fluid');
                    echo $tag; ?>
				</div>
				<?php } ?>
			</div>

			<?php
            } ?>
		</div><!-- end .bestSeller-page-list-pages -->

		<?php if (count($pages) == 0) { ?>
		<div class="bestSeller-page-list-no-pages"><?php echo h($noResultsMessage) ?></div>
		<?php } ?>
	</div><!-- end .container -->
</div><!-- end .bestSeller-page-list-wrapper -->


<?php if ($showPagination) { ?>
<?php echo $pagination; ?>
<?php } ?>

<?php

} ?>