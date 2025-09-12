<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container py-4">
     <!-- Header & Saldo Section -->
    <div class="row g-4 align-items-center">
        <div class="col-lg-7 d-flex align-items-center gap-3">
            <img src="<?= esc($avatar) ?>" class="rounded-circle" width="60" height="60" style="object-fit:cover;" alt="">
            <div>
                <small class="text-muted d-block">Selamat datang,</small>
                <h2 class="fw-bold mb-0"><?= esc($name) ?></h2>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card text-white border-0" style="background:#e53935;border-radius:20px;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="opacity-75">Saldo anda</small>
                    </div>
                    <h3 class="fw-bold mt-2 mb-0" id="saldoText">
                        Rp <?= $saldoHidden ? '••••••••••' : number_format((float)$balance, 0, ',', '.') ?>
                    </h3>
                    <a href="#" class="link-light small text-decoration-none mt-2 d-inline-block" id="saldo_status" onclick="toggleSaldo(event)">Tutup Saldo 👁</a>
                </div>
            </div>
        </div>
    </div>
     <!-- Form Pembayaran -->
    <div class="mt-5">
        <div class="mb-3">
            <small class="text-muted d-block">Pembayaran</small>
            <div class="d-flex align-items-center gap-2 mt-1">
                <?php if (!empty($service['icon'])): ?>
                    <img src="<?= esc($service['icon']) ?>" width="20" height="20" style="object-fit:contain;" alt="">
                <?php endif; ?>
                <strong><?= esc($service['name']) ?></strong>
            </div>
        </div>

        <?php $errs = session('errors') ?? []; ?>
        <?php if (isset($errs['form'])): ?><div class="alert alert-danger"><?= esc($errs['form']) ?></div><?php endif; ?>
        <?php if (isset($errs['api'])):   ?><div class="alert alert-danger"><?= esc($errs['api'])   ?></div><?php endif; ?>

        <form action="<?= base_url('payment/' . $service['code']) ?>" method="post" id="payForm">
            <?= csrf_field() ?>

            <?php if ((int)$service['tariff'] > 0): ?>
                <div class="mb-3">
                    <input type="text" class="form-control" value="Rp <?= number_format((int)$service['tariff'], 0, ',', '.') ?>" readonly>
                    <input type="hidden" name="amount" value="<?= (int)$service['tariff'] ?>">
                </div>
                <button type="submit" class="btn btn-danger w-100">Bayar</button>
            <?php endif; ?>
        </form>
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

    <?php if ((int)$service['tariff'] === 0): ?>
            (function() {
                const input = document.getElementById('amount');
                const btn = document.getElementById('btnPay');
                const parse = v => {
                    const n = parseInt((v || '').toString().replace(/\D+/g, ''), 10);
                    return isNaN(n) ? 0 : n
                };
                const fmt = n => n.toLocaleString('id-ID');

                const sync = () => {
                    let n = parse(input.value);
                    if (!n) {
                        input.value = '';
                        btn.disabled = true;
                        return;
                    }
                };

                input.addEventListener('input', sync);
                input.addEventListener('blur', sync);
                sync();

                document.getElementById('payForm').addEventListener('submit', () => {
                    input.value = parse(input.value);
                    btn.disabled = true;
                });
            })();
    <?php endif; ?>
</script>
<?= $this->endSection() ?>