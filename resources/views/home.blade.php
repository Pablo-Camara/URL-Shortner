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

            .form-box .form-box-title {
                font-size: 18px;
                font-weight: bold;
                margin-bottom: 10px;
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

                border: 1px solid #AAAAAA;
            }

            .form-box .input-container input.has-error {
                border: 1px solid red;
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
            }

            .form-box .button:hover {
                font-weight: bold;
                cursor: pointer;
            }

            .form-box .button.disabled {
                cursor: wait;
                background: gray;
            }

            .form-box a {
                color: #333333;
            }

            .form-box-feedback {
                margin-top: 8px;
            }

            .form-box-feedback.error {
                color: red;
            }

            .form-box-feedback.info {
                color: gray;
            }

            #generate-another-shortlink {
                width: 100%;
                margin-top: 5px;
                font-size: 14px;
                display: block;
            }

            .mtop-22 {
                margin-top: 22px;
            }
        </style>

        <script>
            window._authManager = {
                at: null,

                isAuthenticated: false,
                isLoggedIn: false,

                api: {
                    url: "{{ url('/api') }}",
                    endpoints: {
                        authentication: "/authenticate",
                        login: "/login",
                    },
                },

                customEvents: {
                    userAuthenticatedEvent: null,
                    userLoggedInEvent: null,
                    userLoginFailed: null,
                },

                initialize: function () {
                    this.customEvents.userAuthenticatedEvent =
                        document.createEvent("Event");
                    this.customEvents.userAuthenticatedEvent.initEvent(
                        "userAuthenticated",
                        true,
                        true
                    );

                    this.customEvents.userLoggedInEvent =
                        document.createEvent("Event");
                    this.customEvents.userLoggedInEvent.initEvent(
                        "userLoggedIn",
                        true,
                        true
                    );

                    this.customEvents.userLoginFailedEvent =
                        document.createEvent("Event");
                    this.customEvents.userLoginFailedEvent.initEvent(
                        "userLoginFailed",
                        true,
                        true
                    );

                    this.authenticate();
                },

                authenticate: function () {
                    var xhr = new XMLHttpRequest();
                    xhr.withCredentials = true;

                    xhr.addEventListener("readystatechange", function () {
                        if (this.status === 200 && this.readyState === 4) {
                            const resObj = JSON.parse(this.response);
                            window._authManager.at = resObj.at;
                            window._authManager.isAuthenticated = true;
                            window._authManager.isLoggedIn = resObj.guest
                                ? false
                                : true;

                            // trigger userAuthenticated event
                            document.dispatchEvent(
                                window._authManager.customEvents
                                    .userAuthenticatedEvent
                            );
                        }
                    });

                    xhr.open(
                        "POST",
                        this.api.url + this.api.endpoints.authentication
                    );
                    xhr.send();
                },

                login: function (email, password) {
                    if (this.isAuthenticated !== true) {
                        // must authenticate as guest first
                        return;
                    }

                    var xhr = new XMLHttpRequest();
                    xhr.withCredentials = true;

                    xhr.addEventListener("readystatechange", function () {
                        if (this.readyState === 4) {
                            const resObj = JSON.parse(this.response); //TODO: Catch exception

                            if (this.status === 200) {
                                window._authManager.at = resObj.at;
                                window._authManager.isLoggedIn = resObj.guest
                                    ? false
                                    : true;

                                // trigger userLoggedIn event
                                document.dispatchEvent(
                                    window._authManager.customEvents
                                        .userLoggedInEvent
                                );
                            }

                            if (this.status === 401) {
                                if (
                                    resObj.error_id === "incorrect_credentials"
                                ) {
                                    // trigger userLoginFailed event
                                    window._authManager.customEvents.userLoginFailedEvent.reason =
                                        resObj.message;
                                    window._authManager.customEvents.userLoginFailedEvent.isError = true;
                                    document.dispatchEvent(
                                        window._authManager.customEvents
                                            .userLoginFailedEvent
                                    );
                                }
                            }
                        }
                    });

                    const credentialsQueryStr =
                        "?email=" + email + "&password=" + password;
                    xhr.open(
                        "POST",
                        this.api.url +
                            this.api.endpoints.login +
                            credentialsQueryStr
                    );
                    xhr.setRequestHeader("Authorization", "Bearer " + this.at);
                    xhr.send();
                },
            };

            window._authManager.initialize();


        </script>
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
                    Cole aqui o seu URL loongo..
                </div>
                <input type="text" id="long-url" />
            </div>

            <div class="input-container">
                <div class="input-label" id="destination-email-label">
                    Email destino
                </div>
                <input type="text" id="destination-email" />
            </div>

            <div class="button disabled" id="generate-shortlink">Gerar Link Curto!</div>

            <div id="form-box-feedback" class="form-box-feedback" style="display: none"></div>
        </div>

        <div
            class="form-box"
            id="form-box-with-shortlink"
            style="display: none"
        >
            <div class="form-box-title">O seu link curto está pronto!</div>
            <div class="input-container">
                <input type="text" id="shortlink" readonly />
            </div>

            <div class="button" id="save-shortlink" style="display: none">Guardar nos meus links</div>
            <div class="button" id="go-to-my-links" style="display: none">Ver meus links curtos</div>
            <a href="#" id="generate-another-shortlink">Encurtar outro link</a>
        </div>

        <script>
            const formBox = document.getElementById("form-box");
            const formBoxFeedback = document.getElementById('form-box-feedback');
            const longUrlLabel = document.getElementById("long-url-label");
            const longUrlInput = document.getElementById("long-url");

            const emailLabel = document.getElementById(
                "destination-email-label"
            );
            const emailInput = document.getElementById("destination-email");

            const generateShortlinkBtn =
                document.getElementById("generate-shortlink");

            const formBoxWithShortlink = document.getElementById(
                "form-box-with-shortlink"
            );
            const shortlinkResultInput = document.getElementById("shortlink");
            const generateAnotherShortlinkLink = document.getElementById(
                "generate-another-shortlink"
            );
            const saveShortlinkBtn = document.getElementById('save-shortlink');
            const goToMyLinksBtn = document.getElementById('go-to-my-links');

            generateAnotherShortlinkLink.onclick = function () {
                shortlinkResultInput.value = "";
                formBoxWithShortlink.style.display = "none";
                longUrlInput.value = "";
                formBox.style.display = "block";
                longUrlInput.focus();
            };

            shortlinkResultInput.onclick = function (e) {
                e.target.focus();
                e.target.select();
            };

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

            longUrlInput.addEventListener("focusout", function (e) {
                e.target.value = e.target.value.trim();
                if (longUrlInput.value.length == 0) {
                    longUrlLabel.parentNode.classList.remove("active");
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

            emailInput.addEventListener("focusout", function (e) {
                e.target.value = e.target.value.trim();
                if (emailInput.value.length == 0) {
                    emailLabel.parentNode.classList.remove("active");
                    emailLabel.parentNode.classList.remove("mtop-22");
                }
            });

            generateShortlinkBtn.onclick = function (e) {

                if (!window._authManager.isAuthenticated) {
                    return false;
                }


                if (longUrlInput.value.length == 0) {
                    longUrlInput.classList.add('has-error');
                    return false;
                } else {
                    longUrlInput.classList.remove('has-error');
                }

                if (emailInput.value.length == 0) {
                    emailInput.classList.add('has-error');
                    return false;
                } else {
                    emailInput.classList.remove('has-error');
                }

                var xhr = new XMLHttpRequest();
                xhr.withCredentials = true;

                xhr.addEventListener("readystatechange", function () {
                    if (this.readyState === 4) {
                        try {
                            const jsonResObj = JSON.parse(this.responseText);

                            if (this.status === 201) {
                                formBox.style.display = "none";
                                formBoxFeedback.innerText = '';
                                formBoxFeedback.classList.remove('error');
                                formBoxFeedback.classList.remove('info');
                                formBoxFeedback.style.display = 'none';

                                shortlinkResultInput.value =
                                    jsonResObj.shortlink;
                                formBoxWithShortlink.style.display = "block";

                                if (window._authManager.isLoggedIn) {
                                    goToMyLinksBtn.style.display = 'block';
                                    saveShortlinkBtn.style.display = 'none';
                                } else {
                                    goToMyLinksBtn.style.display = 'none';
                                    saveShortlinkBtn.style.display = 'block';
                                }

                            }

                            if(this.status === 503) {
                                formBoxFeedback.innerText = jsonResObj.message;
                                formBoxFeedback.classList.add('error');
                                formBoxFeedback.classList.remove('info');
                                formBoxFeedback.style.display = 'block';
                            }

                            if(this.status === 500) {
                                formBoxFeedback.innerText = 'Ocorreu um erro no nosso servidor..';
                                formBoxFeedback.classList.add('error');
                                formBoxFeedback.classList.remove('info');
                                formBoxFeedback.style.display = 'block';
                            }
                        } catch (e) {
                            // invalid json something went wrong
                            formBoxFeedback.innerText = 'Ocorreu um erro no nosso servidor..';
                            formBoxFeedback.classList.add('error');
                            formBoxFeedback.classList.remove('info');
                            formBoxFeedback.style.display = 'block';
                        }
                    }
                });

                xhr.open(
                    "POST",
                    '{{ url("/api/shorten") }}?long_url=https://asas.com&destination_email=pablo@camara.pt'
                );
                xhr.setRequestHeader("Authorization", "Bearer " + window._authManager.at);

                formBoxFeedback.innerText = 'por favor espere..'
                formBoxFeedback.classList.add('info');
                formBoxFeedback.style.display = 'block';
                xhr.send();
            };



            document.addEventListener('userAuthenticated', (e) => {
                generateShortlinkBtn.classList.remove('disabled');
            }, false);


            if (window._authManager.isAuthenticated) {
                generateShortlinkBtn.classList.remove('disabled');
            }
        </script>
    </body>
</html>
