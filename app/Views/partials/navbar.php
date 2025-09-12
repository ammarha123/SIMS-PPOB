<nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="<?= base_url('/') ?>">
            <img src="<?= base_url('img/logo.png') ?>" alt="logo" height="24">
            <span class="fw-semibold">SIMS PPOB</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
            <span class="navbar-toggler-icon"></span>
            <h2 class="fw-bold m-0"><?= esc($name) ?></h2>
        </button>

        <div class="collapse navbar-collapse" id="navMain">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-3">
                <li class="nav-item"><a class="nav-link" href="<?= base_url('/topup') ?>">Top Up</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= base_url('/transaction') ?>">Transaction</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= base_url('/profile') ?>">Akun</a></li>
                <li class="nav-item">
                    <a class="btn btn-outline-secondary btn-sm" href="<?= base_url('logout') ?>">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>