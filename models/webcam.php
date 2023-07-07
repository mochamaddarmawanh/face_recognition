<div class="row" style="max-height: 350px;">
    <div class="col-7 border-end">
        <div class="row mb-1">
            <div class="col-10 mt-1">
                <p>Webcam</p>
            </div>
            <div class="col-2 mt-1">
                <a href="#" class="text-dark" onclick="back_webcam(<?= $_GET['number'] ?>)">
                    <i class="bi-x float-end" style="font-size: 35px; margin-top: -10px; margin-right: -5px;"></i>
                </a>
            </div>
        </div>
        <select name="" id="webcam_select_<?= $_GET['number'] ?>" class="form-select" onchange="change_webcam(<?= $_GET['number'] ?>)"></select>
        <video class="rounded-2 mt-2 w-100" id="video_<?= $_GET['number'] ?>" controls></video>
        <button class="btn btn-sm btn-primary col-12 mt-1 mb-1" onmousedown="start_capture(<?= $_GET['number'] ?>)" onmouseup="stop_capture(<?= $_GET['number'] ?>)" onmouseleave="stop_capture(<?= $_GET['number'] ?>)">Hold to Record &nbsp;<i class="bi-camera-video"></i></button>
    </div>
    <div class="col-5 mt-1">
        <p id="image_samples_<?= $_GET['number'] ?>"></p>
        <div class="row img-samples p-2" id="imgSamples_<?= $_GET['number'] ?>"></div>
    </div>
</div>

<script>
    function setCookie(name, value, daysToExpire) {
        const date = new Date();
        date.setTime(date.getTime() + (daysToExpire * 24 * 60 * 60 * 1000));
        const expires = 'expires=' + date.toUTCString();
        document.cookie = name + '=' + value + '; ' + expires + '; path=/';
    }

    function getCookie(name) {
        const cookies = document.cookie.split(';');
        for (let i = 0; i < cookies.length; i++) {
            const cookie = cookies[i].trim();
            if (cookie.startsWith(name + '=')) {
                return cookie.substring(name.length + 1);
            }
        }
        return null;
    }

    if (localStorage.length === 0) {
        document.getElementById('imgSamples_<?= $_GET['number'] ?>').innerHTML = "<div class='alert alert-primary'>No sample were made.</div>";
    }

    function lengthImageLocalData(number) {
        const image_samples = document.getElementById('image_samples_' + number);

        const imageKeys = [];
        for (let i = 0; i < localStorage.length; i++) {
            const key = localStorage.key(i);
            if (key.startsWith(number + "-")) {
                imageKeys.push(key);
            }
        }

        if (imageKeys.length === 0) {
            image_samples.innerText = "Add Image Samples:";
        } else {
            image_samples.innerText = imageKeys.length + " Image Samples";
        }
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

        imageKeys.forEach(function(key, index) {
            const value = localStorage.getItem(key);

            const newImageDiv = document.createElement('div');
            newImageDiv.className = 'col-4 col-lg-3';
            newImageDiv.style.padding = '1px';

            const imgTrashDiv = document.createElement('div');
            imgTrashDiv.className = 'img-trash';

            const aToggle = document.createElement('a');
            aToggle.href = '#';
            aToggle.setAttribute('data-bs-toggle', 'modal');
            aToggle.setAttribute('data-bs-target', '#imageSample');
            aToggle.onclick = function() {
                $('#modal_sample').load('models/modal_sample.php?number=' + number + '&key=' + key + '&index=' + parseInt(index + 1));
            };

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

    function clearAllLocalData() {
        localStorage.clear();
    }

    function start_capture(number) {
        const video = document.getElementById('video_' + number);
        if (video.readyState === 4) {
            captureInterval = setInterval(() => {
                capture_image(number);
            }, 100);
            setCookie('captureInterval', captureInterval, 1);
        }
    }

    function stop_capture(number) {
        const captureInterval = getCookie('captureInterval');
        clearInterval(captureInterval);
        setCookie('captureInterval', clearInterval(captureInterval), 1);
    }

    function capture_image(number) {
        const quota = (1024 * 1024) * 1.5; // quota
        const currentUsage = JSON.stringify(localStorage).length;

        if (currentUsage >= quota) {
            stop_capture();
            alert('Local storage quota telah melebihi batas.');
            return;
        }

        const video = document.getElementById('video_' + number);
        const canvas = document.createElement('canvas');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;

        const context = canvas.getContext('2d');
        context.drawImage(video, 0, 0, canvas.width, canvas.height);

        const imageId = new Date().getTime();
        const imageURL = canvas.toDataURL();

        saveImage(number, imageId, imageURL);
    }
</script>