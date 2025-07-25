<?php
defined('C5_EXECUTE') or die("Access Denied.");

$this->inc('elements/header.php');
?>

<article>
    <section class="page-not-found">
        <div class="wrapper">
            <div class="content">
                <?php
                $content = new GlobalArea('Page Not Found ' . LANGAREA);
                $content->setBlockLimit(1);
                $content->display();
                ?>
            </div>
        </div>
        <div class="wave-circle">
            <div class="effect">
                <span></span>
                <span></span>
                <span></span>
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </section>
</article>

<?php
$this->inc('elements/footer.php');
