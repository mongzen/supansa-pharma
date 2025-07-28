<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>



<?php // Repeatable entries ?>

<?php if (is_array($entries) AND count($entries)): ?>

    <?php foreach ($entries as $entry): ?>


        <?php if (!empty($entry['slide_title'])): ?>
            <?php echo h($entry['slide_title']); ?>
        <?php endif; ?>


        <?php if (!empty($entry['slide_subtitle'])): ?>
            <?php echo nl2br(h($entry['slide_subtitle']), false); ?>
        <?php endif; ?>


        <?php if (!empty($entry['slide_button_text'])): ?>
            <?php echo h($entry['slide_button_text']); ?>
        <?php endif; ?>


        <?php if (!empty($entry['slide_button_link_link'])): ?>
            <a href="<?php echo $entry['slide_button_link_link']; ?>"></a>
        <?php endif; ?>


        <?php if (!empty($entry['slide_image_link'])): ?>
            <?php // Original image ?>
            <img src="<?php echo $entry['slide_image_link']; ?>" alt="<?php echo h($entry['slide_image_alt']); ?>" width="<?php echo $entry['slide_image_width']; ?>" height="<?php echo $entry['slide_image_height']; ?>">
        <?php endif; ?>

        <?php if (!empty($entry['slide_image_fullscreenLink'])): ?>
            <?php // Fullscreen image ?>
            <img src="<?php echo $entry['slide_image_fullscreenLink']; ?>" alt="<?php echo h($entry['slide_image_alt']); ?>" width="<?php echo $entry['slide_image_fullscreenWidth']; ?>" height="<?php echo $entry['slide_image_fullscreenHeight']; ?>">
        <?php endif; ?>

        <?php if (!empty($entry['slide_image_thumbnailLink'])): ?>
            <?php // Thumbnail image ?>
            <img src="<?php echo $entry['slide_image_thumbnailLink']; ?>" alt="<?php echo h($entry['slide_image_alt']); ?>" width="<?php echo $entry['slide_image_thumbnailWidth']; ?>" height="<?php echo $entry['slide_image_thumbnailHeight']; ?>">
        <?php endif; ?>


    <?php endforeach; ?>

<?php endif; ?>

