<div class="row mb-1">
    <div class="col-10 mt-1">
        <p>File</p>
    </div>
    <div class="col-2 mt-1">
        <a href="#" class="text-dark" onclick="back(<?= $_GET['number'] ?>)">
            <i class="bi-x float-end" style="font-size: 35px; margin-top: -10px; margin-right: -5px;"></i>
        </a>
    </div>
</div>
<input type="file" name="image" id="image_<?= $_GET['number'] ?>" class="form-control">
<p style="margin: 20px 0px 5px;" id="image_samples_<?= $_GET['number'] ?>"></p>
<div class="row img-samples ps-2 me-0" id="imgSamples_<?= $_GET['number'] ?>"></div>

<script>
    document.getElementById('image_' + <?= $_GET['number'] ?>).addEventListener('change', function() {
        upload(<?= $_GET['number'] ?>);
    });

    if (localStorage.length === 0) {
        document.getElementById('imgSamples_' + <?= $_GET['number'] ?>).innerHTML = "<div class='alert alert-primary mt-1'>No sample were made.</div>";
    }

    function lengthImageLocalData(number) {
        const image_samples = document.getElementById('image_samples_' + number);

        if (localStorage.length === 0) {
            image_samples.innerText = "Add Image Samples:";
        } else {
            image_samples.innerText = localStorage.length + " Image Samples";
        }
    }

    function upload(number) {
        const quota = (1024 * 1024) * 1.5; // quota
        const currentUsage = JSON.stringify(localStorage).length;

        const image = document.getElementById('image_' + number).files[0];
        const imageId = new Date().getTime();
        const reader = new FileReader();
        const allowedFormats = ["image/png", "image/jpeg", "image/jpg", "image/webp"];

        // const maxFileSize = 5 * 1024 * 1024; // 5 MB

        // if (image.size > maxFileSize) {
        //     alert("Ukuran file terlalu besar. Maksimum ukuran file yang diizinkan adalah 5 MB.");
        //     return;
        // }

        if (currentUsage >= quota) {
            stop_capture();
            alert('Local storage quota telah melebihi batas.');
            return;
        }

        if (!allowedFormats.includes(image.type)) {
            alert("Format file tidak valid. Hanya file dengan format PNG, JPG, JPEG, dan WebP yang diizinkan.");
            return;
        }

        reader.onload = function(e) {
            const imageDataURL = e.target.result;

            saveImage(number, imageId, imageDataURL);
        };

        reader.readAsDataURL(image);
    }

    function saveImage(number, imageId, imageURL) {
        const image = document.createElement('img');
        image.src = imageURL;

        image.onload = function() {
            const canvas = document.createElement('canvas');
            const maxWidth = 250;
            const maxHeight = 250;

            let width = image.width;
            let height = image.height;
            let offsetX = 0;
            let offsetY = 0;

            if (width > maxWidth || height > maxHeight) {
                if (width / height > maxWidth / maxHeight) {
                    width = Math.floor(maxHeight * width / height);
                    height = maxHeight;
                    offsetX = Math.floor((width - maxWidth) / 2);
                } else {
                    height = Math.floor(maxWidth * height / width);
                    width = maxWidth;
                    offsetY = Math.floor((height - maxHeight) / 2);
                }
            }

            canvas.width = maxWidth;
            canvas.height = maxHeight;

            const context = canvas.getContext('2d');
            context.drawImage(image, -offsetX, -offsetY, width, height);

            const croppedImageURL = canvas.toDataURL('image/jpeg', 0.5); // quality

            localStorage.setItem(number + "-" + imageId.toString(), croppedImageURL);

            displayImage(number, imageId, croppedImageURL);
        };
    }

    function displayImage(number, imageId, imageDataURL) {
        lengthImageLocalData(number);

        const imgSamplesDiv = document.getElementById('imgSamples_' + number);
        imgSamplesDiv.innerHTML = "";

        const imageKeys = [];
        for (let i = 0; i < localStorage.length; i++) {
            const key = localStorage.key(i);
            if (key.startsWith(number + "-")) {
                imageKeys.push(key);
            }
        }

        imageKeys.sort(function(a, b) {
            return parseInt(b.split("-")[1]) - parseInt(a.split("-")[1]);
        });

        imageKeys.forEach(function(key) {
            const value = localStorage.getItem(key);

            const newImageDiv = document.createElement('div');
            newImageDiv.className = 'col-4 col-lg-3 p-1';

            const imgTrashDiv = document.createElement('div');
            imgTrashDiv.className = 'img-trash';

            const aToggle = document.createElement('a');
            aToggle.href = '#';
            aToggle.setAttribute('data-bs-toggle', 'modal');
            aToggle.setAttribute('data-bs-target', '#imageSample');

            const newImage = document.createElement('img');
            newImage.src = value;
            newImage.alt = 'Captured Image';
            newImage.className = 'img-fluid rounded-2 border object-fit-contain';

            newImage.setAttribute('id', key);

            const aTrash = document.createElement('a');
            aTrash.href = '#';

            const iTrash = document.createElement('i');
            iTrash.className = 'bi bi-trash text-light';

            aTrash.appendChild(iTrash);

            aToggle.appendChild(newImage);

            imgTrashDiv.appendChild(aToggle);
            imgTrashDiv.appendChild(aTrash);

            newImageDiv.appendChild(imgTrashDiv);

            imgSamplesDiv.appendChild(newImageDiv);
        });
    }
</script>