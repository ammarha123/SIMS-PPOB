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

     <!-- History Semua Transaksi Section -->
    <h5 class="mt-5 mb-3">Semua Transaksi</h5>
    <ul class="list-group list-group-flush" id="txList">
        <?php if (!$items): ?>
            <li class="list-group-item py-4 text-center text-muted">Belum ada transaksi.</li>
        <?php else: ?>
            <?php foreach ($items as $it):
                $isTopup = strtoupper($it['type'] ?? '') === 'TOPUP';
                $sign    = $isTopup ? '+' : '−';
                $color   = $isTopup ? 'text-success' : 'text-danger';
                $label   = $isTopup ? 'Top Up Saldo' : ($it['name'] ?: 'Pembayaran');
            ?>
                <li class="list-group-item">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="<?= $color ?> fw-semibold"><?= $sign ?> Rp<?= number_format((int)$it['amount'], 0, ',', '.') ?></div>
                            <small class="text-muted"><?= esc($it['time']) ?></small>
                        </div>
                        <div class="small text-muted text-end">
                            <?= esc($label) ?><br>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>
    <!-- Show More Button -->
    <?php if ($hasMore): ?>
        <div class="text-center mt-3" id="moreWrap">
            <button class="btn btn-link text-danger fw-semibold" id="showMore"
                data-limit="<?= (int)$limit ?>" data-offset="<?= (int)$nextOffset ?>">
                Show more
            </button>
        </div>
    <?php endif; ?>
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
        const btn = document.getElementById('showMore');
        if (!btn) return;

        btn.addEventListener('click', async (e) => {
            e.preventDefault();
            if (btn.disabled) return;

            const limit = parseInt(btn.dataset.limit || '5', 10);
            const offset = parseInt(btn.dataset.offset || '0', 10);

            btn.disabled = true;

            try {
                const res = await fetch('<?= base_url('transaction/more') ?>?limit=' + limit + '&offset=' + offset, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                if (!res.ok) throw new Error('HTTP ' + res.status);
                const data = await res.json();

                const list = document.getElementById('txList');
                (data.items || []).forEach(it => {
                    const isTopup = (it.type || '').toUpperCase() === 'TOPUP';
                    const sign = isTopup ? '+' : '−';
                    const color = isTopup ? 'text-success' : 'text-danger';
                    const label = isTopup ? 'Top Up Saldo' : (it.name || 'Pembayaran');

                    const li = document.createElement('li');
                    li.className = 'list-group-item';
                    li.innerHTML = `
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <div class="${color} fw-semibold">${sign} Rp${new Intl.NumberFormat('id-ID').format(parseInt(it.amount||0))}</div>
              <small class="text-muted">${it.time || '-'}</small>
            </div>
            <div class="small text-muted text-end">
              ${label}<br>
            </div>
          </div>`;
                    list.appendChild(li);
                });

                if (data.hasMore) {
                    btn.dataset.offset = data.nextOffset;
                    btn.disabled = false;
                } else {
                    const wrap = document.getElementById('moreWrap');
                    if (wrap) wrap.remove();
                }
            } catch (err) {
                btn.disabled = false;
                // optional: tampilkan toast/alert
                console.error(err);
            }
        });
    })();
</script>
<?= $this->endSection() ?>