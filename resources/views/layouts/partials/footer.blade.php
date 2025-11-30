<footer>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-9 col-12">
                <ul class="footer-text">
                    <li>
                        &copy;
                        <span id="currentYear"></span> Bank Garansi | Customer Create | SINARMEADOW | MIS. All Rights Reserved.
                    </li>
                </ul>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        document.getElementById('currentYear').textContent = new Date().getFullYear();
                    });
                </script>
            </div>
            <div class="col-md-3 ">
                <ul class="footer-text text-end ">
                    <li class="bg-gray-800 rounded-md p-1">
                        <a class="nav-link text-bold text-amber-500 hover:text-amber-900 " href="https://sinarmeadow.com"
                            target="_blank">PT SINAR MEADOW OFFICIAL</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</footer>
