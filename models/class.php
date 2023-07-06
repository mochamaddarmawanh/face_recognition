<div class="card shadow-sm mb-3">
    <div class="card-header bg-white">
        <div class="row">
            <div class="col-11">
                <h5>Class 1 &nbsp;<a href="#" class="text-decoration-none text-secondary"><i class="bi-pencil" style="font-size: 20px;"></i></a></h5>
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
    <div class="card-body" id="content">
        <p>Add Image Samples:</p>
        <button class="btn btn-sm btn-primary" onclick="webcam()">
            <i class="bi-camera-video"></i>
            <br>
            Webcam
        </button>
        <button class="btn btn-sm btn-primary" onclick="file()">
            <i class="bi-upload"></i>
            <br>
            Upload
        </button>
        <button class="btn btn-sm btn-primary" onclick="preview()">
            <i class="bi-image"></i>
            <br>
            <span id="image_samples_button"></span>
        </button>
    </div>
</div>

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
    });

    if (localStorage.length === 0) {
        document.getElementById('image_samples_button').innerText = "0 Sample";
    } else {
        document.getElementById('image_samples_button').innerText = localStorage.length + " Samples";
    }

    function lengthImageLocalData() {
        const image_samples = document.getElementById('image_samples');

        if (localStorage.length === 0) {
            image_samples.innerText = "Add Image Samples:";
        } else {
            image_samples.innerText = localStorage.length + " Image Samples";
        }
    }

    function displayImageLocalData() {
        const imgSamplesDiv = document.getElementById('imgSamples');

        const imageKeys = [];
        for (let i = 0; i < localStorage.length; i++) {
            const key = localStorage.key(i);
            imageKeys.push(key);
        }

        imageKeys.sort(function(a, b) {
            return b - a;
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

    function webcam() {
        new Promise(function(resolve, reject) {
            $('#content').load('models/webcam.php', function() {
                resolve();
            }, function(error) {
                reject(error);
            });
        }).then(function() {
            return navigator.mediaDevices.getUserMedia({
                video: true
            });
        }).then(function(mediaStream) {
            lengthImageLocalData();
            displayImageLocalData();

            const video = document.getElementById('video');
            video.srcObject = mediaStream;

            getAvailableWebcams().then(function(webcams) {
                const webcam_select = document.getElementById('webcam_select');
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
        }).catch(function(error) {
            $('#content').load('models/method.php');
            alert('There was an error opening your webcam. Make sure permissions are enabled or switch to image uploading.');
        });
    }

    function change_webcam() {
        const selectedWebcam = document.getElementById('webcam_select').value;

        navigator.mediaDevices.getUserMedia({
            video: {
                deviceId: selectedWebcam
            }
        }).then(function(stream) {
            const video = document.getElementById('video');
            video.srcObject = stream;
        }).catch(function(error) {
            alert('Error accessing webcam:', error);
        });
    }

    function file() {
        new Promise(function(resolve, reject) {
            $('#content').load('models/file.php', function() {
                resolve();
            }, function(error) {
                reject(error);
            });
        }).then(function(mediaStream) {
            lengthImageLocalData();
            displayImageLocalData();
        }).catch(function(error) {
            $('#content').load('models/method.php');
            alert(error);
        });
    }

    function preview() {
        new Promise(function(resolve, reject) {
            $('#content').load('models/preview.php', function() {
                resolve();
            }, function(error) {
                reject(error);
            });
        }).then(function(mediaStream) {
            lengthImageLocalData();
            displayImageLocalData();
        }).catch(function(error) {
            $('#content').load('models/method.php');
            alert(error);
        });
    }

    function back() {
        $('#content').load('models/method.php');
    }

    function back_webcam() {
        const mediaStream = document.getElementById('video').srcObject;

        if (mediaStream) {
            mediaStream.getTracks().forEach(function(track) {
                track.stop();
            });
        }

        $('#content').load('models/method.php');
    }

    // for (var i = 0; i < localStorage.length; i++) {
    //     var key = localStorage.key(i);
    //     var value = localStorage.getItem(key);
    //     console.log("Key: " + key + ", Value: " + value);
    // }
</script>