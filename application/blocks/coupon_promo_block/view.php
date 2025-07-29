<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>



<?php // Repeatable entries ?>

<?php if (is_array($entries) AND count($entries)): ?>

    <?php foreach ($entries as $entry): ?>


        <?php if (!empty($entry['titleBadge'])): ?>
            <?php echo h($entry['titleBadge']); ?>
        <?php endif; ?>


        <?php if (!empty($entry['mainText'])): ?>
            <?php echo str_replace('/>', '>', $entry['mainText']); ?>
        <?php endif; ?>


        <?php if (!empty($entry['buttonText'])): ?>
            <?php echo h($entry['buttonText']); ?>
        <?php endif; ?>


        <?php if (!empty($entry['buttonURL_link'])): ?>
            <a href="<?php echo $entry['buttonURL_link']; ?>"></a>
        <?php endif; ?>


        <?php if (!empty($entry['image_link'])): ?>
            <?php // Original image ?>
            <img src="<?php echo $entry['image_link']; ?>" alt="<?php echo h($entry['image_alt']); ?>" width="<?php echo $entry['image_width']; ?>" height="<?php echo $entry['image_height']; ?>">
        <?php endif; ?>

        <?php if (!empty($entry['image_fullscreenLink'])): ?>
            <?php // Fullscreen image ?>
            <img src="<?php echo $entry['image_fullscreenLink']; ?>" alt="<?php echo h($entry['image_alt']); ?>" width="<?php echo $entry['image_fullscreenWidth']; ?>" height="<?php echo $entry['image_fullscreenHeight']; ?>">
        <?php endif; ?>

        <?php if (!empty($entry['image_thumbnailLink'])): ?>
            <?php // Thumbnail image ?>
            <img src="<?php echo $entry['image_thumbnailLink']; ?>" alt="<?php echo h($entry['image_alt']); ?>" width="<?php echo $entry['image_thumbnailWidth']; ?>" height="<?php echo $entry['image_thumbnailHeight']; ?>">
        <?php endif; ?>


        <?php if (!empty($entry['backgroundColor'])): ?>
            <?php echo h($entry['backgroundColor']); ?>
        <?php endif; ?>


        <?php if (!empty($entry['isImageRight'])): ?>
            Key: <?php echo $entry['isImageRight']; ?><br>
            Value: <?php echo h($entry_isImageRight_options[$entry['isImageRight']] ?? ''); ?>
        <?php endif; ?>


    <?php endforeach; ?>

<?php endif; ?>

