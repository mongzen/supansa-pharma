<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
</div>

<script src="<?php echo THEMEDIR; ?>/assets/libs/swiper-10.2.0/swiper-bundle.min.js"></script>
<script src="<?php echo THEMEDIR; ?>/assets/js/global.js?v=<?php echo time(); ?>"></script>

<?php $pagejs = $_SERVER['DOCUMENT_ROOT'] . THEMEDIR . '/assets/js/' . PAGETEMPLATE . '.js'; ?>
<?php if (file_exists($pagejs)) : ?>
	<script src="<?php echo THEMEDIR; ?>/assets/js/<?php echo PAGETEMPLATE; ?>.js?v=<?php echo time(); ?>"></script>
<?php endif; ?>

<?php Loader::element('footer_required'); ?>
</body>

</html>