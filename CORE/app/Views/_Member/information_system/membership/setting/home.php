<?php $this->extend('Layout/Layout.php'); ?>


<?php $this->section('content'); ?>

<div class="inblock content_box mb1 mr1" style="width: 100%; max-width: 250px; display: inline-block; vertical-align: top;">
    <div class="flex v_center h_between tx_ct" style="font-size: 1.1rem;">
        <section class="flex v_center h_center mr1 tx_ct" style="font-size: 2.5rem; height: 50px;">
            <i class="ri-list-check-2"></i>
        </section>
        <section class="context_box sig_main sld bold" style="max-width: fit-content; border-radius: 20px; font-size: inherit; padding: 5px 8px;">
            Active
        </section>

    </div>

    <div class="flex v_center h_between mt1">
        <div class="flex_child mr1 bold">
            Project
        </div>

        <div class="flex_child context_box netral1 sld bold" style="white-space: nowrap; max-width: fit-content; box-sizing: border-box; border-radius: 20px; font-size: 0.9rem; padding: 5px 8px;">
            Admin
        </div>
    </div>

    <div class="smaller_tx" style="margin-top: 5px; color: rgb(130, 130, 130);">
        Monitor project and update project status
    </div>


    <div class="mt1 smaller_tx bold">
        Authority
    </div>

    <div class="block small_tx" style="color: rgb(100, 100, 100); margin-top: 5px;">

        <div style="margin-top: 2px;">
            <b>Monitor</b>: monitor every project
        </div>
        <div style="margin-top: 2px;">
            <b>Update</b>: update active project status
        </div>

    </div>

</div>

<?php $this->endSection('content'); ?>