<?php if (!isset($pageHead)) return ""; ?>

<div class="page_title">
    <div class="flex y_center x_center title">

        <?php if (isset($pageHead['go_back']) && $pageHead['go_back'] === true) : ?>

            <div class="flex_child fits mr2">
                <a class="flex y_center" onclick="history.go(-1);" style="font-size: 1.5rem;">
                    <i class="ri-arrow-left-line"></i>
                </a>
            </div>

        <?php endif; ?>

        <div class="flex_child mr1">
            <?= $pageHead['title'] ?? ''; ?>
        </div>

        <div class="flex_child hide dk_block"></div>

        <?php if (isset($pageHead['cta_button'])) : ?>
            <?php foreach ($pageHead['cta_button'] as $button) : ?>

                <div class="flex_child fits ml1">
                    <button id="<?= $button['id']; ?>" class="flex y_center context_box <?= $button['context'] ?? 'sig_primary'; ?> sld">
                        <i class="<?= $button['icon'] ?? 'hide'; ?> regular mr1"></i>

                        <div class="flex_child nwrap">
                            <?= $button['name']; ?>
                        </div>
                    </button>
                </div>

            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <?php if (isset($pageHead['description'])) : ?>
        <div class="description">
            <?= $pageHead['description']; ?>
        </div>
    <?php endif; ?>

</div>