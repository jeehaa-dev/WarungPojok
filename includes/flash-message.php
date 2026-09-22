<?php
/**
 * WARUNG POJOK - Flash message (notifikasi setelah aksi)
 * Developer: KelasPojok-Dev
 */
$flash = get_flash();
if ($flash):
?>
<div class="flash flash--<?= e($flash['tipe']) ?>" role="status">
  <div class="container flash__inner">
    <span><?= e($flash['pesan']) ?></span>
    <button class="flash__close" type="button" aria-label="Tutup notifikasi">&times;</button>
  </div>
</div>
<?php endif; ?>
