<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <title>InIdeia.com - Encurtador de Urls</title>

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

                position: relative;
            }

            .form-box .close-form-box {
                position: absolute;
                top: 10px;
                right: 15px;
                color: red;
                font-size: 16px;
                cursor: pointer;
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

            .form-box .button.red {
                background: linear-gradient(
                    120deg,
                    rgb(255 0 0) 0%,
                    rgb(255 65 65) 25%,
                    rgb(255 204 204) 100%
                );
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

            .form-link {
                width: 100%;
                margin-top: 5px;
                font-size: 14px;
                display: block;
            }

            #resend-verification-email-link {
                margin-bottom: 5px;
            }

            #my-account-link {
                text-align: center;
            }

            #form-box-login-feedback,
            #form-box-register-feedback {
                margin-bottom: 4px;
                font-size: 14px;
            }

            .mtop-22 {
                margin-top: 22px;
            }

            .form-box .list-container {

            }

            .form-box .list-container .list-item {
                margin-bottom: 10px;
                border-bottom: 1px solid #EEEEEE;
                padding-bottom: 4px;
            }

            .form-box .list-container .list-item .short-url {

            }

            .form-box .list-container .list-item .long-url {
                color: gray;
                overflow: hidden;
                word-break: break-word;
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
                        register: "/register",
                        logout: "/logout",
                        resendVerificationEmail: "/resend-verification-email",
                        recoverPassword: "/recover-password",
                        changePassword: "/change-password"
                    },
                },

                customEvents: {
                    userAuthenticatedEvent: null,
                    userLoggedInEvent: null,
                    userLoginFailedEvent: null,
                    userRegisterFailedEvent: null,
                    userRegisterSuccessEvent: null,
                    userPasswordChangedEvent: null
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

                    this.customEvents.userRegisterFailedEvent =
                        document.createEvent("Event");
                    this.customEvents.userRegisterFailedEvent.initEvent(
                        "userRegisterFailed",
                        true,
                        true
                    );

                    this.customEvents.userRegisterSuccessEvent =
                        document.createEvent("Event");
                    this.customEvents.userRegisterSuccessEvent.initEvent(
                        "userRegisterSuccess",
                        true,
                        true
                    );

                    this.customEvents.userPasswordChangedEvent =
                        document.createEvent("Event");
                    this.customEvents.userPasswordChangedEvent.initEvent(
                        "userPasswordChanged",
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
                logout: function () {
                    if (this.isAuthenticated !== true) {
                        // must authenticate as guest first
                        return;
                    }

                    var xhr = new XMLHttpRequest();
                    xhr.withCredentials = true;


                    xhr.addEventListener("readystatechange", function () {
                        if (this.readyState === 4) {
                            if (this.status === 200) {
                                window.location.reload();
                            }
                        }
                    });

                    xhr.open(
                        "POST",
                        this.api.url + this.api.endpoints.logout
                    );
                    xhr.setRequestHeader("Authorization", "Bearer " + this.at);
                    xhr.send();
                },
                login: function (email, password, captchaToken) {
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

                            if (
                                typeof resObj.message !== 'undefined'
                                &&
                                (
                                    typeof resObj.errors !== 'undefined'
                                    ||
                                    typeof resObj.error_id !== 'undefined'
                                )
                            ) {
                                // trigger userLoginFailed event
                                window._authManager.customEvents.userLoginFailedEvent.reason =
                                    resObj.message;
                                window._authManager.customEvents.userLoginFailedEvent.isError = true;
                                window._authManager.customEvents.userLoginFailedEvent.error_id = resObj.error_id;

                                document.dispatchEvent(
                                    window._authManager.customEvents
                                        .userLoginFailedEvent
                                );

                            }
                        }
                    });


                    email = encodeURIComponent(email);
                    password = encodeURIComponent(password);

                    const credentialsQueryStr =
                        "?email=" + email + "&password=" + password + '&g-recaptcha-response=' + captchaToken;
                    xhr.open(
                        "POST",
                        this.api.url +
                            this.api.endpoints.login +
                            credentialsQueryStr
                    );
                    xhr.setRequestHeader("Authorization", "Bearer " + this.at);
                    xhr.send();
                },
                register: function (
                    name,
                    email,
                    emailConfirmation,
                    password,
                    passwordConfirmation,
                    captchaToken
                ) {
                    if (this.isAuthenticated !== true) {
                        // must authenticate as guest first
                        return;
                    }

                    var xhr = new XMLHttpRequest();
                    xhr.withCredentials = true;

                    xhr.addEventListener("readystatechange", function () {
                        if (this.readyState === 4) {
                            const resObj = JSON.parse(this.response); //TODO: Catch exception

                            if (this.status === 201) {

                                if(
                                    typeof resObj.at !== 'undefined'
                                    &&
                                    typeof resObj.isLoggedIn !== 'undefined'
                                ) {
                                    window._authManager.at = resObj.at;
                                    window._authManager.isLoggedIn = resObj.guest
                                        ? false
                                        : true;

                                    // trigger userLoggedIn event
                                    document.dispatchEvent(
                                        window._authManager.customEvents
                                            .userLoggedInEvent
                                    );
                                    return;
                                }

                                if (
                                    typeof resObj.success !== 'undefined'
                                ) {
                                    if (resObj.success == 1) {
                                        document.dispatchEvent(
                                            window._authManager.customEvents
                                                .userRegisterSuccessEvent
                                        );
                                    } else {
                                        document.dispatchEvent(
                                            window._authManager.customEvents
                                                .userRegisterFailedEvent
                                        );
                                    }

                                    return;
                                }
                            }

                            if (
                                typeof resObj.message !== 'undefined'
                                &&
                                (
                                    typeof resObj.errors !== 'undefined'
                                    ||
                                    typeof resObj.error_id !== 'undefined'
                                )
                            ) {
                                // trigger userRegisterFailed event
                                window._authManager.customEvents.userRegisterFailedEvent.reason =
                                    resObj.message;
                                window._authManager.customEvents.userRegisterFailedEvent.isError = true;
                                document.dispatchEvent(
                                    window._authManager.customEvents
                                        .userRegisterFailedEvent
                                );

                            }
                        }
                    });

                    name = encodeURIComponent(name);
                    email = encodeURIComponent(email);
                    emailConfirmation = encodeURIComponent(emailConfirmation);
                    password = encodeURIComponent(password);
                    passwordConfirmation = encodeURIComponent(passwordConfirmation);

                    const credentialsQueryStr =
                        "?name=" + name + "&email=" + email + "&email_confirmation=" + emailConfirmation + "&password=" + password + "&password_confirmation=" + passwordConfirmation+ '&g-recaptcha-response=' + captchaToken;;
                    xhr.open(
                        "POST",
                        this.api.url +
                            this.api.endpoints.register +
                            credentialsQueryStr
                    );
                    xhr.setRequestHeader("Authorization", "Bearer " + this.at);
                    xhr.send();
                },
                resendVerificationEmail: function (email, captchaToken) {
                    if (this.isAuthenticated !== true) {
                        // must authenticate as guest first
                        return;
                    }

                    var xhr = new XMLHttpRequest();
                    xhr.withCredentials = true;
                    email = encodeURIComponent(email);

                    const credentialsQueryStr =
                        "?email=" + email + '&g-recaptcha-response=' + captchaToken;

                    xhr.open(
                        "POST",
                        this.api.url +
                            this.api.endpoints.resendVerificationEmail +
                            credentialsQueryStr
                    );
                    xhr.setRequestHeader("Authorization", "Bearer " + this.at);
                    xhr.send();
                },
                sendPasswordRecoveryEmail: function (email, captchaToken) {
                    if (this.isAuthenticated !== true) {
                        // must authenticate as guest first
                        return;
                    }

                    var xhr = new XMLHttpRequest();
                    xhr.withCredentials = true;
                    email = encodeURIComponent(email);

                    const credentialsQueryStr =
                        "?email=" + email + '&g-recaptcha-response=' + captchaToken;

                    xhr.open(
                        "POST",
                        this.api.url +
                            this.api.endpoints.recoverPassword +
                            credentialsQueryStr
                    );
                    xhr.setRequestHeader("Authorization", "Bearer " + this.at);
                    xhr.send();
                },
                changePassword: function (newPassword, newPasswordConfirmation, captchaToken) {
                    if (this.isAuthenticated !== true) {
                        // must authenticate as guest first
                        return;
                    }

                    if(typeof this.passwordRecoveryToken === 'undefined') {
                        // pwd recovery token needed
                        return;
                    }

                    var xhr = new XMLHttpRequest();
                    xhr.withCredentials = true;

                    newPassword = encodeURIComponent(newPassword);
                    newPasswordConfirmation = encodeURIComponent(newPasswordConfirmation);

                    const credentialsQueryStr =
                        "?new_password=" + newPassword + '&new_password_confirmation=' + newPasswordConfirmation + '&g-recaptcha-response=' + captchaToken;


                    xhr.addEventListener("readystatechange", function () {
                        if (this.readyState === 4) {

                            if (this.status === 200) {

                                // trigger userLoggedIn event
                                document.dispatchEvent(
                                    window._authManager.customEvents
                                        .userPasswordChangedEvent
                                );
                                return;
                            }

                            const resObj = JSON.parse(this.response); //TODO: Catch exception

                            if (
                                typeof resObj.message !== 'undefined'
                                &&
                                (
                                    typeof resObj.errors !== 'undefined'
                                    ||
                                    typeof resObj.error_id !== 'undefined'
                                )
                            ) {
                                // trigger userLoginFailed event
                                /*window._authManager.customEvents.userLoginFailedEvent.reason =
                                    resObj.message;
                                window._authManager.customEvents.userLoginFailedEvent.isError = true;
                                window._authManager.customEvents.userLoginFailedEvent.error_id = resObj.error_id;

                                document.dispatchEvent(
                                    window._authManager.customEvents
                                        .userLoginFailedEvent
                                );*/

                            }
                        }
                    });

                    xhr.open(
                        "POST",
                        this.api.url +
                            this.api.endpoints.changePassword +
                            credentialsQueryStr
                    );
                    xhr.setRequestHeader("Authorization", "Bearer " + this.passwordRecoveryToken);
                    xhr.send();
                }
            };

            window._authManager.initialize();




            window.App = {
                Views: {
                    ShortenUrl: {
                        el: function () {
                            return document.getElementById("form-box");
                        },
                        show: function () {
                            this.initialize();
                            this.el().style.display = 'block';
                            const longUrlInput = this.Components.LongUrl.el();
                            longUrlInput.value = "";
                            longUrlInput.focus();

                            if (window._authManager.isAuthenticated) {
                                this.Components.GenerateBtn.enable();
                            }
                        },
                        hide: function() {
                            this.el().style.display = 'none';
                            this.Components.Feedback.hide();
                        },
                        Components: {
                            Feedback: {
                                el: function () {
                                    return document.getElementById('form-box-feedback');
                                },
                                hide: function () {
                                    const el = this.el();
                                    el.innerText = '';
                                    el.classList.remove('error');
                                    el.classList.remove('info');
                                    el.style.display = 'none';
                                },
                                showError: function (message) {
                                    const el = this.el();
                                    el.innerText = message;
                                    el.classList.add('error');
                                    el.classList.remove('info');
                                    el.style.display = 'block';
                                },
                                showInfo: function (message) {
                                    const el = this.el();
                                    el.innerText = message;
                                    el.classList.add('info');
                                    el.classList.remove('error');
                                    el.style.display = 'block';
                                }
                            },
                            LongUrl: {
                                hasInitialized: false,
                                el: function() {
                                    return document.getElementById("long-url");
                                },
                                labelEl: function () {
                                    return document.getElementById("long-url-label");
                                },
                                initialize: function () {
                                    if (this.hasInitialized === false) {
                                        const $this = this;
                                        this.labelEl().onclick = function (e) {
                                            e.target.parentNode.classList.add("active");
                                            window.App.Views.ShortenUrl.el().classList.add("has-active-input");
                                            $this.el().focus();
                                        }

                                        this.el().onfocus = function (e) {
                                            e.target.parentNode.classList.add("active");
                                            e.target.value = e.target.value.trim();
                                            window.App.Views.ShortenUrl.el().classList.add("has-active-input");
                                        };

                                        this.el().addEventListener("focusout", function (e) {
                                            e.target.value = e.target.value.trim();
                                            if (e.target.value.length == 0) {
                                                $this.labelEl().parentNode.classList.remove("active");
                                                window.App.Views.ShortenUrl.el().classList.remove("has-active-input");
                                            }
                                        });

                                        this.hasInitialized = true;
                                    }
                                }
                            },
                            DestinationEmail: {
                                el: function () {
                                    return document.getElementById("destination-email");
                                },
                                labelEl: function () {
                                    return document.getElementById("destination-email-label");
                                },
                                initialize: function () {
                                    const $this = this;
                                    this.labelEl().onclick = function (e) {
                                        e.target.parentNode.classList.add("active");
                                        $this.el().focus();
                                    };

                                    this.el().onfocus = function (e) {
                                        e.target.parentNode.classList.add("active");
                                        e.target.parentNode.classList.add("mtop-22");
                                        e.target.value = e.target.value.trim();
                                    };

                                    this.el().addEventListener("focusout", function (e) {
                                        e.target.value = e.target.value.trim();
                                        if (e.target.value.length == 0) {
                                            $this.labelEl().parentNode.classList.remove("active");
                                            $this.labelEl().parentNode.classList.remove("mtop-22");
                                        }
                                    });
                                }
                            },
                            GenerateBtn: {
                                hasInitialized: false,
                                el: function () {
                                    return document.getElementById("generate-shortlink");
                                },
                                enable: function () {
                                    this.el().classList.remove('disabled');
                                },
                                initialize: function () {
                                    if (this.hasInitialized !== false) {
                                        return;
                                    }
                                    // code to initialize once:
                                    this.el().onclick = function (e) {
                                        if (
                                            !window._authManager.isAuthenticated
                                            ||
                                            e.target.classList.contains('disabled')
                                        ) {
                                            return false;
                                        }

                                        const longUrlInput = window.App.Views.ShortenUrl.Components.LongUrl.el();
                                        const destinationEmailInput = window.App.Views.ShortenUrl.Components.DestinationEmail.el();

                                        if (longUrlInput.value.length == 0) {
                                            longUrlInput.classList.add('has-error');
                                            return false;
                                        } else {
                                            longUrlInput.classList.remove('has-error');
                                        }

                                        grecaptcha.ready(function() {
                                            grecaptcha.execute('{{ $captchaSitekey }}', {action: 'submit'}).then(function(token) {
                                                var xhr = new XMLHttpRequest();
                                                xhr.withCredentials = true;

                                                xhr.addEventListener("readystatechange", function () {
                                                    if (this.readyState === 4) {
                                                        try {
                                                            const jsonResObj = JSON.parse(this.responseText);

                                                            if (this.status === 201) {
                                                                window.App.Views.ShortenUrl.hide();
                                                                window.App.Views.ShortlinkResult.Components.Shortlink.set(
                                                                    jsonResObj.shortlink
                                                                );
                                                                window.App.Views.ShortlinkResult.show();
                                                                return;
                                                            }

                                                            if(typeof jsonResObj.message !== 'undefined') {
                                                                window.App.Views.ShortenUrl.Components.Feedback.showError(jsonResObj.message);
                                                            } else {
                                                                window.App.Views.ShortenUrl.Components.Feedback.showError('Ocorreu um erro no nosso servidor..');
                                                            }

                                                            e.target.classList.remove('disabled');
                                                        } catch (e) {
                                                            // invalid json something went wrong
                                                            window.App.Views.ShortenUrl.Components.Feedback.showError('Ocorreu um erro no nosso servidor..');
                                                        }
                                                    }
                                                });

                                                var paramsStr = 'long_url='+ longUrlInput.value;
                                                if (destinationEmailInput.value.length > 0) {
                                                    paramsStr += '&destination_email=' + destinationEmailInput.value;
                                                }
                                                paramsStr += '&g-recaptcha-response=' + token;

                                                xhr.open(
                                                    "POST",
                                                    '{{ url("/api/shorten") }}?' + paramsStr
                                                );
                                                xhr.setRequestHeader("Authorization", "Bearer " + window._authManager.at);

                                                // disable generate button to prevent double requests
                                                e.target.classList.add('disabled');

                                                window.App.Views.ShortenUrl.Components.Feedback.showInfo('por favor espere..');
                                                xhr.send();
                                            });
                                        });
                                    };
                                    this.hasInitialized = true;
                                }
                            },
                            MyAccountLink: {
                                hasInitialized: false,
                                el: function () {
                                    return document.getElementById("my-account-link");
                                },
                                show: function () {
                                    this.el().style.display = 'block';
                                },
                                initialize: function () {
                                    if (this.hasInitialized === false) {
                                        this.el().onclick = function (e) {
                                            window.App.Views.ShortenUrl.hide();
                                            window.App.Views.MyAccount.show();
                                        };

                                        this.hasInitialized = true;
                                    }
                                }
                            }
                        },
                        initialize: function () {
                            this.Components.LongUrl.initialize();
                            this.Components.DestinationEmail.initialize();
                            this.Components.GenerateBtn.initialize();
                            this.Components.MyAccountLink.initialize();
                        }
                    },
                    ShortlinkResult: {
                        el: function () {
                            return document.getElementById("form-box-with-shortlink");
                        },
                        show: function () {
                            this.initialize();
                            this.el().style.display = "block";

                            if (window._authManager.isLoggedIn) {
                                this.Components.GotoMyLinksBtn.show();
                                this.Components.SaveShortlinkBtn.hide();
                            } else {
                                this.Components.GotoMyLinksBtn.hide();
                                this.Components.SaveShortlinkBtn.show();
                            }
                        },
                        hide: function () {
                            this.el().style.display = "none";
                        },
                        Components: {
                            Shortlink: {
                                hasInitialized: false,
                                el: function () {
                                    return document.getElementById("shortlink");
                                },
                                set: function (value) {
                                    this.el().value = value;
                                },
                                initialize: function () {
                                    if (this.hasInitialized === false) {
                                        this.el().onclick = function (e) {
                                            e.target.focus();
                                            e.target.select();
                                        };
                                        this.hasInitialized = true;
                                    }
                                }
                            },
                            SaveShortlinkBtn: {
                                hasInitialized: false,
                                el: function () {
                                    return document.getElementById('save-shortlink');
                                },
                                show: function () {
                                    this.el().style.display = 'block';
                                },
                                hide: function () {
                                    this.el().style.display = 'none';
                                },
                                initialize: function () {
                                    if (this.hasInitialized === false) {
                                        this.el().onclick = function (e) {
                                            window.App.Views.ShortlinkResult.hide();
                                            window.App.Views.Login.show();
                                            window.App.previousView = window.App.Views.ShortlinkResult.el().id;
                                        };
                                        this.hasInitialized = true;
                                    }
                                }

                            },
                            GotoMyLinksBtn: {
                                hasInitialized: false,
                                el: function() {
                                    return document.getElementById('go-to-my-links');
                                },
                                show: function () {
                                    this.el().style.display = 'block';
                                },
                                hide: function () {
                                    this.el().style.display = 'none';
                                },
                                initialize: function () {
                                    if (this.hasInitialized === false) {
                                        this.el().onclick = function () {
                                            window.App.Views.ShortlinkResult.hide();
                                            window.App.Views.MyAccount.show();
                                        };
                                        this.hasInitialized = true;
                                    }
                                }
                            },
                            GenerateAnotherLink: {
                                hasInitialized: false,
                                el: function () {
                                    return document.getElementById("generate-another-shortlink");
                                },
                                initialize: function () {
                                    if (this.hasInitialized === false) {
                                        this.el().onclick = function () {
                                            window.App.Views.ShortenUrl.show();
                                            window.App.Views.ShortlinkResult.hide();
                                        };
                                        this.hasInitialized = true;
                                    }
                                }
                            },
                        },
                        initialize: function () {
                            this.Components.Shortlink.initialize();
                            this.Components.GotoMyLinksBtn.initialize();
                            this.Components.SaveShortlinkBtn.initialize();
                            this.Components.GenerateAnotherLink.initialize();
                        }

                    },
                    RegisterAvailableShortlink: {
                        el: function () {
                            return document.getElementById("form-box-shortlink-requested");
                        },
                        show: function () {
                            this.initialize();
                            this.el().style.display = "block";
                            this.Components.RequestedShortlinkLongUrl.el().focus();
                        },
                        hide: function () {
                            this.el().style.display = "none";
                        },
                        Components: {
                            RequestedShortlink: {
                                el: function () {
                                    return document.getElementById('requested-shortlink');
                                },
                                getShortstring: function () {
                                    return this.el().dataset.shortstring;
                                }
                            },
                            RequestedShortlinkLongUrl: {
                                el: function () {
                                    return document.getElementById('requested-shortlink-long-url');
                                },
                            },
                            Feedback: {
                                el: function () {
                                    return document.getElementById('requested-shortlink-register-feedback');
                                },
                                hide: function () {
                                    const el = this.el();
                                    el.innerText = '';
                                    el.classList.remove('error');
                                    el.classList.remove('info');
                                    el.style.display = 'none';
                                },
                                showError: function (message) {
                                    const el = this.el();
                                    el.innerText = message;
                                    el.classList.add('error');
                                    el.classList.remove('info');
                                    el.style.display = 'block';
                                },
                                showInfo: function (message) {
                                    const el = this.el();
                                    el.innerText = message;
                                    el.classList.add('info');
                                    el.classList.remove('error');
                                    el.style.display = 'block';
                                }
                            },
                            ContinueBtn: {
                                hasInitialized: false,
                                el: function () {
                                    return document.getElementById('shortlink-register');
                                },
                                enable: function () {
                                    this.el().classList.remove('disabled');
                                },
                                initialize: function () {
                                    if (this.hasInitialized === false) {
                                        this.el().onclick = function (e) {
                                            if (
                                                !window._authManager.isAuthenticated
                                                ||
                                                e.target.classList.contains('disabled')
                                            ) {
                                                return false;
                                            }

                                            const shortStr = window.App.Views.RegisterAvailableShortlink.Components.RequestedShortlink.getShortstring();
                                            const longUrlField = window.App.Views.RegisterAvailableShortlink.Components.RequestedShortlinkLongUrl.el();

                                            if (longUrlField.value.length == 0) {
                                                longUrlField.classList.add('has-error');
                                                return;
                                            }

                                            longUrlField.classList.remove('has-error');

                                            // disable generate button to prevent double requests
                                            e.target.classList.add('disabled');

                                            window.App.Views.RegisterAvailableShortlink.Components.Feedback.showInfo('por favor espere..');

                                            grecaptcha.ready(function() {
                                                grecaptcha.execute('{{ $captchaSitekey }}', {action: 'submit'}).then(
                                                    function(token) {
                                                        var xhr = new XMLHttpRequest();
                                                        xhr.withCredentials = true;

                                                        xhr.addEventListener("readystatechange", function () {
                                                            if (this.readyState === 4) {
                                                                try {
                                                                    const jsonResObj = JSON.parse(this.responseText);

                                                                    if (this.status === 201) {
                                                                        window.App.Views.RegisterAvailableShortlink.hide();
                                                                        window.App.Views.ShortlinkResult.Components.Shortlink.set(
                                                                            jsonResObj.shortlink
                                                                        );
                                                                        window.App.Views.ShortlinkResult.show();
                                                                        return;
                                                                    }

                                                                    if(typeof jsonResObj.message !== 'undefined') {
                                                                        window.App.Views.RegisterAvailableShortlink.Components.Feedback.showError(jsonResObj.message);
                                                                    } else {
                                                                        window.App.Views.RegisterAvailableShortlink.Components.Feedback.showError('Ocorreu um erro no nosso servidor..');
                                                                    }

                                                                    e.target.classList.remove('disabled');
                                                                } catch (e) {
                                                                    // invalid json something went wrong
                                                                    window.App.Views.RegisterAvailableShortlink.Components.Feedback.showError('Ocorreu um erro no nosso servidor..');
                                                                }
                                                            }
                                                        });

                                                        xhr.open(
                                                            "POST",
                                                            '{{ url("/api/register-available") }}?long_url='+ longUrlField.value +'&shortstring=' + shortStr + '&g-recaptcha-response=' + token
                                                        );
                                                        xhr.setRequestHeader("Authorization", "Bearer " + window._authManager.at);
                                                        xhr.send();
                                                    }
                                                );
                                            });


                                        };
                                        this.hasInitialized = true;
                                    }
                                }
                            }
                        },
                        initialize: function () {
                            this.Components.ContinueBtn.initialize();
                        }

                    },
                    Login: {
                        el: function () {
                            return document.getElementById("form-box-login");
                        },
                        show: function () {
                            this.initialize();
                            this.el().style.display = 'block';
                            this.Components.Email.el().focus();
                            this.Components.Feedback.hide();
                        },
                        hide: function () {
                            this.el().style.display = 'none';
                        },
                        Components: {
                            Email: {
                                hasInitialized: false,
                                el: function () {
                                    return document.getElementById("login-email");
                                },
                                labelEl: function () {
                                    return document.getElementById("login-email-label");
                                },
                                initialize: function () {

                                    if (this.hasInitialized === false) {
                                        const $this = this;
                                        this.labelEl().onclick = function (e) {
                                            e.target.parentNode.classList.add("active");
                                            $this.el().focus();
                                        };

                                        this.el().onfocus = function (e) {
                                            e.target.parentNode.classList.add("active");
                                            e.target.parentNode.classList.add("mtop-22");
                                            e.target.value = e.target.value.trim();
                                        };

                                        this.el().addEventListener("focusout", function (e) {
                                            e.target.value = e.target.value.trim();
                                            if (e.target.value.length == 0) {
                                                $this.labelEl().parentNode.classList.remove("active");
                                                $this.labelEl().parentNode.classList.remove("mtop-22");
                                            }
                                        });

                                        this.hasInitialized = true;
                                    }
                                }
                            },
                            Password: {
                                hasInitialized: false,
                                el: function () {
                                    return document.getElementById("login-password");
                                },
                                labelEl: function () {
                                    return document.getElementById("login-password-label");
                                },
                                initialize: function () {
                                    if (this.hasInitialized === false) {
                                        const $this = this;

                                        this.labelEl().onclick = function (e) {
                                            e.target.parentNode.classList.add("active");
                                            $this.el().focus();
                                        };

                                        this.el().onfocus = function (e) {
                                            e.target.parentNode.classList.add("active");
                                            e.target.parentNode.classList.add("mtop-22");
                                            e.target.value = e.target.value.trim();
                                        };

                                        this.el().addEventListener("focusout", function (e) {
                                            e.target.value = e.target.value.trim();
                                            if (e.target.value.length == 0) {
                                                $this.labelEl().parentNode.classList.remove("active");
                                                $this.labelEl().parentNode.classList.remove("mtop-22");
                                            }
                                        });


                                        this.hasInitialized = true;
                                    }

                                }
                            },
                            LoginBtn: {
                                hasInitialized: false,
                                el: function () {
                                    return document.getElementById("login-button");
                                },
                                enable: function () {
                                    this.el().classList.remove('disabled');
                                },
                                disable: function () {
                                    this.el().classList.add('disabled');
                                },
                                initialize: function () {
                                    if (this.hasInitialized === false) {
                                        const $this = this;
                                        this.el().onclick = function (e) {
                                            if (
                                                !window._authManager.isAuthenticated
                                                ||
                                                e.target.classList.contains('disabled')
                                            ) {
                                                return false;
                                            }

                                            const loginEmailInput = window.App.Views.Login.Components.Email.el();
                                            const loginPasswordInput = window.App.Views.Login.Components.Password.el();

                                            if (loginEmailInput.value.length == 0) {
                                                loginEmailInput.classList.add('has-error');
                                                return false;
                                            } else {
                                                loginEmailInput.classList.remove('has-error');
                                            }

                                            if (loginPasswordInput.value.length == 0) {
                                                loginPasswordInput.classList.add('has-error');
                                                return false;
                                            } else {
                                                loginPasswordInput.classList.remove('has-error');
                                            }

                                            $this.disable();
                                            window.App.Views.Login.Components.Feedback.showInfo('por favor espere..');
                                            grecaptcha.ready(function() {
                                                grecaptcha.execute('{{ $captchaSitekey }}', {action: 'submit'}).then(
                                                    function(token) {
                                                        window._authManager.login(loginEmailInput.value, loginPasswordInput.value, token);
                                                    }
                                                );
                                            });
                                        };

                                        this.hasInitialized = true;
                                    }
                                }
                            },
                            CloseBtn: {
                                hasInitialized: false,
                                el: function () {
                                    return document.getElementById('form-box-login-close-btn');
                                },
                                initialize: function () {
                                    if (this.hasInitialized === false) {
                                        this.el().onclick = function (e) {
                                            window.App.Views.Login.hide();

                                            if (window.App.previousView == window.App.Views.ShortlinkResult.el().id) {
                                                window.App.Views.ShortlinkResult.show();
                                                return;
                                            }

                                            window.App.Views.ShortenUrl.show();
                                        };
                                        this.hasInitialized = true;
                                    }
                                }
                            },
                            CreateAccLink: {
                                hasInitialized: false,
                                el: function () {
                                    return document.getElementById('create-account-link');
                                },

                                initialize: function () {
                                    if (this.hasInitialized === false) {
                                        this.el().onclick = function (e) {
                                            window.App.Views.Login.hide();
                                            window.App.Views.Register.show();
                                        };
                                        this.hasInitialized = true;
                                    }
                                }
                            },
                            Feedback: {
                                el: function () {
                                    return document.getElementById('form-box-login-feedback');
                                },
                                hide: function () {
                                    const el = this.el();
                                    el.style.display = 'none';
                                    el.innerText = '';
                                },
                                showInfo: function(message) {
                                    const el = this.el();
                                    el.innerText = message;
                                    el.classList.remove('error');
                                    el.classList.add('info');
                                    el.style.display = 'block';
                                },
                                showError: function(message) {
                                    const el = this.el();
                                    el.innerText = message;
                                    el.classList.remove('info');
                                    el.classList.add('error');
                                    el.style.display = 'block';
                                },

                            },
                            ResendVerificationEmail: {
                                hasInitialized: false,
                                el: function () {
                                    return document.getElementById('resend-verification-email-link');
                                },
                                show: function () {
                                    this.initialize();
                                    this.el().style.display = 'block';
                                },
                                hide: function () {
                                    this.el().style.display = 'none';
                                },
                                initialize: function () {
                                    if (this.hasInitialized === false) {

                                        this.el().onclick = function (e) {
                                            const loginEmailInput = window.App.Views.Login.Components.Email.el();

                                            if (loginEmailInput.value.length == 0) {
                                                loginEmailInput.classList.add('has-error');
                                                return false;
                                            } else {
                                                loginEmailInput.classList.remove('has-error');
                                            }

                                            grecaptcha.ready(function() {
                                                grecaptcha.execute('{{ $captchaSitekey }}', {action: 'submit'}).then(
                                                    function(token) {
                                                        window._authManager.resendVerificationEmail(
                                                            loginEmailInput.value,
                                                            token
                                                        );
                                                    }
                                                );
                                            });

                                            window.App.Views.Login.Components.ResendVerificationEmail.hide();

                                        };

                                        this.hasInitialized = true;
                                    }
                                }
                            },
                            ForgotPasswordLink: {
                                hasInitialized: false,
                                el: function () {
                                    return document.getElementById('forgot-pwd-link');
                                },
                                initialize: function () {
                                    if (this.hasInitialized == false) {

                                        this.el().onclick = function (e) {
                                            window.App.Views.Login.hide();
                                            window.App.Views.PasswordRecovery.show();
                                        };

                                        this.hasInitialized = true;
                                    }
                                }
                            }
                        },
                        initialize: function () {
                            this.Components.Email.initialize();
                            this.Components.Password.initialize();
                            this.Components.LoginBtn.initialize();
                            this.Components.CloseBtn.initialize();
                            this.Components.CreateAccLink.initialize();
                            this.Components.ForgotPasswordLink.initialize();
                        }

                    },
                    Register: {
                        el: function () {
                            return document.getElementById("form-box-register");
                        },
                        show: function () {
                            this.initialize();
                            this.el().style.display = 'block';
                            this.Components.Name.el().focus();
                            this.Components.Feedback.hide();
                        },
                        hide: function () {
                            this.el().style.display = 'none';
                        },
                        Components: {
                            Name: {
                                hasInitialized: false,
                                el: function () {
                                    return document.getElementById("register-name");
                                },
                                labelEl: function () {
                                    return document.getElementById("register-name-label");
                                },
                                initialize: function () {

                                    if (this.hasInitialized === false) {
                                        const $this = this;
                                        this.labelEl().onclick = function (e) {
                                            e.target.parentNode.classList.add("active");
                                            $this.el().focus();
                                        };

                                        this.el().onfocus = function (e) {
                                            e.target.parentNode.classList.add("active");
                                            e.target.parentNode.classList.add("mtop-22");
                                            e.target.value = e.target.value.trim();
                                        };

                                        this.el().addEventListener("focusout", function (e) {
                                            e.target.value = e.target.value.trim();
                                            if (e.target.value.length == 0) {
                                                $this.labelEl().parentNode.classList.remove("active");
                                                $this.labelEl().parentNode.classList.remove("mtop-22");
                                            }
                                        });

                                        this.hasInitialized = true;
                                    }
                                }
                            },
                            Email: {
                                hasInitialized: false,
                                el: function () {
                                    return document.getElementById("register-email");
                                },
                                labelEl: function () {
                                    return document.getElementById("register-email-label");
                                },
                                initialize: function () {

                                    if (this.hasInitialized === false) {
                                        const $this = this;
                                        this.labelEl().onclick = function (e) {
                                            e.target.parentNode.classList.add("active");
                                            $this.el().focus();
                                        };

                                        this.el().onfocus = function (e) {
                                            e.target.parentNode.classList.add("active");
                                            e.target.parentNode.classList.add("mtop-22");
                                            e.target.value = e.target.value.trim();
                                        };

                                        this.el().addEventListener("focusout", function (e) {
                                            e.target.value = e.target.value.trim();
                                            if (e.target.value.length == 0) {
                                                $this.labelEl().parentNode.classList.remove("active");
                                                $this.labelEl().parentNode.classList.remove("mtop-22");
                                            }
                                        });

                                        this.hasInitialized = true;
                                    }
                                }
                            },
                            EmailConfirmation: {
                                hasInitialized: false,
                                el: function () {
                                    return document.getElementById("register-email-confirmation");
                                },
                                labelEl: function () {
                                    return document.getElementById("register-email-confirmation-label");
                                },
                                initialize: function () {

                                    if (this.hasInitialized === false) {
                                        const $this = this;
                                        this.labelEl().onclick = function (e) {
                                            e.target.parentNode.classList.add("active");
                                            $this.el().focus();
                                        };

                                        this.el().onfocus = function (e) {
                                            e.target.parentNode.classList.add("active");
                                            e.target.parentNode.classList.add("mtop-22");
                                            e.target.value = e.target.value.trim();
                                        };

                                        this.el().addEventListener("focusout", function (e) {
                                            e.target.value = e.target.value.trim();
                                            if (e.target.value.length == 0) {
                                                $this.labelEl().parentNode.classList.remove("active");
                                                $this.labelEl().parentNode.classList.remove("mtop-22");
                                            }
                                        });

                                        this.hasInitialized = true;
                                    }
                                }
                            },
                            Password: {
                                hasInitialized: false,
                                el: function () {
                                    return document.getElementById("register-password");
                                },
                                labelEl: function () {
                                    return document.getElementById("register-password-label");
                                },
                                initialize: function () {
                                    if (this.hasInitialized === false) {
                                        const $this = this;

                                        this.labelEl().onclick = function (e) {
                                            e.target.parentNode.classList.add("active");
                                            $this.el().focus();
                                        };

                                        this.el().onfocus = function (e) {
                                            e.target.parentNode.classList.add("active");
                                            e.target.parentNode.classList.add("mtop-22");
                                            e.target.value = e.target.value.trim();
                                        };

                                        this.el().addEventListener("focusout", function (e) {
                                            e.target.value = e.target.value.trim();
                                            if (e.target.value.length == 0) {
                                                $this.labelEl().parentNode.classList.remove("active");
                                                $this.labelEl().parentNode.classList.remove("mtop-22");
                                            }
                                        });


                                        this.hasInitialized = true;
                                    }

                                }
                            },
                            PasswordConfirmation: {
                                hasInitialized: false,
                                el: function () {
                                    return document.getElementById("register-password-confirmation");
                                },
                                labelEl: function () {
                                    return document.getElementById("register-password-confirmation-label");
                                },
                                initialize: function () {
                                    if (this.hasInitialized === false) {
                                        const $this = this;

                                        this.labelEl().onclick = function (e) {
                                            e.target.parentNode.classList.add("active");
                                            $this.el().focus();
                                        };

                                        this.el().onfocus = function (e) {
                                            e.target.parentNode.classList.add("active");
                                            e.target.parentNode.classList.add("mtop-22");
                                            e.target.value = e.target.value.trim();
                                        };

                                        this.el().addEventListener("focusout", function (e) {
                                            e.target.value = e.target.value.trim();
                                            if (e.target.value.length == 0) {
                                                $this.labelEl().parentNode.classList.remove("active");
                                                $this.labelEl().parentNode.classList.remove("mtop-22");
                                            }
                                        });


                                        this.hasInitialized = true;
                                    }

                                }
                            },
                            RegisterBtn: {
                                hasInitialized: false,
                                el: function () {
                                    return document.getElementById("register-button");
                                },
                                enable: function () {
                                    this.el().classList.remove('disabled');
                                },
                                disable: function () {
                                    this.el().classList.add('disabled');
                                },
                                initialize: function () {
                                    if (this.hasInitialized === false) {
                                        const $this = this;
                                        this.el().onclick = function (e) {
                                            if (
                                                !window._authManager.isAuthenticated
                                                ||
                                                e.target.classList.contains('disabled')
                                            ) {
                                                return false;
                                            }
                                            const registerNameInput = window.App.Views.Register.Components.Name.el();
                                            const registerEmailInput = window.App.Views.Register.Components.Email.el();
                                            const registerEmailConfInput = window.App.Views.Register.Components.EmailConfirmation.el();
                                            const registerPasswordInput = window.App.Views.Register.Components.Password.el();
                                            const registerPasswordConfInput = window.App.Views.Register.Components.PasswordConfirmation.el();

                                            if (registerNameInput.value.length == 0) {
                                                registerNameInput.classList.add('has-error');
                                                return false;
                                            } else {
                                                registerNameInput.classList.remove('has-error');
                                            }

                                            if (registerEmailInput.value.length == 0) {
                                                registerEmailInput.classList.add('has-error');
                                                return false;
                                            } else {
                                                registerEmailInput.classList.remove('has-error');
                                            }

                                            if (registerEmailConfInput.value !== registerEmailInput.value) {
                                                registerEmailConfInput.classList.add('has-error');
                                                return false;
                                            } else {
                                                registerEmailConfInput.classList.remove('has-error');
                                            }

                                            if (registerPasswordInput.value.length == 0) {
                                                registerPasswordInput.classList.add('has-error');
                                                return false;
                                            } else {
                                                registerPasswordInput.classList.remove('has-error');
                                            }

                                            if (registerPasswordConfInput.value !== registerPasswordInput.value) {
                                                registerPasswordConfInput.classList.add('has-error');
                                                return false;
                                            } else {
                                                registerPasswordConfInput.classList.remove('has-error');
                                            }

                                            $this.disable();
                                            window.App.Views.Register.Components.Feedback.showInfo('por favor espere..');
                                            grecaptcha.ready(function() {
                                                grecaptcha.execute('{{ $captchaSitekey }}', {action: 'submit'}).then(
                                                    function(token) {
                                                        window._authManager.register(
                                                            registerNameInput.value,
                                                            registerEmailInput.value,
                                                            registerEmailConfInput.value,
                                                            registerPasswordInput.value,
                                                            registerPasswordConfInput.value,
                                                            token
                                                        );
                                                    }
                                                );
                                            });
                                        };

                                        this.hasInitialized = true;
                                    }
                                }
                            },
                            LoginToAccLink: {
                                hasInitialized: false,
                                el: function () {
                                    return document.getElementById('login-to-account-link');
                                },
                                initialize: function () {
                                    if (this.hasInitialized === false) {
                                        this.el().onclick = function (e) {
                                            window.App.Views.Register.hide();
                                            window.App.Views.Login.show();
                                        };
                                        this.hasInitialized = true;
                                    }
                                }
                            },
                            CloseBtn: {
                                hasInitialized: false,
                                el: function () {
                                    return document.getElementById('form-box-register-close-btn');
                                },
                                initialize: function () {
                                    if (this.hasInitialized === false) {
                                        this.el().onclick = function (e) {
                                            window.App.Views.Register.hide();

                                            if (window.App.previousView == window.App.Views.ShortlinkResult.el().id) {
                                                window.App.Views.ShortlinkResult.show();
                                                return;
                                            }

                                            window.App.Views.ShortenUrl.show();
                                        };
                                        this.hasInitialized = true;
                                    }
                                }
                            },
                            Feedback: {
                                el: function () {
                                    return document.getElementById('form-box-register-feedback');
                                },
                                hide: function () {
                                    const el = this.el();
                                    el.style.display = 'none';
                                    el.innerText = '';
                                },
                                showInfo: function(message) {
                                    const el = this.el();
                                    el.innerText = message;
                                    el.classList.remove('error');
                                    el.classList.add('info');
                                    el.style.display = 'block';
                                },
                                showError: function(message) {
                                    const el = this.el();
                                    el.innerText = message;
                                    el.classList.remove('info');
                                    el.classList.add('error');
                                    el.style.display = 'block';
                                },

                            }
                        },
                        initialize: function () {
                            this.Components.Name.initialize();
                            this.Components.Email.initialize();
                            this.Components.EmailConfirmation.initialize();
                            this.Components.Password.initialize();
                            this.Components.PasswordConfirmation.initialize();
                            this.Components.RegisterBtn.initialize();
                            this.Components.LoginToAccLink.initialize();
                            this.Components.CloseBtn.initialize();
                        }

                    },
                    RegisterSuccess: {
                        el: function () {
                            return document.getElementById('form-box-account-registered');
                        },
                        show: function () {
                            this.el().style.display = 'block';
                        },
                        hide: function () {
                            this.el().style.display = 'none';
                        }
                    },
                    PasswordRecovery: {
                        el: function () {
                            return document.getElementById('password-recovery');
                        },
                        show: function () {
                            this.initialize();
                            this.el().style.display = 'block';
                            this.Components.Email.el().focus();
                        },
                        hide: function () {
                            this.el().style.display = 'none';
                        },
                        Components: {
                            CloseBtn: {
                                hasInitialized: false,
                                el: function () {
                                    return document.getElementById('pwd-recovery-close-btn');
                                },
                                initialize: function () {
                                    if (this.hasInitialized === false) {
                                        this.el().onclick = function (e) {
                                            window.App.Views.PasswordRecovery.hide();
                                            window.App.Views.Login.show();
                                        };
                                        this.hasInitialized = true;
                                    }
                                }
                            },
                            Email: {
                                hasInitialized: false,
                                el: function () {
                                    return document.getElementById("pwd-recovery-email");
                                },
                                labelEl: function () {
                                    return document.getElementById("pwd-recovery-email-label");
                                },
                                initialize: function () {
                                    if (this.hasInitialized === false) {
                                        const $this = this;
                                        this.labelEl().onclick = function (e) {
                                            e.target.parentNode.classList.add("active");
                                            $this.el().focus();
                                        };

                                        this.el().onfocus = function (e) {
                                            e.target.parentNode.classList.add("active");
                                            e.target.parentNode.classList.add("mtop-22");
                                            e.target.value = e.target.value.trim();
                                        };

                                        this.el().addEventListener("focusout", function (e) {
                                            e.target.value = e.target.value.trim();
                                            if (e.target.value.length == 0) {
                                                $this.labelEl().parentNode.classList.remove("active");
                                                $this.labelEl().parentNode.classList.remove("mtop-22");
                                            }
                                        });

                                        this.hasInitialized = true;
                                    }
                                }
                            },
                            Feedback: {
                                el: function () {
                                    return document.getElementById('pwd-recovery-feedback');
                                },
                                hide: function () {
                                    const el = this.el();
                                    el.style.display = 'none';
                                    el.innerText = '';
                                },
                                showInfo: function(message) {
                                    const el = this.el();
                                    el.innerText = message;
                                    el.classList.remove('error');
                                    el.classList.add('info');
                                    el.style.display = 'block';
                                },
                                showError: function(message) {
                                    const el = this.el();
                                    el.innerText = message;
                                    el.classList.remove('info');
                                    el.classList.add('error');
                                    el.style.display = 'block';
                                },

                            },
                            SendPwdRecoveryBtn: {
                                hasInitialized: false,
                                el: function () {
                                    return document.getElementById('pwd-recovery-button');
                                },
                                enable: function () {
                                    this.el().classList.remove('disabled');
                                },
                                disable: function () {
                                    this.el().classList.add('disabled');
                                },
                                initialize: function () {
                                    if ( this.hasInitialized == false ) {

                                        this.el().onclick = function (e) {

                                            if (
                                                !window._authManager.isAuthenticated
                                                ||
                                                e.target.classList.contains('disabled')
                                            ) {
                                                return false;
                                            }

                                            const emailInput = window.App.Views.PasswordRecovery.Components.Email.el();

                                            if (emailInput.value.length == 0) {
                                                emailInput.classList.add('has-error');
                                                return false;
                                            } else {
                                                emailInput.classList.remove('has-error');
                                            }

                                            grecaptcha.ready(function() {
                                                grecaptcha.execute('{{ $captchaSitekey }}', {action: 'submit'}).then(
                                                    function(token) {
                                                        window._authManager.sendPasswordRecoveryEmail(
                                                            emailInput.value,
                                                            token
                                                        );

                                                        window.App.Views.PasswordRecovery.Components.Feedback.showInfo(
                                                            'Acabamos de lhe enviar um email para que possa criar uma nova palavra-passe.'
                                                        );
                                                        window.App.Views.PasswordRecovery.Components.SendPwdRecoveryBtn.disable();
                                                    }
                                                );
                                            });
                                        };

                                        this.hasInitialized = true;
                                    }
                                }

                            }
                        },
                        initialize: function () {
                            this.Components.CloseBtn.initialize();
                            this.Components.Email.initialize();
                            this.Components.SendPwdRecoveryBtn.initialize();
                        }
                    },
                    ChangePassword: {
                        el: function () {
                            return document.getElementById('change-password');
                        },
                        show: function () {
                            this.initialize();
                            this.el().style.display = 'block';
                        },
                        hide: function () {
                            this.el().style.display = 'none';
                        },
                        Components: {
                            NewPassword: {
                                hasInitialized: false,
                                el: function () {
                                    return document.getElementById("new-password");
                                },
                                labelEl: function () {
                                    return document.getElementById("new-password-label");
                                },
                                initialize: function () {
                                    if (this.hasInitialized === false) {
                                        const $this = this;

                                        this.labelEl().onclick = function (e) {
                                            e.target.parentNode.classList.add("active");
                                            $this.el().focus();
                                        };

                                        this.el().onfocus = function (e) {
                                            e.target.parentNode.classList.add("active");
                                            e.target.parentNode.classList.add("mtop-22");
                                            e.target.value = e.target.value.trim();
                                        };

                                        this.el().addEventListener("focusout", function (e) {
                                            e.target.value = e.target.value.trim();
                                            if (e.target.value.length == 0) {
                                                $this.labelEl().parentNode.classList.remove("active");
                                                $this.labelEl().parentNode.classList.remove("mtop-22");
                                            }
                                        });


                                        this.hasInitialized = true;
                                    }

                                }
                            },
                            NewPasswordConfirmation: {
                                hasInitialized: false,
                                el: function () {
                                    return document.getElementById("new-password-confirmation");
                                },
                                labelEl: function () {
                                    return document.getElementById("new-password-confirmation-label");
                                },
                                initialize: function () {
                                    if (this.hasInitialized === false) {
                                        const $this = this;

                                        this.labelEl().onclick = function (e) {
                                            e.target.parentNode.classList.add("active");
                                            $this.el().focus();
                                        };

                                        this.el().onfocus = function (e) {
                                            e.target.parentNode.classList.add("active");
                                            e.target.parentNode.classList.add("mtop-22");
                                            e.target.value = e.target.value.trim();
                                        };

                                        this.el().addEventListener("focusout", function (e) {
                                            e.target.value = e.target.value.trim();
                                            if (e.target.value.length == 0) {
                                                $this.labelEl().parentNode.classList.remove("active");
                                                $this.labelEl().parentNode.classList.remove("mtop-22");
                                            }
                                        });


                                        this.hasInitialized = true;
                                    }

                                }
                            },
                            Feedback: {
                                el: function () {
                                    return document.getElementById('change-password-feedback');
                                },
                                hide: function () {
                                    const el = this.el();
                                    el.style.display = 'none';
                                    el.innerText = '';
                                },
                                showInfo: function(message) {
                                    const el = this.el();
                                    el.innerText = message;
                                    el.classList.remove('error');
                                    el.classList.add('info');
                                    el.style.display = 'block';
                                },
                                showError: function(message) {
                                    const el = this.el();
                                    el.innerText = message;
                                    el.classList.remove('info');
                                    el.classList.add('error');
                                    el.style.display = 'block';
                                },

                            },
                            ChangePasswordBtn: {
                                hasInitialized: false,
                                el: function () {
                                    return document.getElementById("change-password-button");
                                },
                                enable: function () {
                                    this.el().classList.remove('disabled');
                                },
                                disable: function () {
                                    this.el().classList.add('disabled');
                                },
                                initialize: function () {
                                    if (this.hasInitialized === false) {
                                        const $this = this;
                                        this.el().onclick = function (e) {
                                            if (
                                                !window._authManager.isAuthenticated
                                                ||
                                                e.target.classList.contains('disabled')
                                            ) {
                                                return false;
                                            }

                                            const newPassword = window.App.Views.ChangePassword.Components.NewPassword.el();
                                            const newPasswordConfirmation = window.App.Views.ChangePassword.Components.NewPasswordConfirmation.el();

                                            if (newPassword.value.length == 0) {
                                                newPassword.classList.add('has-error');
                                                return false;
                                            } else {
                                                newPassword.classList.remove('has-error');
                                            }

                                            if (newPasswordConfirmation.value.length == 0) {
                                                newPasswordConfirmation.classList.add('has-error');
                                                return false;
                                            } else {
                                                newPasswordConfirmation.classList.remove('has-error');
                                            }

                                            $this.disable();
                                            window.App.Views.ChangePassword.Components.Feedback.showInfo('por favor espere..');
                                            grecaptcha.ready(function() {
                                                grecaptcha.execute('{{ $captchaSitekey }}', {action: 'submit'}).then(
                                                    function(token) {
                                                        window._authManager.changePassword(
                                                            newPassword.value,
                                                            newPasswordConfirmation.value,
                                                            token
                                                        );
                                                    }
                                                );
                                            });
                                        };

                                        this.hasInitialized = true;
                                    }
                                }
                            },
                        },
                        initialize: function () {
                            this.Components.NewPassword.initialize();
                            this.Components.NewPasswordConfirmation.initialize();
                            this.Components.ChangePasswordBtn.initialize();
                        }
                    },
                    PasswordHasChanged: {
                        el: function () {
                            return document.getElementById('password-has-changed-view');
                        },
                        show: function () {
                            this.initialize();
                            this.el().style.display = 'block';
                        },
                        hide: function () {
                            this.el().style.display = 'none';
                        },
                        Components: {
                            LoginBtn: {
                                hasInitialized: false,
                                el: function() {
                                    return document.getElementById('changed-password-login');
                                },
                                initialize: function () {
                                    if (this.hasInitialized == false) {

                                        this.el().onclick = function(e) {
                                            window.App.Views.PasswordHasChanged.hide();
                                            window.App.Views.Login.show();
                                        };

                                        this.hasInitialized = true;
                                    }
                                }

                            }
                        },
                        initialize: function () {
                            this.Components.LoginBtn.initialize();
                        }

                    },
                    MyAccount: {
                        hasInitialized: false,
                        el: function () {
                            return document.getElementById('form-box-account');
                        },
                        hide: function () {
                            this.el().style.display = 'none';
                        },
                        show: function () {
                            if (!window._authManager.isLoggedIn) {
                                window.App.Views.Login.show();
                                return;
                            }

                            this.initialize();
                            this.el().style.display = 'block';
                        },
                        Components: {
                            ShortenNewBtn: {
                                hasInitialized: false,
                                el: function () {
                                    return document.getElementById('account-shorten-new');
                                },
                                initialize: function () {
                                    if (this.hasInitialized === false) {
                                        this.el().onclick = function (e) {
                                            window.App.Views.MyAccount.hide();
                                            window.App.Views.ShortenUrl.show();
                                        };
                                        this.hasInitialized = true;
                                    }
                                }
                            },
                            LogoutBtn: {
                                hasInitialized: false,
                                el: function () {
                                    return document.getElementById('account-logout');
                                },
                                initialize: function () {
                                    if (this.hasInitialized === false) {
                                        this.el().onclick = function (e) {
                                            window._authManager.logout();
                                        };
                                        this.hasInitialized = true;
                                    }
                                }
                            },
                            MyLinks: {
                                initialize: function () {
                                    this.Components.Loading.show();
                                    this.Components.Links.show();
                                },
                                Components: {
                                    Loading: {
                                        el: function () {
                                            return document.getElementById('form-box-acc-links-loading');
                                        },
                                        show: function () {
                                            this.el().style.display = 'block';
                                        },
                                        hide: function () {
                                            this.el().style.display = 'none';
                                        },
                                    },
                                    Links: {
                                        api: "{{ url('/api/links') }}",
                                        el: function () {
                                            return document.getElementById('form-box-acc-links');
                                        },
                                        show: function () {
                                            this.el().style.display = 'block';
                                            this.fetch();
                                        },
                                        hide: function () {
                                            this.el().style.display = 'none';
                                        },
                                        clear: function () {
                                            this.el().innerHTML = '';
                                        },
                                        addLink: function (id, long_url, shortlink, destination_email) {
                                            const listItem = document.createElement("div");
                                            listItem.classList.add('list-item');

                                            const shortlinkContainer = document.createElement("div");
                                            shortlinkContainer.classList.add('short-url');
                                            shortlinkContainer.innerText = shortlink;

                                            const longUrlContainer = document.createElement("div");
                                            longUrlContainer.classList.add('long-url');
                                            longUrlContainer.innerText = long_url;

                                            this.el().appendChild(listItem);
                                            listItem.appendChild(shortlinkContainer);
                                            listItem.appendChild(longUrlContainer);
                                        },
                                        fetch: function () {
                                            if (window._authManager.isAuthenticated !== true) {
                                                return;
                                            }
                                            this.clear();
                                            window.App.Views.MyAccount.Components.MyLinks.Components.Loading.show();

                                            var $this = this;

                                            var xhr = new XMLHttpRequest();
                                            xhr.withCredentials = true;

                                            xhr.addEventListener("readystatechange", function () {
                                                if (this.readyState === 4) {
                                                    const resObj = JSON.parse(this.response); //TODO: Catch exception

                                                    if (this.status === 200) {
                                                        window.App.Views.MyAccount.Components.MyLinks.Components.Loading.hide();
                                                        for (var i = 0; i < resObj.length; i++) {
                                                            $this.addLink(
                                                                resObj[i].id,
                                                                resObj[i].long_url,
                                                                resObj[i].shortlink,
                                                                resObj[i].email
                                                            );
                                                        }
                                                    }
                                                }
                                            });

                                            xhr.open("POST", this.api);
                                            xhr.setRequestHeader("Authorization", "Bearer " + window._authManager.at);
                                            xhr.send();
                                        },
                                    }
                                }
                            }
                        },
                        initialize: function () {
                            this.Components.ShortenNewBtn.initialize();
                            this.Components.LogoutBtn.initialize();
                            this.Components.MyLinks.initialize();
                        }

                    },
                    EmailConfirmed: {
                        el: function () {
                            return document.getElementById('email-confirmed');
                        },
                        show: function () {
                            this.initialize();
                            this.el().style.display = 'block';
                        },
                        hide: function () {
                            this.el().style.display = 'none';
                        },
                        Components: {
                            MyAccountBtn: {
                                hasInitialized: false,
                                el: function () {
                                    return document.getElementById('email-confirmed-login-btn');
                                },
                                initialize: function () {
                                    if ( this.hasInitialized === false ) {

                                        this.el().onclick = function (e) {
                                            window.App.Views.EmailConfirmed.hide();
                                            window.App.Views.Login.show();
                                        };

                                        this.hasInitialized = true;
                                    }
                                }
                            }
                        },
                        initialize: function () {
                            this.Components.MyAccountBtn.initialize();
                        }
                    }
                }
            };


        </script>

        @if (isset($passwordRecoveryToken))
            <script>
                window._authManager.passwordRecoveryToken = '{{ $passwordRecoveryToken }}';
            </script>
        @endif

        <script src="https://www.google.com/recaptcha/api.js?render={{ $captchaSitekey }}"></script>
    </head>
    <body
        class="antialiased"
        style="
            background: url('{{ $currentBackground }}');
            background-size: 100%;
            background-repeat: no-repeat;
        "
    >
        <div style="text-align: center; margin-top: 10px">
            <img
                src="https://www.inideia.com/wp-content/uploads/2019/01/logo_white.png"
                style="max-width: 300px"
            />
        </div>

        <div class="form-box" id="form-box" style="display: none">
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
            <a href="javascript:void(0);" id="my-account-link" class="form-link" style="display: none">Minha conta</a>

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

            <div class="button" id="save-shortlink" style="display: none">Guardar na minha lista de links</div>
            <div class="button" id="go-to-my-links" style="display: none">Ver minha lista de links</div>
            <a href="javascript:void(0);" id="generate-another-shortlink" class="form-link">Encurtar outro link</a>
        </div>

        <div
            class="form-box"
            id="form-box-shortlink-requested"
            style="display: none"
        >
            <div class="form-box-title">
                Este link curto está disponível!
            </div>
            @if(isset($shortlink) && isset($shortlink_shortstring))
                <div class="input-container">
                    <input type="text" id="requested-shortlink" data-shortstring="{{$shortlink_shortstring}}" value="{{ $shortlink}}" readonly />
                </div>
            @endif
            <div>Para onde quer apontar este link?</div>
            <div class="input-container">
                <input type="text" id="requested-shortlink-long-url"/>
            </div>

            <div id="requested-shortlink-register-feedback" class="form-box-feedback" style="display: none"></div>

            <div class="button disabled" id="shortlink-register">Continuar</div>
        </div>

        <div
            class="form-box"
            id="form-box-login" style="display: none"
        >
            <div class="form-box-title">Minha conta</div>
            <div class="close-form-box" id="form-box-login-close-btn">X</div>
            <div class="input-container">
                <div class="input-label" id="login-email-label">
                    Email
                </div>
                <input type="text" id="login-email" />
            </div>

            <div class="input-container">
                <div class="input-label" id="login-password-label">
                    Palavra-passe
                </div>
                <input type="password" id="login-password" />
            </div>

            <div id="form-box-login-feedback" class="form-box-feedback" style="display: none"></div>
            <a
                href="javascript:void(0);"
                class="form-link"
                id="resend-verification-email-link"
                style="display: none">
                Reenviar email de confirmação
            </a>

            <div class="button disabled" id="login-button">Entrar</div>
            <a href="javascript:void(0);" id="create-account-link" class="form-link">Ainda não tenho uma conta</a>
            <a href="javascript:void(0);" id="forgot-pwd-link" class="form-link">Esqueci-me da palavra-passe</a>
        </div>


        <div
            class="form-box"
            id="password-recovery" style="display: none"
        >
            <div class="form-box-title">Recuperar conta</div>
            <div class="close-form-box" id="pwd-recovery-close-btn">X</div>
            <div class="input-container">
                <div class="input-label" id="pwd-recovery-email-label">
                    Email
                </div>
                <input type="text" id="pwd-recovery-email" />
            </div>

            <div id="pwd-recovery-feedback" class="form-box-feedback" style="display: none; margin-bottom: 10px;"></div>

            <div class="button disabled" id="pwd-recovery-button">Alterar palavra-passe</div>
        </div>


        <div
            class="form-box"
            id="change-password" style="display: none"
        >
            <div class="form-box-title">Alterar palavra-passe</div>

            <div class="input-container">
                <div class="input-label" id="new-password-label">
                    Digite a sua nova palavra-passe
                </div>
                <input type="password" id="new-password" />
            </div>

            <div class="input-container">
                <div class="input-label" id="new-password-confirmation-label">
                    Confirmar palavra-passe
                </div>
                <input type="password" id="new-password-confirmation" />
            </div>

            <div id="change-password-feedback" class="form-box-feedback" style="display: none"></div>

            <div class="button disabled" id="change-password-button">Continuar</div>
        </div>

        <div class="form-box" id="password-has-changed-view" style="display: none">
            <div class="form-box-title">A sua password foi alterada!</div>
            <p>
                Agora já pode entrar na sua conta com a sua nova password!
            </p>
            <div class="button" id="changed-password-login">Entrar</div>
        </div>

        <div
            class="form-box"
            id="form-box-register" style="display: none"
        >
            <div class="form-box-title">Criar conta</div>
            <div class="close-form-box" id="form-box-register-close-btn">X</div>
            <div class="input-container">
                <div class="input-label" id="register-name-label">
                    O seu nome
                </div>
                <input type="text" id="register-name" />
            </div>

            <div class="input-container">
                <div class="input-label" id="register-email-label">
                    Email
                </div>
                <input type="text" id="register-email" />
            </div>

            <div class="input-container">
                <div class="input-label" id="register-email-confirmation-label">
                    Confirmar email
                </div>
                <input type="text" id="register-email-confirmation" />
            </div>

            <div class="input-container">
                <div class="input-label" id="register-password-label">
                    Crie uma palavra-passe
                </div>
                <input type="password" id="register-password" />
            </div>

            <div class="input-container">
                <div class="input-label" id="register-password-confirmation-label">
                    Confirmar palavra-passe
                </div>
                <input type="password" id="register-password-confirmation" />
            </div>

            <div id="form-box-register-feedback" class="form-box-feedback" style="display: none"></div>

            <div class="button disabled" id="register-button">Continuar</div>
            <a href="javascript:void(0);" id="login-to-account-link" class="form-link">Já tenho uma conta</a>
        </div>

        <div
            class="form-box"
            id="form-box-account-registered" style="display: none"
        >
            <div class="form-box-title">Conta criada com successo!</div>
            <p>Verifique o seu email pois acabamos de lhe enviar um email para que possa confirmar que o email fornecido lhe pertence.<br/><br/>Muito obrigado.</p>
        </div>


        <div
            class="form-box"
            id="form-box-account" style="display: none"
        >
            <div class="form-box-title">Minha conta</div>
            <div class="button" id="account-shorten-new">Criar novo link curto</div>
            <div class="button red" id="account-logout" style="margin-top: 10px">Sair</div>
            <hr/>
            <div id="form-box-account-my-links">
                <div class="form-box-title" style="margin-top: 12px">Os meus links curtos:</div>
                <div id="form-box-acc-links-loading">A carregar links..</div>
                <div id="form-box-acc-links" class="list-container" style="display: none"></div>
            </div>
        </div>

        <div
            class="form-box"
            id="email-confirmed" style="display: none"
        >
            <div class="form-box-title">Email confirmado!</div>
            <div class="button" id="email-confirmed-login-btn">Minha conta</div>
        </div>

        @if(isset($shortlink) && (isset($shortlink_available) && $shortlink_available === true))
            <script>
                window.App.isUserRequestingAvailableShortstring = true;
            </script>
        @else
            <script>
                window.App.isUserRequestingAvailableShortstring = false;
            </script>
        @endif

        @if(isset($page))
            <script>
                window.App.currentPage = '{{ $page }}';
            </script>
        @endif

        <script>



            function enableAuthenticationDependentButtons() {
                window.App.Views.ShortenUrl.Components.GenerateBtn.enable();
                window.App.Views.ShortenUrl.Components.MyAccountLink.show();
                window.App.Views.Login.Components.LoginBtn.enable();
                window.App.Views.Register.Components.RegisterBtn.enable();
                window.App.Views.RegisterAvailableShortlink.Components.ContinueBtn.enable();
                window.App.Views.PasswordRecovery.Components.SendPwdRecoveryBtn.enable();
                window.App.Views.ChangePassword.Components.ChangePasswordBtn.enable();
            }

            if (window.App.isUserRequestingAvailableShortstring) {
                window.App.Views.ShortenUrl.hide();
                window.App.Views.RegisterAvailableShortlink.show();
            } else {
                if (
                    typeof window.App.currentPage !== 'undefined'
                    &&
                    typeof window.App.Views[window.App.currentPage] !== 'undefined'
                    &&
                    typeof window.App.Views[window.App.currentPage].show === 'function'
                ) {
                    window.App.Views[window.App.currentPage].show();
                } else {
                    window.App.Views.ShortenUrl.show();
                }
            }

            document.addEventListener('userAuthenticated', (e) => {
                enableAuthenticationDependentButtons();
            }, false);

            document.addEventListener('userLoggedIn', (e) => {
                window.App.Views.Login.hide();
                window.App.Views.Register.hide();
                window.App.Views.MyAccount.show();
            }, false);

            document.addEventListener('userLoginFailed', (e) => {

                if (e.isError) {
                    window.App.Views.Login.Components.Feedback.showError(e.reason);

                    if(e.error_id === 'unverified_account') {
                        window.App.Views.Login.Components.ResendVerificationEmail.show();
                    }
                } else {
                    window.App.Views.Login.Components.Feedback.showInfo(e.reason);
                }

                window.App.Views.Login.Components.LoginBtn.enable();
            }, false);


            document.addEventListener('userRegisterFailed', (e) => {

                if (e.isError) {
                    window.App.Views.Register.Components.Feedback.showError(e.reason);
                } else {
                    window.App.Views.Register.Components.Feedback.showInfo(e.reason);
                }

                window.App.Views.Register.Components.RegisterBtn.enable();
            }, false);

            document.addEventListener('userRegisterSuccess', (e) => {
                window.App.Views.Register.hide();
                window.App.Views.RegisterSuccess.show();
            }, false);

            document.addEventListener('userPasswordChanged', (e) => {
                window.App.Views.ChangePassword.hide();
                window.App.Views.PasswordHasChanged.show();
            }, false);


            if (window._authManager.isAuthenticated) {
                enableAuthenticationDependentButtons();
            }
        </script>
    </body>
</html>
