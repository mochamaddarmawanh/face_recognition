    <!-- content -->
    <main class="container" style="margin-top: 200px; margin-bottom: 200px;">
        <h1 class="fw-bold">Training Model</h1>
        <div class="row mt-3">
            <div class="col-6 col-lg-5">
                <div id="class"></div>
                <button class="btn btn-outline-primary col-12" onclick="add_new_class()"><i class="bi-plus"></i> Add New class</button>
                <button class="btn btn-primary col-12 mt-2" onclick="train()">Train Model</button>
                <div class="alert alert-success alert-dismissible mt-3 visually-hidden" id="train_success">
                    <strong>Data gambar berhasil di latih!</strong> Untuk melakukan test pengenalan wajah silahkan kembali kehalaman sebelumnya halaman "Test Model".<br><br>If you want to remove all model please <span class="text-decoration-underline text-primary" style="cursor: pointer;" onclick="delete_all_model()">click here</span>.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <div class="alert alert-danger alert-dismissible mt-3 visually-hidden" id="train_fail"></div>
            </div>
        </div>
    </main>

    <!-- Image Sample Modal -->
    <div class="modal fade" id="imageSample" tabindex="-1" aria-labelledby="imageSampleLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content" id="modal_sample"></div>
        </div>
    </div>

    <!-- <button onclick="read()">read</button> -->
    <!-- <button onclick="test()">test</button> -->

    <script>
        // function read() {
        //     for (var i = 0; i < localStorage.length; i++) {
        //         var key = localStorage.key(i);
        //         var value = localStorage.getItem(key);
        //         console.log("Key: " + key + ", Value: " + value);
        //     }

        // for (var i = 0; i < getCookie('newClass'); i++) {
        //     const check_class_name = Object.keys(localStorage).filter(key => key.startsWith('class-' + i));
        //     if (check_class_name.length > 0) {
        //         console.log(localStorage.getItem('class-' + i));
        //     }
        // }

        //     console.log("=============================== end.");
        // }

        // function test() {
        //     var nameValue = document.getElementById('name').innerHTML;
        //     console.log(nameValue);
        // }

        $('#class').load('models/class.php?number=1');

        function blockUIMyCustom() {
            $.blockUI({
                message: '<div class="d-justify-content-center align-items-center"><p>Please wait...</p><p class="spinner-border text-white"></p></div>',
                css: {
                    backgroundColor: 'transparent',
                    color: '#fff',
                    border: '0'
                },
                overlayCSS: {
                    opacity: 0.5
                },
            });
        }

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

        function add_new_class() {
            let newClass = parseInt(getCookie('newClass')) + 1;

            $.ajax({
                url: 'models/class.php?number=' + newClass,
                type: 'POST',
                dataType: 'html',
                success: function(response) {
                    $('#class').append(response);
                    setCookie('newClass', newClass, 1);
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        }

        function train() {
            const count_class = parseInt(getCookie('newClass'));
            let imageDataArray = {};
            let className = {};

            for (let i = 0; i < localStorage.length; i++) {
                const key = localStorage.key(i);
                const value = localStorage.getItem(key);

                if (value.includes('data:image')) {
                    const cls = key.split('-')[0];
                    const id = key.split('-')[1];
                    const imageObj = {
                        id: id,
                        val: value
                    };

                    if (!imageDataArray.hasOwnProperty(cls)) {
                        imageDataArray[cls] = [];
                    }
                    imageDataArray[cls].push(imageObj);
                }
            }

            for (let i = 0; i < count_class; i++) {
                const empty_class = (i + 1).toString();
                const name = document.getElementById('span_class_name_' + empty_class);
                className[empty_class] = name.innerHTML;
            }

            if (Object.keys(imageDataArray).length === count_class) {
                if (count_class > 1) {
                    confirmText = "Anda yakin ingin melatih model-model ini?";
                } else {
                    confirmText = "Anda yakin ingin melatih model ini?";
                }

                if (confirm(confirmText)) {
                    blockUIMyCustom();
                    $.ajax({
                        url: 'models/training.php',
                        type: 'POST',
                        data: {
                            imageDataArray: JSON.stringify(imageDataArray),
                            className: JSON.stringify(className)
                        },
                        success: function(response) {
                            $.unblockUI();
                            if (response === "") {
                                alert('Data gambar berhasil di latih.');
                                alert('Untuk melakukan test pengenalan wajah silahkan kembali kehalaman sebelumnya halaman "Test Model".');
                                document.getElementById('train_success').classList.remove('visually-hidden');
                                document.getElementById('train_fail').classList.add('visually-hidden');
                            } else {
                                alert(response);
                                document.getElementById('train_fail').classList.remove('visually-hidden');
                                document.getElementById('train_fail').innerHTML = `
                                    <strong>` + response + `</strong> Please rename the class to a different name and try again.
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                `;
                                document.getElementById('train_success').classList.add('visually-hidden');
                            }
                        },
                        error: function(xhr, status, error) {
                            alert('Maaf telah terjadi kesalahan saat melatih model, silahkan ulangi kembali.');
                            console.log(xhr.responseText);
                            console.log(status);
                            console.log(error);
                        }
                    });
                }
            } else {
                $.unblockUI();

                let empty_class = '';

                for (let i = 1; i <= count_class; i++) {
                    if (!imageDataArray.hasOwnProperty(i.toString())) {
                        empty_class = i.toString();
                        break;
                    }
                }

                if (empty_class !== '') {
                    alert('Kelas "' + document.getElementById('span_class_name_' + empty_class).innerHTML + '" memerlukan setidaknya 1 sampel.');
                }
            }
        }

        function delete_all_model() {
            localStorage.clear();
            setCookie('newClass', 1, 1);
            $('#class').load('models/class.php?number=1');
        }
    </script>