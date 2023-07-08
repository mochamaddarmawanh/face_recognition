<!-- content -->
<main class="container" style="margin-top: 200px;">
    <h1 class="fw-bold">Select Project</h1>
    <div class="row mt-3 flex-nowrap">
        <div class="col-4 mb-3" style="width: 20rem;">
            <div class="text-decoration-none" style="cursor: pointer;" onclick="train()">
                <div class="card transform-shadow">
                    <img src="assets/img/other/teaching.webp" class="card-img-top object-fit-contain" alt="From File Project" style="height: 13rem;">
                    <div class="card-body" style="height: 8rem;">
                        <h5 class="card-title">Training Model</h5>
                        <p class="card-text">Teach based on images, from files or your webcam.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-4 mb-3" style="width: 20rem;">
            <a href="test" class="text-decoration-none">
                <div class="card transform-shadow">
                    <img src="assets/img/other/face-api-js.gif" class="card-img-top object-fit-contain" alt="From Webcam Project" style="height: 13rem;">
                    <div class="card-body" style="height: 8rem;">
                        <h5 class="card-title">Test Model</h5>
                        <p class="card-text">Preview based on images, from files or your webcam.</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
</main>

<script>
    function setCookie(name, value, daysToExpire) {
        const date = new Date();
        date.setTime(date.getTime() + (daysToExpire * 24 * 60 * 60 * 1000));
        const expires = 'expires=' + date.toUTCString();
        document.cookie = name + '=' + value + '; ' + expires + '; path=/';
    }

    function train() {
        setCookie('newClass', 1, 1);
        window.location.href = "train";
    }
</script>