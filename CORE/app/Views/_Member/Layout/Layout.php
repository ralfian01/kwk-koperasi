<?php $this->extend('../BaseLayout/Layout.php'); ?>

<?php $this->section('main'); ?>

<div class="foldable_container" style="overflow: hidden; height: 100vh; width: 100vw;">

    <!-- Sidebar -->
    <?= isset($layout['sidebar']) ? $layout['sidebar'] : ''; ?>

    <!-- Content -->
    <section class="content">

        <!-- Page head -->
        <?= isset($layout['header']) ? $layout['header'] : ''; ?>

        <?php $this->renderSection('content'); ?>
    </section>
</div>

<script type="text/javascript">
    // Event
    $('.foldable_container > .content').on('scroll', function() {

        let pageTitle = $(this).find('.page_title').not('.ds'),
            scroll = this.scrollTop;

        if ($(pageTitle).length >= 1) {
            if (scroll > 0) {
                $(pageTitle).addClass('stick');
            } else {
                $(pageTitle).removeClass('stick');
            }
        }
    });

    $('.top_nav > button.menu').on('click', function() {
        let sideNav = $(this).parents('.foldable_container').find('.sd_nav');

        $(sideNav).toggleClass('active');
    });

    $('.foldable_container > section.content').on('click', function() {
        let sideNav = $(this).parents('.foldable_container').find('.sd_nav');

        $(sideNav).removeClass('active');
    });
</script>

<?php $this->endSection('main'); ?>