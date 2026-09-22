<?php
/**
 * WARUNG POJOK - Keranjang belanja (berbasis session)
 * Developer: KelasPojok-Dev
 */
require_once __DIR__ . '/config/app.php';

$items = cart_items();
$total = cart_total($items);

$page_title = 'Keranjang';
$page_desc  = 'Isi keranjang pesanan di ' . setting('site_name') . '.';
$active     = '';
include __DIR__ . '/includes/header.php';
?>

<section class="bagian bagian--krem">
  <div class="container">
    <h1 class="bagian__judul">Keranjang</h1>

    <?php if (!$items): ?>
      <div class="kosong">
        <h2>Keranjang masih kosong</h2>
        <p>Pilih menu dulu, nanti pesananmu muncul di sini.</p>
        <a class="btn btn--merah" href="<?= url('menu.php') ?>">Lihat menu</a>
      </div>
    <?php else: ?>
      <div class="dua-kolom">
        <div>
          <table class="tabel-keranjang">
            <thead>
              <tr>
                <th scope="col">Menu</th>
                <th scope="col">Harga</th>
                <th scope="col">Jumlah</th>
                <th scope="col" class="kolom-kanan">Subtotal</th>
                <th scope="col"><span class="sr-only">Aksi</span></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($items as $it): ?>
                <tr>
                  <td data-label="Menu">
                    <div class="produk-mini">
                      <img src="<?= e(upload_url('products', $it['image'])) ?>" alt="" width="66" height="66" loading="lazy">
                      <div>
                        <strong><a href="<?= url('detail-produk.php?slug=' . urlencode($it['slug'])) ?>"><?= e($it['name']) ?></a></strong>
                        <span class="ulasan__tanggal">Sisa stok <?= (int)$it['stock'] ?></span>
                      </div>
                    </div>
                  </td>
                  <td data-label="Harga"><?= rupiah($it['price']) ?></td>
                  <td data-label="Jumlah">
                    <form method="post" action="<?= url('keranjang-aksi.php') ?>" data-autosubmit>
                      <?= csrf_field() ?>
                      <input type="hidden" name="aksi" value="ubah">
                      <input type="hidden" name="product_id" value="<?= (int)$it['id'] ?>">
                      <input type="hidden" name="kembali" value="keranjang.php">
                      <div class="jumlah">
                        <button type="button" data-aksi="kurang" aria-label="Kurangi jumlah <?= e($it['name']) ?>">&minus;</button>
                        <label class="sr-only" for="qty<?= (int)$it['id'] ?>">Jumlah <?= e($it['name']) ?></label>
                        <input type="number" id="qty<?= (int)$it['id'] ?>" name="qty" value="<?= (int)$it['qty'] ?>"
                               min="1" max="<?= (int)$it['stock'] ?>" inputmode="numeric">
                        <button type="button" data-aksi="tambah" aria-label="Tambah jumlah <?= e($it['name']) ?>">+</button>
                      </div>
                      <noscript><button class="btn btn--kecil btn--merah" type="submit">Perbarui</button></noscript>
                    </form>
                  </td>
                  <td data-label="Subtotal" class="kolom-kanan"><strong><?= rupiah($it['subtotal']) ?></strong></td>
                  <td data-label="Aksi" class="kolom-kanan">
                    <form method="post" action="<?= url('keranjang-aksi.php') ?>">
                      <?= csrf_field() ?>
                      <input type="hidden" name="aksi" value="hapus">
                      <input type="hidden" name="product_id" value="<?= (int)$it['id'] ?>">
                      <input type="hidden" name="kembali" value="keranjang.php">
                      <button class="btn btn--kecil btn--detail" type="submit"
                              data-konfirmasi="Hapus <?= e($it['name']) ?> dari keranjang?">Hapus</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>

          <div class="keranjang__bawah">
            <a class="btn btn--kecil btn--detail" href="<?= url('menu.php') ?>">Tambah menu lain</a>
            <form method="post" action="<?= url('keranjang-aksi.php') ?>">
              <?= csrf_field() ?>
              <input type="hidden" name="aksi" value="kosongkan">
              <input type="hidden" name="kembali" value="keranjang.php">
              <button class="btn btn--kecil btn--detail" type="submit"
                      data-konfirmasi="Kosongkan seluruh keranjang?">Kosongkan keranjang</button>
            </form>
          </div>
        </div>

        <aside class="ringkasan">
          <h2>Ringkasan</h2>
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
          <a class="btn btn--merah btn--blok" href="<?= url('checkout.php') ?>">Lanjut ke checkout</a>
          <p class="form__bantuan">Di halaman checkout kamu bisa memilih pesan lewat WhatsApp atau transfer.</p>
        </aside>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
