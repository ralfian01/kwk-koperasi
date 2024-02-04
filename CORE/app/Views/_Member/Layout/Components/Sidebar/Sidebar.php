<?php

/**
 * @return array
 */
$sidebarMenuTarget = function (array $targetList) {
    $paths = explode('/', str_replace(member_url(), '', clean_current_url()));

    foreach ($targetList as &$target) {
        if (strpos($target, '$') !== false) {
            $len = substr_count($target, '$');

            $paths = array_splice($paths, 0, (count($paths) - $len));
            $paths = implode('/', $paths);

            $offsetTargetUrl = member_url($paths);
            $targetUrl = str_replace(['/$'], '', $target);

            if ($offsetTargetUrl == $targetUrl) {
                $target = clean_current_url();
            }
        }
    }

    return $targetList;
};

/**
 * @return mixed
 */
$sidebarMenu = function (array $option, bool $hasParent = false) use ($auth, &$sidebarMenuTarget, &$sidebarMenu) {

    $current_url = clean_current_url();

    if (isset($option['privilege'])) {
        if (!inArrayFound($auth['privilege'], $option['privilege']))
            return null;
    }

    // Menu Icon
    if ($hasParent) {
        $option['icon'] = !isset($option['icon'])
            ? 'ri-checkbox-blank-circle-fill tx_sm3'
            : "{$option['icon']} tx_sm2";
    }

    // Menu target
    $option['target'] = $sidebarMenuTarget($option['target'] ?? []);
    $selected = in_array($current_url, $option['target']) ? 'fc' : '';

    // Menu has sub menu
    if (isset($option['menus'])) {
        $groupMenu = '';

        foreach ($option['menus'] as $sbMenu) {
            $groupMenu .= $sidebarMenu($sbMenu, true);
        }

        return
            <<<HTML
            <div class="menu_gr">
                <div class="initial">
                    <div class="icon">
                        <i class="{$option['icon']}"></i>
                    </div>
                    <div class="name">
                        {$option['name']}
                    </div>
                </div>

                <div class="fold_container">
                    {$groupMenu}
                </div>
            </div>
            HTML;
    }

    return
        <<<HTML
            <a href="{$option['url']}" class="menu {$selected}">
                <div class="icon">
                    <i class="{$option['icon']}"></i>
                </div>
                <div class="name">
                    {$option['name']}
                </div>
            </a>
        HTML;
};
?>


<section class="top_nav" style="height: 60px;">

    <button class="flex y_center menu fits tx_lh0">
        <div class="flex_child fits mr1">
            <i class="ri-menu-line" style="font-size: 1.2rem;"></i>
        </div>
        <div class="flex_child">
            Menu
        </div>
    </button>

    <div class="menu"></div>

    <div class="menu flex y_center x_center fits">
        <img class="mb_hide" src="<?= asset_url('logo/original/admin_full_v1.svg'); ?>" alt="" style="width: auto; height: 25px;">

        <img class="tb_hide dk_hide" src="<?= asset_url('logo/original/admin_simplied_v1.svg'); ?>" alt="" style="width: auto; height: 25px;">
    </div>
</section>

<section class="sd_nav">

    <div class="mb_hide tb_hide flex y_center x_start" style="width: 100%; padding: 10px 15px;">
        <img src="<?= asset_url('logo/original/admin_full_v1.svg'); ?>" alt="" style="width: auto; height: 30px;">
    </div>

    <button class="dk_hide flex y_center x_start tx_lh0 mb2" style="padding: 10px; box-sizing: border-box; max-height: 60px; min-height: 60px; border-bottom: 1px solid rgb(200, 200, 200);">
        <div class="flex_child fits mr1">
            <i class="ri-arrow-left-line" style="font-size: 1.2rem"></i>
        </div>
        <div class="flex_child">
            Tutup
        </div>
    </button>

    <div class="block tb_hide mb_hide menu_container" style="max-height: 30px;"></div>

    <div class="menu_container">

        <?php include_once(__DIR__ . '/MenuObject.php'); ?>

        <!-- Menu general - start -->
        <?php
        if (isset($menus)) {
            foreach ($menus as $menu) {
                echo $sidebarMenu($menu);
            }
        }
        ?>
        <!-- Menu general - end -->

        <!-- Menu group - start-->
        <?php
        if (isset($menu_group)) {
            foreach ($menu_group as $group) {
                if (isset($group['privilege'])) {
                    if (!inArrayFound($auth['privilege'], $group['privilege']))
                        continue;
                }

                $groupMenu = '';

                foreach ($group['group_menu'] as $menu) {
                    $groupMenu .= $sidebarMenu($menu);
                }

                echo
                <<<HTML
                    <hr class="single">
                    <div class="menu ds">
                        {$group['group_name']}
                    </div>

                    {$groupMenu}
                HTML;
            }
        }
        ?>
        <!-- Menu group - end -->
    </div>

    <a onclick="window.location.href = '<?= member_url('logout'); ?>';" class="menu ds_hover logout context_box sig_secondary sld" style="border-radius: 0;">
        <div class="icon">
            <i class="ri-logout-circle-r-line"></i>
        </div>

        <div class="name">
            Logout
        </div>
    </a>

</section>

<script type="text/javascript">
    // Event
    $('.sd_nav > .menu_container > .menu_gr > .initial').on('click', function() {

        let elem = this,
            height = $(this).parents('.menu_gr').find('.fold_container')[0].scrollHeight;

        if ($(this).parents('.menu_gr').classExists('expand')) {

            $(this).parents('.menu_gr').removeClass('expand')
                .find('.fold_container')[0].style.removeProperty('max-height');
        } else {

            $(this).parents('.menu_container').find('.menu_gr').each(function() {

                $(this)
                    .removeClass('expand')
                    .find('.fold_container')[0].style.removeProperty('max-height');
            });

            $(this).parents('.menu_gr').addClass('expand')
                .find('.fold_container')[0].style.maxHeight = height + 'px';
        }
    });

    $('.sd_nav > button.dk_hide.flex.v_center.h_start.mb2').on('click', function() {

        let sideNav = $(this).parents('.foldable_container').find('.sd_nav');

        $(sideNav).toggleClass('active');
    });
</script>