<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="">
    <div class="row vh-100 g-0">
        <div class="col-md-6 d-flex align-items-center justify-content-center bg-white p-3">
            <div style="width:100%; max-width:500px;">
                <div class="text-center mb-4">
                    <img src="<?= base_url('img/logo.png') ?>" alt="SIMS PPOB" height="40"> <span class="fw-bold">SIMS PPOB</span>

                    <h4 class="mt-3">Masuk atau buat akun untuk memulai</h4>
                </div>

                <?php if ($msg = session()->getFlashdata('success')): ?>
                    <div class="alert alert-success"><?= esc($msg) ?></div>
                <?php endif; ?>

                <?php if ($errs = session('errors')): ?>
                    <div class="alert alert-danger mb-3">
                        <ul class="mb-0">
                            <?php foreach ($errs as $e): ?>
                                <li><?= esc($e) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="post" action="/login" onsubmit="this.querySelector('button[type=submit]').disabled=true;">
                    <?= csrf_field() ?>
                    <div class="mb-3 position-relative">
                        <i class="bi bi-at text-secondary position-absolute top-50 start-0 translate-middle-y ms-3"></i>
                        <input type="email" name="email" class="form-control ps-5 <?= isset($errs['email']) ? 'is-invalid' : '' ?>" placeholder="masukkan email anda" value="<?= old('email') ?>" required>
                        <?php if (isset($errs['email'])): ?>
                            <div class="invalid-feedback"><?= esc($errs['email']) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3 position-relative">
                        <i class="bi bi-lock text-secondary position-absolute top-50 start-0 translate-middle-y ms-3"></i>
                        <input type="password" id="password" name="password" class="form-control ps-5 pe-5 <?= isset($errs['password']) ? 'is-invalid' : '' ?>" placeholder="masukkan password anda">
                        <button type="button"
                            id="togglePwd"
                            class="btn eye-btn position-absolute top-50 end-0 translate-middle-y me-2 p-0 border-0"
                            aria-label="Tampilkan sandi">
                            <i class="bi bi-eye"></i>
                        </button>
                        <?php if (isset($errs['password'])): ?>
                            <div class="invalid-feedback"><?= esc($errs['password']) ?></div>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="btn btn-danger w-100">Masuk</button>
                    <div class="text-center mt-3">
                        <small>Belum punya akun? registrasi <a href="/registration" class="text-danger">di sini</a></small>
                    </div>
                </form>

            </div>
        </div>

        <div class="col-md-6 d-none d-md-block p-0"
            style="background:url('<?= base_url('img/login.png') ?>') no-repeat center center; background-size:cover;">
        </div>
    </div>
</div>
<script>
    (function() {
        const pwd = document.getElementById('password');
        const btn = document.getElementById('togglePwd');
        if (!pwd || !btn) return;

        btn.addEventListener('click', () => {
            const icon = btn.querySelector('i');
            if (pwd.type === 'password') {
                pwd.type = 'text';
                icon.classList.replace('bi-eye', 'bi-eye-slash');
                btn.setAttribute('aria-label', 'Sembunyikan sandi');
            } else {
                pwd.type = 'password';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
                btn.setAttribute('aria-label', 'Tampilkan sandi');
            }
        });
    })();
</script>
<?= $this->endSection() ?>