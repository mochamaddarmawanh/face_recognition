<div class="card shadow-sm mb-3">
    <div class="card-header bg-white">
        <div class="row">
            <div class="col-11">
                <div id="class_name" onclick="change_class_name(event, <?= $_GET['number'] ?>)">
                    <span class="h5" contenteditable="true" id="span_class_name_<?= $_GET['number'] ?>">Class <?= $_GET['number'] ?></span>&nbsp;&nbsp;
                    <span class="text-decoration-none text-secondary" style="cursor: pointer;" onclick="pencil_click(event, <?= $_GET['number'] ?>)">
                        <i class="bi-pencil" style="font-size: 20px;"></i>
                    </span>
                </div>
            </div>
            <div class="col-1">
                <div class="dropdown">
                    <i class="bi-three-dots-vertical mt-1" type="button" data-bs-toggle="dropdown" aria-expanded="false"></i>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item disabled" onclick="delete_class(<?= $_GET['number'] ?>)">Delete Class</a></li>
                        <li><span class="dropdown-item" style="cursor: pointer;" onclick="remove_all_samples(<?= $_GET['number'] ?>)">Remove All Samples</span></li>
                        <li><span class="dropdown-item" style="cursor: pointer;" onclick="download_samples(<?= $_GET['number'] ?>)">Download Samples</span></li>
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

    function change_class_name(event, number) {
        const span = event.target;
        span.addEventListener('keydown', function(e) {
            handleKeyPress(e, span, number);
        });
        span.addEventListener('blur', function(e) {
            save_class_name(span.innerText, number);
        });
    }

    function pencil_click(event, number) {
        const span = event.target.parentElement.previousElementSibling;
        span.contentEditable = true;
        span.focus();
        span.addEventListener('keydown', function(e) {
            handleKeyPress(e, span, number);
        });
        span.addEventListener('blur', function(e) {
            save_class_name(span.innerText, number);
        });
    }

    function handleKeyPress(e, span, number) {
        if (e.key === 'Enter') {
            e.preventDefault();
            span.blur();
            span.removeEventListener('keydown', function(event) {
                handleKeyPress(event, span, number);
            });
            save_class_name(span.innerText, number);
        }
    }

    function save_class_name(data, number) {
        replace = data.replace(/[^a-zA-Z0-9]/g, ' ');

        document.getElementById('span_class_name_' + number).innerText = replace;

        localStorage.setItem('class-' + number, replace);
    }

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
                new Promise(function(resolve, reject) {
                    $('#modal_sample').load('models/modal_sample.php?number=' + number + '&key=' + key + '&index=' + parseInt(index + 1), function() {
                        resolve();
                    }, function(error) {
                        reject(error);
                    });
                }).then(function(mediaStream) {
                    const check_class_name = Object.keys(localStorage).filter(key => key.startsWith('class-' + number));

                    if (check_class_name.length > 0) {
                        document.getElementById('imageSampleLabel').innerText = localStorage.getItem('class-' + number);
                    }
                }).catch(function(error) {
                    console.log(error);
                });
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

    function delete_class(number) {
        // const newClass = parseInt(getCookie('newClass'));

        // if (newClass === 1) {
        //     alert('minimum of class at least one');
        //     return;
        // }

        // setCookie('newClass', newClass - 1, 1);

        // document.getElementById('class').innerHTML = "";

        // for (let i = 1; i <= parseInt(getCookie('newClass')); i++) {
        //     $.ajax({
        //         url: 'models/class.php?number=' + i,
        //         type: 'POST',
        //         dataType: 'html',
        //         success: function(response) {
        //             $('#class').append(response);

        //             const check_class_name = Object.keys(localStorage).filter(key => key.startsWith('class-' + i));

        //             if (check_class_name.length > 0) {
        //                 document.getElementById('class_name').innerText = localStorage.getItem('class-' + i);
        //             }
        //         },
        //         error: function(xhr, status, error) {
        //             console.error(error);
        //         }
        //     });
        // }

        alert('under contruction');
    }

    function remove_all_samples(number) {
        const keys = Object.keys(localStorage).filter(key => key.startsWith(number + "-"));

        keys.forEach(key => {
            localStorage.removeItem(key);
        });

        const imgSamples = document.getElementById('imgSamples_' + number);

        if (imgSamples) {
            lengthImageLocalData(number);
            displayImageLocalData(number);

            imgSamples.innerHTML = "<div class='alert alert-primary'>No sample were made.</div>";
        } else {
            document.getElementById('image_samples_button_' + number).innerText = "0 Sample";
        }
    }

    function download_samples(number) {
        const keys = Object.keys(localStorage).filter(key => key.startsWith(number + "-"));

        if (keys.length === 0) {
            alert('No images found in storage for this class');
            return;
        }

        const zip = new JSZip();

        keys.forEach(key => {
            const imageData = localStorage.getItem(key);

            if (!imageData) {
                console.error(`Image data not found in storage for the key: ${key}`);
                return;
            }

            const base64Data = imageData.split(',')[1];

            zip.file(key + '.jpeg', base64Data, {
                base64: true
            });
        });

        const span_class_name = document.getElementById('span_class_name_' + number).innerHTML;

        zip.generateAsync({
            type: 'blob'
        }).then(blob => {
            saveAs(blob, 'image_samples_' + span_class_name.toLowerCase().replace(/\s/g, '_') + '.zip');
        }).catch(error => {
            console.error('Error creating the ZIP file:', error);
        });
    }
</script>