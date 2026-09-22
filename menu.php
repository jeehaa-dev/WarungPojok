<?php
/**
 * WARUNG POJOK - Katalog produk / Menu
 * Developer: KelasPojok-Dev
 * Fitur: pencarian, filter kategori, sorting, paginasi
 */
require_once __DIR__ . '/config/app.php';

$cari     = trim($_GET['cari'] ?? '');
$kategori = trim($_GET['kategori'] ?? '');
$urut     = $_GET['urut'] ?? 'terbaru';
$halaman  = max(1, (int)($_GET['hal'] ?? 1));
$per_hal  = 8;

// Susun query dengan prepared statement
$where  = ["p.status <> 'nonaktif'"];
$params = [];

if ($cari !== '') {
    $where[] = '(p.name LIKE ? OR p.description LIKE ?)';
    $params[] = '%' . $cari . '%';
    $params[] = '%' . $cari . '%';
}
if ($kategori !== '') {
    $where[] = 'c.slug = ?';
    $params[] = $kategori;
}

$urutan = [
    'terbaru'      => 'p.created_at DESC',
    'harga-murah'  => 'p.price ASC',
    'harga-mahal'  => 'p.price DESC',
    'nama'         => 'p.name ASC',
];
$order = $urutan[$urut] ?? $urutan['terbaru'];
$sql_where = 'WHERE ' . implode(' AND ', $where);

// Hitung total untuk paginasi
$stmt = $pdo->prepare("SELECT COUNT(*) FROM products p LEFT JOIN categories c ON c.id = p.category_id $sql_where");
$stmt->execute($params);
$total      = (int)$stmt->fetchColumn();
$total_hal  = max(1, (int)ceil($total / $per_hal));
$halaman    = min($halaman, $total_hal);
$offset     = ($halaman - 1) * $per_hal;

$stmt = $pdo->prepare(
    "SELECT p.*, c.name AS category_name
     FROM products p LEFT JOIN categories c ON c.id = p.category_id
     $sql_where ORDER BY p.is_featured DESC, $order LIMIT $per_hal OFFSET $offset"
);
$stmt->execute($params);
$produk = $stmt->fetchAll();

$daftar_kategori = $pdo->query('SELECT * FROM categories ORDER BY name')->fetchAll();

/** Bangun URL filter sambil mempertahankan parameter lain. */
function link_filter(array $ganti = [])
{
    $q = array_merge($_GET, $ganti);
    $q = array_filter($q, function ($v) { return $v !== '' && $v !== null; });
    return url('menu.php' . ($q ? '?' . http_build_query($q) : ''));
}

$page_title = 'Menu';
$page_desc  = 'Daftar menu dimsum ' . setting('site_name') . ', lengkap dengan harga dan ketersediaan stok.';
$active     = 'menu';
include __DIR__ . '/includes/header.php';
?>

<section class="bagian bagian--krem">
  <div class="container">
    <h1 class="bagian__judul">Menu <?= e(setting('site_name')) ?></h1>
    <p class="bagian__intro">Semua harga sudah termasuk saus. Stok diperbarui langsung dari dapur.</p>

    <form class="form form--filter" method="get" action="<?= url('menu.php') ?>" data-filter-form>
      <div class="filter-baris">
        <div class="form__baris">
          <label for="cari">Cari menu</label>
          <input type="text" id="cari" name="cari" value="<?= e($cari) ?>" placeholder="Misalnya: mentai keju">
        </div>
        <div class="form__baris">
          <label for="urut">Urutkan</label>
          <select id="urut" name="urut">
            <option value="terbaru"     <?= $urut === 'terbaru' ? 'selected' : '' ?>>Terbaru</option>
            <option value="harga-murah" <?= $urut === 'harga-murah' ? 'selected' : '' ?>>Harga termurah</option>
            <option value="harga-mahal" <?= $urut === 'harga-mahal' ? 'selected' : '' ?>>Harga tertinggi</option>
            <option value="nama"        <?= $urut === 'nama' ? 'selected' : '' ?>>Nama A-Z</option>
          </select>
        </div>
        <div class="form__baris form__baris--tombol">
          <button class="btn btn--merah" type="submit">Cari</button>
        </div>
      </div>
      <?php if ($kategori !== ''): ?>
        <input type="hidden" name="kategori" value="<?= e($kategori) ?>">
      <?php endif; ?>
    </form>

    <ul class="chips">
      <li><a class="chip<?= $kategori === '' ? ' is-active' : '' ?>" href="<?= link_filter(['kategori' => null, 'hal' => null]) ?>">Semua</a></li>
      <?php foreach ($daftar_kategori as $k): ?>
        <li>
          <a class="chip<?= $kategori === $k['slug'] ? ' is-active' : '' ?>"
             href="<?= link_filter(['kategori' => $k['slug'], 'hal' => null]) ?>"><?= e($k['name']) ?></a>
        </li>
      <?php endforeach; ?>
    </ul>

    <?php if (!$produk): ?>
      <div class="kosong">
        <h2>Menu tidak ditemukan</h2>
        <p>Coba kata kunci lain, atau lihat seluruh menu kami.</p>
        <a class="btn btn--merah" href="<?= url('menu.php') ?>">Tampilkan semua menu</a>
      </div>
    <?php else: ?>
      <p class="hasil-info">Menampilkan <?= count($produk) ?> dari <?= $total ?> menu<?= $cari !== '' ? ' untuk pencarian "' . e($cari) . '"' : '' ?>.</p>
      <div class="grid-produk">
        <?php foreach ($produk as $p) { include __DIR__ . '/includes/kartu-produk.php'; } ?>
      </div>

      <?php if ($total_hal > 1): ?>
        <nav class="paginasi" aria-label="Navigasi halaman menu">
          <?php if ($halaman > 1): ?>
            <a href="<?= link_filter(['hal' => $halaman - 1]) ?>" rel="prev">&larr;</a>
          <?php endif; ?>
          <?php for ($i = 1; $i <= $total_hal; $i++): ?>
            <?php if ($i === $halaman): ?>
              <span class="is-active" aria-current="page"><?= $i ?></span>
            <?php else: ?>
              <a href="<?= link_filter(['hal' => $i]) ?>"><?= $i ?></a>
            <?php endif; ?>
          <?php endfor; ?>
          <?php if ($halaman < $total_hal): ?>
            <a href="<?= link_filter(['hal' => $halaman + 1]) ?>" rel="next">&rarr;</a>
          <?php endif; ?>
        </nav>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
