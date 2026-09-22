<?php
/**
 * WARUNG POJOK - Footer panel admin
 * Developer: KelasPojok-Dev
 */
?>
    <p class="adm-footer">
      &copy; <?= date('Y') ?> <?= e(setting('site_name')) ?>. Panel admin dikembangkan oleh
      <?php $dv = setting('developer_url'); ?>
      <?php if ($dv): ?><a href="<?= e($dv) ?>" target="_blank" rel="noopener"><?= e(setting('developer_name', 'KelasPojok-Dev')) ?></a>
      <?php else: ?><strong><?= e(setting('developer_name', 'KelasPojok-Dev')) ?></strong><?php endif; ?>.
    </p>
  </main>
</div>
<script src="<?= asset('js/admin.js') ?>"></script>
</body>
</html>
