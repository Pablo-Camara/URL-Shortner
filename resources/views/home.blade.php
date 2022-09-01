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

                                        if (destinationEmailInput.value.length == 0) {
                                            destinationEmailInput.classList.add('has-error');
                                            return false;
                                        } else {
                                            destinationEmailInput.classList.remove('has-error');
                                        }

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

                                                        if (window._authManager.isLoggedIn) {
                                                            window.App.Views.ShortlinkResult.Components.GotoMyLinksBtn.show();
                                                            window.App.Views.ShortlinkResult.Components.SaveShortlinkBtn.hide();
                                                        } else {
                                                            window.App.Views.ShortlinkResult.Components.GotoMyLinksBtn.hide();
                                                            window.App.Views.ShortlinkResult.Components.SaveShortlinkBtn.show();
                                                        }

                                                    }

                                                    if(this.status === 503) {
                                                        window.App.Views.ShortenUrl.Components.Feedback.showError(jsonResObj.message);
                                                    }

                                                    if(this.status === 500) {
                                                        window.App.Views.ShortenUrl.Components.Feedback.showError('Ocorreu um erro no nosso servidor..');
                                                    }

                                                    e.target.classList.remove('disabled');
                                                } catch (e) {
                                                    // invalid json something went wrong
                                                    window.App.Views.ShortenUrl.Components.Feedback.showError('Ocorreu um erro no nosso servidor..');
                                                }
                                            }
                                        });

                                        xhr.open(
                                            "POST",
                                            '{{ url("/api/shorten") }}?long_url='+ longUrlInput.value +'&destination_email=' + destinationEmailInput.value
                                        );
                                        xhr.setRequestHeader("Authorization", "Bearer " + window._authManager.at);

                                        // disable generate button to prevent double requests
                                        e.target.classList.add('disabled');

                                        window.App.Views.ShortenUrl.Components.Feedback.showInfo('por favor espere..');
                                        xhr.send();
                                    };
                                    this.hasInitialized = true;
                                }
                            },
                            MyAccountLink: {
                                hasInitialized: false,
                                el: function () {
                                    return document.getElementById("my-account-link");
                                },
                                initialize: function () {
                                    if (this.hasInitialized === false) {
                                        this.el().onclick = function (e) {
                                            window.App.Views.ShortenUrl.hide();
                                            window.App.Views.Login.show();
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
                                        //TODO: goto my links
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
                    Login: {
                        el: function () {
                            return document.getElementById("form-box-login");
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
                                            window._authManager.login(loginEmailInput.value, loginPasswordInput.value);
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
                                    this.el().style.display = 'none';
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
                            this.Components.Email.initialize();
                            this.Components.Password.initialize();
                            this.Components.LoginBtn.initialize();
                            this.Components.CloseBtn.initialize();
                            this.Components.CreateAccLink.initialize();
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

                                            const registerEmailInput = window.App.Views.Register.Components.Email.el();
                                            const registerPasswordInput = window.App.Views.Register.Components.Password.el();

                                            if (registerEmailInput.value.length == 0) {
                                                registerEmailInput.classList.add('has-error');
                                                return false;
                                            } else {
                                                registerEmailInput.classList.remove('has-error');
                                            }

                                            if (registerPasswordInput.value.length == 0) {
                                                registerPasswordInput.classList.add('has-error');
                                                return false;
                                            } else {
                                                registerPasswordInput.classList.remove('has-error');
                                            }

                                            $this.disable();
                                            window.App.Views.Register.Components.Feedback.showInfo('por favor espere..');
                                            //window._authManager.login(registerEmailInput.value, registerPasswordInput.value);
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
                                    this.el().style.display = 'none';
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
                }
            };


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
            <a href="javascript:void(0);" id="my-account-link" class="form-link">Minha conta</a>

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

            <div class="button disabled" id="login-button">Entrar</div>
            <a href="javascript:void(0);" id="create-account-link" class="form-link">Ainda não tenho uma conta</a>
        </div>


        <div
            class="form-box"
            id="form-box-register" style="display: block"
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
                    Digite novamente o seu email
                </div>
                <input type="text" id="register-email-confirmation" />
            </div>

            <div class="input-container">
                <div class="input-label" id="register-password-label">
                    Crie uma password
                </div>
                <input type="password" id="register-password" />
            </div>

            <div class="input-container">
                <div class="input-label" id="register-password-confirmation-label">
                    Digite novamente a sua password
                </div>
                <input type="password" id="register-password-confirmation" />
            </div>

            <div id="form-box-register-feedback" class="form-box-feedback" style="display: none"></div>

            <div class="button disabled" id="register-button">Continuar</div>
            <a href="javascript:void(0);" id="login-to-account-link" class="form-link">Já tenho uma conta</a>
        </div>



        <script>

            function enableAuthenticationDependentButtons() {
                window.App.Views.ShortenUrl.Components.GenerateBtn.enable();
                window.App.Views.Login.Components.LoginBtn.enable();
                window.App.Views.Register.Components.RegisterBtn.enable();
            }


            //window.App.Views.ShortenUrl.show();
            window.App.Views.Register.show();

            document.addEventListener('userAuthenticated', (e) => {
                enableAuthenticationDependentButtons();
            }, false);

            document.addEventListener('userLoggedIn', (e) => {
                //TODO: login
                window.App.Views.Login.Components.Feedback.hide();
            }, false);

            document.addEventListener('userLoginFailed', (e) => {

                if (e.isError) {
                    window.App.Views.Login.Components.Feedback.showError(e.reason);
                } else {
                    window.App.Views.Login.Components.Feedback.showInfo(e.reason);
                }

                window.App.Views.Login.Components.LoginBtn.enable();
            }, false);


            if (window._authManager.isAuthenticated) {
                enableAuthenticationDependentButtons();
            }
        </script>
    </body>
</html>
