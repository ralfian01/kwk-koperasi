<?php $this->extend('Layout/Layout.php'); ?>

<?php $this->section('content'); ?>

<h2>
    Selamat datang, <?= $auth['username'] ?? ''; ?>
</h2>

<?php $this->endSection('content'); ?>