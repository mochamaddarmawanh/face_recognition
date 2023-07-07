<div class="modal-header">
    <h1 class="modal-title fs-5" id="imageSampleLabel">...</h1>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <img class="img-fluid rounded-2 object-fit-contain w-100" style="width: 200px;" id="modal_image">
    <div class="row mt-4">
        <div class="col-4 text-start">
            <i class="bi-chevron-left btn" id="modal_previous" onclick="modal_previous()"></i>
        </div>
        <div class="col-4 text-center">
            <span id="modal_pagination"></span>
        </div>
        <div class="col-4 text-end">
            <i class="bi-chevron-right btn" id="modal_next" onclick="modal_next()"></i>
        </div>
    </div>
</div>
<div class="modal-footer">
    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-9">
                <select name="" id="modal_class" class="form-select w-100"></select>
            </div>
            <div class="col-auto">
                <button type="button" class="btn btn-danger" style="margin-left: 6px;" onclick="modal_Image_Delete()"><i class="bi-trash"></i></button>
            </div>
        </div>
    </div>
</div>

<script>
    function displayModalImage() {
        const image_key = <?= json_encode($_GET['key']) ?>;
        const image_value = localStorage.getItem(image_key);

        const modal_image = document.getElementById('modal_image');

        const modal_pagination = document.getElementById('modal_pagination');
        const image_count = Object.keys(localStorage).filter(key => key.startsWith(<?= $_GET['number'] ?> + "-")).length;

        modal_image.src = image_value;
        modal_image.alt = image_key;

        modal_pagination.innerText = <?= $_GET['index'] ?> + " / " + image_count;
    }

    function modal_next() {
        const local_storage_length = localStorage.length;
        const next_index = parseInt(<?php echo $_GET['index']; ?>) + 1;

        if (next_index > local_storage_length) {
            return;
        }

        const image_keys = [];
        for (let i = 0; i < local_storage_length; i++) {
            const key = localStorage.key(i);
            if (key.startsWith(<?php echo $_GET['number']; ?> + "-")) {
                const image_key = {
                    k: key
                };
                image_keys.push(image_key);
            }
        }

        image_keys.sort(function(a, b) {
            var keyA = a.k;
            var keyB = b.k;
            if (keyA < keyB) {
                return -1;
            }
            if (keyA > keyB) {
                return 1;
            }
            return 0;
        });

        image_keys.sort(function(a, b) {
            return parseInt(b.k.split("-")[1]) - parseInt(a.k.split("-")[1]);
        });

        const next_image_key = image_keys[next_index - 1];
        $('#modal_sample').load('models/modal_sample.php?number=' + <?php echo $_GET['number']; ?> + '&key=' + next_image_key.k + '&index=' + next_index);
    }

    function modal_previous() {
        const local_storage_length = localStorage.length;
        const previous_index = parseInt(<?php echo $_GET['index']; ?>) - 1;

        if (previous_index === 0) {
            return;
        }

        const image_keys = [];
        for (let i = 0; i < local_storage_length; i++) {
            const key = localStorage.key(i);
            if (key.startsWith(<?php echo $_GET['number']; ?> + "-")) {
                const image_key = {
                    k: key
                };
                image_keys.push(image_key);
            }
        }

        image_keys.sort(function(a, b) {
            var keyA = a.k;
            var keyB = b.k;
            if (keyA < keyB) {
                return -1;
            }
            if (keyA > keyB) {
                return 1;
            }
            return 0;
        });

        image_keys.sort(function(a, b) {
            return parseInt(b.k.split("-")[1]) - parseInt(a.k.split("-")[1]);
        });

        const previous_image_key = image_keys[previous_index - 1];
        $('#modal_sample').load('models/modal_sample.php?number=' + <?php echo $_GET['number']; ?> + '&key=' + previous_image_key.k + '&index=' + previous_index);
    }

    displayModalImage();
</script>