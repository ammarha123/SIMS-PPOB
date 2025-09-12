<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container py-5" style="max-width:760px;">
    <!-- Profile Section -->
    <div class="text-center mb-4">
         <!-- Edit Photo Section -->
        <form id="photoForm" method="post" action="<?= base_url('profile/image') ?>" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <input type="file" name="photo" id="photoInput" accept="image/*" hidden>
            <div id="avatarWrap" class="position-relative d-inline-block" style="cursor:pointer;">
                <img src="<?= esc($avatar ?: base_url('img/default-avatar.png')) ?>" alt="avatar"
                    class="rounded-circle shadow-sm" style="width:96px;height:96px;object-fit:cover;">
                <span class="position-absolute bottom-0 end-0 translate-middle p-1 bg-white border rounded-circle">
                    <img src="<?= base_url('img/logo.png') ?>" alt="" style="width:16px;height:16px;">
                </span>
            </div>
        </form>
        <h3 class="mt-3 mb-0"><?= esc($name) ?></h3>
    </div>

     <!-- Edit Profile Section -->
    <form id="profileForm" method="post" action="<?= base_url('profile/update') ?>" class="card border-0 shadow-sm">
        <?= csrf_field() ?>
        <div class="card-body p-4">
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" value="<?= esc($email) ?>" disabled>
            </div>
            <div class="mb-3">
                <label class="form-label">Nama Depan</label>
                <input type="text" name="first_name" id="firstName"
                    class="form-control <?= isset($errs['first_name']) ? 'is-invalid' : '' ?>"
                    value="<?= old('first_name', $first) ?>" disabled>
                <?php if (isset($errs['first_name'])): ?>
                    <div class="invalid-feedback"><?= esc($errs['first_name']) ?></div>
                <?php endif; ?>
            </div>
            <div class="mb-4">
                <label class="form-label">Nama Belakang</label>
                <input type="text" name="last_name" id="lastName"
                    class="form-control <?= isset($errs['last_name']) ? 'is-invalid' : '' ?>"
                    value="<?= old('last_name', $last) ?>" disabled>
                <?php if (isset($errs['last_name'])): ?>
                    <div class="invalid-feedback"><?= esc($errs['last_name']) ?></div>
                <?php endif; ?>
            </div>
            <div id="viewButtons">
                <button type="button" id="btnEdit" class="btn btn-outline-danger w-100 mb-3">Edit Profile</button>
                <a href="<?= base_url('logout') ?>" class="btn btn-danger w-100">Logout</a>
            </div>

            <div id="editButtons" class="d-none">
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-danger">Simpan</button>
                    <a href="<?= base_url('profile') ?>" class="btn btn-light">Batalkan</a>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    (function() {
        const photoWrap = document.getElementById('avatarWrap');
        const photoInput = document.getElementById('photoInput');
        const photoForm = document.getElementById('photoForm');

        photoWrap.addEventListener('click', () => photoInput.click());
        photoInput.addEventListener('change', () => {
            if (!photoInput.files.length) return;
            const f = photoInput.files[0];
            if (f.size > 100 * 1024) {
                alert('Ukuran gambar maksimum 100 KB.');
                photoInput.value = '';
                return;
            }
            photoForm.submit();
        });

        const btnEdit = document.getElementById('btnEdit');
        const first = document.getElementById('firstName');
        const last = document.getElementById('lastName');
        const viewButtons = document.getElementById('viewButtons');
        const editButtons = document.getElementById('editButtons');

        btnEdit.addEventListener('click', () => {
            first.disabled = false;
            last.disabled = false;
            viewButtons.classList.add('d-none');
            editButtons.classList.remove('d-none');
            first.focus();
        });
    })();
</script>
<?= $this->endSection() ?>