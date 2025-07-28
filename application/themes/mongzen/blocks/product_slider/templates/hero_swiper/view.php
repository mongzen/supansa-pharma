<?php
defined('C5_EXECUTE') or die('Access Denied.');
$slides = $controller->slides;
?>

<!-- Swiper -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

<?php // Repeatable entries ?>

<?php if (is_array($entries) AND count($entries)): ?>
<!-- Slider main container -->
<div class="swiper product-slider">
	<!-- Additional required wrapper -->
	<div class="swiper-wrapper">
		<?php foreach ($entries as $entry): ?>
		<!-- Slides -->
		<div class="swiper-slide">
			<div class="w-100 d-flex align-items-center justify-content-center">
				<!-- Slide content -->
				<div class="slide-content">
					<?php if (!empty($entry['slide_subtitle'])): ?>
					<div class="slide-subtitle">
						<?php echo nl2br(h($entry['slide_subtitle']), false); ?>
					</div>
					<?php endif; ?>

					<?php if (!empty($entry['slide_title'])): ?>
					<div class="slide-title"><?php echo h($entry['slide_title']); ?></div>
					<?php endif; ?>

					<?php if (!empty($entry['slide_button_link_link'])): ?>
					<a class="slide-button"
						href="<?php echo $entry['slide_button_link_link']; ?>"><?php if (!empty($entry['slide_button_text'])): ?>
						<?php echo h($entry['slide_button_text']); ?>
						<?php endif; ?></a>
					<?php endif; ?>
				</div>

				<div class="slide-image">
					<?php /*if (!empty($entry['slide_image_link'])): ?>
					<?php // Original image ?>
					<img src=" <?php echo $entry['slide_image_link']; ?>"
						alt="<?php echo h($entry['slide_image_alt']); ?>"
						width="<?php echo $entry['slide_image_width']; ?>"
						height="<?php echo $entry['slide_image_height']; ?>">
					<?php endif;*/ ?>

					<?php if (!empty($entry['slide_image_fullscreenLink'])): ?>
					<?php // Fullscreen image ?>
					<img src="<?php echo $entry['slide_image_fullscreenLink']; ?>"
						alt="<?php echo h($entry['slide_image_alt']); ?>"
						width="<?php echo $entry['slide_image_fullscreenWidth']; ?>"
						height="<?php echo $entry['slide_image_fullscreenHeight']; ?>">
					<?php endif; ?>

					<?php /*if (!empty($entry['slide_image_thumbnailLink'])): ?>
					<?php // Thumbnail image ?>
					<img src="<?php echo $entry['slide_image_thumbnailLink']; ?>"
						alt="<?php echo h($entry['slide_image_alt']); ?>"
						width="<?php echo $entry['slide_image_thumbnailWidth']; ?>"
						height="<?php echo $entry['slide_image_thumbnailHeight']; ?>">
					<?php endif;*/ ?>
				</div>
			</div>
		</div>

		<?php endforeach; ?>
	</div>
	<!-- If we need pagination -->
	<div class="swiper-pagination"></div>
</div>
<?php endif; ?>

<?php if(!EDITMODE): ?>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
const swiper = new Swiper('.product-slider', {
	loop: true,
	autoplay: {
		delay: 5000,
	},
	pagination: {
		el: '.swiper-pagination',
		clickable: true,
	},
});
</script>
<?php endif; ?>