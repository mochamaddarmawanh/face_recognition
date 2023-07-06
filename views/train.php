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
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="imageSampleLabel">Class 1</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <img src="assets/img/other/me.png" alt="" class="img-fluid rounded-2 object-fit-contain w-100" style="width: 200px;">
                    <div class="row mt-4">
                        <div class="col-4 text-start">
                            <i class="bi-chevron-left"></i>
                        </div>
                        <div class="col-4 text-center">
                            <span>1 / 20</span>
                        </div>
                        <div class="col-4 text-end">
                            <i class="bi-chevron-right"></i>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="container-fluid p-0">
                        <div class="row">
                            <div class="col-9">
                                <select name="" id="" class="form-select w-100">
                                    <option value="">Class 1</option>
                                    <option value="">Class 2</option>
                                </select>
                            </div>
                            <div class="col-auto">
                                <button type="button" class="btn btn-danger" style="margin-left: 6px;"><i class="bi-trash"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $('#class').load('models/class.php');

        function add_new_class() {
            $.ajax({
                url: 'models/class.php',
                type: 'POST',
                dataType: 'html',
                success: function(response) {
                    $('#class').append(response);
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        }
    </script>

