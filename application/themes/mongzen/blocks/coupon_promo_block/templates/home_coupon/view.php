<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<?php // Repeatable entries ?>

<?php if (is_array($entries) AND count($entries)): ?>

<section id=="home-coupon">
	<div class="container">
		<h2><?php echo t('คูปองลดราคา'); ?></h2>
		<div class="d-flex gap-4">
			<?php foreach ($entries as $entry): ?>

			<div class="promo-block"
				style="<?php if($entry['image_fullscreenLink']): echo 'background-image: url('.$entry['image_fullscreenLink'].');'; endif; ?>">
				<div class="promo-content <?= $isImageRight ? 'image-right' : 'image-left' ?>">
					<div class=" promo-text">

						<?php if (!empty($entry['titleBadge'])): ?>
						<div class="d-flex">
							<span class="badge">
								<?php echo h($entry['titleBadge']); ?>
							</span>
						</div>
						<?php endif; ?>

						<?php if (!empty($entry['mainText'])): ?>
						<?php echo str_replace('/>', '>', $entry['mainText']); ?>
						<?php endif; ?>

						<?php if (!empty($entry['buttonURL_link'])): ?>
						<a href=" <?php echo $entry['buttonURL_link']; ?>">
							<?php if (!empty($entry['buttonText'])): ?>
							<?php echo h($entry['buttonText']); ?>
							<?php endif; ?>
						</a>
						<?php endif; ?>
					</div>
				</div>
			</div>

			<?php endforeach; ?>
		</div>
	</div>
</section>
<?php endif; ?>