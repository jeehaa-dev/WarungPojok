<?php
/**
 * WARUNG POJOK - Detail produk
 * Developer: KelasPojok-Dev
 */
require_once __DIR__ . '/config/app.php';

$slug = trim($_GET['slug'] ?? '');
$stmt = $pdo->prepare(
    "SELECT p.*, c.name AS category_name, c.slug AS category_slug
     FROM products p LEFT JOIN categories c ON c.id = p.category_id
     WHERE p.slug = ? AND p.status <> 'nonaktif' LIMIT 1"
);
$stmt->execute([$slug]);
$produk = $stmt->fetch();

if (!$produk) {
    http_response_code(404);
    include __DIR__ . '/404.php';
    exit;
}

$habis  = produk_habis($produk);
$rating = rating_produk($produk['id']);

// Ulasan yang sudah disetujui admin
$stmt = $pdo->prepare("SELECT * FROM reviews WHERE product_id = ? AND status = 'disetujui' ORDER BY created_at DESC");
$stmt->execute([$produk['id']]);
$ulasan = $stmt->fetchAll();

// Produk lain dari kategori yang sama
$stmt = $pdo->prepare(
    "SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON c.id = p.category_id
     WHERE p.status <> 'nonaktif' AND p.id <> ? AND (p.category_id = ? OR ? IS NULL)
     ORDER BY RAND() LIMIT 4"
);
$stmt->execute([$produk['id'], $produk['category_id'], $produk['category_id']]);
$lainnya = $stmt->fetchAll();

$pesan_wa = "Halo " . setting('site_name') . ", saya mau pesan " . $produk['name']
          . " (" . rupiah($produk['price']) . "). Apakah masih tersedia?";

$page_title = $produk['name'];
$page_desc  = potong($produk['description'], 155);
$active     = 'menu';
include __DIR__ . '/includes/header.php';
?>

<div class="container">
  <nav class="breadcrumb" aria-label="Breadcrumb">
    <a href="<?= url('index.php') ?>">Beranda</a> /
    <a href="<?= url('menu.php') ?>">Menu</a> /
    <?php if ($produk['category_slug']): ?>
      <a href="<?= url('menu.php?kategori=' . urlencode($produk['category_slug'])) ?>"><?= e($produk['category_name']) ?></a> /
    <?php endif; ?>
    <span><?= e($produk['name']) ?></span>
  </nav>

  <article class="detail">
    <div class="detail__media">
      <img src="<?= e(upload_url('products', $produk['image'])) ?>" alt="<?= e($produk['name']) ?>" width="700" height="700">
    </div>

    <div class="detail__info">
      <p class="kartu__kategori"><?= e($produk['category_name'] ?? 'Menu') ?></p>
      <h1><?= e($produk['name']) ?></h1>

      <div class="detail__meta">
        <?= bintang($rating['rata']) ?>
        <span><?= $rating['jumlah'] ? number_format($rating['rata'], 1, ',', '.') . ' dari ' . $rating['jumlah'] . ' ulasan' : 'Belum ada ulasan' ?></span>
        <?php if ($habis): ?>
          <span class="badge badge--habis badge--stok">Stok habis</span>
        <?php else: ?>
          <span class="badge badge--stok">Sisa <?= (int)$produk['stock'] ?> porsi</span>
        <?php endif; ?>
      </div>

      <p class="detail__harga"><?= rupiah($produk['price']) ?></p>
      <p><?= nl2br(e($produk['description'])) ?></p>

      <?php if ($habis): ?>
        <p class="form__error">Menu ini sedang habis. Hubungi kami untuk tahu kapan tersedia lagi.</p>
        <a class="btn btn--merah" href="<?= e(wa_link('Halo, kapan ' . $produk['name'] . ' tersedia lagi?')) ?>" target="_blank" rel="noopener">Tanya ketersediaan</a>
      <?php else: ?>
        <form class="detail__form" method="post" action="<?= url('keranjang-aksi.php') ?>">
          <?= csrf_field() ?>
          <input type="hidden" name="aksi" value="tambah">
          <input type="hidden" name="product_id" value="<?= (int)$produk['id'] ?>">
          <input type="hidden" name="kembali" value="<?= e($_SERVER['REQUEST_URI']) ?>">
          <div class="jumlah">
            <button type="button" data-aksi="kurang" aria-label="Kurangi jumlah">&minus;</button>
            <label class="sr-only" for="qty">Jumlah porsi</label>
            <input type="number" id="qty" name="qty" value="1" min="1" max="<?= (int)$produk['stock'] ?>" inputmode="numeric">
            <button type="button" data-aksi="tambah" aria-label="Tambah jumlah">+</button>
          </div>
          <button class="btn btn--merah" type="submit">Tambah ke keranjang</button>
          <a class="btn btn--lime" href="<?= e(wa_link($pesan_wa)) ?>" target="_blank" rel="noopener">Pesan via WhatsApp</a>
        </form>
      <?php endif; ?>
    </div>
  </article>
</div>

<!-- ============ ULASAN ============ -->
<section class="bagian bagian--krem">
  <div class="container dua-kolom">
    <div>
      <h2>Ulasan pelanggan (<?= count($ulasan) ?>)</h2>
      <?php if (!$ulasan): ?>
        <div class="kosong">
          <h3>Belum ada ulasan</h3>
          <p>Jadilah yang pertama menulis pengalamanmu dengan menu ini.</p>
        </div>
      <?php else: ?>
        <?php foreach ($ulasan as $u): ?>
          <article class="ulasan">
            <div class="ulasan__kepala">
              <h3 class="ulasan__nama"><?= e($u['customer_name']) ?></h3>
              <span class="ulasan__tanggal"><?= tgl_id($u['created_at']) ?></span>
            </div>
            <?= bintang($u['rating']) ?>
            <p class="ulasan__teks"><?= nl2br(e($u['comment'])) ?></p>
          </article>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <div>
      <form class="form" method="post" action="<?= url('proses-ulasan.php') ?>" data-loading>
        <h2>Tulis ulasan</h2>
        <?= csrf_field() ?>
        <input type="hidden" name="product_id" value="<?= (int)$produk['id'] ?>">
        <input type="hidden" name="slug" value="<?= e($produk['slug']) ?>">

        <div class="form__baris">
          <label for="nama">Nama kamu</label>
          <input type="text" id="nama" name="customer_name" maxlength="100" required>
        </div>

        <div class="form__baris">
          <span class="form__label-grup">Rating</span>
          <div class="rating-input">
            <?php for ($i = 5; $i >= 1; $i--): ?>
              <input type="radio" id="bintang<?= $i ?>" name="rating" value="<?= $i ?>" <?= $i === 5 ? 'checked' : '' ?>>
              <label for="bintang<?= $i ?>" title="<?= $i ?> bintang">&#9733;<span class="sr-only"><?= $i ?> bintang</span></label>
            <?php endfor; ?>
          </div>
        </div>

        <div class="form__baris">
          <label for="komentar">Komentar</label>
          <textarea id="komentar" name="comment" maxlength="600" required placeholder="Bagaimana rasanya menurut kamu?"></textarea>
        </div>

        <button class="btn btn--merah btn--blok" type="submit">Kirim ulasan</button>
        <p class="form__bantuan">Ulasan tampil setelah disetujui admin.</p>
      </form>
    </div>
  </div>
</section>

<?php if ($lainnya): ?>
<section class="bagian bagian--krem-tua">
  <div class="container">
    <h2>Menu lain yang cocok</h2>
    <div class="grid-produk">
      <?php foreach ($lainnya as $p) { include __DIR__ . '/includes/kartu-produk.php'; } ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
