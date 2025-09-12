<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container py-4">

    <!-- Header & Saldo Section -->
    <div class="row g-4 align-items-center">
        <div class="col-lg-7 d-flex align-items-center gap-3">
            <img src="<?= esc($avatar) ?>" class="rounded-circle" alt="avatar" width="60" height="60" style="object-fit:cover;">
            <div>
                <small class="text-muted d-block">Selamat datang,</small>
                <h2 class="fw-bold mb-0"><?= esc($name) ?></h2>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card text-white border-0" style="background:#e53935; border-radius:20px;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="opacity-75">Saldo anda</small>
                    </div>
                    <h3 class="fw-bold mt-2 mb-0" id="saldoText">
                        Rp <?= $saldoHidden ? '••••••••••' : number_format((float)$balance, 0, ',', '.') ?>
                    </h3>
                    <a href="#" class="link-light small text-decoration-none mt-2 d-inline-block" id="saldo_status" onclick="toggleSaldo(event)">Lihat Saldo 👁</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Servis Section -->
    <div class="mt-5 d-flex flex-wrap gap-4">
        <?php foreach (($services ?? []) as $svc):
            $code = $svc['service_code'] ?? $svc['code'] ?? '';
        ?>
            <a class="text-center text-decoration-none text-body" href="<?= base_url('payment/' . $code) ?>" style="width:80px;">
                <div class="mb-2">
                    <img src="<?= esc($svc['service_icon'] ?? '') ?>" alt="" width="40" height="40" style="object-fit:contain;">
                </div>
                <small><?= esc($svc['service_name'] ?? '-') ?></small>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Promo menarik Section -->
    <h5 class="mt-5 mb-3">Temukan promo menarik</h5>
    <div id="bannerCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <?php
            $chunks = array_chunk($banners ?? [], 4);
            foreach ($chunks as $i => $group): ?>
                <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>">
                    <div class="row g-3">
                        <?php foreach ($group as $bn): ?>
                            <div class="col-6 col-md-3">
                                <div class="card border-0 shadow-sm h-100">
                                    <img src="<?= esc($bn['banner_image'] ?? '') ?>"
                                        class="card-img-top"
                                        alt="banner"
                                        style="border-radius:12px; object-fit:cover; height:120px;">
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#bannerCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#bannerCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>

</div>

<script>
    function toggleSaldo(e) {
        e.preventDefault();
        const balance = document.getElementById('saldoText');
        const saldo_status = document.getElementById('saldo_status')
        if (!balance) return;
        if (balance.dataset.shown === '1') {
            balance.textContent = 'Rp ••••••••••';
            balance.dataset.shown = '0';
            saldo_status.textContent = "Lihat Saldo 👁"
        } else {
            balance.textContent = 'Rp <?= number_format((float)$balance, 0, ',', '.') ?>';
            balance.dataset.shown = '1';
            saldo_status.textContent = "Tutup Saldo"
        }
    }
</script>
<?= $this->endSection() ?>