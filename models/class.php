<div class="card shadow-sm mb-3">
    <div class="card-header bg-white">
        <div class="row">
            <div class="col-11">
                <h5>Class <?= $_GET['number'] ?> &nbsp;<a href="#" class="text-decoration-none text-secondary"><i class="bi-pencil" style="font-size: 20px;"></i></a></h5>
            </div>
            <div class="col-1">
                <div class="dropdown">
                    <i class="bi-three-dots-vertical mt-1" type="button" data-bs-toggle="dropdown" aria-expanded="false"></i>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item text-secondary disabled" href="#">Delete Class</a></li>
                        <li><a class="dropdown-item text-secondary disabled" href="#">Remove All Samples</a></li>
                        <li><a class="dropdown-item text-secondary disabled" href="#">Download All Samples</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body" id="content_<?= $_GET['number'] ?>">
        <p>Add Image Samples:</p>
        <button class="btn btn-sm btn-primary" onclick="webcam(<?= $_GET['number'] ?>)">
            <i class="bi-camera-video"></i>
            <br>
            Webcam
        </button>
        <button class="btn btn-sm btn-primary" onclick="file(<?= $_GET['number'] ?>)">
            <i class="bi-upload"></i>
            <br>
            Upload
        </button>
        <button class="btn btn-sm btn-primary" onclick="preview(<?= $_GET['number'] ?>)">
            <i class="bi-image"></i>
            <br>
            <span id="image_samples_button_<?= $_GET['number'] ?>"></span>
        </button>
    </div>
</div>

<!-- <button onclick="read()">read</button> -->

<script>
    window.addEventListener('beforeunload', function(event) {
        event.preventDefault();
        event.returnValue = '';
        var confirmationMessage = 'Changes you made may not be saved.';
        event.returnValue = confirmationMessage; // Untuk browser yang mendukung returnValue
        return confirmationMessage; // Untuk browser yang tidak mendukung returnValue
    });

    window.addEventListener('unload', function() {
        localStorage.clear();
        setCookie('newClass', 1, 1);
    });

    if (Object.keys(localStorage).filter(key => key.startsWith(<?= $_GET['number'] ?> + "-")).length === 0) {
        document.getElementById('image_samples_button_<?= $_GET['number'] ?>').innerText = "0 Sample";
    } else {
        const sampleCount = Object.keys(localStorage).filter(key => key.startsWith(<?= $_GET['number'] ?> + "-")).length;
        document.getElementById('image_samples_button_<?= $_GET['number'] ?>').innerText = sampleCount + " Samples";
    }

    function keyImageLocalData(number) {
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
        return imageKeys;
    }

    function lengthImageLocalData(number) {
        const imageKeys = keyImageLocalData(number);
        const image_samples = document.getElementById('image_samples_' + number);

        if (imageKeys.length === 0) {
            image_samples.innerText = "Add Image Samples:";
        } else {
            image_samples.innerText = imageKeys.length + " Image Samples";
        }
    }

    function displayImageLocalData(number) {
        const imageKeys = keyImageLocalData(number);
        const imgSamplesDiv = document.getElementById('imgSamples_' + number);
        imgSamplesDiv.innerHTML = "";

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

            const divTrash = document.createElement('div');
            divTrash.style.cursor = 'pointer';
            divTrash.onclick = function() {
                localStorage.removeItem(key);
                displayImageLocalData(number);
            };

            const iTrash = document.createElement('i');
            iTrash.className = 'bi bi-trash text-light';

            divTrash.appendChild(iTrash);

            aToggle.appendChild(newImage);

            imgTrashDiv.appendChild(aToggle);
            imgTrashDiv.appendChild(divTrash);

            newImageDiv.appendChild(imgTrashDiv);

            imgSamplesDiv.appendChild(newImageDiv);
        });
    }

    function getAvailableWebcams() {
        return new Promise(function(resolve, reject) {
            navigator.mediaDevices.enumerateDevices()
                .then(function(devices) {
                    const webcams = devices.filter(function(device) {
                        return device.kind === 'videoinput';
                    });
                    resolve(webcams);
                })
                .catch(function(error) {
                    reject(error);
                });
        });
    }

    function webcam(number) {
        new Promise(function(resolve, reject) {
            $('#content_' + number).load('models/webcam.php?number=' + number, function() {
                resolve();
            }, function(error) {
                reject(error);
            });
        }).then(function() {
            return navigator.mediaDevices.getUserMedia({
                video: true
            });
        }).then(function(mediaStream) {
            lengthImageLocalData(number);
            displayImageLocalData(number);

            const video = document.getElementById('video_' + number);
            video.srcObject = mediaStream;

            getAvailableWebcams().then(function(webcams) {
                const webcam_select = document.getElementById('webcam_select_' + number);
                webcam_select.innerHTML = "<option value='' selected disabled>Switch Webcam</option>";
                webcams.forEach(function(webcam) {
                    const option = document.createElement('option');
                    option.value = webcam.deviceId;
                    option.text = webcam.label;
                    webcam_select.appendChild(option);
                });
            }).catch(function(error) {
                console.error('Error:', error);
            });

            if (Object.keys(localStorage).filter(key => key.startsWith(number + "-")).length === 0) {
                document.getElementById('imgSamples_' + number).innerHTML = "<div class='alert alert-primary'>No sample were made.</div>";
            }
        }).catch(function(error) {
            $('#content_' + number).load('models/method.php?number=' + number);
            alert('There was an error opening your webcam. Make sure permissions are enabled or switch to image uploading.');
            console.log(error)
        });
    }

    function change_webcam(number) {
        const selectedWebcam = document.getElementById('webcam_select_' + number).value;
        console.log(selectedWebcam)

        navigator.mediaDevices.getUserMedia({
            video: {
                deviceId: selectedWebcam
            }
        }).then(function(stream) {
            const video = document.getElementById('video_' + number);
            video.srcObject = stream;
        }).catch(function(error) {
            alert('Error accessing webcam:', error);
        });
    }

    function file(number) {
        new Promise(function(resolve, reject) {
            $('#content_' + number).load('models/file.php?number=' + number, function() {
                resolve();
            }, function(error) {
                reject(error);
            });
        }).then(function(mediaStream) {
            lengthImageLocalData(number);
            displayImageLocalData(number);

            if (Object.keys(localStorage).filter(key => key.startsWith(number + "-")).length === 0) {
                document.getElementById('imgSamples_' + number).innerHTML = "<div class='alert alert-primary mt-1'>No sample were made.</div>";
            }
        }).catch(function(error) {
            $('#content_' + number).load('models/method.php?number=' + number);
            alert(error);
        });
    }

    function preview(number) {
        new Promise(function(resolve, reject) {
            $('#content_' + number).load('models/preview.php?number=' + number, function() {
                resolve();
            }, function(error) {
                reject(error);
            });
        }).then(function(mediaStream) {
            lengthImageLocalData(number);
            displayImageLocalData(number);

            if (Object.keys(localStorage).filter(key => key.startsWith(number + "-")).length === 0) {
                document.getElementById('image_samples_button_' + number).innerText = "0 Sample";
            } else {
                const sampleCount = Object.keys(localStorage).filter(key => key.startsWith(number + "-")).length;
                document.getElementById('image_samples_button_' + number).innerText = sampleCount + " Samples";
            }

            if (Object.keys(localStorage).filter(key => key.startsWith(number + "-")).length === 0) {
                document.getElementById('imgSamples_' + number).innerHTML = "<div class='alert alert-primary'>No sample were made.</div>";
            }
        }).catch(function(error) {
            $('#content_' + number).load('models/method.php?number=' + number);
            alert(error);
        });
    }

    function back(number) {
        $('#content_' + number).load('models/method.php?number=' + number);
    }

    function back_webcam(number) {
        const mediaStream = document.getElementById('video_' + number).srcObject;

        if (mediaStream) {
            mediaStream.getTracks().forEach(function(track) {
                track.stop();
            });
        }

        $('#content_' + number).load('models/method.php?number=' + number);
    }

    // function read() {
    //     for (var i = 0; i < localStorage.length; i++) {
    //         var key = localStorage.key(i);
    //         var value = localStorage.getItem(key);
    //         console.log("Key: " + key + ", Value: " + value);
    //     }
    // }
</script>