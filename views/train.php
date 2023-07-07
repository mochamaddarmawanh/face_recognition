    <!-- content -->
    <main class="container" style="margin-top: 200px; margin-bottom: 200px;">
        <h1 class="fw-bold">Training Model</h1>
        <div class="row mt-3">
            <div class="col-6 col-lg-5">
                <div id="class"></div>
                <button class="btn btn-outline-primary col-12" onclick="add_new_class()"><i class="bi-plus"></i> Add New class</button>
                <button class="btn btn-primary col-12 mt-2" disabled>Train Model</button>
            </div>
        </div>
    </main>

    <!-- Image Sample Modal -->
    <div class="modal fade" id="imageSample" tabindex="-1" aria-labelledby="imageSampleLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content" id="modal_sample"></div>
        </div>
    </div>

    <script>
        $('#class').load('models/class.php?number=1');

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
                    console.error('Error:', error);
                }
            });
        }
    </script>