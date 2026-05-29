    </div><!-- end content -->

    <div class="footer">
        <div class="wrapper">

            <p>&copy;<?php echo date("Y"); ?> Personal Media Library</p>

        </div>
    </div>

    <!-- GLOBAL AUTH SCRIPT (Password Toggle) -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            const toggles = document.querySelectorAll(".toggle-password");

            toggles.forEach(function(toggle) {

                toggle.addEventListener("click", function() {

                    const targetId = this.getAttribute("data-target");
                    const input = document.getElementById(targetId);

                    if (!input) return;

                    if (input.type === "password") {
                        input.type = "text";
                        this.textContent = "🙈";
                    } else {
                        input.type = "password";
                        this.textContent = "👁";
                    }

                });

            });

        });
    </script>

    </body>

    </html>