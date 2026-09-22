<?php
/**
 * WARUNG POJOK - Beranda
 * Developer: KelasPojok-Dev  |  Website UMKM kuliner (PHP native + MySQL)
 */
require_once __DIR__ . '/config/app.php';

// Produk unggulan
$unggulan = $pdo->query(
    "SELECT p.*, c.name AS category_name
     FROM products p LEFT JOIN categories c ON c.id = p.category_id
     WHERE p.status <> 'nonaktif'
     ORDER BY p.is_featured DESC, p.created_at DESC
     LIMIT 4"
)->fetchAll();

// Kategori + jumlah produk
$kategori = $pdo->query(
    "SELECT c.*, COUNT(p.id) AS jumlah
     FROM categories c LEFT JOIN products p ON p.category_id = c.id AND p.status <> 'nonaktif'
     GROUP BY c.id ORDER BY c.name"
)->fetchAll();

// Ulasan terbaru yang sudah disetujui
$testimoni = $pdo->query(
    "SELECT r.*, p.name AS product_name, p.slug
     FROM reviews r JOIN products p ON p.id = r.product_id
     WHERE r.status = 'disetujui'
     ORDER BY r.created_at DESC LIMIT 3"
)->fetchAll();

// Galeri singkat
$galeri = $pdo->query('SELECT * FROM gallery ORDER BY created_at DESC LIMIT 4')->fetchAll();

$page_title = 'Dimsum mentai hangat';
$page_desc  = setting('hero_desc');
$active     = 'beranda';
include __DIR__ . '/includes/header.php';
?>

<!-- ============ HERO ============ -->
<!-- Background hero memakai gambar asli dari desain (bukan lagi SVG buatan tangan). -->
<section class="hero" style="background-image:url('<?= e(upload_url('settings', setting('hero_bg'), 'assets/images/hero-bg.jpg')) ?>')">
  <div class="container hero__inner">
    <div class="hero__teks">
      <h1 class="hero__title">
        <span class="baris-1"><?= e(setting('hero_title_1', 'DIMSUM')) ?></span>
        <span class="baris-2"><?= e(setting('hero_title_2', 'MENTAI')) ?></span>
      </h1>
      <p class="hero__desc"><?= e(setting('hero_desc')) ?></p>
      <div class="hero__aksi">
        <a class="btn btn--kuning" href="<?= url('menu.php') ?>"><?= e(setting('cta_primary_text', 'Lihat menu')) ?></a>
        <a class="btn btn--garis" href="<?= e(wa_link('Halo ' . setting('site_name') . ', saya mau pesan dimsum.')) ?>" target="_blank" rel="noopener"><?= e(setting('cta_secondary_text', 'Pesan Sekarang')) ?></a>
      </div>
    </div>
    <div class="hero__media">
      <?php if (setting('hero_badge')): ?>
        <span class="hero__badge"><?= e(setting('hero_badge')) ?></span>
      <?php endif; ?>
      <img class="hero__img" src="<?= e(upload_url('settings', setting('hero_image'), 'assets/images/hero-dimsum.png')) ?>"
           alt="Sebaki dimsum mentai dengan taburan daun bawang dan wijen" width="628" height="505">
    </div>
  </div>
</section>

<!-- ============ PRODUK UNGGULAN ============ -->
<section class="bagian bagian--krem">
  <div class="container">
    <div class="judul-baris">
      <div>
        <h2 class="bagian__judul">Menu yang paling dicari</h2>
        <p class="bagian__intro">Dibuat setelah pesanan masuk, jadi sampai di tangan kamu masih hangat.</p>
      </div>
      <a class="btn btn--kecil btn--merah" href="<?= url('menu.php') ?>">Lihat semua menu</a>
    </div>
    <div class="grid-produk">
      <?php foreach ($unggulan as $p) { include __DIR__ . '/includes/kartu-produk.php'; } ?>
    </div>
  </div>
</section>

<!-- ============ KATEGORI ============ -->
<section class="bagian bagian--krem-tua">
  <div class="container">
    <h2 class="bagian__judul">Pilih sesuai selera</h2>
    <p class="bagian__intro">Empat kelompok menu, dari yang paling ringan sampai paket untuk ramai-ramai.</p>
    <ul class="chips">
      <?php foreach ($kategori as $k): ?>
        <li>
          <a class="chip" href="<?= url('menu.php?kategori=' . urlencode($k['slug'])) ?>">
            <?= e($k['name']) ?> (<?= (int)$k['jumlah'] ?>)
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>

<!-- ============ KEUNGGULAN ============ -->
<section class="bagian bagian--merah">
  <div class="container">
    <h2 class="bagian__judul">Kenapa pesan di sini</h2>
    <div class="grid-nilai">
      <div class="nilai"><h3><?= e(setting('advantage_1')) ?></h3><p>Tidak ada stok semalam. Kukusan baru jalan setelah pesanan kamu masuk.</p></div>
      <div class="nilai"><h3><?= e(setting('advantage_2')) ?></h3><p>Takaran mayo, tobiko, dan cabainya kami racik sendiri di warung.</p></div>
      <div class="nilai"><h3><?= e(setting('advantage_3')) ?></h3><p>Porsi isi 6 pcs, cukup untuk camilan sore berdua.</p></div>
      <div class="nilai"><h3><?= e(setting('advantage_4')) ?></h3><p>Pesan lewat WhatsApp, kami kabari estimasi antarnya.</p></div>
    </div>
  </div>
</section>

<!-- ============ TESTIMONI ============ -->
<?php if ($testimoni): ?>
<section class="bagian bagian--krem">
  <div class="container">
    <h2 class="bagian__judul">Kata pelanggan</h2>
    <p class="bagian__intro">Ulasan ini ditulis langsung di halaman produk dan disetujui admin sebelum tampil.</p>
    <div class="grid-testimoni">
      <?php foreach ($testimoni as $t): ?>
        <blockquote class="testimoni">
          <?= bintang($t['rating']) ?>
          <p><?= e($t['comment']) ?></p>
          <p class="testimoni__nama"><?= e($t['customer_name']) ?></p>
          <p class="testimoni__produk">tentang <a href="<?= url('detail-produk.php?slug=' . urlencode($t['slug'])) ?>"><?= e($t['product_name']) ?></a></p>
        </blockquote>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ============ GALERI SINGKAT ============ -->
<?php if ($galeri): ?>
<section class="bagian bagian--krem-tua">
  <div class="container">
    <div class="judul-baris">
      <h2>Dari dapur kami</h2>
      <a class="btn btn--kecil btn--merah" href="<?= url('galeri.php') ?>">Buka galeri</a>
    </div>
    <div class="grid-galeri">
      <?php foreach ($galeri as $g): ?>
        <button class="galeri__item" type="button"
                data-full="<?= e(upload_url('gallery', $g['image'])) ?>"
                data-judul="<?= e($g['title']) ?>">
          <img src="<?= e(upload_url('gallery', $g['image'])) ?>" alt="<?= e($g['title']) ?>" loading="lazy" width="400" height="400">
        </button>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ============ CTA + LOKASI SINGKAT ============ -->
<section class="bagian bagian--krem">
  <div class="container">
    <div class="cta">
      <h2>Lapar sekarang?</h2>
      <p>Kirim pesanan lewat WhatsApp, atau susun dulu keranjangmu di halaman menu.</p>
      <div class="cta__aksi">
        <a class="btn btn--kuning" href="<?= e(wa_link('Halo ' . setting('site_name') . ', saya mau pesan dimsum.')) ?>" target="_blank" rel="noopener">Pesan lewat WhatsApp</a>
        <a class="btn btn--garis" href="<?= url('menu.php') ?>">Susun pesanan</a>
      </div>
    </div>
  </div>
</section>

<section class="bagian bagian--krem-tua">
  <div class="container kontak-grid">
    <div class="kontak-kartu">
      <h3>Alamat warung</h3>
      <p><?= e(setting('address')) ?></p>
      <p><?= e(setting('open_hours')) ?></p>
      <a class="btn btn--kecil btn--lime" href="<?= e(setting('maps_link')) ?>" target="_blank" rel="noopener">Buka di Google Maps</a>
    </div>
    <div class="kontak-kartu">
      <h3>Pesan cepat</h3>
      <p>WhatsApp <?= e(setting('whatsapp')) ?></p>
      <p>Balasan paling cepat saat jam buka.</p>
      <a class="btn btn--kecil btn--merah" href="<?= e(wa_link()) ?>" target="_blank" rel="noopener">Chat sekarang</a>
    </div>
    <div class="kontak-kartu">
      <h3>Butuh porsi banyak?</h3>
      <p>Untuk acara atau arisan, kabari kami sehari sebelumnya supaya porsinya kami siapkan.</p>
      <a class="btn btn--kecil btn--garis btn--detail" href="<?= url('kontak.php') ?>">Halaman kontak</a>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
