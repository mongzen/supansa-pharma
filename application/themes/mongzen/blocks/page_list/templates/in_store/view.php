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

<div class="inStore-page-list-wrapper">
	<div class="container">

		<?php if (isset($pageListTitle) && $pageListTitle) {
            ?>
		<div class="inStore-page-list-header">
			<<?php echo $titleFormat; ?>><?php echo h($pageListTitle) ?></<?php echo $titleFormat; ?>>
		</div>
		<?php
        } ?>

		<?php if (isset($rssUrl) && $rssUrl) {
            ?>
		<a href="<?php echo $rssUrl ?>" target="_blank" class="inStore-page-list-rss-feed">
			<i class="fas fa-rss"></i>
		</a>
		<?php
        } ?>

		<div class="inStore-page-list-pages">

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
                $buttonClasses = 'inStore-page-list-read-more';
                $entryClasses = 'inStore-page-list-page-entry';
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

			<div class="inStore-page-list-page-entry">
				<?php if (is_object($thumbnail)) { ?>
				<div class="inStore-page-list-page-entry-thumbnail">
					<?php
					$img = Core::make('html/image', ['f' => $thumbnail]);
					$tag = $img->getTag();
					$tag->addClass('img-fluid');
					echo $tag; ?>
				</div>
				<?php } ?>

				<?php if ($includeEntryText) { ?>
				<div class="inStore-page-list-page-entry-text">

					<?php if (isset($includeName) && $includeName) { ?>
					<div class="inStore-page-list-title">
						<?php if (isset($useButtonForLink) && $useButtonForLink) { ?>
						<?php echo h($title); ?>
						<?php } else { ?>
						<a href="<?php echo h($url) ?>" target="<?php echo h($target) ?>"><?php echo h($title) ?></a>
						<?php } ?>
					</div>
					<?php } ?>

					<?php /*if (isset($includeDate) && $includeDate) { ?>
					<div class="inStore-page-list-date"><?php echo h($date) ?></div>
					<?php }*/ ?>

					<?php if (isset($includeDescription) && $includeDescription) { ?>
					<div class="inStore-page-list-description"><?php echo h($description) ?></div>
					<?php } ?>

					<?php if (isset($useButtonForLink) && $useButtonForLink) { ?>
					<div class="inStore-page-list-page-entry-read-more">
						<a href="<?php echo h($url) ?>" target="<?php echo h($target) ?>"
							class="<?php echo h($buttonClasses) ?>"><?php echo h($buttonLinkText) ?></a>
					</div>
					<?php } ?>
				</div>
				<?php } ?>
			</div>

			<?php } ?>
		</div><!-- end .inStore-page-list-pages -->

		<?php if (count($pages) == 0) { ?>
		<div class="inStore-page-list-no-pages"><?php echo h($noResultsMessage) ?></div>
		<?php } ?>
	</div>
</div><!-- end .inStore-page-list-wrapper -->


<?php if ($showPagination) { ?>
<?php echo $pagination; ?>
<?php } ?>

<?php

} ?>