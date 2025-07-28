<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<footer class="site-footer">
	<section class="mb-4">
		<div class="container">
			<div class="row">
				<div class="col-md-4">
					<?php
					$area = new GlobalArea('Footer Column 1');
					$area->display();
					?>
				</div>
				<div class="col-md-8">
					<div class="d-flex gap-3">
						<div class="col">
							<?php
							$area = new GlobalArea('Footer Column 2');
							$area->display();
							?>
						</div>
						<div class="col">
							<?php
							$area = new GlobalArea('Footer Column 3');
							$area->display();
							?>
						</div>
						<div class="col site-footer--contact">
							<?php
							$area = new GlobalArea('Footer Column 4');
							$area->display();
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	<section class="footer-branding">
		<div class="container">
			<div class="row">
				<div class="col">
					<?=t('Copyright %s ', date('Y'))?>
					<?php echo t(SITE.' All Rights Reserved'); ?>
				</div>
			</div>
		</div>
	</section>
</footer>
<?php $this->inc('elements/footer_bottom.php');?>