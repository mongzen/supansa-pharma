<?php defined('C5_EXECUTE') or die("Access Denied.");
$this->inc('elements/header_top.php');
?>
<header class="site-header">
	<!-- Top Bar -->
	<div class="top-bar">
		<div class="container d-flex justify-content-between gap-3">
			<span class="d-flex align-items-center">
				<i class="bi bi-geo-alt"></i>
				<?php
					$location = new GlobalArea('Location : ' . LANGAREA);
					$location->display();
				?>
			</span>
			<?php
				$language = new GlobalArea('Language : ' . LANGAREA);
				$language->display();
			?>
		</div>
	</div>

	<!-- Main Navigation -->
	<div class="main-nav">
		<div class="container d-flex justify-content-between align-items-center">
			<div class="d-flex align-items-center gap-3">
				<!-- Brand -->
				<a href=" /" class="brand-name">
					<?php $this->inc('assets/svg/logo.php'); ?>
				</a>

				<!-- Menu -->
				<nav class="main-menu">
					<?php
					$mainMenu = new GlobalArea('Main Menu: ' . LANGAREA);
					$mainMenu->display();
				?>
				</nav>
			</div>
			<div class="sub-note">
				<?php
					$subnote = new GlobalArea('Sub Note : ' . LANGAREA);
					$subnote->display();
				?>
			</div>
		</div>
	</div>

	<!-- Search Bar -->
	<div class="search-bar">
		<div class="container">
			<?php
				$search = new GlobalArea('Search : ' . LANGAREA);
				$search->display();
			?>
		</div>
	</div>
</header>