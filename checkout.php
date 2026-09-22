<?php
/**
 * WARUNG POJOK - Checkout
 * Developer: KelasPojok-Dev
 * Dua metode: pesan via WhatsApp, atau transfer bank.
 */
require_once __DIR__ . '/config/app.php';

$items = cart_items();
$total = cart_total($items);

if (!$items) {
    set_flash('info', 'Keranjang masih kosong. Pilih menu dulu ya.');
    redirect('menu.php');
}

$bank_terisi = setting('bank_name') !== '' && setting('bank_account') !== '';

$page_title = 'Checkout';
$page_desc  = 'Selesaikan pesanan di ' . setting('site_name') . '.';
$active     = '';
include __DIR__ . '/includes/header.php';
?>

<section class="bagian bagian--krem">
  <div class="container">
    <h1 class="bagian__judul">Checkout</h1>
    <p class="bagian__intro">Isi data pemesan, lalu pilih cara pembayarannya.</p>

    <div class="dua-kolom">
      <form class="form" method="post" action="<?= url('proses-checkout.php') ?>" data-loading>
        <?= csrf_field() ?>

        <div class="form__baris">
          <label for="nama">Nama pemesan</label>
          <input type="text" id="nama" name="customer_name" maxlength="100" required
                 value="<?= e($_SESSION['checkout_nama'] ?? '') ?>">
        </div>

        <div class="form__baris">
          <label for="wa">Nomor WhatsApp</label>
          <input type="tel" id="wa" name="whatsapp" maxlength="20" required placeholder="0857xxxxxxx"
                 value="<?= e($_SESSION['checkout_wa'] ?? '') ?>">
          <p class="form__bantuan">Dipakai kalau kami perlu mengabari pesananmu.</p>
        </div>

        <div class="form__baris">
          <label for="alamat">Alamat antar <span class="opsional">(kosongkan kalau ambil sendiri)</span></label>
          <textarea id="alamat" name="address" maxlength="300"></textarea>
        </div>

        <div class="form__baris">
          <label for="catatan">Catatan pesanan</label>
          <textarea id="catatan" name="notes" maxlength="300" placeholder="Misalnya: saus dipisah, tidak pedas"></textarea>
        </div>

        <div class="form__baris">
          <span class="form__label-grup">Cara pembayaran</span>
          <div class="pilihan">
            <label>
              <input type="radio" name="payment_method" value="whatsapp" checked>
              <strong>Pesan via WhatsApp</strong>
              <span class="form__bantuan">Isi keranjang otomatis jadi pesan WhatsApp. Pembayaran diatur saat chat.</span>
            </label>
            <label>
              <input type="radio" name="payment_method" value="transfer" <?= $bank_terisi ? '' : 'disabled' ?>>
              <strong>Transfer bank</strong>
              <span class="form__bantuan">
                <?= $bank_terisi
                    ? 'Nomor rekening muncul setelah pesanan dibuat, lalu unggah bukti transfer.'
                    : 'Belum tersedia. Admin belum mengisi data rekening di pengaturan.' ?>
              </span>
            </label>
          </div>
        </div>

        <button class="btn btn--merah btn--blok" type="submit">Buat pesanan</button>
        <p class="form__bantuan">Pesanan tersimpan di database dan bisa dilihat admin.</p>
      </form>

      <aside class="ringkasan">
        <h2>Pesananmu</h2>
        <?php foreach ($items as $it): ?>
          <div class="ringkasan__baris">
            <span><?= e($it['name']) ?> &times;<?= (int)$it['qty'] ?></span>
            <span><?= rupiah($it['subtotal']) ?></span>
          </div>
        <?php endforeach; ?>
        <div class="ringkasan__total">
          <span>Total</span>
          <span><?= rupiah($total) ?></span>
        </div>
        <a class="btn btn--detail btn--blok" href="<?= url('keranjang.php') ?>">Ubah keranjang</a>

        <?php if ($bank_terisi): ?>
          <div class="info-bank" style="margin-top:18px">
            <h3>Informasi transfer</h3>
            <dl>
              <dt>Bank</dt><dd><?= e(setting('bank_name')) ?></dd>
              <dt>Nomor</dt><dd><?= e(setting('bank_account')) ?></dd>
              <dt>Atas nama</dt><dd><?= e(setting('bank_holder')) ?></dd>
            </dl>
          </div>
        <?php endif; ?>
      </aside>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
