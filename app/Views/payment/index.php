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

    <!-- Top Up Form Section -->
    <?php $res = session()->getFlashdata('pay_result'); ?>

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

        <form id="payForm" action="<?= base_url('payment/' . $service['code']) ?>" method="post">
            <?= csrf_field() ?>

            <?php if ((int)$service['tariff'] > 0): ?>
                <div class="mb-3">
                    <input type="text" class="form-control" value="Rp <?= number_format((int)$service['tariff'], 0, ',', '.') ?>" readonly>
                    <input type="hidden" name="amount" id="amount" value="<?= (int)$service['tariff'] ?>">
                </div>
            <?php endif; ?>

            <div class="d-grid">
                <button type="button" class="btn btn-danger" id="btnPay">Bayar</button>
            </div>
        </form>
    </div>
</div>


<!-- Modal konfirmasi -->
<div class="modal fade" id="confirmPay" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content p-4 text-center">
      <div class="mx-auto mb-3 d-flex align-items-center justify-content-center"
           style="width:64px;height:64px;border-radius:50%;background:#fdecea;color:#dc3545;">
        <span class="fs-3">🧾</span>
      </div>
      <div>Beli <?= esc($service['name']) ?> senilai</div>
      <h4 class="fw-bold my-2" id="confirmAmount">Rp0</h4>
      <div class="d-grid gap-2 mt-2">
        <button id="confirmYes" class="btn btn-link text-danger fw-semibold">Ya, lanjutkan Bayar</button>
        <button class="btn btn-light" data-bs-dismiss="modal">Batalkan</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal: Hasil -->
<div class="modal fade" id="resultPay" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-4 text-center">
            <div id="resultIcon" class="mx-auto mb-3 d-flex align-items-center justify-content-center"
                style="width:64px;height:64px;border-radius:50%;background:#e9f7ef;color:#28a745;">
                <span class="fs-3">✔</span>
            </div>
            <div>Pembayaran <?= esc($service['name']) ?> sebesar</div>
            <h4 class="fw-bold my-2" id="resultAmount">Rp0</h4>
            <div id="resultText" class="mb-3">berhasil!!</div>
            <a href="<?= base_url('/') ?>" class="text-danger fw-semibold">Kembali ke Beranda</a>
        </div>
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

   (function(){
  const btn   = document.getElementById('btnPay');
  const form  = document.getElementById('payForm');
  const amtH  = document.getElementById('amount');
  const fmt   = n => new Intl.NumberFormat('id-ID').format(n);
  const parse = v => { const n = parseInt(String(v||'').replace(/\D+/g,''),10); return isNaN(n)?0:n; };
  const modalResult = new bootstrap.Modal(document.getElementById('resultPay'));

  btn.addEventListener('click', () => {
    const n = parse(amtH.value);
    document.getElementById('confirmAmount').textContent = 'Rp ' + fmt(n);
    new bootstrap.Modal(document.getElementById('confirmPay')).show();
  });

  document.getElementById('confirmYes').addEventListener('click', () => {
    const n = parse(amtH.value);
    if (!n) return;
    bootstrap.Modal.getInstance(document.getElementById('confirmPay')).hide();
    form.submit();
  });

  const serverRes = <?= json_encode($res ?? null) ?>;
        if (serverRes) {
            const ok = !!serverRes.ok,
                amt = parseInt(serverRes.amount || 0, 10),
                msg = serverRes.message || (ok ? 'Payment berhasil.' : 'Payment gagal.');
            document.getElementById('resultAmount').textContent = 'Rp' + fmt(amt);
            document.getElementById('resultText').textContent = msg;
            const ic = document.getElementById('resultIcon');
            if (ok) {
                ic.style.background = '#e9f7ef';
                ic.style.color = '#28a745';
                ic.innerHTML = '<span class="fs-3">✔</span>';
            } else {
                ic.style.background = '#fdecea';
                ic.style.color = '#dc3545';
                ic.innerHTML = '<span class="fs-3">✖</span>';
            }
            modalResult.show();
        }
})();
</script>
<?= $this->endSection() ?>