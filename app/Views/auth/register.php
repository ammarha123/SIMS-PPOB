<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row vh-100 g-0">
        <div class="col-md-6 d-flex align-items-center justify-content-center bg-white">
            <div style="width:100%; max-width:500px;">
                <div class="text-center mb-4">
                    <img src="<?= base_url('img/logo.png') ?>" alt="SIMS PPOB" height="40">
                    <h5 class="mt-2 fw-bold">SIMS PPOB</h5>
                    <h4 class="mt-3">Lengkapi data untuk membuat akun</h4>
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

                <form method="post" action="/registration" onsubmit="this.querySelector('button[type=submit]').disabled=true;">
                    <?= csrf_field() ?>
                    <div class="form-group mb-3">
                        <input type="email" name="email" class="form-control <?= isset($errs['email']) ? 'is-invalid' : '' ?>" placeholder="Masukkan email anda" value="<?= old('email') ?>" required>
                        <?php if (isset($errs['email'])): ?><div class="invalid-feedback"><?= esc($errs['email']) ?></div><?php endif; ?>
                    </div>
                    <div class="form-group mb-3">
                        <input type="text" name="first_name" class="form-control <?= isset($errs['first_name']) ? 'is-invalid' : '' ?>" placeholder="Nama pengguna" value="<?= old('first_name') ?>" required>
                        <?php if (isset($errs['first_name'])): ?><div class="invalid-feedback"><?= esc($errs['first_name']) ?></div><?php endif; ?>
                    </div>
                    <div class="form-group mb-3">
                        <input type="text" name="last_name" class="form-control <?= isset($errs['last_name']) ? 'is-invalid' : '' ?>" placeholder="Nama lengkap" value="<?= old('last_name') ?>" required>
                        <?php if (isset($errs['last_name'])): ?><div class="invalid-feedback"><?= esc($errs['last_name']) ?></div><?php endif; ?>
                    </div>
                    <div class="form-group mb-3 position-relative">
                        <input type="password" name="password" class="form-control <?= isset($errs['password']) ? 'is-invalid' : '' ?>" placeholder="Buat password">
                        <?php if (isset($errs['password'])): ?><div class="invalid-feedback"><?= esc($errs['password']) ?></div><?php endif; ?>
                    </div>
                    <div class="form-group mb-4">
                        <input type="password" name="password_confirm" class="form-control <?= isset($errs['password_confirm']) ? 'is-invalid' : '' ?>" placeholder="Konfirmasi password" required>
                        <?php if (isset($errs['password_confirm'])): ?><div class="invalid-feedback"><?= esc($errs['password_confirm']) ?></div><?php endif; ?>
                    </div>
                    <button type="submit" class="btn btn-danger w-100">
                        Registrasi
                    </button>
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
<?= $this->endSection() ?>