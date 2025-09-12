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

    <!-- Top Up Form Section -->
    <?php $res = session()->getFlashdata('topup_result'); ?>

    <h5 class="mt-5 mb-1">Silahkan Masukkan</h5>
    <h1>Nominal Top Up</h1>

    <div class="row">
        <div class="col-8 mt-4">
            <form method="post" action="/topup" id="topupForm">
                <?= csrf_field() ?>
                <div class="mb-3 position-relative">
                    <i class="bi bi-credit-card position-absolute top-50 start-0 translate-middle-y ms-3"></i>
                    <input type="text" inputmode="numeric" name="nominal" id="nominal" class="form-control ps-5" placeholder="Masukkan nominal Top Up" value="<?= old('nominal') ?>">
                </div>
                <button type="button" id="btnTopup" class="btn btn-danger w-100" disabled>Top Up</button>
            </form>
        </div>
        <div class="col-4 mt-4">
            <div class="d-flex">
                <div class="click_value border border-2 py-2 text-center me-2" data-amount="10000" style="width:100px;">Rp10.000</div>
                <div class="click_value border border-2 py-2 text-center me-2" data-amount="20000" style="width:100px;">Rp20.000</div>
                <div class="click_value border border-2 py-2 text-center me-2" data-amount="50000" style="width:100px;">Rp50.000</div>
            </div>
            <div class="d-flex mt-2">
                <div class="click_value border border-2 py-2 text-center me-2" data-amount="100000" style="width:100px;">Rp100.000</div>
                <div class="click_value border border-2 py-2 text-center me-2" data-amount="250000" style="width:100px;">Rp250.000</div>
                <div class="click_value border border-2 py-2 text-center me-2" data-amount="500000" style="width:100px;">Rp500.000</div>
            </div>
        </div>
    </div>

    <!-- Modal: Konfirmasi -->
    <div class="modal fade" id="confirmTopup" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-4 text-center">
                <div class="mx-auto mb-3 d-flex align-items-center justify-content-center"
                    style="">
                    <img src="<?= base_url('img/logo.png') ?>" style="width: 64px; height:64px;" alt="logo">
                </div>
                <div>Anda yakin untuk Top Up sebesar</div>
                <h4 class="fw-bold my-2" id="confirmAmount">Rp0</h4>
                <div class="d-grid gap-2 mt-3">
                    <button class="btn btn-link text-danger fw-semibold" id="confirmYes">Ya, lanjutkan Top Up</button>
                    <button class="btn btn-light" data-bs-dismiss="modal">Batalkan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Hasil (sukses/gagal) -->
    <div class="modal fade" id="resultTopup" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-4 text-center">
                <div id="resultIcon" class="mx-auto mb-3 d-flex align-items-center justify-content-center"
                    style="width:64px;height:64px;border-radius:50%;background:#e9f7ef;color:#28a745;">
                    <span class="fs-3">✔</span>
                </div>
                <div>Top Up sebesar</div>
                <h4 class="fw-bold my-2" id="resultAmount">Rp0</h4>
                <div id="resultText" class="mb-3">berhasil!</div>
                <a href="<?= base_url('/') ?>" class="text-danger fw-semibold">Kembali ke Beranda</a>
            </div>
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

    (function() {
        const MIN = 0, MAX = 9999999999;
        const input = document.getElementById('nominal');
        const btn = document.getElementById('btnTopup');
        const modalConfirm = new bootstrap.Modal(document.getElementById('confirmTopup'));
        const modalResult = new bootstrap.Modal(document.getElementById('resultTopup'));
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
            if (n > MAX) n = MAX;
            input.value = 'Rp ' + fmt(n);
            btn.disabled = !(n >= MIN && n <= MAX);
        };
        input.addEventListener('input', sync);
        input.addEventListener('blur', sync);
        document.querySelectorAll('.click_value').forEach(q => {
            q.addEventListener('click', () => {
                input.value = 'Rp ' + fmt(parseInt(q.dataset.amount, 10));
                sync();
            });
        });
        sync();

        btn.addEventListener('click', () => {
            const n = parse(input.value);
            if (n < MIN) {
                document.getElementById('resultAmount').textContent = 'Rp' + fmt(MIN);
                document.getElementById('resultText').textContent = 'gagal (minimal isi saldo Rp10.000)';
                const ic = document.getElementById('resultIcon');
                ic.style.background = '#fdecea';
                ic.style.color = '#dc3545';
                ic.innerHTML = '<span class="fs-3">✖</span>';
                modalResult.show();
                return;
            }
            document.getElementById('confirmAmount').textContent = 'Rp' + fmt(n);
            modalConfirm.show();
        });

        document.getElementById('confirmYes').addEventListener('click', () => {
            const raw = parse(input.value);
            input.value = raw;
            document.getElementById('topupForm').submit();
        });

        const serverRes = <?= json_encode($res ?? null) ?>;
        if (serverRes) {
            const ok = !!serverRes.ok,
                amt = parseInt(serverRes.amount || 0, 10),
                msg = serverRes.message || (ok ? 'Top Up berhasil.' : 'Top Up gagal.');
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