<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="">
    <div class="row vh-100 g-0">
        <div class="col-md-6 d-flex align-items-center justify-content-center bg-white">
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
                    <div class="form-group mb-3">
                        <input type="email" name="email" class="form-control <?= isset($errs['email']) ? 'is-invalid' : '' ?>" placeholder="Masukkan email anda" value="<?= old('email') ?>" required>
                        <?php if (isset($errs['email'])): ?><div class="invalid-feedback"><?= esc($errs['email']) ?></div><?php endif; ?>
                    </div>
                    <div class="form-group mb-3 position-relative">
                        <input type="password" name="password" class="form-control <?= isset($errs['password']) ? 'is-invalid' : '' ?>" placeholder="Masukkan password anda">
                        <?php if (isset($errs['password'])): ?><div class="invalid-feedback"><?= esc($errs['password']) ?></div><?php endif; ?>
                    </div>
                    <button type="submit" class="btn btn-danger w-100">
                        Masuk
                    </button>
                    <div class="text-center mt-3">
                        <small>Belum punya akun? registrasi <a href="/login" class="text-danger">di sini</a></small>
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