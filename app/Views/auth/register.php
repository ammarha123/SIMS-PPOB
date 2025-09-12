<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="">
    <div class="row vh-100 g-0">
        <div class="col-md-6 d-flex align-items-center justify-content-center bg-white">
            <div style="width:100%; max-width:500px;">
                <div class="text-center mb-4">
                    <img src="<?= base_url('img/logo.png') ?>" alt="SIMS PPOB" height="40">
                    <h5 class="mt-2 fw-bold">SIMS PPOB</h5>
                    <h4 class="mt-3">Lengkapi data untuk membuat akun</h4>
                </div>

                <?php if ($errs = session('errors')): ?>
                    <div class="alert alert-danger mb-3">
                        <ul class="mb-0">
                            <?php foreach ($errs as $e): ?>
                                <li><?= esc($e) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="post" action="/registration" onsubmit="this.querySelector('button[type=submit]').disabled=true;">
                    <?= csrf_field() ?>
                    <div class="mb-3 position-relative">
                        <i class="bi bi-at input-icon position-absolute top-50 start-0 translate-middle-y ms-3"></i>
                        <input type="email" name="email" class="form-control ps-5 <?= isset($errs['email']) ? 'is-invalid' : '' ?>" placeholder="Masukkan email anda" value="<?= old('email') ?>" required>
                        <?php if (isset($errs['email'])): ?><div class="invalid-feedback"><?= esc($errs['email']) ?></div><?php endif; ?>
                    </div>

                    <div class="mb-3 position-relative">
                        <i class="bi bi-person input-icon position-absolute top-50 start-0 translate-middle-y ms-3"></i>
                        <input type="text" name="first_name" class="form-control ps-5 <?= isset($errs['first_name']) ? 'is-invalid' : '' ?>" placeholder="Nama depan" value="<?= old('first_name') ?>" required>
                        <?php if (isset($errs['first_name'])): ?><div class="invalid-feedback"><?= esc($errs['first_name']) ?></div><?php endif; ?>
                    </div>

                    <div class="mb-3 position-relative">
                        <i class="bi bi-person-lines-fill input-icon position-absolute top-50 start-0 translate-middle-y ms-3"></i>
                        <input type="text" name="last_name" class="form-control ps-5 <?= isset($errs['last_name']) ? 'is-invalid' : '' ?>" placeholder="Nama belakang" value="<?= old('last_name') ?>" required>
                        <?php if (isset($errs['last_name'])): ?><div class="invalid-feedback"><?= esc($errs['last_name']) ?></div><?php endif; ?>
                    </div>

                    <div class="mb-3 position-relative">
                        <i class="bi bi-lock input-icon position-absolute top-50 start-0 translate-middle-y ms-3"></i>
                        <input type="password" id="password" name="password" class="form-control px-5 <?= isset($errs['password']) ? 'is-invalid' : '' ?>" placeholder="Buat password">
                        <button type="button" id="togglePass" class="btn eye-btn position-absolute top-50 end-0 translate-middle-y me-2 p-0 border-0" aria-label="Tampilkan sandi">
                            <i class="bi bi-eye"></i>
                        </button>
                        <?php if (isset($errs['password'])): ?><div class="invalid-feedback"><?= esc($errs['password']) ?></div><?php endif; ?>
                    </div>

                    <div class="mb-4 position-relative">
                        <i class="bi bi-lock-fill input-icon position-absolute top-50 start-0 translate-middle-y ms-3"></i>
                        <input type="password" id="password2" name="password_confirm" class="form-control px-5 <?= isset($errs['password_confirm']) ? 'is-invalid' : '' ?>" placeholder="Konfirmasi password" required>
                        <button type="button" id="togglePass2" class="btn eye-btn position-absolute top-50 end-0 translate-middle-y me-2 p-0 border-0" aria-label="Tampilkan sandi">
                            <i class="bi bi-eye"></i>
                        </button>
                        <?php if (isset($errs['password_confirm'])): ?><div class="invalid-feedback"><?= esc($errs['password_confirm']) ?></div><?php endif; ?>
                        <div id="matchHelp" class="form-text"></div>
                    </div>

                    <button type="submit" class="btn btn-danger w-100">Registrasi</button>
                    <div class="text-center mt-3">
                        <small>Sudah punya akun? <a href="/login" class="text-danger">Masuk</a></small>
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
        const pass = document.getElementById('password');
        const pass2 = document.getElementById('password2');

        function attachToggle(btnId, input) {
            const btn = document.getElementById(btnId);
            if (!btn || !input) return;
            btn.addEventListener('click', () => {
                const icon = btn.querySelector('i');
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.replace('bi-eye', 'bi-eye-slash');
                    btn.setAttribute('aria-label', 'Sembunyikan sandi');
                } else {
                    input.type = 'password';
                    icon.classList.replace('bi-eye-slash', 'bi-eye');
                    btn.setAttribute('aria-label', 'Tampilkan sandi');
                }
            });
        }
        attachToggle('togglePass', pwd);
        attachToggle('togglePass', pwd2);
    })();
</script>
<?= $this->endSection() ?>