<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <title>Laravel</title>

        <!-- Fonts -->
        <link
            href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap"
            rel="stylesheet"
        />

        <!-- Styles -->
        <style>
            html,
            body {
                margin: 0;
                padding: 0;
            }

            body {
                font-family: "Nunito", sans-serif;
            }

            .form-box {
                width: 260px;
                background: #ffffff;
                border-radius: 10px;
                box-shadow: 0 1px 3px 0 rgb(0 0 0 / 10%),
                    0 1px 2px 0 rgb(0 0 0 / 6%);
                padding: 10px 14px 20px;
                /* margin-left: 100px; */
                margin: auto;
                margin-top: 30px;
            }

            .form-box h1 {
                font-size: 22px;
                margin: 10px;
                margin-left: 0;
            }

            .form-box.has-active-input h1 {
                margin-bottom: 18px;
            }

            .form-box .input-container {
                position: relative;
                margin-bottom: 10px;
            }

            .form-box .input-container .input-label {
                position: absolute;
                left: 0;
                right: 0;
                bottom: 0;
                top: 0;
                padding: 10px;
                font-size: 14px;
            }

            .form-box .input-container.active .input-label {
                top: -26px;
                left: -10px;
                font-size: 12px;
                bottom: unset;
                padding-bottom: 0;
            }

            .form-box .input-container input {
                width: 100%;
                height: 40px;
                box-sizing: border-box;

                padding-left: 5px;
                font-size: 12px;
            }

            .form-box .button {
                padding: 10px;
                background: linear-gradient(
                    120deg,
                    rgb(102, 147, 179) 0%,
                    rgb(102, 157, 183) 25%,
                    rgb(100, 186, 196) 100%
                );
                color: white;
                border-radius: 5px;

                cursor: pointer;
            }

            .form-box .button:hover {
                font-weight: bold;
            }

            .mtop-22 {
                margin-top: 22px;
            }
        </style>
    </head>
    <body
        class="antialiased"
        style="
            background: url('https://www.inideia.com/wp-content/uploads/2019/11/fundo_2.jpg');
            background-size: cover;
        "
    >
        <div style="text-align: center; margin-top: 10px">
            <img
                src="https://www.inideia.com/wp-content/uploads/2019/01/logo_white.png"
                style="max-width: 300px"
            />
        </div>

        <div class="form-box" id="form-box">
            <h1>Encurtador de URLs</h1>
            <div class="input-container">
                <div class="input-label" id="long-url-label">
                    Cole aqui o seu URL..
                </div>
                <input type="text" id="long-url" />
            </div>

            <div class="input-container">
                <div class="input-label" id="email-label">
                    Digite aqui o seu Email
                </div>
                <input type="text" id="email" />
            </div>

            <div class="button">Gerar Link Curto!</div>
        </div>

        <script>
            const formBox = document.getElementById("form-box");
            const longUrlLabel = document.getElementById("long-url-label");
            const longUrlInput = document.getElementById("long-url");

            const emailLabel = document.getElementById("email-label");
            const emailInput = document.getElementById("email");

            longUrlLabel.onclick = function (e) {
                e.target.parentNode.classList.add("active");
                formBox.classList.add("has-active-input");
                longUrlInput.focus();
            };

            longUrlInput.onfocus = function (e) {
                e.target.parentNode.classList.add("active");
                formBox.classList.add("has-active-input");
                e.target.value = e.target.value.trim();
            };

            longUrlInput.addEventListener("focusout", function(e) {
                e.target.value = e.target.value.trim();
                if (
                    longUrlInput.value.length == 0
                ) {
                    longUrlLabel.parentNode.classList.remove('active');
                    formBox.classList.remove("has-active-input");
                }
            });

            emailLabel.onclick = function (e) {
                e.target.parentNode.classList.add("active");
                emailInput.focus();
            };

            emailInput.onfocus = function (e) {
                e.target.parentNode.classList.add("active");
                e.target.parentNode.classList.add("mtop-22");
                e.target.value = e.target.value.trim();
            };

            emailInput.addEventListener("focusout", function(e) {
                e.target.value = e.target.value.trim();
                if (
                    emailInput.value.length == 0
                ) {
                    emailLabel.parentNode.classList.remove('active');
                    emailLabel.parentNode.classList.remove('mtop-22');
                }
            });

        </script>
    </body>
</html>
