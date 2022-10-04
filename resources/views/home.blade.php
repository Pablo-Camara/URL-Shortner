<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <title>web into link - url shortner em português</title>
        <meta name="description" content="url shortner em português, transforme os seus links longos em links curtos, edita os teus links se quiseres e veja quantas pessoas visitam o seu link">
        <meta name="keywords" content="url shortner, encurtador de links, encurtador de urls, url shortner em português, url shortner português, encurtador de links português, encurtador de links em português, português">
        <link rel="icon" type="image/x-icon" href="{{ asset('/img/WIL_favicon.png') }}">

        <!-- Fonts -->
        <link
            href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap"
            rel="preload"
            as="style"
            onload="this.onload=null;this.rel='stylesheet';"
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
                background: url('{{ $currentBackground }}');
                background-size: cover;
                background-repeat: no-repeat;
                min-height: 100%;
                background-position-x: 70%;
                background-attachment: fixed;
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

            .form-box.overlay {
                /** mobile */
            }

            .form-box.overlay .form-box-title {
                margin-top: 12px;
                text-transform: uppercase;
                margin-bottom: 22px;
            }

            .form-box .close-form-box {
                position: absolute;
                top: 10px;
                right: 15px;
                color: red;
                font-size: 16px;
                cursor: pointer;
            }


            .my-links-list-options {
                text-align: right;
            }

            .my-links-list-options .my-links-order {
                font-size: 12px;
                text-align: right;
                margin-bottom: 4px;
                color: #333333;
                display: inline-block;
                position: relative;
                cursor: pointer;
            }

            .my-links-list-options .my-links-order .arrow-down {
                width: 0;
                height: 0;
                border-style: solid;
                border-width: 7px 5px 0 5px;
                border-color: #a57212 transparent transparent transparent;
                display: inline-block;
            }

            .my-links-list-options .my-links-order .my-links-order-list {
                position: absolute;
                background: white;
                z-index: 99999;
                border: 1px solid #e4e4e4;
                right: 0;
            }

            .my-links-list-options .my-links-order .my-links-order-list .order-item {
                padding: 10px;
                border-bottom: 1px solid #dfdfdf;
                cursor: pointer;
                min-width: 222px;
                color: #a06900;
            }
            .my-links-list-options .my-links-order .my-links-order-list .order-item:last-child {
                border-bottom: 0;
            }

            .my-links-list-options .my-links-order .my-links-order-list .order-item:hover {
                background: #a76d00;
                color: white;
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

            .personalized-shortstring-length {
                display: block;
                padding: 0 0 8px 8px;
            }

            .personalized-shortstring-length.has-error {
                border: 1px solid red;
            }

            #cancel-and-generate-normally {
                color: #f80c0c;
            }

            .form-box .radio-input-container {
                margin-top: 10px;
                margin-bottom: 10px;
            }

            .form-box .input-generic-1 {
                color: #0077c8;
                width: 100%;
                padding: 12px 6px;
                box-sizing: border-box;
                border: 1px solid #EEE;
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

            .form-box .input-container input,
            .form-box .input-container select,
            .form-box .input-container textarea
            {
                width: 100%;
                height: 40px;
                box-sizing: border-box;

                padding-left: 5px;
                font-size: 12px;

                border: 1px solid #AAAAAA;
            }

            .form-box .input-container textarea
            {
                height: 80px;
            }

            .form-box .input-container input[type="checkbox"] {
                width: auto;
                height: auto;
            }

            .form-box .input-container input.has-error,
            .form-box .input-container textarea.has-error
            {
                border: 1px solid red;
            }

            .form-box .button.btn-color-hover-only:hover {
                font-weight: normal;
                background: linear-gradient( 120deg, rgb(102, 147, 179) 0%, rgb(75 130 193) 25%, rgb(36 87 145) 100% );
            }

            .form-box .button {
                position: relative;
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

            .form-box .button.inline {
                display: inline-block;
            }

            .option-buttons-container {
                padding: 10px 0;
            }

            .form-box .button.red {
                background: linear-gradient(
                    120deg,
                    rgb(255 0 0) 0%,
                    rgb(255 65 65) 25%,
                    rgb(255 204 204) 100%
                );
            }

            .form-box .button.dark {
                background: linear-gradient(
                    120deg,
                    rgb(44 44 44) 0%,
                    rgb(79 79 79) 25%,
                    rgb(88 88 88) 100%
                );
            }

            .form-box .button.blue {
                background: linear-gradient(
                    120deg,
                    rgb(1 101 225) 0%,
                    rgb(22 166 251) 25%,
                    rgb(244 244 244) 100%
                );
            }

            .form-box .button.sky-blue {
                background: linear-gradient(
                    120deg,
                    rgb(0 174 240) 0%,
                    rgb(0 174 240) 25%,
                    rgb(224 241 247) 100%
                );
            }

            .form-box .button.darker-blue {
                background: linear-gradient(
                    120deg,
                    rgb(9 103 194) 0%,
                    rgb(9 103 194) 25%,
                    rgb(97 153 210) 100%
                );
            }

            .form-box .button.light-blue {
                background: linear-gradient(
                    120deg,
                    rgb(229 243 255) 0%,
                    rgb(226 244 255) 25%,
                    rgb(255 255 255) 100%
                );
                color: #555;
            }

            .form-box .button.light-blue.disabled {
                background: white !important;
                color: #AAA !important;
            }

            .form-box .button img {
                position: absolute;
                top: 7px;
                right: 10px;
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

            .edit-long-url-feedback {
                font-size: 12px;
            }

            .form-box-feedback.error {
                color: red;
            }

            .form-box-feedback.success {
                color: green;
            }

            .form-box-feedback.info {
                color: gray;
            }

            .form-link {
                width: 100%;
                margin-top: 8px;
                font-size: 14px;
                display: block;
            }

            #resend-verification-email-link {
                margin-bottom: 5px;
            }

            #pa-view,
            #my-links-view {
                width: 85%;
            }

            #my-links-view-title {
                margin-bottom: 22px;
            }

            #my-links-view-title.has-visible-list-options {
                margin-bottom: 0px;
            }

            #profile-view .profile-permissions {
                margin-top: 10px;
            }
            #profile-view .profile-permissions ul {
                font-size: 14px;
                padding: 0 0 0 10px;
            }

            #profile-view .profile-permissions ul li {
                margin-bottom: 8px;
            }

            .loading-overlay {
                position: absolute;
                background-color: rgba(255,255,255,0.5);
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-repeat: no-repeat;
                background-position: center;
                background-size: 50px;
                background-image: url("{{ asset('/img/loading.gif') }}");
                z-index: 99999;
            }

            .dashboard-item-container .dashboard-item {
                text-align: center;
                display: block;
                cursor: pointer;
                margin-right: 14px;
                margin-bottom: 35px;
            }

            .dashboard-item-container .dashboard-item .dashboard-item-img img {
                width: 100%;
                max-width: 30px;
            }

            .dashboard-back-button {
                text-align: center;
                color: #333333;
                background: #EEE;
                padding: 4px;
                display: block;
                margin: auto;
                margin-top: 10px;
                margin-bottom: 10px;
                cursor: pointer;
            }

            .dashboard-list-items-container .dashboard-list-item {
                padding: 10px;
                background: #245691;
                margin-bottom: 10px;
                color: white;
                cursor: pointer;
            }

            .dashboard-list-items-container .dashboard-list-item:hover {
                background-color: #318fff;
            }

            .dashboard-filter-container .input-container {
                margin-right: 10px;
            }

            .dashboard-filter-container .button {
                display: inline-block;
                margin-bottom: 10px;
            }


            #dashboard-edit {
                padding: 10px;
                border: 1px solid #EEE;
                margin: 20px 0;
            }

            #dashboard-edit .other-options {
                font-size: 12px;
                text-align: right;
                margin: 10px 0 0;
            }

            #dashboard-edit .button {
                display: inline-block;
                margin-right: 10px;
            }

            #dashboard-edit-title {
                font-size: 18px;
                font-weight: bold;
                margin-bottom: 10px;
            }

            #dashboard-results-table-container {
                overflow: auto;
                margin-top: 4px;
            }

            .dashboard-results-container {
                width: 100%;
            }

            .dashboard-results-container tr:hover td {
                background: rgba(0,0,0, 0.3);
                color: white;
                cursor: pointer;
            }

            .dashboard-results-container th {
                background: #333333;
                color: #FFFFFF;
                padding: 10px;
            }

            .dashboard-results-container td {
                padding: 10px;
                background: rgba(0,0,0, 0.1);
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

            .mtop-10 {
                margin-top: 10px;
            }

            .form-box .external-logins {
                margin-top: 22px;
            }

            .form-box .external-logins .button {
                margin-bottom: 10px;
            }

            .form-box .list-container {

            }

            .pagination-container {
                font-size: 12px;
                text-align: center;
            }

            .pagination-container a {
                margin-right: 12px;
            }

            .pagination-container a.current {
                text-decoration: none;
                color: #888;
            }

            .pagination-container a:last-child {
                margin-right: 0;
            }

            .form-box .list-container .list-item {
                margin-bottom: 10px;
                border: 1px solid #EEEEEE;
                padding: 10px;
                position: relative;
            }

            .form-box .list-container .list-item-options {
                position: absolute;
                right: 8px;
                background-image: url("{{ asset('/img/acc-settings.png') }}");
                background-size: contain;
                width: 24px;
                height: 24px;
                cursor: pointer;
            }

            .form-box .list-container .list-item-options-container {
                position: absolute;
                right: 10px;
                background: #ffffff;
                top: 38px;
                color: #000000;
                box-shadow: 0 1px 3px 0 rgb(0 0 0 / 10%), 0 1px 2px 0 rgb(0 0 0 / 6%);
            }

            .form-box .list-container .list-item-options-option {
                padding: 4px 12px;
                font-size: 14px;
                height: 24px;
                line-height: 24px;
                border-bottom: 1px solid #EEEEEE;
                cursor: pointer;
            }

            .form-box .list-container .list-item-options-option img {
                height: 24px;
                float: left;
                margin-right: 10px;
            }

            .form-box .list-container .list-item .long-url-label {
                font-size: 12px;
            }

            .form-box .list-container .list-item .short-url-label {
                margin-top: 14px;
                font-size: 14px;
            }

            .form-box .list-container .list-item .info-row .info-col {
                display: inline-block;
                margin-right: 16px;
                margin-top: 10px;
                font-size: 12px;
            }
            .form-box .list-container .list-item .short-url input {
                color: #a57212;
                width: 100%;
                padding: 12px 6px;
                box-sizing: border-box;
                border: 1px solid #EEE;
            }

            .form-box .list-container .list-item .long-url {
                color: gray;
                overflow: hidden;
                word-break: break-word;
            }
            .form-box .list-container .list-item .generic-edit-link-1 {
                font-size: 12px;
                text-decoration: underline;
                cursor: pointer;
                margin-right: 14px;
            }

            .form-box .list-container .list-item .previous-urls-container {
                position: absolute;
                background: white;
                border: 1px dashed #d7b262;
                padding: 10px;
                z-index: 9999;
                left: 10px;
                right: 10px;
            }

            .form-box .list-container .list-item .previously-used-link {
                border-bottom: 1px dashed #d7b262;
                padding: 10px;
                word-break: break-all;
                cursor: pointer;
                font-size: 12px;
            }

            .form-box .list-container .list-item .previously-used-link:last-child {
                border-bottom: 0;
            }

           /*  .form-box .list-container .list-item .previously-used-link .link {

            } */
            .form-box .list-container .list-item .previously-used-link .time-range {
                color: #555;
                font-size: 10px;
                margin-bottom: 3px;
            }

            #logo-top-mobile {
                max-width: 115px;
            }

            #logo-top {
                cursor: pointer;
                display: none;
            }

            #logo-top-container {
                margin-top: 40px;
                text-align: center;
            }

            #current-presentation {
                display: none;
            }

            #shorten-urls {
                padding-top: 24px;
            }

            #mobile-menu-toggle {
                position: absolute;
                top: 10px;
                left: 10px;
            }

            #mobile-menu-toggle .menu-bar {
                height: 4px;
                width: 30px;
                background: #000000;
                margin-bottom: 4px;
            }

            #menu-top,
            #menu-top-acc {
                display: none;
            }

            #menu-top-user-name,
            #menu-mobile-user-name
            {
                color: #ba7d0a;
            }

            #menu-mobile {
                position: absolute;
                top: 0;
                width: 100%;
                height: 100%;
                background: #ffffff;
                z-index: 99999;
                padding-top: 50px;
                overflow: auto;
                bottom: 0;
            }

            #menu-mobile .close-btn {
                position: absolute;
                top: 0;
                text-align: center;
                display: block;
                width: 100%;
                color: #888;
                padding: 10px 0;
                background: #EEE;
            }

            #menu-mobile .close-btn .x {
                color: #b10000;
                position: absolute;
                left: 10px;
            }

            #menu-mobile .menu-group-title {
                margin-top: 10px;
                font-weight: bold;
                padding-left: 6px;
            }

            #menu-top .menu-item,
            #menu-mobile .menu-item {
                display: block;
                padding: 10px;
                font-size: 14px;
                border-bottom: 1px solid #EEE;
            }

            .profile-pic-container {
                text-align: center;
                margin-bottom: 10px;
            }
            img.profile-pic-big {
                -webkit-border-radius: 100px;
                -moz-border-radius: 100px;
                border-radius: 100px;
                width: 100px;
            }

            @media (min-width: 1024px) {

                #pa-view,
                #my-links-view {
                    width: 50%;
                    max-width: unset;
                }

                .dashboard-item-container .dashboard-item {
                    display: inline-block;
                    margin-bottom: 4px;
                }

                #menu-top,
                #menu-top-acc {
                    display: block;
                }


                #mobile-menu-toggle {
                    display: none;
                }

                .form-box.overlay {
                    display: block;
                    position: absolute;
                    top: 34px;
                    right: 2%;
                }

                #logo-top-mobile,
                #logo-top-mobile-desc {
                    display: none;
                }

                #logo-top {
                    display: block;
                    max-width: 60px;
                }

                #logo-top-container {
                    margin-left: 10%;
                    text-align: left;
                    display: inline-block;
                }

                #menu-top {
                    position: absolute;
                    top: 10px;
                    right: 9%;
                    background: #ffffff;
                    -webkit-border-radius: 12px;
                    -moz-border-radius: 12px;
                    border-radius: 12px;

                    box-shadow: 0 1px 3px 0 rgb(0 0 0 / 10%), 0 1px 2px 0 rgb(0 0 0 / 6%);
                }

                #menu-top .menu-item {
                    display: inline-block;
                    border-right: 1px solid #EEE;
                    padding-right: 16px;

                    cursor: pointer;
                    border-bottom: 0;
                }

                #menu-top .menu-item:last-child {
                    border-right: 0;
                }

                #menu-top .menu-item:hover,
                #menu-top-acc .menu-item:hover {
                    text-decoration: underline;
                }


                #menu-top-acc {
                    width: 70px;
                    position: absolute;
                    top: 10px;
                    right: 2%;
                    background: #ffffff;
                    -webkit-border-radius: 12px;
                    -moz-border-radius: 12px;
                    border-radius: 12px;
                    box-shadow: 0 1px 3px 0 rgb(0 0 0 / 10%), 0 1px 2px 0 rgb(0 0 0 / 6%);
                    height: 38px;

                    cursor: pointer;
                }

                #menu-top-acc .settings-icon {
                    display: inline-block;
                    max-width: 24px;
                    margin-top: 7px;
                    margin-left: 8px;
                }

                #menu-top-acc .settings-icon img {
                    width: 100%;
                }

                #menu-top-acc .profile-pic  {
                    display: inline-block;
                    background: gray;
                    width: 24px;
                    height: 24px;
                    -webkit-border-radius: 30px;
                    -moz-border-radius: 30px;
                    border-radius: 30px;
                    margin-left: 0px;

                    font-size: 16px;
                    overflow: hidden;
                    text-align: center;
                    line-height: 24px;
                    color: #FFFFFF;

                    background-size: contain;
                }

                #menu-acc-items {
                    background: white;
                    width: 200px;
                    position: absolute;
                    top: 44px;
                    right: 2%;
                    margin-top: 12px;
                    -webkit-border-radius: 12px;
                    -moz-border-radius: 12px;
                    border-radius: 12px;
                    box-shadow: 0 1px 3px 0 rgb(0 0 0 / 10%), 0 1px 2px 0 rgb(0 0 0 / 6%);
                    z-index: 9999999;
                }

                #menu-acc-items .menu-item
                {
                    padding: 10px;
                    border-bottom: 1px solid #EEE;
                    font-size: 14px;
                    cursor: pointer;
                }

                #menu-acc-items .menu-item:hover {
                    text-decoration: underline;
                }

                #menu-acc-items .menu-item:last-child
                {
                    border-bottom: 0;
                }

                #current-presentation {
                    display: block;
                    position: absolute;
                    left: 43%;
                }

                #cp-title {
                    font-size: 100px;
                    font-weight: bold;
                    line-height: 80px;
                    letter-spacing: -6px;
                }

                #cp-desc {
                    font-size: 34px;
                    font-weight: lighter;
                    line-height: 31px;
                    margin-top: 20px;
                    font-family: system-ui;
                }

                #shorten-urls,
                #form-box-with-shortlink,
                #form-box-shortlink-requested {
                    margin-left: 10%;
                    margin-top: 60px;
                }
            }

            .grecaptcha-badge {
                display: none !important;
            }

            .form-box .gray-note {
                color: #999;
                font-size: 12px;
                margin-bottom: 10px;
            }

            .cookie-notice-bar {
                background: rgba(255,255,255,0.9);
                z-index: 999999;
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                padding: 10px;
                font-size: 12px;
                color: #333;
                text-align: center;
                cursor: pointer;
            }

            .close-cookie-notice {
                background: linear-gradient( 120deg, rgb(102, 147, 179) 0%, rgb(102, 157, 183) 25%, rgb(100, 186, 196) 100% );
                display: inline-block;
                padding: 4px 10px;
                color: white;
                margin-left: 10px;
            }

            table.limit-table th {
                color: white;
                background: #333;
                padding: 5px;
            }

            table.limit-table td {
                background: #EEEEEE;
                padding: 5px;
            }

        </style>

        @if(isset($enableCaptcha) && $enableCaptcha === true)
            <script>
                window._enableCaptcha = true;
            </script>
        @endif

        <script>

            Array.prototype.diff = function(a) {
                return this.filter(function(i) {return a.indexOf(i) < 0;});
            };

            function hideOnClickOutside(element, exceptions, optionalCallback) {
                const outsideClickListener = event => {
                    if (
                        !element.contains(event.target) && isVisible(element)
                        &&
                        !exceptions.includes(event.target)
                    ) {
                        element.style.display = 'none';
                        removeClickListener();

                        if (typeof optionalCallback === 'function') {
                            optionalCallback();
                        }
                    }
                }

                const removeClickListener = () => {
                    document.removeEventListener('click', outsideClickListener);
                }

                document.addEventListener('click', outsideClickListener);
            }

            const isVisible = elem => !!elem && !!( elem.offsetWidth || elem.offsetHeight || elem.getClientRects().length );

            window._authManager = {
                at: null,

                isAuthenticated: false,
                isLoggedIn: false,

                userPermissions: null,
                userData: null,

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
                    userPasswordChangedEvent: null,
                    userPasswordChangeFailedEvent: null,
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

                    this.customEvents.userPasswordChangeFailedEvent =
                        document.createEvent("Event");
                    this.customEvents.userPasswordChangeFailedEvent.initEvent(
                        "userPasswordChangeFailed",
                        true,
                        true
                    );
                },

                authenticate: function () {
                    var xhr = new XMLHttpRequest();
                    xhr.withCredentials = true;

                    xhr.addEventListener("readystatechange", function () {
                        if (this.status === 200 && this.readyState === 4) {
                            const resObj = JSON.parse(this.response);
                            window._authManager.at = resObj.at;
                            window._authManager.isAuthenticated = true;
                            window._authManager.isLoggedIn = resObj.guest === 0;
                            window._authManager.userPermissions = resObj.permissions;

                            if (resObj.guest === 0) {
                                window._authManager.userData = resObj.data;
                            }

                            // trigger userAuthenticated event
                            document.dispatchEvent(
                                window._authManager.customEvents
                                    .userAuthenticatedEvent
                            );
                        }
                    });

                    var dWidth = (window.innerWidth > 0) ? window.innerWidth : screen.width;
                    var dHeight = (window.innerHeight > 0) ? window.innerHeight : screen.height;
                    var ua = navigator.userAgent;


                    var urlStr = this.api.url + this.api.endpoints.authentication + '?dw=' + dWidth + '&dh=' + dHeight + '&ua=' + ua;
                    xhr.open(
                        "POST",
                        urlStr
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
                                window.location.href = '/';
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
                            if (this.status === 401) {
                                window.location.reload();
                            }

                            const resObj = JSON.parse(this.response); //TODO: Catch exception

                            if (this.status === 200) {
                                window._authManager.at = resObj.at;
                                window._authManager.isLoggedIn = resObj.guest
                                    ? false
                                    : true;

                                window._authManager.userPermissions = resObj.permissions;
                                window._authManager.userData = resObj.data;

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

                    var credentialsQueryStr =
                        "?email=" + email + "&password=" + password;

                    if(
                        typeof captchaToken !== 'undefined'
                        &&
                        captchaToken != null
                        &&
                        captchaToken.length > 0
                    ) {
                        credentialsQueryStr += '&g-recaptcha-response=' + captchaToken;
                    }

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
                            if (this.status === 401) {
                                window.location.reload();
                            }

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

                    var credentialsQueryStr =
                        "?name=" + name + "&email=" + email + "&email_confirmation=" + emailConfirmation + "&password=" + password + "&password_confirmation=" + passwordConfirmation;

                    if (
                        typeof captchaToken !== 'undefined'
                        &&
                        captchaToken != null
                        &&
                        captchaToken.length > 0
                    ) {
                        credentialsQueryStr += '&g-recaptcha-response=' + captchaToken;
                    }

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

                    xhr.addEventListener("readystatechange", function () {
                        if (this.readyState === 4) {
                            if (this.status === 401) {
                                window.location.reload();
                            }
                        }
                    });

                    email = encodeURIComponent(email);

                    var credentialsQueryStr = "?email=" + email;

                    if (
                        typeof captchaToken !== 'undefined'
                        &&
                        captchaToken != null
                        &&
                        captchaToken.length > 0
                    ) {
                        credentialsQueryStr += '&g-recaptcha-response=' + captchaToken;
                    }

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

                    xhr.addEventListener("readystatechange", function () {
                        if (this.readyState === 4) {
                            if (this.status === 401) {
                                window.location.reload();
                            }
                        }
                    });

                    email = encodeURIComponent(email);

                    var credentialsQueryStr = "?email=" + email;

                    if (
                        typeof captchaToken !== 'undefined'
                        &&
                        captchaToken != null
                        &&
                        captchaToken.length > 0
                    ) {
                        credentialsQueryStr += '&g-recaptcha-response=' + captchaToken;
                    }

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

                    var credentialsQueryStr =
                        "?new_password=" + newPassword + '&new_password_confirmation=' + newPasswordConfirmation;

                    if (
                        typeof captchaToken !== 'undefined'
                        &&
                        captchaToken != null
                        &&
                        captchaToken.length > 0
                    ) {
                        credentialsQueryStr += '&g-recaptcha-response=' + captchaToken;
                    }


                    xhr.addEventListener("readystatechange", function () {
                        if (this.readyState === 4) {
                            if (this.status === 401) {
                                window.location.reload();
                            }

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
                            ) {

                                window._authManager.customEvents.userPasswordChangeFailedEvent.reason =
                                    resObj.message;

                                if (
                                    typeof resObj.errors !== 'undefined'
                                    ||
                                    typeof resObj.error_id !== 'undefined'
                                ) {
                                    window._authManager.customEvents.userPasswordChangeFailedEvent.error_id = resObj.error_id;
                                }
                                window._authManager.customEvents.userPasswordChangeFailedEvent.isError = true;


                                document.dispatchEvent(
                                    window._authManager.customEvents
                                        .userPasswordChangeFailedEvent
                                );

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
                },
                userHasPermission: function (permissionName) {
                    return (
                        this.userPermissions !== null
                        &&
                        typeof this.userPermissions[permissionName] !== 'undefined'
                        &&
                        window._authManager.userPermissions[permissionName] == true
                    );
                },
                hasDataItem: function (dataItemName) {
                    if (
                        window._authManager.userData != null
                    ) {
                        if (
                            typeof window._authManager.userData[dataItemName] !== 'undefined'
                        ) {
                            return true;
                        }
                    }

                    return false;
                },
                getDataItem: function (dataItemName) {
                    if (
                        this.hasDataItem(dataItemName)
                    ) {
                        return window._authManager.userData[dataItemName];
                    }
                    return null;
                },
                getUserLimit: function (limitName) {
                    if (
                        window._authManager.userData != null
                    ) {
                        if (
                            typeof window._authManager.userData.limits !== 'undefined'
                            &&
                            typeof window._authManager.userData.limits[limitName] !== 'undefined'
                        ) {
                            if (window._authManager.userData.limits[limitName] === null) {
                                return 'não definido';
                            }

                            return window._authManager.userData.limits[limitName];
                        }
                    }

                    return '';
                }
            };

            window._authManager.initialize();

            window.App = {
                currentView: null,
                showComponents: function(componentNamesArr, conditionCallback = null) {
                    for (var i = 0; i < componentNamesArr.length; i++) {
                        const componentName = componentNamesArr[i];

                        if(
                            conditionCallback === null
                        ) {
                            window.App.Components[componentName].show();
                        } else {
                            if(
                                typeof conditionCallback === 'function'
                                &&
                                conditionCallback(
                                    window.App.Components[componentName]
                                ) === true
                            ) {
                                window.App.Components[componentName].show();
                            }
                        }

                    }
                },
                hideComponents: function(componentNamesArr, conditionCallback = null) {
                    for (var i = 0; i < componentNamesArr.length; i++) {
                        const componentName = componentNamesArr[i];

                        if(
                            conditionCallback === null
                            &&
                            typeof window.App.Components[componentName].hide === 'function'
                        ) {
                            window.App.Components[componentName].hide();
                        } else {
                            if(
                                typeof conditionCallback === 'function'
                                &&
                                conditionCallback(
                                    window.App.Components[componentName]
                                ) === true
                            ) {
                                window.App.Components[componentName].hide();
                            }
                        }
                    }
                },
                getCurrentView: function() {
                    return this.Views[this.currentView];
                },
                getCurrentViewStickyComponents: function(viewName) {
                    return this.getCurrentView().components.sticky;
                },
                hideNonStickyComponents: function() {
                    this.hideComponents(
                        Object.keys(window.App.Components).diff(window.App.getCurrentViewStickyComponents())
                    );
                },
                isMobileSize: function () {
                    var width = (window.innerWidth > 0) ? window.innerWidth : screen.width;
                    return width < 1024;
                },
                Components: {
                    LogoTop: {
                        hasInitialized: false,
                        containerEl: function () {
                            return document.getElementById('logo-top-container');
                        },
                        desktopEl: function () {
                            return document.getElementById('logo-top');
                        },
                        mobileEl: function () {
                            return document.getElementById('logo-top-mobile');
                        },
                        show: function () {
                            this.initialize();
                            this.containerEl().style.display = 'block';
                        },
                        hide: function () {
                            this.containerEl().style.display = 'none';
                        },
                        initialize: function () {
                            if (this.hasInitialized === false) {

                                this.desktopEl().onclick = function(e) {
                                    window.App.Views.HomePage.show();
                                };

                                this.mobileEl().onclick = function(e) {
                                    window.App.Views.HomePage.show();
                                };

                                this.hasInitialized = true;
                            }
                        }
                    },
                    MenuToggleMobile: {
                        hasInitialized: false,
                        el: function () {
                            return document.getElementById('mobile-menu-toggle');
                        },
                        hide: function () {
                            if (!window.App.isMobileSize()) {
                                this.el().style.display = 'none';
                            }
                        },
                        show: function () {
                            this.initialize();

                            // menu hidden by default in mobile view
                            if (window.App.isMobileSize()) {
                                this.el().style.display = 'block';
                            } else {
                                this.hide();
                            }
                        },
                        initialize: function () {
                            if (this.hasInitialized == false) {

                                this.el().onclick = function(e) {

                                    if (
                                        window._authManager.isLoggedIn
                                    ) {
                                        window.App.Components.MenuMobile.GuestItems.hide();
                                        window.App.Components.MenuMobile.UserItems.show();
                                    } else {
                                        window.App.Components.MenuMobile.UserItems.hide();
                                        window.App.Components.MenuMobile.GuestItems.show();
                                    }

                                    window.App.Components.MenuMobile.show();
                                };

                                const $this = this;
                                window.addEventListener("resize", function () {
                                    $this.show();
                                });

                                this.hasInitialized = true;
                            }
                        }
                    },
                    MenuMobile: {
                        hasInitialized: false,
                        el: function () {
                            return document.getElementById('menu-mobile');
                        },
                        show: function () {
                            this.initialize();
                            this.el().style.display = 'block';
                        },
                        hide: function () {
                            this.el().style.display = 'none';
                        },
                        Items: {
                            CloseMenu: {
                                hasInitialized: false,
                                el: function () {
                                    return document.getElementById('close-menu-mobile');
                                },
                                initialize: function () {
                                    if ( this.hasInitialized == false ) {

                                        this.el().onclick = function (e) {
                                            window.App.Components.MenuMobile.hide();
                                        };

                                        this.hasInitialized = true;
                                    }
                                }
                            },
                            MyLinks: {
                                hasInitialized: false,
                                el: function () {
                                    return document.getElementById('menu-mobile-item-my-links');
                                },
                                show: function () {
                                    this.el().style.display = 'inline-block';
                                },
                                initialize: function () {
                                    if ( this.hasInitialized == false ) {

                                        this.el().onclick = function (e) {
                                            window.App.Components.MenuMobile.hide();
                                            window.App.Views.MyLinks.show();
                                        };

                                        this.hasInitialized = true;
                                    }
                                }
                            },
                            ContactUs: {
                                hasInitialized: false,
                                el: function () {
                                    return document.getElementById('menu-mobile-item-contact-us');
                                },
                                show: function () {
                                    this.el().style.display = 'inline-block';
                                },
                                initialize: function () {
                                    if ( this.hasInitialized == false ) {

                                        this.el().onclick = function (e) {
                                            window.App.Components.MenuMobile.hide();//TODO: test if this line is needed
                                            window.App.Views.ContactUs.show();
                                        };

                                        this.hasInitialized = true;
                                    }
                                }
                            },
                        },
                        GuestItems: {
                            el: function () {
                                return document.getElementById('menu-mobile-acc-items-guest');
                            },
                            show: function () {
                                this.initialize();
                                this.el().style.display = 'block';
                            },
                            hide: function () {
                                this.el().style.display = 'none';
                            },
                            Items: {
                                Login: {
                                    hasInitialized: false,
                                    el: function () {
                                        return document.getElementById('menu-mobile-acc-login');
                                    },
                                    initialize: function () {
                                        if ( this.hasInitialized == false ) {

                                            this.el().onclick = function (e) {
                                                window.App.Views.Login.show();
                                            };

                                            this.hasInitialized = true;
                                        }
                                    }
                                },
                                Register: {
                                    hasInitialized: false,
                                    el: function () {
                                        return document.getElementById('menu-mobile-acc-register');
                                    },
                                    initialize: function () {
                                        if ( this.hasInitialized == false ) {

                                            this.el().onclick = function (e) {
                                                window.App.Views.Register.show();
                                            };

                                            this.hasInitialized = true;
                                        }
                                    }
                                },
                            },
                            initialize: function () {
                                this.Items.Login.initialize();
                                this.Items.Register.initialize();
                            }
                        },
                        UserItems: {
                            el: function () {
                                return document.getElementById('menu-mobile-acc-items-user');
                            },
                            show: function () {
                                this.initialize();
                                this.el().style.display = 'block';
                            },
                            hide: function () {
                                this.el().style.display = 'none';
                            },
                            Items: {
                                LogoutBtn: {
                                    hasInitialized: false,
                                    el: function () {
                                        return document.getElementById('menu-mobile-acc-logout');
                                    },
                                    initialize: function () {
                                        if (this.hasInitialized === false) {
                                            this.el().onclick = function (e) {
                                                e.target.innerText = 'A terminar sessão..';
                                                window._authManager.logout();
                                            };
                                            this.hasInitialized = true;
                                        }
                                    }
                                },
                                UserProfile: {
                                    hasInitialized: false,
                                    el: function () {
                                        return document.getElementById('menu-mobile-user-name');
                                    },
                                    show: function () {
                                        this.setUserName();
                                        this.el().style.display = 'block';
                                    },
                                    hide: function () {
                                        this.el().style.display = 'none';
                                    },
                                    setUserName: function () {
                                        if (
                                            window._authManager.userData != null
                                        ) {
                                            if (
                                                typeof window._authManager.userData.name !== 'undefined'
                                                &&
                                                typeof window._authManager.userData.name === 'string'
                                            ) {
                                                this.el().innerText = window._authManager.userData.name;
                                            }
                                        }
                                    },
                                    initialize: function () {
                                        this.show();

                                        if (this.hasInitialized === false) {

                                            this.el().onclick = function(e) {
                                                window.App.Views.Profile.show();
                                            };

                                            this.hasInitialized = true;
                                        }
                                    }
                                },
                                PA: {
                                    hasInitialized: false,
                                    panelLocation: null,
                                    el: function () {
                                        return document.getElementById('menu-mobile-admin-dashboard');
                                    },
                                    show: function () {
                                        if ( window._authManager.userHasPermission('is_admin') ) {
                                            this.el().style.display = 'block';
                                        }
                                    },
                                    hide: function () {
                                        this.el().style.display = 'none';
                                    },
                                    setPanelLocation: function () {
                                        if (
                                            window._authManager.userHasPermission('is_admin')
                                            &&
                                            typeof window._authManager.userData.adminPanel !== 'undefined'
                                        ) {
                                            this.panelLocation = window._authManager.userData.adminPanel;
                                        }
                                    },
                                    initialize: function () {
                                        if (this.hasInitialized === false) {
                                            const $this = this;

                                            if (window._authManager.isAuthenticated) {
                                                $this.setPanelLocation();
                                                $this.show();
                                            } else {
                                                document.addEventListener('userAuthenticated', (e) => {
                                                    $this.setPanelLocation();
                                                    $this.show();
                                                }, false);
                                            }

                                            document.addEventListener('userLoggedIn', (e) => {
                                                $this.setPanelLocation();
                                                $this.show();
                                            }, false);

                                            this.el().onclick = function (e) {
                                                if (
                                                    typeof window.App.Views.PA !== 'undefined'
                                                ) {
                                                    window.history.pushState(null, 'Painel de Administração', $this.panelLocation);
                                                    window.App.Views.PA.show();
                                                } else {
                                                    e.target.innerText = 'A carregar painel de administração..';
                                                    window.location.href = $this.panelLocation;
                                                }
                                            };
                                            this.hasInitialized = true;
                                        }
                                    }
                                }
                            },
                            initialize: function () {
                                this.Items.LogoutBtn.initialize();
                                this.Items.UserProfile.initialize();
                                this.Items.PA.initialize();
                            }
                        },
                        initialize: function () {
                            this.Items.CloseMenu.initialize();
                            this.Items.MyLinks.initialize();
                            this.Items.ContactUs.initialize();

                            if (this.hasInitialized === false) {
                                const $this = this;
                                window.addEventListener("resize", function () {
                                    $this.hide();
                                });

                                this.hasInitialized = true;
                            }
                        }
                    },
                    MenuTop: {
                        hasInitialized: false,
                        el: function () {
                            return document.getElementById('menu-top');
                        },
                        show: function () {
                            this.initialize();

                            // menu hidden by default in mobile view
                            if (!window.App.isMobileSize()) {
                                this.el().style.display = 'block';
                            } else {
                                this.hide();
                            }
                        },
                        hide: function () {
                            this.el().style.display = 'none';
                        },
                        Items: {
                            MyLinks: {
                                hasInitialized: false,
                                el: function () {
                                    return document.getElementById('menu-item-my-links');
                                },
                                show: function () {
                                    this.el().style.display = 'inline-block';
                                },
                                hide: function () {
                                    this.el().style.display = 'none';
                                },
                                initialize: function () {
                                    if ( this.hasInitialized == false ) {

                                        this.el().onclick = function (e) {
                                            window.App.Views.MyLinks.show();
                                        };

                                        this.hasInitialized = true;
                                    }
                                }
                            },
                            ContactUs: {
                                hasInitialized: false,
                                el: function () {
                                    return document.getElementById('menu-item-contact-us');
                                },
                                show: function () {
                                    this.el().style.display = 'inline-block';
                                },
                                initialize: function () {
                                    if ( this.hasInitialized == false ) {

                                        this.el().onclick = function (e) {
                                            window.App.Views.ContactUs.show();
                                        };

                                        this.hasInitialized = true;
                                    }
                                }
                            },
                        },
                        initialize: function () {
                            this.Items.MyLinks.initialize();
                            this.Items.ContactUs.initialize();

                            if (this.hasInitialized === false) {

                                const $this = this;
                                window.addEventListener("resize", function () {
                                    $this.show();
                                });

                                this.hasInitialized = true;
                            }
                        }
                    },
                    MenuAccTop: {
                        hasInitialized: false,
                        isOpen: false,
                        el: function () {
                            return document.getElementById('menu-top-acc');
                        },
                        itemsEl: function () {
                            return document.getElementById('menu-acc-items');
                        },
                        open: function () {
                            this.itemsEl().style.display = 'block';
                            this.isOpen = true;

                            const $this = this;
                            hideOnClickOutside(this.itemsEl(), [this.el()], function () {
                                $this.isOpen = false;
                            });
                        },
                        close: function () {
                            this.itemsEl().style.display = 'none';
                            this.isOpen = false;
                        },
                        show: function (){
                            this.initialize();

                            // menu hidden by default in mobile view
                            if (!window.App.isMobileSize()) {
                                this.el().style.display = 'block';

                                if (this.isOpen) {
                                    this.close();
                                }
                            } else {
                                this.hide();
                            }


                        },
                        hide: function (){
                            this.el().style.display = 'none';
                        },
                        GuestItems: {
                            el: function () {
                                return document.getElementById('menu-acc-items-guest');
                            },
                            show: function () {
                                this.initialize();
                                this.el().style.display = 'block';
                            },
                            hide: function () {
                                this.el().style.display = 'none';
                            },
                            Items: {
                                Login: {
                                    hasInitialized: false,
                                    el: function () {
                                        return document.getElementById('menu-top-acc-login');
                                    },
                                    initialize: function () {
                                        if ( this.hasInitialized == false ) {

                                            this.el().onclick = function (e) {
                                                window.App.Views.Login.show();
                                            };

                                            this.hasInitialized = true;
                                        }
                                    }
                                },
                                Register: {
                                    hasInitialized: false,
                                    el: function () {
                                        return document.getElementById('menu-top-acc-register');
                                    },
                                    initialize: function () {
                                        if ( this.hasInitialized == false ) {

                                            this.el().onclick = function (e) {
                                                window.App.Views.Register.show();
                                            };

                                            this.hasInitialized = true;
                                        }
                                    }
                                },
                            },
                            initialize: function () {
                                this.Items.Login.initialize();
                                this.Items.Register.initialize();
                            }
                        },
                        UserItems: {
                            el: function () {
                                return document.getElementById('menu-acc-items-user');
                            },
                            show: function () {
                                this.initialize();
                                this.el().style.display = 'block';
                            },
                            hide: function () {
                                this.el().style.display = 'none';
                            },
                            Items: {
                                LogoutBtn: {
                                    hasInitialized: false,
                                    el: function () {
                                        return document.getElementById('menu-top-acc-logout');
                                    },
                                    initialize: function () {
                                        if (this.hasInitialized === false) {
                                            this.el().onclick = function (e) {
                                                e.target.innerText = 'A terminar sessão..';
                                                window._authManager.logout();
                                            };
                                            this.hasInitialized = true;
                                        }
                                    }
                                },
                                UserProfile: {
                                    hasInitialized: false,
                                    el: function () {
                                        return document.getElementById('menu-top-user-name');
                                    },
                                    show: function () {
                                        this.setUserName();
                                        this.el().style.display = 'block';
                                    },
                                    hide: function () {
                                        this.el().style.display = 'none';
                                    },
                                    setUserName: function () {
                                        if (
                                            window._authManager.userData != null
                                        ) {
                                            if (
                                                typeof window._authManager.userData.name !== 'undefined'
                                                &&
                                                typeof window._authManager.userData.name === 'string'
                                            ) {
                                                this.el().innerText = window._authManager.userData.name;
                                            }
                                        }
                                    },
                                    initialize: function () {
                                        this.show();

                                        if (this.hasInitialized === false) {

                                            this.el().onclick = function(e) {
                                                window.App.Views.Profile.show();
                                            };

                                            this.hasInitialized = true;
                                        }
                                    }
                                },
                                PA: {
                                    hasInitialized: false,
                                    panelLocation: null,
                                    el: function () {
                                        return document.getElementById('menu-top-admin-dashboard');
                                    },
                                    show: function () {
                                        if ( window._authManager.userHasPermission('is_admin') ) {
                                            this.el().style.display = 'block';
                                        }
                                    },
                                    hide: function () {
                                        this.el().style.display = 'none';
                                    },
                                    setPanelLocation: function () {
                                        if (
                                            window._authManager.userHasPermission('is_admin')
                                            &&
                                            typeof window._authManager.userData.adminPanel !== 'undefined'
                                        ) {
                                            this.panelLocation = window._authManager.userData.adminPanel;
                                        }
                                    },
                                    initialize: function () {
                                        if (this.hasInitialized === false) {
                                            const $this = this;

                                            if (window._authManager.isAuthenticated) {
                                                $this.setPanelLocation();
                                                $this.show();
                                            } else {
                                                document.addEventListener('userAuthenticated', (e) => {
                                                    $this.setPanelLocation();
                                                    $this.show();
                                                }, false);
                                            }

                                            document.addEventListener('userLoggedIn', (e) => {
                                                $this.setPanelLocation();
                                                $this.show();
                                            }, false);

                                            this.el().onclick = function (e) {
                                                if (
                                                    typeof window.App.Views.PA !== 'undefined'
                                                ) {
                                                    window.history.pushState(null, 'Painel de Administração', $this.panelLocation);
                                                    window.App.Views.PA.show();
                                                } else {
                                                    e.target.innerText = 'A carregar painel de administração..';
                                                    window.location.href = $this.panelLocation;
                                                }
                                            };
                                            this.hasInitialized = true;
                                        }
                                    }
                                }
                            },
                            initialize: function () {
                                this.Items.LogoutBtn.initialize();
                                this.Items.UserProfile.initialize();
                                this.Items.PA.initialize();
                            }
                        },
                        Components: {
                            UserProfilePicture: {
                                hasInitialized: false,
                                el: function () {
                                    return document.getElementById('user-profile-pic');
                                },
                                setPicture: function () {
                                    if (
                                        window._authManager.userData != null
                                    ) {
                                        if (
                                            typeof window._authManager.userData.avatar !== 'undefined'
                                            &&
                                            typeof window._authManager.userData.avatar === 'string'
                                        ) {
                                            const el = this.el();
                                            el.innerText = '';
                                            el.style.backgroundImage = 'url("'+window._authManager.userData.avatar+'")';
                                        }
                                    }
                                },
                                hidePicture: function () {
                                    const el = this.el();
                                    el.innerText = '?';
                                    el.style.backgroundImage = 'unset';
                                },
                                initialize: function () {
                                    if ( this.hasInitialized === false ) {
                                        const $this = this;

                                        if (
                                            window._authManager.isAuthenticated
                                        ) {
                                            $this.setPicture();
                                        } else {
                                            $this.hidePicture();
                                            document.addEventListener('userAuthenticated', (e) => {
                                                $this.setPicture();
                                            }, false);
                                        }

                                        document.addEventListener('userLoggedIn', (e) => {
                                            $this.setPicture();
                                        }, false);

                                        this.hasInitialized = true;
                                    }
                                }
                            }
                        },
                        initialize: function () {
                            this.Components.UserProfilePicture.initialize();

                            if (this.hasInitialized == false) {
                                const $this = this;
                                this.el().onclick = function (e) {
                                    e.stopPropagation();

                                    if ($this.isOpen) {
                                        $this.close();
                                        return;
                                    }

                                    $this.open();
                                    if (
                                        window._authManager.isLoggedIn
                                    ) {
                                        $this.GuestItems.hide();
                                        $this.UserItems.show();
                                    } else {
                                        $this.UserItems.hide();
                                        $this.GuestItems.show();
                                    }
                                };

                                window.addEventListener("resize", function () {
                                    $this.show();

                                    if ($this.isOpen) {
                                        $this.close();
                                        return;
                                    }
                                });

                                this.hasInitialized = true;
                            }
                        }
                    },
                    Profile: {
                        el: function () {
                            return document.getElementById('profile-view');
                        },
                        show: function (){
                            this.initialize();
                            this.el().style.display = 'block';
                        },
                        hide: function (){
                            this.el().style.display = 'none';
                        },
                        Components: {
                            CloseBtn: {
                                hasInitialized: false,
                                el: function () {
                                    return document.getElementById('profile-view-close-btn');
                                },
                                initialize: function () {
                                    if (this.hasInitialized == false) {
                                        this.el().onclick = function (e) {
                                            window.App.Views.HomePage.show();
                                        };
                                        this.hasInitialized = true;
                                    }
                                }
                            },
                            UserName: {
                                el: function () {
                                    return document.getElementById('profile-view-user-name');
                                },
                                setUserName: function () {
                                    if (
                                        window._authManager.userData != null
                                    ) {
                                        if (
                                            typeof window._authManager.userData.name !== 'undefined'
                                            &&
                                            typeof window._authManager.userData.name === 'string'
                                        ) {
                                            this.el().innerText = window._authManager.userData.name;
                                        }
                                    }
                                },
                                initialize: function () {
                                    this.setUserName();
                                }
                            },
                            ProfilePicture: {
                                el: function () {
                                    return document.getElementById('profile-pic-big');
                                },
                                containerEl: function () {
                                    return document.getElementById('profile-pic-container');
                                },
                                show: function (){
                                    if (
                                        window._authManager.getDataItem('avatar') != null
                                    ) {
                                        this.setPicture();
                                        this.containerEl().style.display = 'block';
                                    } else {
                                        this.hide();
                                    }
                                },
                                hide: function (){
                                    this.containerEl().style.display = 'none';
                                },
                                setPicture: function () {
                                    const el = this.el();
                                    el.src = window._authManager.getDataItem('avatar');
                                },
                                initialize: function () {
                                    this.show();
                                }
                            },
                            ViewPermissionsBtn: {
                                hasInitialized: false,
                                permissionsVisible: false,
                                el: function () {
                                    return document.getElementById('view-profile-permissions');
                                },
                                permissionsContainerEl: function () {
                                    return document.getElementById('profile-permissions');
                                },
                                permissionsListEl: function () {
                                    return document.getElementById('profile-view-permission-list');
                                },
                                showPermissions: function () {
                                    const permissionItems = this.permissionsListEl().querySelectorAll('[data-permission-needed]');
                                    for(var i = 0; i < permissionItems.length; i++) {
                                        const permissionItem = permissionItems[i];
                                        const permissionNeeded = permissionItem.getAttribute('data-permission-needed');
                                        if (
                                            window._authManager.userHasPermission(permissionNeeded)
                                        ) {
                                            permissionItem.style.display = 'list-item';
                                        } else {
                                            permissionItem.style.display = 'none';
                                        }
                                    }
                                    this.permissionsContainerEl().style.display = 'block';
                                    this.permissionsVisible = true;
                                    this.el().innerText = 'Esconder permissões';
                                },
                                hidePermissions: function () {
                                    this.permissionsContainerEl().style.display = 'none';
                                    this.permissionsVisible = false;
                                    this.el().innerText = 'Ver permissões';
                                },
                                toggleShowPermissions: function () {
                                    if (this.permissionsVisible) {
                                        this.hidePermissions();
                                    } else {
                                        this.showPermissions();
                                    }
                                },
                                initialize: function () {
                                    if (this.hasInitialized == false) {
                                        const $this = this;
                                        this.el().onclick = function (e) {
                                            $this.toggleShowPermissions();
                                        };

                                        this.hasInitialized = true;
                                    }
                                }
                            },
                            ViewLimitsBtn: {
                                hasInitialized: false,
                                limitsVisible: false,
                                el: function () {
                                    return document.getElementById('view-profile-limits');
                                },
                                limitsContainerEl: function () {
                                    return document.getElementById('profile-limits');
                                },
                                limitsListEl: function () {
                                    return document.getElementById('profile-view-limit-list');
                                },
                                showLimits: function () {
                                    const limitItems = this.limitsListEl().querySelectorAll('li');
                                    for(var i = 0; i < limitItems.length; i++) {
                                        const limitItem = limitItems[i];

                                        if (limitItem.hasAttribute('data-permission-needed')) {
                                            const permissionNeeded = limitItem.getAttribute('data-permission-needed');
                                            if (
                                                !window._authManager.userHasPermission(permissionNeeded)
                                            ) {
                                                limitItem.style.display = 'none';
                                            } else {
                                                limitItem.style.display = 'list-item';
                                            }
                                        }

                                        const limits = limitItem.querySelectorAll('[data-limit-name]');
                                        for(var ii = 0; ii < limits.length; ii++) {
                                            const limitEl = limits[ii];
                                            const limitName = limitEl.getAttribute('data-limit-name');
                                            limitEl.innerText = window._authManager.getUserLimit(limitName);
                                        }
                                    }
                                    this.limitsContainerEl().style.display = 'block';
                                    this.limitsVisible = true;
                                    this.el().innerText = 'Esconder limites';
                                },
                                hideLimits: function () {
                                    this.limitsContainerEl().style.display = 'none';
                                    this.limitsVisible = false;
                                    this.el().innerText = 'Ver limites';
                                },
                                toggleShowLimits: function () {
                                    if (this.limitsVisible) {
                                        this.hideLimits();
                                    } else {
                                        this.showLimits();
                                    }
                                },
                                initialize: function () {
                                    if (this.hasInitialized == false) {
                                        const $this = this;
                                        this.el().onclick = function (e) {
                                            $this.toggleShowLimits();
                                        };

                                        this.hasInitialized = true;
                                    }
                                }
                            },
                        },
                        initialize: function () {
                            this.Components.CloseBtn.initialize();
                            this.Components.UserName.initialize();
                            this.Components.ProfilePicture.initialize();
                            this.Components.ViewPermissionsBtn.initialize();
                            this.Components.ViewLimitsBtn.initialize();
                        }
                    },
                    ShortenUrl: {
                        el: function () {
                            return document.getElementById("shorten-urls");
                        },
                        show: function () {
                            this.initialize();
                            this.el().style.display = 'block';
                            const longUrlInput = this.Components.LongUrl.el();
                            longUrlInput.value = "";
                            if (!window.App.isMobileSize()) {
                                longUrlInput.focus();
                            }

                            this.Components.DestinationEmail.show();

                            this.Components.CreateCustomLink.show();
                            this.Components.ChooseCustomSize.show();
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
                                            window.App.Components.ShortenUrl.el().classList.add("has-active-input");
                                            $this.el().focus();
                                        }

                                        this.el().onfocus = function (e) {
                                            e.target.parentNode.classList.add("active");
                                            e.target.value = e.target.value.trim();
                                            window.App.Components.ShortenUrl.el().classList.add("has-active-input");
                                        };

                                        this.el().addEventListener("focusout", function (e) {
                                            e.target.value = e.target.value.trim();
                                            if (e.target.value.length == 0) {
                                                $this.labelEl().parentNode.classList.remove("active");
                                                window.App.Components.ShortenUrl.el().classList.remove("has-active-input");
                                            }
                                        });

                                        this.hasInitialized = true;
                                    }
                                }
                            },
                            DestinationEmail: {
                                hasInitialized: false,
                                el: function () {
                                    return document.getElementById("destination-email");
                                },
                                labelEl: function () {
                                    return document.getElementById("destination-email-label");
                                },
                                containerEl: function () {
                                    return document.getElementById("destination-email-container");
                                },
                                show: function () {
                                    if (
                                        !window._authManager.userHasPermission('send_shortlink_by_email_when_generating')
                                    ) {
                                        return false;
                                    }
                                    this.containerEl().style.display = 'block';
                                },
                                hide: function () {
                                    this.containerEl().style.display = 'none';
                                },
                                initialize: function () {
                                    if (this.hasInitialized == false) {
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

                                        if (
                                            window._authManager.isAuthenticated
                                        ) {
                                            $this.show();
                                        } else {
                                            $this.hide();
                                            document.addEventListener('userAuthenticated', (e) => {
                                                $this.show();
                                            }, false);
                                        }

                                        document.addEventListener('userLoggedIn', (e) => {
                                            $this.show();
                                        }, false);

                                        this.hasInitialized = true;
                                    }
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
                                disable: function () {
                                    this.el().classList.add('disabled');
                                },
                                initialize: function () {
                                    if (this.hasInitialized !== false) {
                                        return;
                                    }

                                    const $this = this;
                                    if (
                                        window._authManager.isAuthenticated
                                    ) {
                                        $this.enable();
                                    } else {
                                        $this.disable();
                                        document.addEventListener('userAuthenticated', (e) => {
                                            $this.enable();
                                        }, false);
                                    }

                                    this.el().onclick = function (e) {
                                        if (
                                            !window._authManager.isAuthenticated
                                            ||
                                            e.target.classList.contains('disabled')
                                        ) {
                                            return false;
                                        }

                                        const longUrlInput = window.App.Components.ShortenUrl.Components.LongUrl.el();
                                        const destinationEmailInput = window.App.Components.ShortenUrl.Components.DestinationEmail.el();

                                        if (longUrlInput.value.length == 0) {
                                            longUrlInput.classList.add('has-error');
                                            return false;
                                        } else {
                                            longUrlInput.classList.remove('has-error');
                                        }

                                        var useCustomSize = false;
                                        var customSize = null;
                                        if (
                                            window.App.Components.ShortenUrl.Components.ChooseCustomSize.optionsVisible
                                        ) {

                                            if (window.App.Components.ShortenUrl.Components.ChooseCustomSize.getSelectOptionValue() == null) {
                                                window.App.Components.ShortenUrl.Components.ChooseCustomSize.flagHasError();
                                                return false;
                                            }

                                            window.App.Components.ShortenUrl.Components.ChooseCustomSize.unflagHasError();
                                            useCustomSize = true;
                                            customSize = window.App.Components.ShortenUrl.Components.ChooseCustomSize.getSelectOptionValue();
                                        }

                                        var func = function (token) {
                                            var xhr = new XMLHttpRequest();
                                            xhr.withCredentials = true;

                                            xhr.addEventListener("readystatechange", function () {
                                                if (this.readyState === 4) {
                                                    if (this.status === 401) {
                                                        window.location.reload();
                                                    }

                                                    e.target.classList.remove('disabled');
                                                    try {
                                                        const jsonResObj = JSON.parse(this.responseText);

                                                        if (this.status === 201) {
                                                            window.App.Components.ShortenUrl.hide();
                                                            window.App.Components.ShortlinkResult.Components.Shortlink.set(
                                                                jsonResObj.shortlink
                                                            );
                                                            window.App.Components.ShortlinkResult.show();
                                                            return;
                                                        }

                                                        if(typeof jsonResObj.message !== 'undefined') {
                                                            window.App.Components.ShortenUrl.Components.Feedback.showError(jsonResObj.message);
                                                        } else {
                                                            window.App.Components.ShortenUrl.Components.Feedback.showError('Ocorreu um erro no nosso servidor..');
                                                        }
                                                    } catch (e) {
                                                        // invalid json something went wrong
                                                        window.App.Components.ShortenUrl.Components.Feedback.showError('Ocorreu um erro no nosso servidor..');
                                                    }
                                                }
                                            });

                                            var paramsStr = 'long_url='+ encodeURIComponent(longUrlInput.value);
                                            if (destinationEmailInput.value.length > 0) {
                                                paramsStr += '&destination_email=' + encodeURIComponent(destinationEmailInput.value);
                                            }

                                            if (
                                                typeof token !== 'undefined'
                                                &&
                                                token != null
                                                &&
                                                token.length > 0
                                            ) {
                                                paramsStr += '&g-recaptcha-response=' + token;
                                            }

                                            if (useCustomSize) {
                                                paramsStr += '&custom-size=' + customSize;
                                            }

                                            xhr.open(
                                                "POST",
                                                '{{ url("/api/shorten") }}?' + paramsStr
                                            );
                                            xhr.setRequestHeader("Authorization", "Bearer " + window._authManager.at);

                                            // disable generate button to prevent double requests
                                            e.target.classList.add('disabled');

                                            window.App.Components.ShortenUrl.Components.Feedback.showInfo('por favor espere..');
                                            xhr.send();
                                        };

                                        if (
                                            typeof window._enableCaptcha !== 'undefined'
                                            &&
                                            window._enableCaptcha === true
                                        ) {
                                            grecaptcha.ready(function() {
                                            grecaptcha.execute('{{ $captchaSitekey }}', {action: 'submit'}).then(function(token) {
                                                func(token);
                                                });
                                            });
                                        } else {
                                            func(null);
                                        }


                                    };

                                    this.hasInitialized = true;
                                }
                            },
                            CreateCustomLink: {
                                hasInitialized: false,
                                el: function () {
                                    return document.getElementById("create-personalized-link");
                                },
                                show: function () {
                                    if (
                                        !window._authManager.userHasPermission('create_custom_shortlinks')
                                    ) {
                                        return false;
                                    }

                                    this.el().style.display = 'block';
                                },
                                hide: function () {
                                    this.el().style.display = 'none';
                                },
                                initialize: function () {
                                    if (this.hasInitialized == false) {

                                        this.el().onclick = function (e) {
                                            window.App.Views.RegisterCustomShortlink.show();
                                        };

                                        const $this = this;
                                        if (
                                            window._authManager.isAuthenticated
                                        ) {
                                            $this.show();
                                        } else {
                                            $this.hide();
                                            document.addEventListener('userAuthenticated', (e) => {
                                                $this.show();
                                            }, false);
                                        }

                                        document.addEventListener('userLoggedIn', (e) => {
                                            $this.show();
                                        }, false);

                                        this.hasInitialized = true;
                                    }
                                }
                            },
                            ChooseCustomSize: {
                                hasInitialized: false,
                                optionsVisible: false,
                                el: function () {
                                    return document.getElementById('generate-shorter-shortlinks');
                                },
                                optionsEl: function () {
                                    return document.getElementById('choose-shortstring-length');
                                },
                                cancelChoiceEl: function () {
                                    return document.getElementById('cancel-and-generate-normally');
                                },
                                show: function () {
                                    if (
                                        !window._authManager.userHasPermission('create_shortlinks_with_length_1')
                                        &&
                                        !window._authManager.userHasPermission('create_shortlinks_with_length_2')
                                        &&
                                        !window._authManager.userHasPermission('create_shortlinks_with_length_3')
                                        &&
                                        !window._authManager.userHasPermission('create_shortlinks_with_length_4')
                                    ) {
                                        return false;
                                    }

                                    this.el().style.display = 'block';
                                    this.hideOptions();
                                    this.unselectAllOptions();
                                    this.unflagHasError();
                                },
                                hide: function () {
                                    this.el().style.display = 'none';
                                },
                                getSelectOptionValue: function () {
                                    for(var i = 1; i <= 4; i++) {
                                        const optionEl = document.getElementById('generate_with_length_' + i);
                                        if (optionEl.checked) {
                                            return optionEl.value;
                                        }
                                    }
                                    return null;
                                },
                                unselectAllOptions: function () {
                                    for(var i = 1; i <= 4; i++) {
                                        const optionEl = document.getElementById('generate_with_length_' + i);
                                        optionEl.checked = false;
                                    }
                                    return null;
                                },
                                flagHasError: function () {
                                    this.optionsEl().classList.add('has-error');
                                },
                                unflagHasError: function () {
                                    this.optionsEl().classList.remove('has-error');
                                },
                                showOptions: function () {
                                    this.optionsEl().style.display = 'block';
                                    this.optionsVisible = true;

                                    for(var i = 1; i <= 4; i++) {
                                        const optionEl = document.getElementById('generate_with_length_' + i + '_container');
                                        const permissionNeeded = 'create_shortlinks_with_length_' + i;

                                        if (
                                            window._authManager.userHasPermission(permissionNeeded)
                                        ) {
                                            optionEl.style.display = 'block';
                                        } else {
                                            optionEl.style.display = 'none';
                                        }

                                    }
                                },
                                hideOptions: function () {
                                    this.optionsEl().style.display = 'none';
                                    this.optionsVisible = false;
                                },
                                reset: function () {
                                    this.el().style.display = 'block';
                                    this.hideOptions();
                                    this.unselectAllOptions();
                                },
                                initialize: function () {
                                    if ( this.hasInitialized === false ) {

                                        const $this = this;
                                        this.el().onclick = function (e) {
                                            $this.el().style.display = 'none';
                                            $this.showOptions();
                                        };

                                        this.cancelChoiceEl().onclick = function (e) {
                                            $this.reset();
                                        };

                                        if (
                                            window._authManager.isAuthenticated
                                        ) {
                                            $this.show();
                                        } else {
                                            $this.hide();
                                            document.addEventListener('userAuthenticated', (e) => {
                                                $this.show();
                                            }, false);
                                        }

                                        document.addEventListener('userLoggedIn', (e) => {
                                            $this.show();
                                        }, false);

                                        this.hasInitialized = true;
                                    }
                                }
                            }
                        },
                        initialize: function () {
                            this.Components.LongUrl.initialize();
                            this.Components.DestinationEmail.initialize();
                            this.Components.GenerateBtn.initialize();
                            this.Components.CreateCustomLink.initialize();
                            this.Components.ChooseCustomSize.initialize();
                        }
                    },
                    ShortlinkResult: {
                        el: function () {
                            return document.getElementById("form-box-with-shortlink");
                        },
                        show: function () {
                            this.initialize();
                            window.App.Components.MyLinks.Components.Links.Components.List.Components.ListOptions.Components.OrderBy.currentOrderBy = null;
                            window.App.Components.MyLinks.Components.Links.Components.List.fetch();
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
                                            window.App.Views.Login.show();
                                            window.App.previousView = window.App.Components.ShortlinkResult.el().id;
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
                                            window.App.Views.MyLinks.show();
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
                                            window.App.Components.ShortenUrl.show();
                                            window.App.Components.ShortlinkResult.hide();
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
                    RegisterCustomShortlink: {
                        el: function () {
                            return document.getElementById("form-box-shortlink-requested");
                        },
                        show: function () {
                            this.initialize();
                            this.Components.Feedback.hide();
                            this.Components.RequestedShortlink.reset();
                            this.Components.RequestedShortlinkLongUrl.reset();
                            this.el().style.display = "block";

                            if (this.Components.RequestedShortlink.el().value.length == 0) {
                                const $this = this;

                                var letters = 'personalizar';
                                letters = letters.split('');

                                for(var i = 0; i < letters.length; i++) {
                                    setTimeout(
                                        function(){
                                            $this.Components.RequestedShortlink.el().value += letters.shift();
                                            $this.Components.RequestedShortlink.mirror();
                                        },
                                        i*100
                                    );
                                }

                                setTimeout(
                                    function() {
                                        $this.Components.RequestedShortlink.el().focus();
                                    },
                                    letters.length*100
                                );
                            } else {
                                this.Components.RequestedShortlinkLongUrl.el().focus();
                            }
                        },
                        hide: function () {
                            this.el().style.display = "none";
                        },
                        Components: {
                            RequestedShortlink: {
                                hasInitialized: false,
                                el: function () {
                                    return document.getElementById('requested-shortstring');
                                },
                                mirrorEl: function () {
                                    return document.getElementById('custom-url-shortstring');
                                },
                                reset: function () {
                                    this.el().value = '';
                                },
                                mirror: function () {
                                    this.mirrorEl().innerText = this.el().value;
                                },
                                feedbackEl: function () {
                                    return document.getElementById('custom-url-shortstring-feedback');
                                },
                                hideFeedbackEl: function () {
                                    if(this.feedbackEl() != null) {
                                        this.feedbackEl().style.display = 'none';
                                    }
                                },
                                getShortstring: function () {
                                    return this.el().value;
                                },
                                initialize: function () {
                                    if (this.hasInitialized === false) {

                                        const $this = this;
                                        this.el().onkeyup = function(e) {
                                            $this.mirror();
                                            $this.hideFeedbackEl();
                                            window.App.Components.RegisterCustomShortlink.Components.Feedback.hide();
                                        };

                                        this.hasInitialized = true;
                                    }
                                }
                            },
                            RequestedShortlinkLongUrl: {
                                el: function () {
                                    return document.getElementById('requested-shortlink-long-url');
                                },
                                reset: function () {
                                    this.el().value = '';
                                }
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
                                disable: function () {
                                    this.el().classList.add('disabled');
                                },
                                initialize: function () {
                                    if (this.hasInitialized === false) {

                                        const $this = this;
                                        if (
                                            window._authManager.isAuthenticated
                                        ) {
                                            $this.enable();
                                        } else {
                                            $this.disable();
                                            document.addEventListener('userAuthenticated', (e) => {
                                                $this.enable();
                                            }, false);
                                        }

                                        this.el().onclick = function (e) {
                                            if (
                                                !window._authManager.isAuthenticated
                                                ||
                                                e.target.classList.contains('disabled')
                                            ) {
                                                return false;
                                            }

                                            const shortStr = window.App.Components.RegisterCustomShortlink.Components.RequestedShortlink.getShortstring();
                                            const longUrlField = window.App.Components.RegisterCustomShortlink.Components.RequestedShortlinkLongUrl.el();

                                            if (longUrlField.value.length == 0) {
                                                longUrlField.classList.add('has-error');
                                                return;
                                            }

                                            longUrlField.classList.remove('has-error');

                                            // disable generate button to prevent double requests
                                            e.target.classList.add('disabled');

                                            window.App.Components.RegisterCustomShortlink.Components.Feedback.showInfo('por favor espere..');

                                            var func = function (token) {
                                                var xhr = new XMLHttpRequest();
                                                xhr.withCredentials = true;

                                                xhr.addEventListener("readystatechange", function () {
                                                    if (this.readyState === 4) {
                                                        if (this.status === 401) {
                                                            window.location.reload();
                                                        }

                                                        e.target.classList.remove('disabled');
                                                        try {
                                                            const jsonResObj = JSON.parse(this.responseText);

                                                            if (this.status === 201) {
                                                                window.App.Components.RegisterCustomShortlink.hide();
                                                                window.App.Components.ShortlinkResult.Components.Shortlink.set(
                                                                    jsonResObj.shortlink
                                                                );
                                                                window.App.Components.ShortlinkResult.show();
                                                                return;
                                                            }

                                                            if(typeof jsonResObj.message !== 'undefined') {
                                                                window.App.Components.RegisterCustomShortlink.Components.Feedback.showError(jsonResObj.message);
                                                            } else {
                                                                window.App.Components.RegisterCustomShortlink.Components.Feedback.showError('Ocorreu um erro no nosso servidor..');
                                                            }


                                                        } catch (e) {
                                                            // invalid json something went wrong
                                                            window.App.Components.RegisterCustomShortlink.Components.Feedback.showError('Ocorreu um erro no nosso servidor..');
                                                        }
                                                    }
                                                });

                                                var queryStr = '?long_url='+ encodeURIComponent(longUrlField.value) +'&shortstring=' + shortStr;

                                                if (
                                                    typeof token !== 'undefined'
                                                    &&
                                                    token != null
                                                    &&
                                                    token.length > 0
                                                ) {
                                                    queryStr += '&g-recaptcha-response=' + token;
                                                }

                                                xhr.open(
                                                    "POST",
                                                    '{{ url("/api/register-custom-shortlink") }}' + queryStr
                                                );
                                                xhr.setRequestHeader("Authorization", "Bearer " + window._authManager.at);
                                                xhr.send();
                                            };

                                            if (
                                                typeof window._enableCaptcha !== 'undefined'
                                                &&
                                                window._enableCaptcha === true
                                            ) {
                                                grecaptcha.ready(function() {
                                                    grecaptcha.execute('{{ $captchaSitekey }}', {action: 'submit'}).then(
                                                        function(token) {
                                                            func(token);
                                                        }
                                                    );
                                                });
                                            } else {
                                                func(null);
                                            }



                                        };
                                        this.hasInitialized = true;
                                    }
                                }
                            },
                            CloseBtn: {
                                hasInitialized: false,
                                el: function () {
                                    return document.getElementById('custom-shortlink-close');
                                },
                                initialize: function () {
                                    if ( this.hasInitialized === false ) {

                                        this.el().onclick = function (e) {
                                            window.App.Components.RegisterCustomShortlink.Components.RequestedShortlink.el().value = '';
                                            window.App.Views.HomePage.show();
                                        };

                                        this.hasInitialized = true;
                                    }
                                }
                            }
                        },
                        initialize: function () {
                            this.Components.RequestedShortlink.initialize();
                            this.Components.ContinueBtn.initialize();
                            this.Components.CloseBtn.initialize();
                        }

                    },
                    Login: {
                        hasInitialized: false,
                        el: function () {
                            return document.getElementById("form-box-login");
                        },
                        show: function () {
                            if (window._authManager.isLoggedIn) {
                                window.App.Views.HomePage.show();
                                return;
                            }

                            this.initialize();
                            this.el().style.display = 'block';
                            if (!window.App.isMobileSize()) {
                                this.Components.Email.el().focus();
                            }
                            this.Components.Feedback.hide();
                            this.Components.ResendVerificationEmail.hide();
                            window.history.pushState(null, 'Entrar na minha conta', '/entrar');
                        },
                        hide: function () {
                            this.el().style.display = 'none';
                            this.Components.Password.el().value = '';
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
                                        if (
                                            window._authManager.isAuthenticated
                                        ) {
                                            $this.enable();
                                        } else {
                                            $this.disable();
                                            document.addEventListener('userAuthenticated', (e) => {
                                                $this.enable();
                                            }, false);
                                        }



                                        this.el().onclick = function (e) {
                                            if (
                                                !window._authManager.isAuthenticated
                                                ||
                                                e.target.classList.contains('disabled')
                                            ) {
                                                return false;
                                            }

                                            const loginEmailInput = window.App.Components.Login.Components.Email.el();
                                            const loginPasswordInput = window.App.Components.Login.Components.Password.el();

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
                                            window.App.Components.Login.Components.Feedback.showInfo('por favor espere..');

                                            if (
                                                typeof window._enableCaptcha !== 'undefined'
                                                &&
                                                window._enableCaptcha === true
                                            ) {
                                                grecaptcha.ready(function() {
                                                    grecaptcha.execute('{{ $captchaSitekey }}', {action: 'submit'}).then(
                                                        function(token) {
                                                            window._authManager.login(loginEmailInput.value, loginPasswordInput.value, token);
                                                        }
                                                    );
                                                });
                                            } else {
                                                window._authManager.login(loginEmailInput.value, loginPasswordInput.value, null);
                                            }

                                        };

                                        this.hasInitialized = true;
                                    }
                                }
                            },
                            LoginWithGithubBtn: {
                                hasInitialized: false,
                                el: function () {
                                    return document.getElementById("login-with-github-button");
                                },
                                enable: function () {
                                    if (!this.el() || this.el().length == 0)return;
                                    this.el().classList.remove('disabled');
                                },
                                disable: function () {
                                    if (!this.el() || this.el().length == 0)return;
                                    this.el().classList.add('disabled');
                                },
                                initialize: function () {
                                    if (!this.el() || this.el().length == 0)return;
                                    if (this.hasInitialized === false) {
                                        const $this = this;

                                        if (
                                            window._authManager.isAuthenticated
                                        ) {
                                            $this.enable();
                                        } else {
                                            $this.disable();
                                            document.addEventListener('userAuthenticated', (e) => {
                                                $this.enable();
                                            }, false);
                                        }

                                        this.el().onclick = function (e) {
                                            if (
                                                !window._authManager.isAuthenticated
                                                ||
                                                e.target.classList.contains('disabled')
                                            ) {
                                                return false;
                                            }

                                            window.location.replace('/auth/github/redirect');
                                        };

                                        this.hasInitialized = true;
                                    }
                                }
                            },
                            LoginWithFacebookBtn: {
                                hasInitialized: false,
                                el: function () {
                                    return document.getElementById("login-with-facebook-button");
                                },
                                enable: function () {
                                    if (!this.el() || this.el().length == 0)return;
                                    this.el().classList.remove('disabled');
                                },
                                disable: function () {
                                    if (!this.el() || this.el().length == 0)return;
                                    this.el().classList.add('disabled');
                                },
                                initialize: function () {
                                    if (!this.el() || this.el().length == 0)return;
                                    if (this.hasInitialized === false) {
                                        const $this = this;

                                        if (
                                            window._authManager.isAuthenticated
                                        ) {
                                            $this.enable();
                                        } else {
                                            $this.disable();
                                            document.addEventListener('userAuthenticated', (e) => {
                                                $this.enable();
                                            }, false);
                                        }

                                        this.el().onclick = function (e) {
                                            if (
                                                !window._authManager.isAuthenticated
                                                ||
                                                e.target.classList.contains('disabled')
                                            ) {
                                                return false;
                                            }

                                            window.location.replace('/auth/facebook/redirect');
                                        };

                                        this.hasInitialized = true;
                                    }
                                }
                            },
                            LoginWithGoogleBtn: {
                                hasInitialized: false,
                                el: function () {
                                    return document.getElementById("login-with-google-button");
                                },
                                enable: function () {
                                    if (!this.el() || this.el().length == 0)return;
                                    this.el().classList.remove('disabled');
                                },
                                disable: function () {
                                    if (!this.el() || this.el().length == 0)return;
                                    this.el().classList.add('disabled');
                                },
                                initialize: function () {
                                    if (!this.el() || this.el().length == 0)return;
                                    if (this.hasInitialized === false) {
                                        const $this = this;

                                        if (
                                            window._authManager.isAuthenticated
                                        ) {
                                            $this.enable();
                                        } else {
                                            $this.disable();
                                            document.addEventListener('userAuthenticated', (e) => {
                                                $this.enable();
                                            }, false);
                                        }


                                        this.el().onclick = function (e) {
                                            if (
                                                !window._authManager.isAuthenticated
                                                ||
                                                e.target.classList.contains('disabled')
                                            ) {
                                                return false;
                                            }

                                            window.location.replace('/auth/google/redirect');
                                        };

                                        this.hasInitialized = true;
                                    }
                                }
                            },
                            LoginWithLinkedinBtn: {
                                hasInitialized: false,
                                el: function () {
                                    return document.getElementById("login-with-linkedin-button");
                                },
                                enable: function () {
                                    if (!this.el() || this.el().length == 0)return;
                                    this.el().classList.remove('disabled');
                                },
                                disable: function () {
                                    if (!this.el() || this.el().length == 0)return;
                                    this.el().classList.add('disabled');
                                },
                                initialize: function () {
                                    if (!this.el() || this.el().length == 0)return;
                                    if (this.hasInitialized === false) {
                                        const $this = this;

                                        if (
                                            window._authManager.isAuthenticated
                                        ) {
                                            $this.enable();
                                        } else {
                                            $this.disable();
                                            document.addEventListener('userAuthenticated', (e) => {
                                                $this.enable();
                                            }, false);
                                        }

                                        this.el().onclick = function (e) {
                                            if (
                                                !window._authManager.isAuthenticated
                                                ||
                                                e.target.classList.contains('disabled')
                                            ) {
                                                return false;
                                            }

                                            window.location.replace('/auth/linkedin/redirect');
                                        };

                                        this.hasInitialized = true;
                                    }
                                }
                            },
                            LoginWithTwitterBtn: {
                                hasInitialized: false,
                                el: function () {
                                    return document.getElementById("login-with-twitter-button");
                                },
                                enable: function () {
                                    if (!this.el() || this.el().length == 0)return;
                                    this.el().classList.remove('disabled');
                                },
                                disable: function () {
                                    if (!this.el() || this.el().length == 0)return;
                                    this.el().classList.add('disabled');
                                },
                                initialize: function () {
                                    if (!this.el() || this.el().length == 0)return;
                                    if (this.hasInitialized === false) {
                                        const $this = this;

                                        if (
                                            window._authManager.isAuthenticated
                                        ) {
                                            $this.enable();
                                        } else {
                                            $this.disable();
                                            document.addEventListener('userAuthenticated', (e) => {
                                                $this.enable();
                                            }, false);
                                        }

                                        this.el().onclick = function (e) {
                                            if (
                                                !window._authManager.isAuthenticated
                                                ||
                                                e.target.classList.contains('disabled')
                                            ) {
                                                return false;
                                            }

                                            window.location.replace('/auth/twitter/redirect');
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
                                            window.App.Components.Login.hide();

                                            if (window.App.previousView == window.App.Components.ShortlinkResult.el().id) {
                                                window.App.Components.ShortenUrl.hide();
                                                window.App.Components.ShortlinkResult.show();
                                                return;
                                            }

                                            window.App.Components.ShortenUrl.show();
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
                                            window.App.Components.Login.hide();
                                            window.App.Components.Register.show();
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
                                            const loginEmailInput = window.App.Components.Login.Components.Email.el();

                                            if (loginEmailInput.value.length == 0) {
                                                loginEmailInput.classList.add('has-error');
                                                return false;
                                            } else {
                                                loginEmailInput.classList.remove('has-error');
                                            }

                                            if (
                                                typeof window._enableCaptcha !== 'undefined'
                                                &&
                                                window._enableCaptcha === true
                                            ) {
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
                                            } else {
                                                window._authManager.resendVerificationEmail(
                                                    loginEmailInput.value,
                                                    null
                                                );
                                            }


                                            window.App.Components.Login.Components.ResendVerificationEmail.hide();

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
                                            window.App.Components.Login.hide();
                                            window.App.Components.PasswordRecovery.show();
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
                            this.Components.LoginWithGithubBtn.initialize();
                            this.Components.LoginWithFacebookBtn.initialize();
                            this.Components.LoginWithGoogleBtn.initialize();
                            this.Components.LoginWithLinkedinBtn.initialize();
                            this.Components.LoginWithTwitterBtn.initialize();
                            this.Components.CloseBtn.initialize();
                            this.Components.CreateAccLink.initialize();
                            this.Components.ForgotPasswordLink.initialize();

                            if (this.hasInitialized === false) {

                                const $this = this;
                                document.addEventListener('userLoggedIn', (e) => {
                                    $this.hide();
                                }, false);

                                document.addEventListener('userLoginFailed', (e) => {
                                    if (e.isError) {
                                        $this.Components.Feedback.showError(e.reason);

                                        if(e.error_id === 'unverified_account') {
                                            $this.Components.ResendVerificationEmail.show();
                                        }
                                    } else {
                                        $this.Components.Feedback.showInfo(e.reason);
                                    }

                                    $this.Components.LoginBtn.enable();
                                }, false);

                                this.hasInitialized = true;
                            }
                        }

                    },
                    Register: {
                        hasInitialized: false,
                        el: function () {
                            return document.getElementById("form-box-register");
                        },
                        show: function () {
                            if (window._authManager.isLoggedIn) {
                                window.App.Views.HomePage.show();
                                return;
                            }

                            this.initialize();
                            this.el().style.display = 'block';
                            this.Components.Name.el().focus();
                            this.Components.Feedback.hide();
                            window.history.pushState(null, 'Criar conta', '/criar-conta');
                        },
                        hide: function () {
                            this.el().style.display = 'none';
                            this.Components.Password.el().value = '';
                            this.Components.PasswordConfirmation.el().value = '';
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

                                        if (
                                            window._authManager.isAuthenticated
                                        ) {
                                            $this.enable();
                                        } else {
                                            $this.disable();
                                            document.addEventListener('userAuthenticated', (e) => {
                                                $this.enable();
                                            }, false);
                                        }

                                        this.el().onclick = function (e) {
                                            if (
                                                !window._authManager.isAuthenticated
                                                ||
                                                e.target.classList.contains('disabled')
                                            ) {
                                                return false;
                                            }
                                            const registerNameInput = window.App.Components.Register.Components.Name.el();
                                            const registerEmailInput = window.App.Components.Register.Components.Email.el();
                                            const registerEmailConfInput = window.App.Components.Register.Components.EmailConfirmation.el();
                                            const registerPasswordInput = window.App.Components.Register.Components.Password.el();
                                            const registerPasswordConfInput = window.App.Components.Register.Components.PasswordConfirmation.el();

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
                                            window.App.Components.Register.Components.Feedback.showInfo('por favor espere..');

                                            if (
                                                typeof window._enableCaptcha !== 'undefined'
                                                &&
                                                window._enableCaptcha === true
                                            ) {
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
                                            } else {
                                                window._authManager.register(
                                                    registerNameInput.value,
                                                    registerEmailInput.value,
                                                    registerEmailConfInput.value,
                                                    registerPasswordInput.value,
                                                    registerPasswordConfInput.value,
                                                    null
                                                );
                                            }

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
                                            window.App.Components.Register.hide();

                                            if (window.App.previousView == window.App.Components.ShortlinkResult.el().id) {
                                                window.App.Components.ShortlinkResult.show();
                                                return;
                                            }

                                            window.App.Components.ShortenUrl.show();
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

                            if (this.hasInitialized === false) {

                                const $this = this;
                                document.addEventListener('userRegisterFailed', (e) => {

                                    if (e.isError) {
                                        $this.Components.Feedback.showError(e.reason);
                                    } else {
                                        $this.Components.Feedback.showInfo(e.reason);
                                    }

                                    $this.Components.RegisterBtn.enable();
                                }, false);

                                document.addEventListener('userRegisterSuccess', (e) => {
                                    $this.hide();
                                    window.App.Components.RegisterSuccess.show();
                                }, false);

                                this.hasInitialized = true;

                            }

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

                                        const $this = this;
                                        if (
                                            window._authManager.isAuthenticated
                                        ) {
                                            $this.enable();
                                        } else {
                                            $this.disable();
                                            document.addEventListener('userAuthenticated', (e) => {
                                                $this.enable();
                                            }, false);
                                        }

                                        this.el().onclick = function (e) {

                                            if (
                                                !window._authManager.isAuthenticated
                                                ||
                                                e.target.classList.contains('disabled')
                                            ) {
                                                return false;
                                            }

                                            const emailInput = window.App.Components.PasswordRecovery.Components.Email.el();

                                            if (emailInput.value.length == 0) {
                                                emailInput.classList.add('has-error');
                                                return false;
                                            } else {
                                                emailInput.classList.remove('has-error');
                                            }

                                            if (
                                                typeof window._enableCaptcha !== 'undefined'
                                                &&
                                                window._enableCaptcha === true
                                            ) {
                                                grecaptcha.ready(function() {
                                                    grecaptcha.execute('{{ $captchaSitekey }}', {action: 'submit'}).then(
                                                        function(token) {
                                                            window._authManager.sendPasswordRecoveryEmail(
                                                                emailInput.value,
                                                                token
                                                            );
                                                        }
                                                    );
                                                });
                                            } else {
                                                window._authManager.sendPasswordRecoveryEmail(
                                                    emailInput.value,
                                                    null
                                                );
                                            }

                                            window.App.Components.PasswordRecovery.Components.Feedback.showInfo(
                                                                'Acabamos de lhe enviar um email para que possa criar uma nova palavra-passe.'
                                                            );
                                            window.App.Components.PasswordRecovery.Components.SendPwdRecoveryBtn.disable();

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
                        hasInitialized: false,
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
                                        if (
                                            window._authManager.isAuthenticated
                                        ) {
                                            $this.enable();
                                        } else {
                                            $this.disable();
                                            document.addEventListener('userAuthenticated', (e) => {
                                                $this.enable();
                                            }, false);
                                        }

                                        this.el().onclick = function (e) {
                                            if (
                                                !window._authManager.isAuthenticated
                                                ||
                                                e.target.classList.contains('disabled')
                                            ) {
                                                return false;
                                            }

                                            const newPassword = window.App.Components.ChangePassword.Components.NewPassword.el();
                                            const newPasswordConfirmation = window.App.Components.ChangePassword.Components.NewPasswordConfirmation.el();

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
                                            window.App.Components.ChangePassword.Components.Feedback.showInfo('por favor espere..');

                                            if (
                                                typeof window._enableCaptcha !== 'undefined'
                                                &&
                                                window._enableCaptcha === true
                                            ) {
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
                                            } else {
                                                window._authManager.changePassword(
                                                    newPassword.value,
                                                    newPasswordConfirmation.value,
                                                    null
                                                );
                                            }

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

                            if (this.hasInitialized === false) {

                                const $this = this;
                                document.addEventListener('userPasswordChanged', (e) => {
                                    $this.hide();
                                    window.App.Components.PasswordHasChanged.show();
                                }, false);

                                document.addEventListener('userPasswordChangeFailed', (e) => {
                                    if (e.isError) {
                                        $this.Components.Feedback.showError(e.reason);
                                    } else {
                                        $this.Components.Feedback.showInfo(e.reason);
                                    }

                                    $this.Components.ChangePasswordBtn.enable();
                                }, false);

                                this.hasInitialized = true;
                            }

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
                    MyLinks: {
                        hasInitialized: false,
                        el: function () {
                            return document.getElementById('my-links-view');
                        },
                        hide: function () {
                            this.el().style.display = 'none';
                        },
                        show: function () {
                            this.initialize();
                            document.getElementById('my-links-view-title').classList.remove('has-visible-list-options');
                            this.el().style.display = 'block';
                        },
                        Components: {
                            CloseBtn: {
                                hasInitialized: false,
                                el: function () {
                                    return document.getElementById('my-links-view-close-btn');
                                },
                                initialize: function () {
                                    if ( this.hasInitialized == false ) {

                                        this.el().onclick = function (e) {
                                            window.App.Components.MyLinks.hide();
                                        };

                                        this.hasInitialized = true;
                                    }
                                }
                            },
                            Links: {
                                initialize: function () {
                                    this.Components.Loading.show();
                                    this.Components.List.show();
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
                                    NotFound: {
                                        el: function () {
                                            return document.getElementById('form-box-acc-links-not-found');
                                        },
                                        show: function () {
                                            this.el().style.display = 'block';
                                        },
                                        hide: function () {
                                            this.el().style.display = 'none';
                                        },
                                    },
                                    GuestMsg: {
                                        el: function () {
                                            return document.getElementById('my-links-guest-msg');
                                        },
                                        show: function () {
                                            this.el().style.display = 'block';
                                        },
                                        hide: function () {
                                            this.el().style.display = 'none';
                                        }
                                    },
                                    List: {
                                        api: "{{ url('/api/my-links') }}",
                                        el: function () {
                                            return document.getElementById('form-box-acc-links');
                                        },
                                        show: function () {
                                            this.Components.ListOptions.hide();
                                            this.el().style.display = 'block';

                                            if (window._authManager.isAuthenticated) {
                                                this.fetch();
                                            } else {
                                                const $this = this;
                                                document.addEventListener('userAuthenticated', (e) => {
                                                    if (window.App.currentView == 'MyLinks') {
                                                        $this.fetch();
                                                    }
                                                }, false);
                                            }

                                        },
                                        hide: function () {
                                            this.el().style.display = 'none';
                                        },
                                        clear: function () {
                                            this.el().innerHTML = '';
                                        },
                                        Components: {
                                            ListOptions: {
                                                el: function () {
                                                    return document.getElementById('my-links-list-options');
                                                },
                                                show: function () {
                                                    this.el().style.display = 'block';
                                                },
                                                hide: function () {
                                                    this.el().style.display = 'none';
                                                },
                                                hideIfNoOptionsVisible: function () {
                                                    var hasVisibleOption = false;
                                                    const options = Object.keys(this.Components);
                                                    for(var i = 0; i < options.length; i++) {
                                                        if (
                                                            this.Components[options[i]].isVisible === true
                                                        ) {
                                                            hasVisibleOption = true;
                                                            break;
                                                        }
                                                    }

                                                    if (hasVisibleOption === false) {
                                                        this.hide();
                                                        document.getElementById('my-links-view-title').classList.remove('has-visible-list-options');
                                                    } else {
                                                        document.getElementById('my-links-view-title').classList.add('has-visible-list-options');
                                                    }
                                                },
                                                fixViewTitleMargin: function () {
                                                    var hasVisibleOption = false;
                                                    const options = Object.keys(this.Components);
                                                    for(var i = 0; i < options.length; i++) {
                                                        if (
                                                            this.Components[options[i]].isVisible === true
                                                        ) {
                                                            hasVisibleOption = true;
                                                            break;
                                                        }
                                                    }

                                                    if (hasVisibleOption === false) {
                                                        document.getElementById('my-links-view-title').classList.remove('has-visible-list-options');
                                                    } else {
                                                        document.getElementById('my-links-view-title').classList.add('has-visible-list-options');
                                                    }
                                                },
                                                Components: {
                                                    OrderBy: {
                                                        hasInitialized: false,
                                                        currentOrderBy: null,
                                                        isVisible: false,
                                                        el: function () {
                                                            return document.getElementById('my-links-order');
                                                        },
                                                        selectedOrderByEl: function () {
                                                            return document.getElementById('my-links-selected-order');
                                                        },
                                                        availableOrderBysEl: function () {
                                                            return document.getElementById('my-links-order-list');
                                                        },
                                                        arrowEl: function () {
                                                            return document.getElementById('my-links-order-arrow');
                                                        },
                                                        show: function (selectedOrderBy, availableOrderBys) {
                                                            this.initialize();
                                                            const $this = this;
                                                            this.availableOrderBysEl().innerHTML = '';
                                                            for(var i = 0; i < availableOrderBys.length; i++) {
                                                                const availableOrderBy = availableOrderBys[i];

                                                                if (availableOrderBy.name === selectedOrderBy) {
                                                                    this.selectedOrderByEl().innerText = availableOrderBy.label;
                                                                }

                                                                const availableOrderByEl = document.createElement('div');
                                                                availableOrderByEl.classList.add('order-item');
                                                                availableOrderByEl.innerText = availableOrderBy.label;
                                                                availableOrderByEl.setAttribute('data-name', availableOrderBy.name);
                                                                availableOrderByEl.onclick = function (e) {
                                                                    $this.availableOrderBysEl().style.display = 'none';
                                                                    const orderBy = e.target.getAttribute('data-name');
                                                                    $this.currentOrderBy = orderBy;
                                                                    $this.selectedOrderByEl().innerText = e.target.innerText;
                                                                    window.App.Components.MyLinks.Components.Links.Components.List.fetch(1);
                                                                };

                                                                this.availableOrderBysEl().appendChild(availableOrderByEl);

                                                            }
                                                            this.el().style.display = 'inline-block';
                                                            this.isVisible = true;
                                                            window.App.Components.MyLinks.Components.Links.Components.List.Components.ListOptions.fixViewTitleMargin();
                                                        },
                                                        hide: function () {
                                                            this.el().style.display = 'none';
                                                            this.isVisible = false;
                                                            window.App.Components.MyLinks.Components.Links.Components.List.Components.ListOptions.fixViewTitleMargin();
                                                        },
                                                        initialize: function () {
                                                            if (this.hasInitialized === false) {
                                                                const $this = this;

                                                                const toggleListVisibility = function () {
                                                                    if ($this.availableOrderBysEl().style.display === 'none') {
                                                                        $this.availableOrderBysEl().style.display = 'block';
                                                                        hideOnClickOutside(
                                                                            $this.availableOrderBysEl(),
                                                                            [
                                                                                $this.selectedOrderByEl(),
                                                                                $this.arrowEl()
                                                                            ]
                                                                        );
                                                                    } else {
                                                                        $this.availableOrderBysEl().style.display = 'none';
                                                                    }
                                                                };

                                                                this.selectedOrderByEl().onclick = function (e) {
                                                                    toggleListVisibility();
                                                                };

                                                                this.arrowEl().onclick = function (e) {
                                                                    toggleListVisibility();
                                                                };

                                                                this.hasInitialized = true;
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        },
                                        addLink: function (
                                            id,
                                            long_url,
                                            shortlink,
                                            destinationEmail,
                                            createdAt,
                                            total_views,
                                            total_unique_views,
                                            previous_redirect_urls
                                        ) {
                                            //TODO: reduce addLink() function size / organize.

                                            const listItem = document.createElement("div");
                                            listItem.classList.add('list-item');
                                            listItem.setAttribute('id', 'link-' + id);

                                            const listItemOptions = document.createElement('div');
                                            listItemOptions.classList.add('list-item-options');

                                            const listItemOptionsContainer = document.createElement('div');
                                            listItemOptionsContainer.classList.add('list-item-options-container');
                                            listItemOptionsContainer.style.display = 'none';

                                            const listItemOptionDelete = document.createElement('div');
                                            listItemOptionDelete.classList.add('list-item-options-option');
                                            listItemOptionDelete.innerText = 'apagar';
                                            listItemOptionDelete.style.color = '#d81a1a';

                                            const listItemOptionDeleteConfirm = document.createElement('div');
                                            listItemOptionDeleteConfirm.classList.add('list-item-options-option');
                                            listItemOptionDeleteConfirm.innerText = 'tem a certeza?';
                                            listItemOptionDeleteConfirm.style.color = '#d81a1a';
                                            listItemOptionDeleteConfirm.style.display = 'none';

                                            const listItemOptionDeleteConfirmYes = document.createElement('a');
                                            listItemOptionDeleteConfirmYes.href = 'javascript:void(0);';
                                            listItemOptionDeleteConfirmYes.innerText = 'sim';
                                            listItemOptionDeleteConfirmYes.style.marginLeft = '20px';
                                            listItemOptionDeleteConfirmYes.style.marginRight = '20px';
                                            listItemOptionDeleteConfirmYes.setAttribute('data-shortlink-id', id);

                                            const $this = this;

                                            listItemOptionDeleteConfirmYes.onclick = function (e) {
                                                listItemOptionDeleteConfirmYes.style.cursor = 'wait';
                                                // the next line will invalidate the previous line
                                                // but i think it is better.
                                                listItemOptionDeleteConfirm.innerText = 'apagando..';

                                                var deleteShortlinkFunc = function (token) {
                                                    var xhr = new XMLHttpRequest();
                                                    xhr.withCredentials = true;

                                                    xhr.addEventListener("readystatechange", function () {
                                                        if (this.readyState === 4) {

                                                            if (this.status === 401) {
                                                                window.location.reload();
                                                            }

                                                            if (this.status === 200) {
                                                                const totalListItems = $this.el().getElementsByClassName('list-item').length;
                                                                var currPage = window.App.Helpers.Pagination.getCurrentPage('MyLinksComponent');
                                                                if (currPage > 1 && totalListItems === 1) {
                                                                    currPage -= 1;
                                                                }
                                                                window.App.Components.MyLinks.Components.Links.Components.List.fetch(
                                                                    currPage
                                                                );
                                                                return;
                                                            }

                                                        }
                                                    });


                                                    const shortlinkId = listItemOptionDeleteConfirmYes.getAttribute('data-shortlink-id');

                                                    var credentialsQueryStr =
                                                        "?shortlink_id=" + shortlinkId;

                                                    if (
                                                        typeof token !== 'undefined'
                                                        &&
                                                        token != null
                                                        &&
                                                        token.length > 0
                                                    ) {
                                                        credentialsQueryStr += '&g-recaptcha-response=' + token;
                                                    }

                                                    xhr.open(
                                                        "POST",'/api/shortlinks/delete' + credentialsQueryStr
                                                    );
                                                    xhr.setRequestHeader("Authorization", "Bearer " + window._authManager.at);
                                                    xhr.send();
                                                };

                                                if (
                                                    typeof window._enableCaptcha !== 'undefined'
                                                    &&
                                                    window._enableCaptcha === true
                                                ) {
                                                    grecaptcha.ready(function() {
                                                        grecaptcha.execute('{{ $captchaSitekey }}', {action: 'submit'}).then(function(token) {
                                                            deleteShortlinkFunc(token);
                                                        });
                                                    });
                                                } else {
                                                    deleteShortlinkFunc(null);
                                                }
                                            };

                                            const listItemOptionDeleteConfirmNo = document.createElement('a');
                                            listItemOptionDeleteConfirmNo.href = 'javascript:void(0);';
                                            listItemOptionDeleteConfirmNo.innerText = 'não';

                                            listItemOptionDeleteConfirmNo.onclick = function (e) {
                                                listItemOptionsContainer.style.display = 'none';
                                                listItemOptionDeleteConfirm.style.display = 'none';
                                                listItemOptionDelete.style.display = 'block';
                                            };

                                            listItemOptionDeleteConfirm.appendChild(listItemOptionDeleteConfirmYes);
                                            listItemOptionDeleteConfirm.appendChild(listItemOptionDeleteConfirmNo);



                                            listItemOptionDelete.onclick = function (e) {
                                                listItemOptionDelete.style.display = 'none';
                                                listItemOptionDeleteConfirm.style.display = 'block';
                                            };

                                            listItemOptionsContainer.appendChild(listItemOptionDelete);
                                            listItemOptionsContainer.appendChild(listItemOptionDeleteConfirm);


                                            listItemOptions.onclick = function (e) {
                                                if (listItemOptionsContainer.style.display === 'block') {
                                                    listItemOptionsContainer.style.display = 'none';

                                                    listItemOptionDeleteConfirm.style.display = 'none';
                                                    listItemOptionDelete.style.display = 'block';
                                                } else {
                                                    listItemOptionsContainer.style.display = 'block';
                                                    hideOnClickOutside(listItemOptionsContainer, [
                                                        listItemOptions,
                                                        listItemOptionDeleteConfirmYes,
                                                        listItemOptionDeleteConfirmNo
                                                    ]);
                                                }
                                            };

                                            const shortlinkLabel = document.createElement("div");
                                            shortlinkLabel.classList.add('short-url-label');
                                            shortlinkLabel.innerText = 'Wil link:';



                                            const shortlinkContainer = document.createElement("div");
                                            shortlinkContainer.classList.add('short-url');

                                            const shortlinkInputContainer = document.createElement('input');
                                            shortlinkInputContainer.readOnly = true;
                                            shortlinkInputContainer.value = shortlink;

                                            shortlinkInputContainer.onclick = function (e) {
                                                e.target.focus();
                                                e.target.select();
                                            };

                                            shortlinkContainer.appendChild(shortlinkInputContainer);

                                            const longUrlLabel = document.createElement("div");
                                            longUrlLabel.classList.add('long-url-label');
                                            longUrlLabel.innerText = 'Link original:';

                                            const editLongUrlInput = document.createElement('input');
                                            editLongUrlInput.style.display = 'none';
                                            editLongUrlInput.classList.add('input-generic-1');
                                            editLongUrlInput.value = long_url; //TODO: shortstring

                                            const editLongUrlInputFeedback = document.createElement('div');
                                            editLongUrlInputFeedback.style.display = 'none';
                                            editLongUrlInputFeedback.classList.add('form-box-feedback');
                                            editLongUrlInputFeedback.classList.add('edit-long-url-feedback');



                                            const longUrlContainer = document.createElement("div");
                                            longUrlContainer.classList.add('long-url');
                                            longUrlContainer.innerText = long_url;

                                            const editLongUrlLink = document.createElement('a');
                                            const saveEditLongUrlLink = document.createElement('a');
                                            const cancelEditLongUrlLink = document.createElement('a');
                                            const viewPreviousUrlsLink = document.createElement('a');
                                            const previousUrlsContainer = document.createElement('div');

                                            previousUrlsContainer.classList.add('previous-urls-container');
                                            previousUrlsContainer.style.display = 'none';

                                            for(var i = 0; i < previous_redirect_urls.length; i++) {
                                                const prevUrl = previous_redirect_urls[i];
                                                const prevUrlEl = document.createElement('div');
                                                prevUrlEl.classList.add('previously-used-link');
                                                prevUrlEl.setAttribute('data-url', prevUrl.url);

                                                const prevUrlElUrlContainer = document.createElement('div');
                                                prevUrlElUrlContainer.classList.add('link');
                                                prevUrlElUrlContainer.innerText = prevUrl.url;

                                                prevUrlEl.appendChild(prevUrlElUrlContainer);

                                                const prevUrlUsedTimeRangeContainer = document.createElement('div');
                                                prevUrlUsedTimeRangeContainer.classList.add('time-range');
                                                prevUrlUsedTimeRangeContainer.innerText = '(Desde ' + (new Date(prevUrl.created_at)).toLocaleString('pt-PT') + ' até '+ (new Date(prevUrl.updated_at)).toLocaleString('pt-PT') +')'

                                                prevUrlElUrlContainer.onclick = function (e) {
                                                    editLongUrlInput.value = e.target.innerText;
                                                    previousUrlsContainer.style.display = 'none';
                                                    viewPreviousUrlsLink.innerText = 'ver links utilizados anteriormente';
                                                    window.location.href = '#link-' + id;
                                                };

                                                prevUrlEl.appendChild(prevUrlUsedTimeRangeContainer);
                                                prevUrlEl.appendChild(prevUrlElUrlContainer);

                                                previousUrlsContainer.appendChild(prevUrlEl);
                                            }

                                            viewPreviousUrlsLink.style.display = 'none';
                                            viewPreviousUrlsLink.classList.add('generic-edit-link-1');
                                            viewPreviousUrlsLink.innerText = 'ver links utilizados anteriormente';
                                            viewPreviousUrlsLink.onclick = function (e) {
                                                if (previousUrlsContainer.style.display === 'block') {
                                                    previousUrlsContainer.style.display = 'none';
                                                    viewPreviousUrlsLink.innerText = 'ver links utilizados anteriormente';
                                                } else {
                                                    viewPreviousUrlsLink.innerText = 'esconder links utilizados anteriormente';
                                                    previousUrlsContainer.style.display = 'block';
                                                    hideOnClickOutside(previousUrlsContainer, [
                                                        viewPreviousUrlsLink
                                                    ], function () {
                                                        viewPreviousUrlsLink.innerText = 'ver links utilizados anteriormente';
                                                    })
                                                }
                                            };

                                            editLongUrlLink.classList.add('generic-edit-link-1');
                                            editLongUrlLink.innerText = 'editar';
                                            editLongUrlLink.onclick = function (e) {
                                                editLongUrlInput.style.display = 'block';
                                                longUrlContainer.style.display = 'none';
                                                editLongUrlInput.value = editLongUrlInput.value;
                                                editLongUrlInput.focus();
                                                editLongUrlLink.style.display = 'none';
                                                saveEditLongUrlLink.style.display = 'inline-block';
                                                cancelEditLongUrlLink.style.display = 'inline-block';

                                                if (
                                                    previous_redirect_urls.length > 0
                                                    ||
                                                    previousUrlsContainer.querySelectorAll('.previously-used-link').length > 0
                                                ) {
                                                    viewPreviousUrlsLink.style.display = 'inline-block';
                                                }
                                            };

                                            saveEditLongUrlLink.classList.add('generic-edit-link-1');
                                            saveEditLongUrlLink.innerText = 'guardar';
                                            saveEditLongUrlLink.style.display = 'none';
                                            saveEditLongUrlLink.setAttribute('data-shortlink-id', id);


                                            saveEditLongUrlLink.onclick = function (e) {
                                                if(longUrlContainer.innerText === editLongUrlInput.value) {
                                                    return;
                                                }

                                                saveEditLongUrlLink.innerText = 'a guardar..';
                                                editLongUrlInputFeedback.innerText = '';
                                                editLongUrlInputFeedback.classList.remove('error');
                                                editLongUrlInputFeedback.style.display = 'none';

                                                var saveLongUrlFunc = function (token) {
                                                    var xhr = new XMLHttpRequest();
                                                    xhr.withCredentials = true;

                                                    xhr.addEventListener("readystatechange", function () {
                                                        if (this.readyState === 4) {
                                                            if (this.status === 401) {
                                                                window.location.reload();
                                                            }

                                                            var saveColor = saveEditLongUrlLink.style.color;

                                                            if (this.status === 201) {
                                                                longUrlContainer.innerText = editLongUrlInput.value;
                                                                saveEditLongUrlLink.style.color = 'green';
                                                                setTimeout(function () {
                                                                    saveEditLongUrlLink.style.color = saveColor;
                                                                    saveEditLongUrlLink.style.display = 'none';
                                                                    saveEditLongUrlLink.innerText = 'guardar';
                                                                    viewPreviousUrlsLink.style.display = 'none';
                                                                    editLongUrlInput.style.display = 'none';
                                                                    longUrlContainer.style.display = 'block';
                                                                    cancelEditLongUrlLink.style.display = 'none';
                                                                    editLongUrlLink.style.display = 'inline-block';
                                                                }, 1000);

                                                                const resObj = JSON.parse(this.response); //TODO: Catch exception
                                                                // reload url history
                                                                previousUrlsContainer.innerHTML = '';
                                                                if (
                                                                    typeof resObj.previous_redirect_urls !== 'undefined'
                                                                    &&
                                                                    resObj.previous_redirect_urls.length > 0
                                                                ) {
                                                                    for(var i = 0; i < resObj.previous_redirect_urls.length; i++) {
                                                                        const prevUrl = resObj.previous_redirect_urls[i];
                                                                        const prevUrlEl = document.createElement('div');
                                                                        prevUrlEl.classList.add('previously-used-link');
                                                                        prevUrlEl.setAttribute('data-url', prevUrl.url);

                                                                        const prevUrlElUrlContainer = document.createElement('div');
                                                                        prevUrlElUrlContainer.classList.add('link');
                                                                        prevUrlElUrlContainer.innerText = prevUrl.url;

                                                                        prevUrlEl.appendChild(prevUrlElUrlContainer);

                                                                        const prevUrlUsedTimeRangeContainer = document.createElement('div');
                                                                        prevUrlUsedTimeRangeContainer.classList.add('time-range');
                                                                        prevUrlUsedTimeRangeContainer.innerText = '(Desde ' + (new Date(prevUrl.created_at)).toLocaleString('pt-PT') + ' até '+ (new Date(prevUrl.updated_at)).toLocaleString('pt-PT') +')'

                                                                        prevUrlElUrlContainer.onclick = function (e) {
                                                                            editLongUrlInput.value = e.target.innerText;
                                                                            previousUrlsContainer.style.display = 'none';
                                                                            viewPreviousUrlsLink.innerText = 'ver links utilizados anteriormente';
                                                                            window.location.href = '#link-' + id;
                                                                        };

                                                                        prevUrlEl.appendChild(prevUrlUsedTimeRangeContainer);
                                                                        prevUrlEl.appendChild(prevUrlElUrlContainer);

                                                                        previousUrlsContainer.appendChild(prevUrlEl);
                                                                    }
                                                                }

                                                                return;
                                                            }

                                                            if (this.status === 403) {
                                                                editLongUrlInputFeedback.innerText = 'Não tens permissão para executar esta ação.';
                                                                editLongUrlInputFeedback.style.display = 'block';
                                                                editLongUrlInputFeedback.classList.add('error');
                                                                saveEditLongUrlLink.style.color = saveColor;
                                                                saveEditLongUrlLink.innerText = 'guardar';
                                                                return;
                                                            }

                                                            const resObj = JSON.parse(this.response); //TODO: Catch exception

                                                            if (
                                                                typeof resObj.message !== 'undefined'
                                                            ) {
                                                                editLongUrlInputFeedback.innerText = resObj.message;
                                                                editLongUrlInputFeedback.style.display = 'block';
                                                                editLongUrlInputFeedback.classList.add('error');
                                                                saveEditLongUrlLink.style.color = saveColor;
                                                                saveEditLongUrlLink.innerText = 'guardar';
                                                            }
                                                        }
                                                    });


                                                    const shortlinkId = saveEditLongUrlLink.getAttribute('data-shortlink-id');

                                                    var credentialsQueryStr =
                                                        "?shortlink_id=" + shortlinkId + "&long_url=" + encodeURIComponent(editLongUrlInput.value);

                                                    if (
                                                        typeof token !== 'undefined'
                                                        &&
                                                        token != null
                                                        &&
                                                        token.length > 0
                                                    ) {
                                                        credentialsQueryStr += '&g-recaptcha-response=' + token;
                                                    }

                                                    xhr.open(
                                                        "POST",'/api/shortlinks/edit' + credentialsQueryStr
                                                    );
                                                    xhr.setRequestHeader("Authorization", "Bearer " + window._authManager.at);
                                                    xhr.send();
                                                };

                                                if (
                                                    typeof window._enableCaptcha !== 'undefined'
                                                    &&
                                                    window._enableCaptcha === true
                                                ) {
                                                    grecaptcha.ready(function() {
                                                        grecaptcha.execute('{{ $captchaSitekey }}', {action: 'submit'}).then(function(token) {
                                                            saveLongUrlFunc(token);
                                                        });
                                                    });
                                                } else {
                                                    saveLongUrlFunc(null);
                                                }
                                            };


                                            cancelEditLongUrlLink.classList.add('generic-edit-link-1');
                                            cancelEditLongUrlLink.innerText = 'cancelar';
                                            cancelEditLongUrlLink.style.display = 'none';
                                            cancelEditLongUrlLink.onclick = function (e) {
                                                editLongUrlInput.style.display = 'none';
                                                longUrlContainer.style.display = 'block';
                                                cancelEditLongUrlLink.style.display = 'none';
                                                saveEditLongUrlLink.style.display = 'none';
                                                viewPreviousUrlsLink.style.display = 'none';
                                                editLongUrlLink.style.display = 'inline-block';
                                                editLongUrlInput.value = longUrlContainer.innerText;
                                                editLongUrlInputFeedback.style.display = 'none';
                                            };






                                            const infoRow = document.createElement('div');
                                            infoRow.classList.add('info-row');

                                            const createdAtCol = document.createElement('div');
                                            createdAtCol.classList.add('info-col');
                                            createdAtCol.innerHTML = 'Data criação: <small>' + (new Date(createdAt)).toLocaleString('pt-PT') + '</small>';
                                            infoRow.appendChild(createdAtCol);

                                            if (
                                                typeof total_views !== 'undefined'
                                                &&
                                                total_views != null
                                            ) {
                                                const totalViewsCol = document.createElement('div');
                                                totalViewsCol.classList.add('info-col');
                                                totalViewsCol.innerHTML = 'Visualizações: <small>' + total_views + '</small>';
                                                infoRow.appendChild(totalViewsCol);
                                            }

                                            if (
                                                typeof total_unique_views !== 'undefined'
                                                &&
                                                total_unique_views != null
                                            ) {
                                                const totalUniqueViewsCol = document.createElement('div');
                                                totalUniqueViewsCol.classList.add('info-col');
                                                totalUniqueViewsCol.innerHTML = 'Visualizações Únicas: <small>' + total_unique_views + '</small>';
                                                infoRow.appendChild(totalUniqueViewsCol);
                                            }

                                            this.el().appendChild(listItem);

                                            if (window._authManager.isLoggedIn) {
                                                listItem.appendChild(listItemOptions);
                                                listItem.appendChild(listItemOptionsContainer);
                                            }

                                            listItem.appendChild(longUrlLabel);

                                            if (
                                                window._authManager.userHasPermission('edit_shortlinks_destination_url')
                                            ) {
                                                listItem.appendChild(editLongUrlLink);
                                                listItem.appendChild(cancelEditLongUrlLink);
                                                listItem.appendChild(saveEditLongUrlLink);

                                                if (
                                                    window._authManager.userHasPermission('view_shortlinks_url_history')
                                                ) {
                                                    listItem.appendChild(viewPreviousUrlsLink);
                                                    listItem.appendChild(previousUrlsContainer);
                                                }

                                                listItem.appendChild(editLongUrlInput);
                                                listItem.appendChild(editLongUrlInputFeedback);
                                            }

                                            listItem.appendChild(longUrlContainer);
                                            listItem.appendChild(shortlinkLabel);
                                            listItem.appendChild(shortlinkContainer);
                                            listItem.appendChild(infoRow);

                                            if (
                                                typeof destinationEmail !== 'undefined'
                                                &&
                                                destinationEmail != null
                                                &&
                                                destinationEmail.length > 0
                                            ) {
                                                const destinationEmailRow = document.createElement('div');
                                                destinationEmailRow.classList.add('info-row');

                                                const destinationEmailCol = document.createElement('div');
                                                destinationEmailCol.classList.add('info-col');
                                                destinationEmailCol.innerHTML = 'Enviado para: <small>' + destinationEmail + '</small>';

                                                destinationEmailRow.appendChild(destinationEmailCol);

                                                listItem.appendChild(destinationEmailRow);
                                            }



                                        },
                                        fetch: function (pageNumber = 1) {
                                            if (window._authManager.isAuthenticated !== true) {
                                                return;
                                            }
                                            this.clear();
                                            window.App.Components.MyLinks.Components.Links.Components.NotFound.hide();
                                            window.App.Components.MyLinks.Components.Links.Components.GuestMsg.hide();
                                            window.App.Components.MyLinks.Components.Links.Components.Loading.show();

                                            const $this = this;

                                            var xhr = new XMLHttpRequest();
                                            xhr.withCredentials = true;

                                            xhr.addEventListener("readystatechange", function () {
                                                if (this.readyState === 4) {
                                                    if (this.status === 401) {
                                                        window.location.reload();
                                                    }

                                                    const resObj = JSON.parse(this.response); //TODO: Catch exception

                                                    if (this.status === 200) {
                                                        window.App.Components.MyLinks.Components.Links.Components.Loading.hide();
                                                        if (resObj.search_results.total == 0) {
                                                            window.App.Components.MyLinks.Components.Links.Components.NotFound.show();
                                                            $this.Components.ListOptions.hide();
                                                        } else  {
                                                            window.App.Components.MyLinks.Components.Links.Components.NotFound.hide();
                                                            if (
                                                                typeof resObj.availableOrderBys !== 'undefined'
                                                                &&
                                                                typeof resObj.orderBy !== 'undefined'
                                                            ) {
                                                                $this.Components.ListOptions.Components.OrderBy.show(
                                                                    resObj.orderBy,
                                                                    resObj.availableOrderBys
                                                                );
                                                                $this.Components.ListOptions.show();
                                                            } else {
                                                                $this.Components.ListOptions.hideIfNoOptionsVisible();
                                                            }
                                                        }



                                                        for (var i = 0; i < resObj.search_results.data.length; i++) {
                                                            $this.addLink(
                                                                resObj.search_results.data[i].id,
                                                                resObj.search_results.data[i].long_url,
                                                                resObj.search_results.data[i].shortlink,
                                                                resObj.search_results.data[i].destination_email,
                                                                resObj.search_results.data[i].created_at,
                                                                resObj.search_results.data[i].total_views,
                                                                resObj.search_results.data[i].total_unique_views,
                                                                resObj.search_results.data[i].previous_redirect_urls,
                                                            );
                                                        }

                                                        if (resObj.search_results.total > 0) {
                                                            if (!window._authManager.isLoggedIn) {
                                                                window.App.Components.MyLinks.Components.Links.Components.GuestMsg.show();
                                                            }

                                                            window.App.Helpers.Pagination.setCurrentPage('MyLinksComponent', resObj.search_results.current_page);
                                                            if (resObj.search_results.last_page > 1) {
                                                                const paginationLinks = window.App.Helpers.Pagination.createEl(
                                                                    resObj.search_results.current_page,
                                                                    resObj.search_results.last_page,
                                                                    'MyLinksComponent',
                                                                    function(param) {
                                                                        window.App.Components.MyLinks.Components.Links.Components.List.fetch(param);
                                                                    }
                                                                );
                                                                $this.el().appendChild(paginationLinks);
                                                            }

                                                        }
                                                    }
                                                }
                                            });

                                            var postUrl = this.api + '?page=' + pageNumber;

                                            if (
                                                this.Components.ListOptions.Components.OrderBy.currentOrderBy != null
                                            ) {
                                                postUrl += '&orderBy=' + this.Components.ListOptions.Components.OrderBy.currentOrderBy;
                                            }

                                            xhr.open("POST", postUrl);
                                            xhr.setRequestHeader("Authorization", "Bearer " + window._authManager.at);
                                            xhr.send();
                                        },
                                    }
                                }
                            }
                        },
                        initialize: function () {
                            this.Components.Links.initialize();
                            this.Components.CloseBtn.initialize();
                        }

                    },
                    ContactUs: {
                        hasInitialized: false,
                        el: function () {
                            return document.getElementById('contact-us-view');
                        },
                        hide: function () {
                            this.el().style.display = 'none';
                        },
                        show: function () {
                            this.initialize();
                            this.Components.Feedback.hide();
                            this.el().style.display = 'block';
                        },
                        Components: {
                            CloseBtn: {
                                hasInitialized: false,
                                el: function () {
                                    return document.getElementById('contact-us-view-close-btn');
                                },
                                initialize: function () {
                                    if ( this.hasInitialized == false ) {

                                        this.el().onclick = function (e) {
                                            window.App.Components.ContactUs.hide();
                                        };

                                        this.hasInitialized = true;
                                    }
                                }
                            },
                            Name: {
                                hasInitialized: false,
                                el: function () {
                                    return document.getElementById("contact-name");
                                },
                                labelEl: function () {
                                    return document.getElementById("contact-name-label");
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
                                    return document.getElementById("contact-email");
                                },
                                labelEl: function () {
                                    return document.getElementById("contact-email-label");
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
                            Phone: {
                                hasInitialized: false,
                                el: function () {
                                    return document.getElementById("contact-phone");
                                },
                                labelEl: function () {
                                    return document.getElementById("contact-phone-label");
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
                            Subject: {
                                hasInitialized: false,
                                el: function () {
                                    return document.getElementById("contact-subject");
                                },
                                labelEl: function () {
                                    return document.getElementById("contact-subject-label");
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
                            Message: {
                                hasInitialized: false,
                                el: function () {
                                    return document.getElementById("contact-message");
                                },
                                labelEl: function () {
                                    return document.getElementById("contact-message-label");
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
                                    return document.getElementById('form-box-contact-us-feedback');
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
                                    el.classList.remove('success');
                                    el.classList.add('info');
                                    el.style.display = 'block';
                                },
                                showError: function(message) {
                                    const el = this.el();
                                    el.innerText = message;
                                    el.classList.remove('info');
                                    el.classList.remove('success');
                                    el.classList.add('error');
                                    el.style.display = 'block';
                                },
                                showSuccess: function(message) {
                                    const el = this.el();
                                    el.innerText = message;
                                    el.classList.remove('info');
                                    el.classList.remove('error');
                                    el.classList.add('success');
                                    el.style.display = 'block';
                                },
                            },
                            SendBtn: {
                                hasInitialized: false,
                                api: "{{ url('/api/contact') }}",
                                el: function () {
                                    return document.getElementById('contact-us-btn');
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

                                        if (window._authManager.isAuthenticated) {
                                            $this.enable();
                                        } else {
                                            $this.disable();
                                            document.addEventListener('userAuthenticated', (e) => {
                                                $this.enable();
                                            }, false);
                                        }

                                        this.el().onclick = function (e) {
                                            if (window._authManager.isAuthenticated !== true) {
                                                // authentication is a must
                                                // if not authenticated something must be wrong
                                                return;
                                            }

                                            const nameInput = window.App.Components.ContactUs.Components.Name.el();
                                            const emailInput = window.App.Components.ContactUs.Components.Email.el();
                                            const phoneInput = window.App.Components.ContactUs.Components.Phone.el();
                                            const subjectInput = window.App.Components.ContactUs.Components.Subject.el();
                                            const messageInput = window.App.Components.ContactUs.Components.Message.el();

                                            const requiredFields = [
                                                nameInput,
                                                emailInput,
                                                phoneInput,
                                                subjectInput,
                                                messageInput
                                            ];

                                            for(var i = 0; i < requiredFields.length; i++) {
                                                const field = requiredFields[i];

                                                if (field.value.length == 0) {
                                                    field.classList.add('has-error');
                                                    return false;
                                                } else {
                                                    field.classList.remove('has-error');
                                                }
                                            }

                                            $this.disable();
                                            window.App.Components.ContactUs.Components.Feedback.showInfo('por favor espere..');

                                            var func = function(token) {
                                                var xhr = new XMLHttpRequest();
                                                xhr.withCredentials = true;

                                                xhr.addEventListener("readystatechange", function () {
                                                    if (this.readyState === 4) {
                                                        if (this.status === 401) {
                                                            window.location.reload();
                                                        }
                                                        $this.enable();
                                                        const resObj = JSON.parse(this.response); //TODO: Catch exception

                                                        if (this.status === 201) {
                                                            window.App.Components.ContactUs.Components.Feedback.showSuccess('A sua mensagem foi enviada com sucesso!');

                                                            for(var i = 0; i < requiredFields.length; i++) {
                                                                const field = requiredFields[i];
                                                                field.value = '';
                                                            }
                                                            return;
                                                        }

                                                        if (
                                                            typeof resObj.message !== 'undefined'
                                                        ) {
                                                            window.App.Components.ContactUs.Components.Feedback.showError(resObj.message);
                                                        }
                                                    }
                                                });

                                                const name = encodeURIComponent(nameInput.value);
                                                const email = encodeURIComponent(emailInput.value);
                                                const phone = encodeURIComponent(phoneInput.value);
                                                const subject = encodeURIComponent(subjectInput.value);
                                                const message = encodeURIComponent(messageInput.value);


                                                var credentialsQueryStr =
                                                    "?name=" + name + "&email=" + email + "&phone=" + phone + "&subject=" + subject + "&message=" + message;

                                                if (
                                                    typeof token !== 'undefined'
                                                    &&
                                                    token != null
                                                    &&
                                                    token.length > 0
                                                ) {
                                                    credentialsQueryStr += '&g-recaptcha-response=' + token;
                                                }

                                                xhr.open(
                                                    "POST",
                                                    $this.api + credentialsQueryStr
                                                );
                                                xhr.setRequestHeader("Authorization", "Bearer " + window._authManager.at);
                                                xhr.send();
                                            };


                                            if (
                                                typeof window._enableCaptcha !== 'undefined'
                                                &&
                                                window._enableCaptcha === true
                                            ) {
                                                grecaptcha.ready(function() {
                                                grecaptcha.execute('{{ $captchaSitekey }}', {action: 'submit'}).then(function(token) {
                                                    func(token);
                                                    });
                                                });
                                            } else {
                                                func(null);
                                            }

                                        };
                                        this.hasInitialized = true;
                                    }
                                }
                            }
                        },
                        initialize: function () {
                            this.Components.CloseBtn.initialize();
                            this.Components.Name.initialize();
                            this.Components.Email.initialize();
                            this.Components.Phone.initialize();
                            this.Components.Subject.initialize();
                            this.Components.Message.initialize();
                            this.Components.SendBtn.initialize();
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
                            CloseBtn: {
                                hasInitialized: false,
                                el: function () {
                                    return document.getElementById('email-confirmed-close-btn');
                                },
                                initialize: function () {
                                    if ( this.hasInitialized == false ) {

                                        this.el().onclick = function (e) {
                                            window.App.Components.EmailConfirmed.hide();
                                        };

                                        this.hasInitialized = true;
                                    }
                                }
                            },
                            LoginBtn: {
                                hasInitialized: false,
                                el: function () {
                                    return document.getElementById('email-confirmed-login');
                                },
                                initialize: function () {
                                    if ( this.hasInitialized == false ) {

                                        this.el().onclick = function (e) {
                                            window.App.Views.Login.show();
                                        };

                                        this.hasInitialized = true;
                                    }
                                }
                            },
                        },
                        initialize: function () {
                            this.Components.CloseBtn.initialize();
                            this.Components.LoginBtn.initialize();
                        }
                    },
                },
                Views: {
                    HomePage: {
                        components: {
                            initiallyVisible:  ['LogoTop', 'MenuToggleMobile', 'MenuTop', 'MenuAccTop', 'ShortenUrl'],
                            initiallyHidden: ['ShortlinkResult'],
                            sticky: ['LogoTop', 'MenuTop', 'MenuAccTop', 'ShortenUrl', 'ShortlinkResult', 'MyLinks']
                        },
                        show: function () {
                            window.App.currentView = 'HomePage';
                            window.App.hideNonStickyComponents();
                            window.App.hideComponents(this.components.initiallyHidden);
                            window.App.showComponents(this.components.initiallyVisible);
                            window.history.pushState(null, 'web into link - url shortner', '/');
                        },
                        hide: function () {
                            window.App.hideComponents(this.components.initiallyVisible);
                            window.App.hideComponents(this.components.initiallyHidden);
                        }
                    },
                    Profile: {
                        components: {
                            initiallyVisible:  ['LogoTop', 'MenuToggleMobile', 'MenuTop', 'MenuAccTop', 'ShortenUrl', 'Profile'],
                            initiallyHidden: ['ShortlinkResult'],
                            sticky: ['LogoTop', 'MenuTop', 'MenuAccTop', 'ShortenUrl', 'ShortlinkResult']
                        },
                        show: function () {
                            window.App.currentView = 'Profile';
                            window.App.hideNonStickyComponents();
                            window.App.hideComponents(this.components.initiallyHidden);
                            window.App.showComponents(this.components.initiallyVisible);
                            window.history.pushState(null, 'Meu perfil', '/meu-perfil');
                        },
                        hide: function () {
                            window.App.hideComponents(this.components.initiallyVisible);
                            window.App.hideComponents(this.components.initiallyHidden);
                        }
                    },
                    MyLinks: {
                        components: {
                            initiallyVisible:  ['LogoTop', 'MenuToggleMobile', 'MenuTop', 'MenuAccTop', 'ShortenUrl', 'MyLinks'],
                            initiallyHidden: ['ShortlinkResult'],
                            sticky: ['LogoTop', 'MenuTop', 'MenuAccTop', 'ShortenUrl', 'ShortlinkResult']
                        },
                        show: function () {
                            window.App.currentView = 'MyLinks';
                            window.App.hideNonStickyComponents();
                            window.App.hideComponents(this.components.initiallyHidden);
                            window.App.showComponents(this.components.initiallyVisible);
                            window.history.pushState(null, 'Os meus links', '/os-meus-links');
                        },
                        hide: function () {
                            window.App.hideComponents(this.components.initiallyVisible);
                            window.App.hideComponents(this.components.initiallyHidden);
                        },
                    },
                    ContactUs: {
                        components: {
                            initiallyVisible:  ['LogoTop', 'MenuToggleMobile', 'MenuTop', 'MenuAccTop', 'ShortenUrl', 'ContactUs'],
                            initiallyHidden: ['ShortlinkResult'],
                            sticky: ['LogoTop', 'MenuTop', 'MenuAccTop', 'ShortenUrl', 'ShortlinkResult']
                        },
                        show: function () {
                            window.App.currentView = 'ContactUs';
                            window.App.hideNonStickyComponents();
                            window.App.hideComponents(this.components.initiallyHidden);
                            window.App.showComponents(this.components.initiallyVisible);
                            window.history.pushState(null, 'Contacte', '/contacte');
                        },
                        hide: function () {
                            window.App.hideComponents(this.components.initiallyVisible);
                            window.App.hideComponents(this.components.initiallyHidden);
                        }
                    },
                    Login: {
                        components: {
                            initiallyVisible:  ['LogoTop', 'MenuToggleMobile', 'MenuTop', 'MenuAccTop', 'ShortenUrl', 'Login'],
                            initiallyHidden: ['ShortlinkResult'],
                            sticky: ['LogoTop', 'MenuTop', 'MenuAccTop', 'ShortenUrl', 'ShortlinkResult']
                        },
                        show: function () {
                            window.App.currentView = 'Login';
                            window.App.hideNonStickyComponents();
                            window.App.hideComponents(this.components.initiallyHidden);
                            window.App.showComponents(this.components.initiallyVisible);
                        },
                        hide: function () {
                            window.App.hideComponents(this.components.initiallyVisible);
                            window.App.hideComponents(this.components.initiallyHidden);
                        }
                    },
                    Register: {
                        components: {
                            initiallyVisible:  ['LogoTop', 'MenuToggleMobile', 'MenuTop', 'MenuAccTop', 'ShortenUrl', 'Register'],
                            initiallyHidden: ['ShortlinkResult'],
                            sticky: ['LogoTop', 'MenuTop', 'MenuAccTop', 'ShortenUrl', 'ShortlinkResult']
                        },
                        show: function () {
                            window.App.currentView = 'Register';
                            window.App.hideNonStickyComponents();
                            window.App.hideComponents(this.components.initiallyHidden);
                            window.App.showComponents(this.components.initiallyVisible);
                        },
                        hide: function () {
                            window.App.hideComponents(this.components.initiallyVisible);
                            window.App.hideComponents(this.components.initiallyHidden);
                        }
                    },
                    RegisterCustomShortlink: {
                        components: {
                            initiallyVisible:  ['LogoTop', 'MenuToggleMobile', 'MenuTop', 'MenuAccTop', 'RegisterCustomShortlink'],
                            initiallyHidden: ['ShortlinkResult'],
                            sticky: ['LogoTop', 'MenuTop', 'MenuAccTop', 'RegisterCustomShortlink', 'ShortlinkResult', 'MyLinks']
                        },
                        show: function () {
                            window.App.currentView = 'RegisterCustomShortlink';
                            window.App.hideNonStickyComponents();
                            window.App.hideComponents(this.components.initiallyHidden);
                            window.App.showComponents(this.components.initiallyVisible);
                            window.history.pushState(null, 'Criar links personalizado', '/criar-link-personalizado');
                        },
                        hide: function () {
                            window.App.hideComponents(this.components.initiallyVisible);
                            window.App.hideComponents(this.components.initiallyHidden);
                        }
                    },
                    EmailConfirmed: {
                        components: {
                            initiallyVisible: ['LogoTop', 'MenuToggleMobile', 'MenuTop', 'MenuAccTop', 'ShortenUrl', 'EmailConfirmed'],
                            initiallyHidden: ['ShortlinkResult'],
                            sticky: ['LogoTop', 'MenuTop', 'MenuAccTop', 'ShortenUrl', 'ShortlinkResult']
                        },
                        show: function () {
                            window.App.currentView = 'EmailConfirmed';
                            window.App.hideNonStickyComponents();
                            window.App.hideComponents(this.components.initiallyHidden);
                            window.App.showComponents(this.components.initiallyVisible);
                        },
                        hide: function () {
                            window.App.hideComponents(this.components.initiallyVisible);
                            window.App.hideComponents(this.components.initiallyHidden);
                        }
                    },
                    ChangePassword: {
                        components: {
                            initiallyVisible: ['LogoTop', 'MenuToggleMobile', 'MenuTop', 'MenuAccTop', 'ShortenUrl', 'ChangePassword'],
                            initiallyHidden: ['ShortlinkResult'],
                            sticky: ['LogoTop', 'MenuTop', 'MenuAccTop', 'ShortenUrl', 'ShortlinkResult']
                        },
                        show: function () {
                            window.App.currentView = 'ChangePassword';
                            window.App.hideNonStickyComponents();
                            window.App.hideComponents(this.components.initiallyHidden);
                            window.App.showComponents(this.components.initiallyVisible);
                        },
                        hide: function () {
                            window.App.hideComponents(this.components.initiallyVisible);
                            window.App.hideComponents(this.components.initiallyHidden);
                        }
                    },
                },
                Helpers: {
                    Pagination: {
                        paginations: {},
                        setCurrentPage: function(paginationIdentifier, currentPage) {
                            if (
                                typeof this.paginations[paginationIdentifier] !== 'undefined'
                            ) {
                                this.paginations[paginationIdentifier].currentPage = currentPage;
                                return;
                            }

                            this.paginations[paginationIdentifier] = {
                                currentPage: currentPage
                            };
                        },
                        getCurrentPage: function(paginationIdentifier) {
                            if (
                                typeof this.paginations[paginationIdentifier] !== 'undefined'
                                &&
                                typeof this.paginations[paginationIdentifier].currentPage !== 'undefined'
                            ) {
                                return this.paginations[paginationIdentifier].currentPage;
                            }
                            return 1;
                        },
                        resetPaginationFor: function(paginationIdentifier) {
                            if (
                                typeof this.paginations[paginationIdentifier] !== 'undefined'
                            ) {
                                delete this.paginations[paginationIdentifier];
                            }
                        },
                        resetAllPaginations: function () {
                            const paginations = Object.keys(this.paginations);

                            for(var i = 0; i < paginations.length; i++) {
                                const pagId = paginations[i];
                                this.resetPaginationFor(pagId);
                            }
                        },
                        createEl: function (currentPage, lastPage, paginationIdentifier, fetchFunc) {
                            if (
                                typeof this.paginations[paginationIdentifier] !== 'undefined'
                            ) {
                                this.paginations[paginationIdentifier].currentPage = currentPage;
                                this.paginations[paginationIdentifier].lastPage = lastPage;
                                this.paginations[paginationIdentifier].fetchFunc = fetchFunc;
                            } else {
                                this.paginations[paginationIdentifier] = {
                                    currentPage: currentPage,
                                    lastPage: lastPage,
                                    fetchFunc: fetchFunc
                                };
                            }

                            const paginationContainer = document.createElement('div');
                            paginationContainer.classList.add('pagination-container');

                            const numbersBeginning = 2;
                            const numbersBefore = 2;
                            const numbersAfter = 2;
                            const numbersEnd = 2;

                            var i;
                            var pagination = [];
                            for(i = 1; i <= numbersBeginning; i++) {
                                pagination.push(i);
                            }
                            for(i = currentPage; i >= currentPage - numbersBefore; i--) {
                                pagination.push(i);
                            }
                            pagination.push(currentPage);
                            for(i = currentPage; i <= currentPage + numbersAfter; i++) {
                                pagination.push(i);
                            }
                            for(i = lastPage; i > lastPage - numbersEnd; i--) {
                                pagination.push(i);
                            }

                            pagination = pagination.filter(function(pageNumber) {
                                return pageNumber > 0 && pageNumber <= lastPage;
                            });

                            pagination = pagination.filter(function(value, index, self) {
                                return self.indexOf(value) === index;
                            });

                            pagination = pagination.sort(function(a, b) {
                                if( a === Infinity )
                                    return 1;
                                else if( isNaN(a))
                                    return -1;
                                else
                                    return a - b;
                            });

                            var paginationWithDots = [];
                            for(i = 0; i < pagination.length; i++) {
                                const pageNum = pagination[i];
                                paginationWithDots.push(pageNum);

                                var nextPageNum = null;
                                if ( (i + 1) < pagination.length ) {
                                    nextPageNum = pagination[i + 1];
                                }

                                if (
                                    nextPageNum != null
                                    &&
                                    pageNum+1 < nextPageNum
                                ) {
                                    paginationWithDots.push('...');
                                }
                            }

                            const $this = this;

                            for(i = 0; i < paginationWithDots.length; i++) {
                                const paginationLink = document.createElement('a');
                                paginationLink.href = 'javascript:void(0);';
                                paginationLink.innerHTML = paginationWithDots[i];

                                if (paginationWithDots[i] === currentPage) {
                                    paginationLink.classList.add('current');
                                } else {
                                    if (paginationWithDots[i] !== '...') {
                                        paginationLink.onclick = function (e) {
                                            $this.paginations[paginationIdentifier].fetchFunc(e.target.innerText);
                                        };
                                    }
                                }

                                paginationContainer.appendChild(paginationLink);
                            }
                            return paginationContainer;
                        }
                    },
                }
            };
        </script>

        @if ($isAdmin)
            <script>
                window.App.Components.PA = {
                    hasInitialized: false,
                    el: function () {
                        return document.getElementById('pa-view');
                    },
                    hide: function () {
                        this.el().style.display = 'none';
                    },
                    show: function () {
                        this.initialize();

                        this.Components.BackButton.hide();
                        this.Components.Filters.hide();
                        this.Components.OptionButtons.hide();
                        this.Components.Loading.hide();
                        this.Components.NoResults.hide();
                        this.Components.ResultsTable.hide();

                        this.showAllDashboardItems();

                        this.el().style.display = 'block';


                    },
                    hideAllDashboardItems: function(except = []) {
                        const components = Object.keys(this.Components);
                        for(var i = 0; i < components.length; i++) {
                            const componentName = components[i];
                            if (except.indexOf(componentName) >= 0) {
                                continue;
                            }
                            if (
                                typeof this.Components[componentName].dashboardItem !== 'undefined'
                                &&
                                this.Components[componentName].dashboardItem === true
                                &&
                                typeof this.Components[componentName].hide === 'function'
                            ) {
                                this.Components[componentName].hide();
                            }
                        }
                    },
                    showAllDashboardItems: function() {
                        const components = Object.keys(this.Components);
                        for(var i = 0; i < components.length; i++) {
                            const componentName = components[i];
                            if (
                                typeof this.Components[componentName].dashboardItem !== 'undefined'
                            ) {

                                if (
                                    this.Components[componentName].dashboardItem === true
                                ) {
                                    if (typeof this.Components[componentName].show === 'function') {
                                        this.Components[componentName].show();
                                    }
                                } else {
                                    if (
                                        typeof this.Components[componentName].hide === 'function'
                                    ) {
                                        this.Components[componentName].hide();
                                    }
                                }

                            }
                        }
                    },
                    Components: {
                        LoadingOverlay: {
                            el: function () {
                                return document.getElementById('pa-view-loading');
                            },
                            show: function () {
                                this.el().style.display = 'block';
                            },
                            hide: function () {
                                this.el().style.display = 'none';
                            },
                        },
                        CloseBtn: {
                            hasInitialized: false,
                            el: function () {
                                return document.getElementById('pa-view-close-btn');
                            },
                            initialize: function () {
                                if ( this.hasInitialized == false ) {

                                    this.el().onclick = function (e) {
                                        window.App.Components.PA.hide();
                                    };

                                    this.hasInitialized = true;
                                }
                            }
                        },
                        BackButton: {
                            dashboardItem: false,
                            hasInitialized: false,
                            customBackFunc: null,
                            el: function () {
                                return document.getElementById('dashboard-back-button');
                            },
                            hide: function () {
                                this.el().style.display = 'none';
                            },
                            show: function () {
                                this.initialize();
                                this.customBackFunc = null;
                                this.el().style.display = 'block';
                            },
                            initialize: function () {
                                if ( this.hasInitialized == false ) {

                                    const $this = this;
                                    this.el().onclick = function (e) {
                                        if (
                                            typeof $this.customBackFunc === 'function'
                                        ) {
                                            $this.customBackFunc();
                                            return;
                                        }
                                        window.App.Components.PA.showAllDashboardItems();
                                        $this.hide();
                                    };

                                    this.hasInitialized = true;
                                }
                            }
                        },
                        Filters: {
                            dashboardItem: false,
                            el: function () {
                                return document.getElementById('dashboard-filter-container');
                            },
                            hide: function () {
                                this.el().style.display = 'none';

                                const componentNames = Object.keys(this.Components);
                                for(var i = 0; i < componentNames.length; i++) {
                                    const componentName = componentNames[i];

                                    if (
                                        typeof this.Components[componentName].hide === 'function'
                                    ) {
                                        this.Components[componentName].hide();
                                    }
                                }
                            },
                            show: function () {
                                this.el().style.display = 'block';
                            },
                            Components: {
                                DateSince: {
                                    el: function () {
                                        return document.getElementById('dashboard-filter-date-since');
                                    },
                                    inputEl: function () {
                                        return document.getElementById('dashboard-filter-date-since-input');
                                    },
                                    setDate: function (date) {
                                        this.inputEl().value = date;
                                    },
                                    hide: function () {
                                        this.el().style.display = 'none';
                                    },
                                    show: function (defaultDate = null) {
                                        if (defaultDate != null) {
                                            this.setDate(defaultDate);
                                        }
                                        this.el().style.display = 'inline-block';
                                    },
                                },
                                DateUntil: {
                                    el: function () {
                                        return document.getElementById('dashboard-filter-date-until');
                                    },
                                    inputEl: function () {
                                        return document.getElementById('dashboard-filter-date-until-input');
                                    },
                                    setDate: function (date) {
                                        this.inputEl().value = date;
                                    },
                                    hide: function () {
                                        this.el().style.display = 'none';
                                    },
                                    show: function (defaultDate = null) {
                                        if (defaultDate != null) {
                                            this.setDate(defaultDate);
                                        }
                                        this.el().style.display = 'inline-block';
                                    },
                                },
                                GroupBy: {
                                    el: function () {
                                        return document.getElementById('dashboard-filter-view');
                                    },
                                    inputEl: function () {
                                        return document.getElementById('dashboard-filter-view-input');
                                    },
                                    setGroupBy: function (groupBy) {
                                        this.inputEl().value = groupBy;
                                    },
                                    setAvailableGroupBys: function (availableGroupBys) {
                                        const inputEl = this.inputEl();
                                        inputEl.innerHTML = '';
                                        for(var i = 0; i < availableGroupBys.length; i++) {
                                            const availableGroupBy = availableGroupBys[i];
                                            const optionEl = document.createElement('option');
                                            optionEl.setAttribute('value', availableGroupBy.name);
                                            optionEl.innerText = availableGroupBy.label;
                                            inputEl.appendChild(optionEl);
                                        }
                                    },
                                    hide: function () {
                                        this.el().style.display = 'none';
                                    },
                                    show: function (availableGroupBys, selectedGroupBy = null) {
                                        this.setAvailableGroupBys(availableGroupBys);
                                        if (selectedGroupBy != null) {
                                            this.setGroupBy(selectedGroupBy);
                                        }
                                        this.el().style.display = 'inline-block';
                                    }
                                },
                                OrderBy: {
                                    el: function () {
                                        return document.getElementById('dashboard-filter-order');
                                    },
                                    inputEl: function () {
                                        return document.getElementById('dashboard-filter-order-input');
                                    },
                                    setOrderBy: function (orderBy) {
                                        this.inputEl().value = orderBy;
                                    },
                                    setAvailableOrderBys: function (availableOrderBys) {
                                        const inputEl = this.inputEl();
                                        inputEl.innerHTML = '';
                                        for(var i = 0; i < availableOrderBys.length; i++) {
                                            const availableOrderBy = availableOrderBys[i];
                                            const optionEl = document.createElement('option');
                                            optionEl.setAttribute('value', availableOrderBy.name);
                                            optionEl.innerText = availableOrderBy.label;
                                            inputEl.appendChild(optionEl);
                                        }
                                    },
                                    hide: function () {
                                        this.el().style.display = 'none';
                                    },
                                    show: function (availableOrderBys, selectedOrderBy = null) {
                                        if (
                                            typeof availableOrderBys === 'undefined'
                                            ||
                                            !availableOrderBys
                                        ) {
                                            this.hide();
                                            return;
                                        }
                                        this.setAvailableOrderBys(availableOrderBys);
                                        if (selectedOrderBy != null) {
                                            this.setOrderBy(selectedOrderBy);
                                        }
                                        this.el().style.display = 'inline-block';
                                    }
                                },
                                FreeSearch: {
                                    hasInitialized: false,
                                    refreshFunction: null,
                                    containerEl: function () {
                                        return document.getElementById('free-text-search-container');
                                    },
                                    el: function () {
                                        return document.getElementById('free-text-search');
                                    },
                                    getValue: function () {
                                        return this.el().value;
                                    },
                                    labelEl: function () {
                                        return document.getElementById('free-text-search-label');
                                    },
                                    setRefreshFunction: function (refreshFunc) {
                                        this.refreshFunction = refreshFunc;
                                    },
                                    show: function () {
                                        this.initialize();
                                        this.containerEl().style.display = 'block';
                                    },
                                    hide: function () {
                                        this.containerEl().style.display = 'none';
                                        this.refreshFunction = null;
                                        this.reset();
                                    },
                                    reset: function () {
                                        this.el().value = '';
                                        this.labelEl().parentNode.classList.remove("active");
                                        this.labelEl().parentNode.classList.remove("mtop-22");
                                    },
                                    initialize: function () {
                                        if (this.hasInitialized === false) {
                                            const $this = this;
                                            this.labelEl().onclick = function (e) {
                                                e.target.parentNode.classList.add("active");
                                                $this.el().focus();
                                            };

                                            this.el().onchange = function(e) {
                                                if (
                                                    typeof $this.refreshFunction === 'function'
                                                ) {
                                                    $this.refreshFunction();
                                                }
                                            };

                                            this.el().onfocus = function (e) {
                                                e.target.parentNode.classList.add("active");
                                                e.target.parentNode.classList.add("mtop-22");
                                                e.target.value = e.target.value.trim();
                                            };

                                            this.el().addEventListener("focusout", function (e) {
                                                e.target.value = e.target.value.trim();
                                                if (e.target.value.length == 0) {
                                                    $this.reset();
                                                }
                                            });

                                            this.hasInitialized = true;
                                        }
                                    }
                                },
                                RefreshResultsBtn: {
                                    hasInitialized: false,
                                    refreshResultsFunction: null,
                                    el: function () {
                                        return document.getElementById('refresh-dashboard-results');
                                    },
                                    hide: function () {
                                        this.el().style.display = 'none';
                                    },
                                    show: function (refreshResultsFunc) {
                                        this.initialize();
                                        this.refreshResultsFunction = refreshResultsFunc;
                                        this.el().style.display = 'inline-block';
                                    },
                                    initialize: function () {
                                        if (this.hasInitialized === false) {

                                            const $this = this;
                                            this.el().onclick = function(e) {
                                                if (
                                                    typeof $this.refreshResultsFunction === 'function'
                                                ) {
                                                    $this.refreshResultsFunction();
                                                }
                                            };

                                            this.hasInitialized = true;
                                        }
                                    }
                                },
                            }
                        },
                        OptionButtons: {
                            dashboardItem: false,
                            el: function () {
                                return document.getElementById('option-buttons');
                            },
                            hide: function () {
                                this.el().style.display = 'none';

                                const componentNames = Object.keys(this.Components);
                                for(var i = 0; i < componentNames.length; i++) {
                                    const componentName = componentNames[i];

                                    if (
                                        typeof this.Components[componentName].hide === 'function'
                                    ) {
                                        this.Components[componentName].hide();
                                    }
                                }
                            },
                            show: function () {
                                this.el().style.display = 'block';
                            },
                            Components: {
                                CreateNewBtn: {
                                    hasInitialized: false,
                                    createNewFunction: null,
                                    el: function () {
                                        return document.getElementById('create-new');
                                    },
                                    hide: function () {
                                        this.el().style.display = 'none';
                                    },
                                    show: function (createNewFunc) {
                                        this.initialize();
                                        this.createNewFunction = createNewFunc;
                                        this.el().style.display = 'inline-block';
                                    },
                                    initialize: function () {
                                        if (this.hasInitialized === false) {

                                            const $this = this;
                                            this.el().onclick = function(e) {
                                                if (
                                                    typeof $this.createNewFunction === 'function'
                                                ) {
                                                    $this.createNewFunction();
                                                }
                                            };

                                            this.hasInitialized = true;
                                        }
                                    }
                                },
                            }
                        },
                        Loading: {
                            dashboardItem: false,
                            el: function () {
                                return document.getElementById('dashboard-loading');
                            },
                            hide: function () {
                                this.el().style.display = 'none';
                            },
                            show: function () {
                                this.el().style.display = 'block';
                            },
                        },
                        NoResults: {
                            dashboardItem: false,
                            el: function () {
                                return document.getElementById('dashboard-no-results');
                            },
                            hide: function () {
                                this.el().style.display = 'none';
                            },
                            show: function () {
                                this.el().style.display = 'block';
                            },
                        },
                        Edit: {
                            dashboardItem: false,
                            apis: {
                                Users: "{{ url('/api/users/prepare-edit-form') }}",
                                PermissionGroups: "{{ url('/api/permission-groups/prepare-edit-form') }}",
                            },
                            el: function () {
                                return document.getElementById('dashboard-edit');
                            },
                            hide: function () {
                                this.el().style.display = 'none';
                                this.Components.Title.clear();
                                this.Components.Fields.reset();
                                this.Components.SaveBtn.reset();
                                this.Components.Feedback.hide();
                                this.Components.OtherOptions.hide();
                            },
                            show: function () {
                                this.initialize();
                                this.el().style.display = 'block';
                            },
                            startEditing: function (apiEntityName, entityId, refreshFunc) {
                                if (
                                    typeof this.apis[apiEntityName] == 'undefined'
                                ) {
                                    return;
                                }

                                var xhr = new XMLHttpRequest();
                                xhr.withCredentials = true;

                                const $this = this;

                                window.App.Components.PA.Components.LoadingOverlay.show();
                                xhr.addEventListener("readystatechange", function () {
                                    if (this.readyState === 4) {

                                        if (this.status === 401) {
                                            window.location.reload();
                                        }

                                        if (this.status === 200) {
                                            window.App.Components.PA.Components.LoadingOverlay.hide();
                                            const resObj = JSON.parse(this.response);

                                            $this.Components.Title.set(resObj.form_title);
                                            $this.Components.SaveBtn.setSaveEndpoint(resObj.save_endpoint);
                                            $this.Components.SaveBtn.setRefreshListFunction(refreshFunc);

                                            if (
                                                typeof resObj.remove_endpoint !== 'undefined'
                                                &&
                                                typeof resObj.remove_button_text !== 'undefined'
                                            ) {
                                                $this.Components.OtherOptions.Components.RemoveBtn.setRemoveEndpoint(resObj.remove_endpoint);
                                                $this.Components.OtherOptions.Components.RemoveBtn.setEntityId(resObj.entity_id);
                                                $this.Components.OtherOptions.Components.RemoveBtn.setRefreshListFunction(refreshFunc);
                                                $this.Components.OtherOptions.Components.RemoveBtn.setButtonText(resObj.remove_button_text);
                                                $this.Components.OtherOptions.Components.RemoveBtn.setConfirmationText(resObj.remove_button_confirmation_text);
                                                $this.Components.OtherOptions.Components.RemoveBtn.show();
                                                $this.Components.OtherOptions.show();
                                            } else {
                                                $this.Components.OtherOptions.Components.RemoveBtn.hide();
                                            }

                                            $this.Components.Fields.reset();
                                            for(var i = 0; i < resObj.form_fields.length; i++) {
                                                $this.Components.Fields.add(
                                                    resObj.form_fields[i]
                                                );
                                            }

                                            $this.show();
                                            window.location.href = "#dashboard-edit";
                                        }

                                    }
                                });

                                var urlStr = this.apis[apiEntityName];

                                if (entityId != null) {
                                    urlStr += '?id=' + entityId;
                                }

                                xhr.open(
                                    "POST",
                                    urlStr
                                );
                                xhr.setRequestHeader("Authorization", "Bearer " + window._authManager.at);
                                xhr.send();
                            },
                            Components: {
                                Title: {
                                    el: function () {
                                        return document.getElementById('dashboard-edit-title');
                                    },
                                    set: function (title) {
                                        this.el().innerText = title;
                                    },
                                    clear: function () {
                                        this.el().innerText = '';
                                    }
                                },
                                Fields: {
                                    el: function () {
                                        return document.getElementById('dashboard-edit-fields');
                                    },
                                    reset: function () {
                                        this.el().innerHTML = '';
                                    },
                                    add: function (fieldDataArr) {
                                        const fieldContainer = document.createElement('div');
                                        fieldContainer.classList.add('input-container');

                                        const labelEl = document.createElement('div');
                                        labelEl.innerText = fieldDataArr['label'];

                                        fieldContainer.appendChild(labelEl);

                                        var inputEl = document.createElement(
                                            fieldDataArr['element_type']
                                        );

                                        if (
                                            typeof fieldDataArr['element_attributes'] !== 'undefined'
                                        ) {

                                            const attrNames = Object.keys(fieldDataArr['element_attributes']);
                                            for(var ii = 0; ii < attrNames.length; ii++) {
                                                const attrName = attrNames[ii];
                                                const attrValue = fieldDataArr['element_attributes'][attrName];
                                                inputEl.setAttribute(attrName, attrValue);
                                            }
                                        }

                                        if (
                                            fieldDataArr['element_type'] === 'select'
                                            &&
                                            Array.isArray(fieldDataArr['options'])
                                            &&
                                            fieldDataArr['options'].length > 0
                                        ) {
                                            for(var oi = 0; oi < fieldDataArr['options'].length; oi++) {
                                                const optionEl = document.createElement('option');
                                                optionEl.value = fieldDataArr['options'][oi].value;
                                                optionEl.innerText = fieldDataArr['options'][oi].text;

                                                if (
                                                    typeof fieldDataArr['element_attributes'] !== 'undefined'
                                                    &&
                                                    typeof fieldDataArr['element_attributes']['value'] !== 'undefined'
                                                    &&
                                                    String(fieldDataArr['element_attributes']['value']) === String(optionEl.value)
                                                ) {
                                                    optionEl.setAttribute('selected', 'selected');
                                                }
                                                inputEl.appendChild(optionEl);
                                            }
                                        }

                                        fieldContainer.appendChild(inputEl);
                                        this.el().appendChild(fieldContainer);
                                    }
                                },
                                Feedback: {
                                    el: function () {
                                        return document.getElementById('dashboard-edit-feedback');
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
                                        el.classList.remove('success');
                                        el.classList.add('info');
                                        el.style.display = 'block';
                                    },
                                    showError: function(message) {
                                        const el = this.el();
                                        el.innerText = message;
                                        el.classList.remove('info');
                                        el.classList.remove('success');
                                        el.classList.add('error');
                                        el.style.display = 'block';
                                    },
                                    showSuccess: function(message) {
                                        const el = this.el();
                                        el.innerText = message;
                                        el.classList.remove('info');
                                        el.classList.remove('error');
                                        el.classList.add('success');
                                        el.style.display = 'block';
                                    },
                                },
                                SaveBtn: {
                                    hasInitialized: false,
                                    saveEndpoint: null,
                                    refreshListFunction: null,
                                    setSaveEndpoint: function(endpoint) {
                                        this.saveEndpoint = endpoint;
                                    },
                                    setRefreshListFunction: function(func) {
                                        this.refreshListFunction = func;
                                    },
                                    reset: function () {
                                        this.saveEndpoint = null;
                                        this.refreshListFunction = null;
                                    },
                                    el: function () {
                                        return document.getElementById('dashboard-edit-save');
                                    },
                                    initialize: function () {
                                        if ( this.hasInitialized === false ) {
                                            const $this = this;
                                            this.el().onclick = function (e) {
                                                const fieldsContainerEl = window.App.Components.PA.Components.Edit.Components.Fields.el();
                                                const namedElements = fieldsContainerEl.querySelectorAll('[name]');

                                                var urlParams = '?';
                                                for(var i = 0; i < namedElements.length; i++) {
                                                    const inputVal = namedElements[i].value;
                                                    const paramName = namedElements[i].getAttribute('name');

                                                    var shouldAddParam = true;

                                                    if (
                                                        namedElements[i].nodeName.toLowerCase() === 'input'
                                                        &&
                                                        namedElements[i].getAttribute('type') === 'checkbox'
                                                        &&
                                                        !namedElements[i].checked
                                                    ) {
                                                        shouldAddParam = false;
                                                    }

                                                    if ( shouldAddParam ) {
                                                        urlParams += paramName + '=' + inputVal;

                                                        if (i+1 < namedElements.length) {
                                                            urlParams += '&';
                                                        }
                                                    }

                                                }

                                                var xhr = new XMLHttpRequest();
                                                xhr.withCredentials = true;

                                                window.App.Components.PA.Components.LoadingOverlay.show();
                                                xhr.addEventListener("readystatechange", function () {
                                                    if (this.readyState === 4) {
                                                        if (this.status === 401) {
                                                            window.location.reload();
                                                        }

                                                        window.App.Components.PA.Components.LoadingOverlay.hide();
                                                        const resObj = JSON.parse(this.response);

                                                        if (
                                                            this.status === 200
                                                            ||
                                                            this.status === 201
                                                        ) {
                                                            window.App.Components.PA.Components.Edit.Components.Feedback.showSuccess(
                                                                resObj.message
                                                            );
                                                            if (
                                                                typeof $this.refreshListFunction === 'function'
                                                            ) {
                                                                $this.refreshListFunction(resObj);
                                                            }
                                                            return;
                                                        }

                                                        if (
                                                            typeof resObj.message !== 'undefined'
                                                        ) {
                                                            window.App.Components.PA.Components.Edit.Components.Feedback.showError(
                                                                resObj.message
                                                            );
                                                        }

                                                        const namedElements = fieldsContainerEl.querySelectorAll('[name]');
                                                        var fieldsWithError = [];

                                                        if (
                                                            typeof resObj.errors !== 'undefined'
                                                        ) {
                                                            fieldsWithError = Object.keys(resObj.errors);
                                                        }

                                                        for(var i = 0; i < namedElements.length; i++) {
                                                            const elName = namedElements[i].getAttribute('name');
                                                            var hasError = false;
                                                            for(var ii = 0; ii < fieldsWithError.length; ii++) {
                                                                if (elName === fieldsWithError[ii]) {
                                                                    namedElements[i].classList.add('has-error');
                                                                    hasError = true;
                                                                    break;
                                                                }
                                                            }
                                                            if (hasError) {
                                                                continue;
                                                            }
                                                            namedElements[i].classList.remove('has-error');
                                                        }

                                                    }
                                                });

                                                xhr.open(
                                                    "POST",
                                                    $this.saveEndpoint + urlParams
                                                );
                                                xhr.setRequestHeader("Authorization", "Bearer " + window._authManager.at);
                                                xhr.send();
                                            };
                                            this.hasInitialized = true;
                                        }
                                    }
                                },
                                CancelBtn: {
                                    hasInitialized: false,
                                    el: function () {
                                        return document.getElementById('dashboard-edit-cancel');
                                    },
                                    initialize: function () {
                                        if ( this.hasInitialized === false ) {
                                            this.el().onclick = function (e) {
                                                window.App.Components.PA.Components.Edit.hide();
                                            };
                                            this.hasInitialized = true;
                                        }
                                    }
                                },
                                OtherOptions: {
                                    el: function () {
                                        return document.getElementById('dashboard-edit-other-options');
                                    },
                                    show: function () {
                                        this.initialize();
                                        this.el().style.display = 'block';
                                    },
                                    hide: function () {
                                        this.el().style.display = 'none';
                                        const componentNames = Object.keys(this.Components);
                                        for(var i = 0; i < componentNames.length; i++) {
                                            const componentName = componentNames[i];

                                            if (
                                                typeof this.Components[componentName].hide === 'function'
                                            ) {
                                                this.Components[componentName].hide();
                                            }
                                        }
                                    },
                                    Components: {
                                        RemoveBtn: {
                                            hasInitialized: false,
                                            removeEndpoint: null,
                                            refreshListFunction: null,
                                            el: function () {
                                                return document.getElementById('dashboard-edit-remove');
                                            },
                                            confirmationEl: function () {
                                                return document.getElementById('dashboard-edit-remove-confirmation');
                                            },
                                            confirmationMessageEl: function () {
                                                return document.getElementById('dashboard-edit-remove-confirmation-message');
                                            },
                                            confirmButtonEl: function () {
                                                return document.getElementById('dashboard-edit-remove-confirm');
                                            },
                                            cancelButtonEl: function () {
                                                return document.getElementById('dashboard-edit-remove-cancel');
                                            },
                                            feedbackTextEl: function () {
                                                return document.getElementById('dashboard-edit-remove-feedback');
                                            },
                                            getRemoveEndpoint: function() {
                                                return this.removeEndpoint;
                                            },
                                            setRemoveEndpoint: function(endpoint) {
                                                this.removeEndpoint = endpoint;
                                            },
                                            setRefreshListFunction: function(func) {
                                                this.refreshListFunction = func;
                                            },
                                            show: function () {
                                                this.el().style.display = 'block';
                                                this.hideConfirmation();
                                            },
                                            hide: function () {
                                                this.el().style.display = 'none';
                                                this.reset();
                                            },
                                            showConfirmation: function () {
                                                this.confirmationEl().style.display = 'block';
                                                this.confirmButtonEl().style.display = 'inline-block';
                                                this.cancelButtonEl().style.display = 'inline-block';
                                                this.hideFeedback();
                                            },
                                            hideConfirmation: function () {
                                                this.confirmationEl().style.display = 'none';
                                                this.confirmButtonEl().style.display = 'none';
                                                this.cancelButtonEl().style.display = 'none';
                                                this.hideFeedback();
                                            },
                                            setButtonText: function(text) {
                                                this.el().innerText = text;
                                            },
                                            setConfirmationText: function (confirmationText) {
                                                this.confirmationMessageEl().innerHTML = confirmationText;
                                            },
                                            showFeedback: function (feedbackText) {
                                                const feedbackEl = this.feedbackTextEl();
                                                feedbackEl.innerText = feedbackText;
                                                feedbackEl.style.display = 'block';
                                                feedbackEl.style.color = '#333333';
                                            },
                                            showFeedbackError: function (feedbackText) {
                                                const feedbackEl = this.feedbackTextEl();
                                                feedbackEl.innerText = feedbackText;
                                                feedbackEl.style.display = 'block';
                                                feedbackEl.style.color = 'red';
                                            },
                                            hideFeedback: function () {
                                                this.feedbackTextEl().innerText = '';
                                                this.feedbackTextEl().style.display = 'none';
                                            },
                                            reset: function () {
                                                this.removeEndpoint = null;
                                                this.refreshListFunction = null;
                                                this.setButtonText('');
                                                this.setConfirmationText('');
                                                this.setEntityId('');
                                                this.hideConfirmation();
                                            },
                                            getEntityId: function () {
                                                return this.confirmButtonEl().getAttribute('data-entity-id');
                                            },
                                            setEntityId: function (entityId) {
                                                return this.confirmButtonEl().setAttribute('data-entity-id', entityId);
                                            },
                                            initialize: function () {
                                                if ( this.hasInitialized === false ) {
                                                    const $this = this;
                                                    this.el().onclick = function (e) {
                                                        $this.showConfirmation();
                                                    };

                                                    this.cancelButtonEl().onclick = function (e) {
                                                        $this.hideConfirmation();
                                                    };

                                                    this.confirmButtonEl().onclick = function (e) {
                                                        const removeEndpoint = $this.getRemoveEndpoint();
                                                        const entityId = $this.getEntityId();

                                                        $this.confirmButtonEl().style.display = 'none';
                                                        $this.cancelButtonEl().style.display = 'none';
                                                        $this.showFeedback('A remover..por favor espere..');

                                                        var xhr = new XMLHttpRequest();
                                                        xhr.withCredentials = true;

                                                        xhr.addEventListener("readystatechange", function () {
                                                            if (this.readyState === 4) {
                                                                if (this.status === 401) {
                                                                    window.location.reload();
                                                                }

                                                                if (this.status === 200) {
                                                                    if (
                                                                        typeof $this.refreshListFunction === 'function'
                                                                    ) {
                                                                        $this.refreshListFunction();
                                                                    }
                                                                    window.App.Components.PA.Components.Edit.hide();
                                                                    return;
                                                                }

                                                                const resObj = JSON.parse(this.response);
                                                                if (
                                                                    typeof resObj.message !== 'undefined'
                                                                ) {
                                                                    $this.showFeedbackError(resObj.message);
                                                                    return;
                                                                }

                                                                $this.showFeedbackError('Algo correu mal.. não foi possível remover..');
                                                            }
                                                        });

                                                        var urlStr = removeEndpoint + '?id=' + entityId;

                                                        xhr.open(
                                                            "POST",
                                                            urlStr
                                                        );
                                                        xhr.setRequestHeader("Authorization", "Bearer " + window._authManager.at);
                                                        xhr.send();
                                                    };

                                                    this.hasInitialized = true;
                                                }
                                            }
                                        },
                                    },
                                    initialize: function () {
                                        this.Components.RemoveBtn.initialize();
                                    }
                                },
                            },
                            initialize: function () {
                                this.Components.SaveBtn.initialize();
                                this.Components.CancelBtn.initialize();
                            }
                        },
                        ResultsTable: {
                            dashboardItem: false,
                            el: function () {
                                return document.getElementById('dashboard-results-container');
                            },
                            columnsEl:function () {
                                return document.getElementById('dashboard-results-container-columns');
                            },
                            rowsEl:function () {
                                return document.getElementById('dashboard-results-container-rows');
                            },
                            paginationEl: function () {
                                return document.getElementById('dashboard-results-pagination');
                            },
                            hide: function () {
                                this.el().style.display = 'none';
                                this.paginationEl().innerHTML = '';

                                window.App.Helpers.Pagination.resetAllPaginations();
                            },
                            show: function () {
                                this.el().style.display = 'table';
                            },
                            setColumns: function (columns) {
                                this.columnsEl().innerHTML = '';
                                for(var i = 0; i < columns.length; i++) {
                                    const columnEl = document.createElement('th');
                                    columnEl.innerText = columns[i];
                                    this.columnsEl().appendChild(columnEl);
                                }
                            },
                            setRows: function (rows, editConfig = null, rowClickEvent = null) {
                                this.rowsEl().innerHTML = '';
                                const rowCols = Object.keys(rows[0]);
                                for(var i = 0; i < rows.length; i++) {
                                    const row = document.createElement('tr');
                                    for(var ii = 0; ii < rowCols.length; ii++) {
                                        const colName = rowCols[ii];
                                        const colEl = document.createElement('td');
                                        colEl.innerText = rows[i][colName];

                                        if (
                                            editConfig != null
                                            &&
                                            editConfig['primary_key_column'] === colName
                                        ) {
                                            row.setAttribute('id', 'row-' + rows[i][colName]);
                                            row.setAttribute('data-row-id', rows[i][colName]);
                                        }
                                        row.appendChild(colEl);
                                    }
                                    if (rowClickEvent != null) {
                                        row.onclick = function (e) {
                                            rowClickEvent(e);
                                        }
                                    }
                                    this.rowsEl().appendChild(row);
                                }
                            }
                        },
                        Stats: {
                            dashboardItem: true,
                            hasInitialized: false,
                            api: "{{ url('/api/stats') }}",
                            el: function () {
                                return document.getElementById('pa-stats');
                            },
                            hide: function () {
                                this.el().style.display = 'none';
                            },
                            show: function () {
                                this.initialize();

                                if (window.App.isMobileSize()) {
                                    this.el().style.display = 'block';
                                } else {
                                    this.el().style.display = 'inline-block';
                                }

                                this.Components.ViewsList.hide();
                            },
                            displayFull: function () {
                                this.el().style.display = 'block';
                            },
                            Components: {
                                ViewsList: {
                                    hasInitialized: false,
                                    currentView: null,
                                    views: [
                                        {
                                            name: 'totalRegisteredUsers',
                                            label: 'Total de utilizadores registrados'
                                        },
                                        {
                                            name: 'totalShortlinksGenerated',
                                            label: 'Total de shortlinks gerados'
                                        },
                                        {
                                            name: 'totalTrafficReceivedInShortlinks',
                                            label: 'Total de tráfego recebido em shortlinks'
                                        },
                                        {
                                            name: 'totalUserLogins',
                                            label: 'Total de inícios de sessão'
                                        },
                                        {
                                            name: 'appUsageByDevices',
                                            label: 'Dispositivos usados pelos utilizadores da plataforma'
                                        },
                                        {
                                            name: 'appUsageByAction',
                                            label: 'Total de Ações / Usagem geral da plataforma'
                                        },
                                    ],
                                    el: function () {
                                        return document.getElementById('pa-stats-views');
                                    },
                                    hide: function () {
                                        this.el().style.display = 'none';
                                    },
                                    show: function () {
                                        this.initialize();

                                        window.App.Components.PA.hideAllDashboardItems(['Stats']);
                                        window.App.Components.PA.Components.Filters.hide();
                                        window.App.Components.PA.Components.BackButton.show();
                                        window.App.Components.PA.Components.Stats.displayFull();
                                        window.App.Components.PA.Components.Loading.hide();
                                        window.App.Components.PA.Components.NoResults.hide();
                                        window.App.Components.PA.Components.ResultsTable.hide();
                                        this.showStatsViews();

                                        this.el().style.display = 'block';
                                    },
                                    fetchViewResults: function (pageNumber = 1) {
                                        window.App.Components.PA.Components.ResultsTable.hide();
                                        window.App.Components.PA.Components.NoResults.hide();
                                        window.App.Components.PA.Components.Loading.show();
                                        this.hideStatsViews([this.currentView]);
                                        window.App.Components.PA.Components.BackButton.customBackFunc = function () {
                                            window.App.Components.PA.Components.Stats.Components.ViewsList.show();
                                        };

                                        var xhr = new XMLHttpRequest();
                                        xhr.withCredentials = true;

                                        xhr.addEventListener("readystatechange", function () {
                                            if (this.readyState === 4) {
                                                if (this.status === 401) {
                                                    window.location.reload();
                                                }

                                                if (this.status === 200) {
                                                    const resObj = JSON.parse(this.response);
                                                    window.App.Components.PA.Components.Loading.hide();
                                                    window.App.Components.PA.Components.Filters.show();
                                                    window.App.Components.PA.Components.Filters.Components.DateSince.show(resObj.since);
                                                    window.App.Components.PA.Components.Filters.Components.DateUntil.show(resObj.until);
                                                    window.App.Components.PA.Components.Filters.Components.GroupBy.show(resObj.availableGroupBys, resObj.groupBy);
                                                    window.App.Components.PA.Components.Filters.Components.OrderBy.show(resObj.availableOrderBys, resObj.orderBy);
                                                    window.App.Components.PA.Components.Filters.Components.RefreshResultsBtn.show(
                                                        function () {
                                                            window.App.Components.
                                                                PA.Components.
                                                                Stats.Components.
                                                                ViewsList.fetchViewResults();
                                                        }
                                                    );


                                                    if (resObj.search_results.data.length === 0) {
                                                        window.App.Components.PA.Components.NoResults.show();
                                                        return;
                                                    }
                                                    window.App.Components.PA.Components.NoResults.hide();
                                                    window.App.Components.PA.Components.ResultsTable.setColumns(
                                                        Object.keys(resObj.search_results.data[0])
                                                    );
                                                    window.App.Components.PA.Components.ResultsTable.setRows(
                                                        resObj.search_results.data
                                                    );

                                                    if (resObj.search_results.last_page > 1 && resObj.search_results.data.length > 1) {
                                                        const paginationEl = window.App.Components.PA.Components.ResultsTable.paginationEl();
                                                        paginationEl.innerHTML = '';
                                                        paginationEl.appendChild(
                                                            window.App.Helpers.Pagination.createEl(
                                                                resObj.search_results.current_page,
                                                                resObj.search_results.last_page,
                                                                'DashboardResultList',
                                                                function(param) {
                                                                    window.App.Components.PA.Components.Stats.Components.ViewsList.fetchViewResults(param);
                                                                }
                                                            )
                                                        );
                                                    }


                                                    window.App.Components.PA.Components.ResultsTable.show();
                                                }

                                            }
                                        });

                                        var urlStr = window.App.Components.PA.Components.Stats.api + '?currentView=' + this.currentView + '&page=' + pageNumber;

                                        const groupBy = window.App.Components.PA.Components.Filters.Components.GroupBy.inputEl().value;
                                        const orderBy = window.App.Components.PA.Components.Filters.Components.OrderBy.inputEl().value;
                                        const since = window.App.Components.PA.Components.Filters.Components.DateSince.inputEl().value;
                                        const until = window.App.Components.PA.Components.Filters.Components.DateUntil.inputEl().value;

                                        if (
                                            groupBy.length > 0
                                            ||
                                            orderBy.length > 0
                                            ||
                                            since.length > 0
                                            ||
                                            until.length > 0
                                        ) {
                                            var params = [];

                                            if (orderBy.length > 0) {
                                                params['orderBy'] = orderBy;
                                            }

                                            if (groupBy.length > 0) {
                                                params['groupBy'] = groupBy;
                                            }

                                            if (since.length > 0) {
                                                params['since'] = since;
                                            }

                                            if (until.length > 0) {
                                                params['until'] = until;
                                            }

                                            var paramNames = Object.keys(params);

                                            for(var i = 0; i < paramNames.length; i++) {
                                                const paramName = paramNames[i];
                                                urlStr += '&' + paramName + '=' + params[paramName];
                                            }
                                        }

                                        xhr.open(
                                            "POST",
                                            urlStr
                                        );
                                        xhr.setRequestHeader("Authorization", "Bearer " + window._authManager.at);
                                        xhr.send();
                                    },
                                    renderStatsViews: function () {
                                        const $this = this;
                                        for(var i = 0; i < this.views.length; i++) {
                                            const view = this.views[i];
                                            const dashboardListItem = document.createElement('div');
                                            dashboardListItem.classList.add('dashboard-list-item');
                                            dashboardListItem.innerText = view.label;
                                            dashboardListItem.style.display = 'none';
                                            dashboardListItem.setAttribute('data-view-name', view.name);
                                            dashboardListItem.setAttribute('id', 'pa-stats-view-' + view.name);
                                            dashboardListItem.onclick = function (e) {
                                                $this.currentView = e.target.getAttribute('data-view-name');

                                                // default search , reset filters
                                                window.App.Components.PA.Components.Filters.Components.GroupBy.inputEl().value = '';
                                                window.App.Components.PA.Components.Filters.Components.OrderBy.inputEl().value = '';
                                                window.App.Components.PA.Components.Filters.Components.DateSince.inputEl().value = '';
                                                window.App.Components.PA.Components.Filters.Components.DateUntil.inputEl().value = '';

                                                $this.fetchViewResults();
                                            };

                                            this.el().appendChild(dashboardListItem);
                                        }
                                    },
                                    hideStatsViews: function (except = []) {
                                        for(var i = 0; i < this.views.length; i++) {
                                            const view = this.views[i];
                                            const viewEl = document.getElementById('pa-stats-view-' + view.name);
                                            if (except.indexOf(view.name) >= 0) {
                                                continue;
                                            }
                                            viewEl.style.display = 'none';
                                        }
                                    },
                                    showStatsViews: function (except = []) {
                                        for(var i = 0; i < this.views.length; i++) {
                                            const view = this.views[i];
                                            const viewEl = document.getElementById('pa-stats-view-' + view.name);
                                            if (except.indexOf(view.name) >= 0) {
                                                continue;
                                            }
                                            viewEl.style.display = 'block';
                                        }
                                    },
                                    initialize: function () {
                                        if (this.hasInitialized === false) {
                                            this.renderStatsViews();
                                            this.hasInitialized = true;
                                        }
                                    }
                                },
                            },
                            initialize: function () {
                                if ( this.hasInitialized == false ) {
                                    const $this = this;
                                    this.el().onclick = function (e) {
                                        $this.Components.ViewsList.show();
                                    };

                                    this.hasInitialized = true;
                                }
                            }
                        },
                        PermissionGroups: {
                            dashboardItem: true,
                            hasInitialized: false,
                            el: function () {
                                return document.getElementById('pa-permission-groups');
                            },
                            hide: function () {
                                this.el().style.display = 'none';
                            },
                            show: function () {
                                this.initialize();

                                if (window.App.isMobileSize()) {
                                    this.el().style.display = 'block';
                                } else {
                                    this.el().style.display = 'inline-block';
                                }
                            },
                            displayFull: function () {
                                this.el().style.display = 'block';
                            },
                            Components: {
                                PermissionGroupsList: {
                                    api: "{{ url('/api/permission-groups') }}",
                                    fetchPermissionGroups: function (pageNumber = 1) {
                                        window.App.Components.PA.Components.ResultsTable.hide();
                                        window.App.Components.PA.Components.NoResults.hide();
                                        window.App.Components.PA.Components.Loading.show();

                                        var xhr = new XMLHttpRequest();
                                        xhr.withCredentials = true;

                                        xhr.addEventListener("readystatechange", function () {
                                            if (this.readyState === 4) {
                                                if (this.status === 401) {
                                                    window.location.reload();
                                                }

                                                if (this.status === 200) {
                                                    const resObj = JSON.parse(this.response);
                                                    window.App.Components.PA.Components.Loading.hide();

                                                    if (resObj.search_results.data.length === 0) {
                                                        window.App.Components.PA.Components.NoResults.show();
                                                        return;
                                                    }
                                                    window.App.Components.PA.Components.NoResults.hide();
                                                    window.App.Components.PA.Components.ResultsTable.setColumns(
                                                        Object.keys(resObj.search_results.data[0])
                                                    );
                                                    window.App.Components.PA.Components.ResultsTable.setRows(
                                                        resObj.search_results.data,
                                                        resObj.edit_config,
                                                        function (e) {
                                                            window.App.Components.PA.Components.Edit.startEditing(
                                                                'PermissionGroups',
                                                                e.target.parentElement.getAttribute('data-row-id'),
                                                                function () {
                                                                    window.App.Components.PA.Components.PermissionGroups.Components.PermissionGroupsList.fetchPermissionGroups(
                                                                        window.App.Helpers.Pagination.getCurrentPage(
                                                                            resObj.pagination_identifier
                                                                        )
                                                                    );
                                                                }
                                                            );
                                                        }
                                                    );

                                                    if (resObj.search_results.last_page > 1 && resObj.search_results.data.length > 1) {
                                                        const paginationEl = window.App.Components.PA.Components.ResultsTable.paginationEl();
                                                        paginationEl.innerHTML = '';
                                                        paginationEl.appendChild(
                                                            window.App.Helpers.Pagination.createEl(
                                                                resObj.search_results.current_page,
                                                                resObj.search_results.last_page,
                                                                resObj.pagination_identifier,
                                                                function(param) {
                                                                    window.App.Components.PA.Components.PermissionGroups.Components.PermissionGroupsList.fetchPermissionGroups(param);
                                                                }
                                                            )
                                                        );
                                                    }


                                                    window.App.Components.PA.Components.ResultsTable.show();
                                                }


                                            }
                                        });

                                        var urlStr = this.api + '?page=' + pageNumber;

                                        xhr.open(
                                            "POST",
                                            urlStr
                                        );
                                        xhr.setRequestHeader("Authorization", "Bearer " + window._authManager.at);
                                        xhr.send();
                                    },
                                },
                            },
                            initialize: function () {
                                if ( this.hasInitialized == false ) {
                                    const $this = this;
                                    this.el().onclick = function (e) {
                                        window.App.Components.PA.hideAllDashboardItems(['PermissionGroups']);
                                        $this.displayFull();
                                        window.App.Components.PA.Components.BackButton.show();
                                        window.App.Components.PA.Components.OptionButtons.show();
                                        window.App.Components.PA.Components.OptionButtons.Components.CreateNewBtn.show(
                                            function () {
                                                window.App.Components.PA.Components.Edit.startEditing(
                                                    'PermissionGroups',
                                                    null,
                                                    function (resObj) {
                                                        window.App.Components.PA.Components.PermissionGroups.Components.PermissionGroupsList.fetchPermissionGroups(
                                                            window.App.Helpers.Pagination.getCurrentPage(
                                                                resObj.pagination_identifier
                                                            )
                                                        );
                                                    }
                                                );
                                            }
                                        );
                                        $this.Components.PermissionGroupsList.fetchPermissionGroups();
                                    };

                                    this.hasInitialized = true;
                                }
                            }
                        },
                        Users: {
                            dashboardItem: true,
                            hasInitialized: false,
                            el: function () {
                                return document.getElementById('pa-users');
                            },
                            hide: function () {
                                this.el().style.display = 'none';
                            },
                            show: function () {
                                this.initialize();

                                if (window.App.isMobileSize()) {
                                    this.el().style.display = 'block';
                                } else {
                                    this.el().style.display = 'inline-block';
                                }
                            },
                            displayFull: function () {
                                this.el().style.display = 'block';
                            },
                            Components: {
                                UsersList: {
                                    api: "{{ url('/api/users') }}",
                                    fetchUsersList: function (pageNumber = 1) {
                                        window.App.Components.PA.Components.ResultsTable.hide();
                                        window.App.Components.PA.Components.NoResults.hide();
                                        window.App.Components.PA.Components.Loading.show();

                                        var xhr = new XMLHttpRequest();
                                        xhr.withCredentials = true;

                                        xhr.addEventListener("readystatechange", function () {
                                            if (this.readyState === 4) {
                                                if (this.status === 401) {
                                                    window.location.reload();
                                                }

                                                if (this.status === 200) {
                                                    const resObj = JSON.parse(this.response);
                                                    window.App.Components.PA.Components.Loading.hide();

                                                    if (resObj.search_results.data.length === 0) {
                                                        window.App.Components.PA.Components.NoResults.show();
                                                        return;
                                                    }
                                                    window.App.Components.PA.Components.NoResults.hide();
                                                    window.App.Components.PA.Components.ResultsTable.setColumns(
                                                        Object.keys(resObj.search_results.data[0])
                                                    );
                                                    window.App.Components.PA.Components.ResultsTable.setRows(
                                                        resObj.search_results.data,
                                                        resObj.edit_config,
                                                        function (e) {
                                                            window.App.Components.PA.Components.Edit.startEditing(
                                                                'Users',
                                                                e.target.parentElement.getAttribute('data-row-id'),
                                                                function () {
                                                                    window.App.Components.PA.Components.Users.Components.UsersList.fetchUsersList(
                                                                        window.App.Helpers.Pagination.getCurrentPage(
                                                                            resObj.pagination_identifier
                                                                        )
                                                                    );
                                                                }
                                                            );
                                                        }
                                                    );

                                                    if (resObj.search_results.total > resObj.search_results.per_page) {
                                                        const paginationEl = window.App.Components.PA.Components.ResultsTable.paginationEl();
                                                        paginationEl.innerHTML = '';
                                                        paginationEl.appendChild(
                                                            window.App.Helpers.Pagination.createEl(
                                                                resObj.search_results.current_page,
                                                                resObj.search_results.last_page,
                                                                resObj.pagination_identifier,
                                                                function(param) {
                                                                    window.App.Components.PA.Components.Users.Components.UsersList.fetchUsersList(param);
                                                                }
                                                            )
                                                        );
                                                    }


                                                    window.App.Components.PA.Components.ResultsTable.show();
                                                }

                                            }
                                        });

                                        var urlStr = this.api + '?page=' + pageNumber;

                                        var freeSearchValue = window.App.Components.PA.Components.Filters.Components.FreeSearch.getValue();

                                        urlStr += '&free-search=' + encodeURIComponent(freeSearchValue);

                                        xhr.open(
                                            "POST",
                                            urlStr
                                        );
                                        xhr.setRequestHeader("Authorization", "Bearer " + window._authManager.at);
                                        xhr.send();
                                    },
                                },
                            },
                            initialize: function () {
                                if ( this.hasInitialized == false ) {
                                    const $this = this;
                                    this.el().onclick = function (e) {
                                        window.App.Components.PA.hideAllDashboardItems(['Users']);
                                        $this.displayFull();
                                        window.App.Components.PA.Components.BackButton.show();

                                        window.App.Components.PA.Components.Filters.show();
                                        window.App.Components.PA.Components.Filters.Components.FreeSearch.show();
                                        window.App.Components.PA.Components.Filters.Components.FreeSearch.setRefreshFunction(
                                            function () {
                                                window.App.Components.
                                                    PA.Components.
                                                    Users.Components.
                                                    UsersList.fetchUsersList();
                                            }
                                        );

                                        $this.Components.UsersList.fetchUsersList();
                                    };

                                    this.hasInitialized = true;
                                }
                            }
                        },
                    },
                    initialize: function () {
                        this.Components.CloseBtn.initialize();
                    }

                };

                window.App.Views.PA = {
                    components: {
                        initiallyVisible:  ['LogoTop', 'MenuToggleMobile', 'MenuTop', 'MenuAccTop', 'ShortenUrl', 'PA'],
                        initiallyHidden: ['ShortlinkResult'],
                        sticky: ['LogoTop', 'MenuTop', 'MenuAccTop', 'ShortenUrl', 'ShortlinkResult']
                    },
                    show: function () {
                        window.App.currentView = 'PA';
                        window.App.hideNonStickyComponents();
                        window.App.hideComponents(this.components.initiallyHidden);
                        window.App.showComponents(this.components.initiallyVisible);
                    },
                    hide: function () {
                        window.App.hideComponents(this.components.initiallyVisible);
                        window.App.hideComponents(this.components.initiallyHidden);
                    }
                };
            </script>
        @endif

        @if (isset($passwordRecoveryToken))
            <script>
                window._authManager.passwordRecoveryToken = '{{ $passwordRecoveryToken }}';
            </script>
        @endif

        @if(isset($enableCaptcha) && $enableCaptcha === true)
            <script src="https://www.google.com/recaptcha/api.js?render={{ $captchaSitekey }}"></script>
        @endif
    </head>
    <body>
        <div id="logo-top-container" style="display: none">
            <img
                id="logo-top"
                src="{{ $logoTop }}"
            />
            <img id="logo-top-mobile"
                src="{{ $logoTopMobile }}"
            >
            <div id="logo-top-mobile-desc">url shortner<br/>em português</div>
        </div>

        <div id="mobile-menu-toggle">
            <div class="menu-bar"></div>
            <div class="menu-bar"></div>
            <div class="menu-bar"></div>
        </div>

        <div id="menu-mobile" style="display: none">
            <div class="close-btn" id="close-menu-mobile"><div class="x">x</div>Fechar menu</div>
            <div class="menu-group-title">Menu Principal</div>
            <div class="menu-item" id="menu-mobile-item-my-links">Os meus links</div>
            <div class="menu-item" id="menu-mobile-item-contact-us">Contacte</div>
            <div class="menu-group-title">Conta</div>
            <div id="menu-mobile-acc-items-guest" style="display: none">
                <div class="menu-item" id="menu-mobile-acc-login">Entrar</div>
                <div class="menu-item" id="menu-mobile-acc-register">Criar</div>
            </div>
            <div id="menu-mobile-acc-items-user" style="display: none">
                <div class="menu-item" id="menu-mobile-user-name" style="display: none"></div>
                <div class="menu-item" id="menu-mobile-admin-dashboard" style="display: none">Painel de Administração</div>
                <div class="menu-item" id="menu-mobile-acc-logout">Terminar sessão</div>
            </div>
        </div>

        <div id="menu-top" style="display: none">
            <div class="menu-item" id="menu-item-my-links">Os meus links</div>
            <div class="menu-item" id="menu-item-contact-us">Contacte</div>
        </div>

        <div id="menu-top-acc" style="display: none">
            <div class="settings-icon">
                <img src="{{ asset('/img/acc-settings.png') }}" />
            </div>
            <div class="profile-pic" id="user-profile-pic">?</div>
        </div>
        <div id="menu-acc-items" style="display: none">
            <div id="menu-acc-items-guest" style="display: none">
                <div class="menu-item" id="menu-top-acc-login">Entrar</div>
                <div class="menu-item" id="menu-top-acc-register">Criar conta</div>
            </div>
            <div id="menu-acc-items-user" style="display: none">
                <div class="menu-item" id="menu-top-user-name" style="display: none"></div>
                <div class="menu-item" id="menu-top-admin-dashboard" style="display: none">Painel de Administração</div>
                <div class="menu-item" id="menu-top-acc-logout">Terminar sessão</div>
            </div>
        </div>

        <div id="current-presentation">
            <div id="cp-title">
                web<br/>into<br/>link
            </div>
            <div id="cp-desc">
                url shortner<br/>em português
            </div>
        </div>

        <div
            class="form-box overlay"
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

            <div class="external-logins">
                @if(isset($enableLoginWithGoogleBtn) && $enableLoginWithGoogleBtn === true)
                    <div class="button light-blue disabled" id="login-with-google-button"><img src="{{ asset('img/google-logo.png') }}" width="30">Entrar com o Google</div>
                @endif
                @if(isset($enableLoginWithFacebookBtn) && $enableLoginWithFacebookBtn === true)
                    <div class="button blue disabled" id="login-with-facebook-button"><img src="{{ asset('img/facebook-logo.png') }}" width="26"/>Entrar com o Facebook</div>
                @endif
                @if(isset($enableLoginWithTwitterBtn) && $enableLoginWithTwitterBtn === true)
                    <div class="button sky-blue disabled" id="login-with-twitter-button"><img src="{{ asset('img/twitter-logo.png') }}" width="26"/>Entrar com o Twitter</div>
                @endif
                @if(isset($enableLoginWithLinkedinBtn) && $enableLoginWithLinkedinBtn === true)
                    <div class="button darker-blue disabled" id="login-with-linkedin-button"><img src="{{ asset('img/linkedin-logo.png') }}" width="26">Entrar com o LinkedIn</div>
                @endif
                @if(isset($enableLoginWithGithubBtn) && $enableLoginWithGithubBtn === true)
                    <div class="button dark disabled" id="login-with-github-button"><img src="{{ asset('img/github-logo.png') }}" width="30"/>Entrar com o Github</div>
                @endif
            </div>
        </div>


        <div
            class="form-box overlay"
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
            class="form-box overlay"
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

        <div class="form-box overlay" id="password-has-changed-view" style="display: none">
            <div class="form-box-title">Palavra-passe alterada!</div>
            <p>
                Agora já pode entrar na sua conta com a sua nova palavra-passe!
            </p>
            <div class="button" id="changed-password-login">Entrar</div>
        </div>

        <div
            class="form-box overlay"
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
            class="form-box overlay"
            id="form-box-account-registered" style="display: none"
        >
            <div class="form-box-title">Conta criada com successo!</div>
            <p>Verifique a sua caixa de correio pois acabamos de lhe enviar um email para que possa ativar a sua conta.<br/><br/>Muito obrigado.</p>
        </div>

        <div
            class="form-box overlay"
            id="my-links-view" style="display: none"
        >
            <div class="form-box-title" id="my-links-view-title">Os meus links</div>
            <div class="gray-note" id="my-links-guest-msg" style="display: none">Atenção, estás a utilizar a plataforma como convidado.<br>Entra na tua conta, ou cria uma, para poderes editar os teus links e guarda-los permanentemente.</div>
            <div class="close-form-box" id="my-links-view-close-btn">X</div>

            <div id="my-links-list-options" class="my-links-list-options" style="display: none">
                <div class="my-links-order" id="my-links-order" style="display: none">
                    <span id="my-links-selected-order"></span>
                    <div id="my-links-order-arrow" class="arrow-down"></div>
                    <div id="my-links-order-list" class="my-links-order-list" style="display: none"></div>
                </div>
            </div>

            <div id="form-box-acc-links-loading">A carregar links..</div>
            <div id="form-box-acc-links-not-found" style="display: none">Ainda não gerou nenhum link..</div>
            <div id="form-box-acc-links" class="list-container" style="display: none"></div>
        </div>


        <div
            class="form-box overlay"
            id="contact-us-view" style="display: none"
        >
            <div class="form-box-title">Contacte</div>
            <div class="close-form-box" id="contact-us-view-close-btn">X</div>
            <div id="contact-form-fields">

                <div class="input-container">
                    <div class="input-label" id="contact-name-label">
                        Nome
                    </div>
                    <input type="text" id="contact-name" maxlength="255"/>
                </div>

                <div class="input-container">
                    <div class="input-label" id="contact-email-label">
                        Email
                    </div>
                    <input type="email" id="contact-email" maxlength="255"/>
                </div>

                <div class="input-container">
                    <div class="input-label" id="contact-phone-label">
                        Telefone
                    </div>
                    <input type="text" id="contact-phone" maxlength="50"/>
                </div>

                <div class="input-container">
                    <div class="input-label" id="contact-subject-label">
                        Assunto
                    </div>
                    <input type="text" id="contact-subject" maxlength="255"/>
                </div>

                <div class="input-container">
                    <div class="input-label" id="contact-message-label">
                        Mensagem
                    </div>
                    <textarea id="contact-message"></textarea>
                </div>

                <div id="form-box-contact-us-feedback" class="form-box-feedback" style="display: none"></div>

                <div class="button disabled" id="contact-us-btn">Enviar</div>

            </div>
        </div>

        <div
            class="form-box overlay"
            id="profile-view" style="display: none"
        >
            <div class="form-box-title" id="profile-view-user-name"></div>
            <div class="close-form-box" id="profile-view-close-btn">X</div>
            <div class="profile-pic-container" id="profile-pic-container" style="display: none">
                <img class="profile-pic-big" id="profile-pic-big"/>
            </div>
            <div class="button blue" id="view-profile-permissions">Ver permissões</div>
            <div class="profile-permissions" id="profile-permissions" style="display: none">
                <b>As minhas permissões:</b>
                <ul id="profile-view-permission-list">
                    <li data-permission-needed="is_admin" style="color: green">
                        Administrador
                    </li>
                    <li data-permission-needed="send_shortlink_by_email_when_generating">
                        Enviar links por email ao gerar
                    </li>
                    <li data-permission-needed="edit_shortlinks_destination_url">
                        Editar URL destino dos links gerados
                    </li>
                    <li data-permission-needed="view_shortlinks_url_history">
                        Ver histórico de URLs destino dos links editados
                    </li>
                    <li data-permission-needed="view_shortlinks_total_views">
                        Ver nº de visualizações dos links
                    </li>
                    <li data-permission-needed="view_shortlinks_total_unique_views">
                        Ver nº de visualizações únicas dos links
                    </li>
                    <li data-permission-needed="create_custom_shortlinks">
                        Criar links personalizados
                    </li>
                    <li data-permission-needed="create_shortlinks_with_length_1">
                        Criar links com 1 caractere <br/><small>(depois da barra)</small>
                    </li>
                    <li data-permission-needed="create_shortlinks_with_length_2">
                        Criar links com 2 caracteres <br/>(depois da barra)
                    </li>
                    <li data-permission-needed="create_shortlinks_with_length_3">
                        Criar links com 3 caracteres <br/>(depois da barra)
                    </li>
                    <li data-permission-needed="create_shortlinks_with_length_4">
                        Criar links com 4 caracteres <br/>(depois da barra)
                    </li>
                </ul>
            </div>

            <div class="button red" style="margin-top: 10px" id="view-profile-limits">Ver limites</div>
            <div class="profile-permissions" id="profile-limits" style="display: none">
                <b>Os meus limites:</b>
                <ul id="profile-view-limit-list">
                    <li>
                        Links com 5 ou mais caracteres<br><small>(depois da barra)</small>:
                        <table class="limit-table">
                            <thead>
                                <tr>
                                    <th>Total</th>
                                    <th>Por Dia</th>
                                    <th>Por Mês</th>
                                    <th>Por Ano</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td data-limit-name="max_shortlinks_with_5_or_more_of_length"></td>
                                    <td data-limit-name="max_shortlinks_per_day_with_5_or_more_of_length"></td>
                                    <td data-limit-name="max_shortlinks_per_month_with_5_or_more_of_length"></td>
                                    <td data-limit-name="max_shortlinks_per_year_with_5_or_more_of_length"></td>
                                </tr>
                            </tbody>
                        </table>
                    </li>

                    <li data-permission-needed="create_shortlinks_with_length_4">
                        Links com 4 caracteres<br><small>(depois da barra)</small>:
                        <table class="limit-table">
                            <thead>
                                <tr>
                                    <th>Total</th>
                                    <th>Por Dia</th>
                                    <th>Por Mês</th>
                                    <th>Por Ano</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td data-limit-name="max_shortlinks_with_length_4"></td>
                                    <td data-limit-name="max_shortlinks_per_day_with_length_4"></td>
                                    <td data-limit-name="max_shortlinks_per_month_with_length_4"></td>
                                    <td data-limit-name="max_shortlinks_per_year_with_length_4"></td>
                                </tr>
                            </tbody>
                        </table>
                    </li>

                    <li data-permission-needed="create_shortlinks_with_length_3">
                        Links com 3 caracteres<br><small>(depois da barra)</small>:
                        <table class="limit-table">
                            <thead>
                                <tr>
                                    <th>Total</th>
                                    <th>Por Dia</th>
                                    <th>Por Mês</th>
                                    <th>Por Ano</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td data-limit-name="max_shortlinks_with_length_3"></td>
                                    <td data-limit-name="max_shortlinks_per_day_with_length_3"></td>
                                    <td data-limit-name="max_shortlinks_per_month_with_length_3"></td>
                                    <td data-limit-name="max_shortlinks_per_year_with_length_3"></td>
                                </tr>
                            </tbody>
                        </table>
                    </li>

                    <li data-permission-needed="create_shortlinks_with_length_2">
                        Links com 2 caracteres<br><small>(depois da barra)</small>:
                        <table class="limit-table">
                            <thead>
                                <tr>
                                    <th>Total</th>
                                    <th>Por Dia</th>
                                    <th>Por Mês</th>
                                    <th>Por Ano</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td data-limit-name="max_shortlinks_with_length_2"></td>
                                    <td data-limit-name="max_shortlinks_per_day_with_length_2"></td>
                                    <td data-limit-name="max_shortlinks_per_month_with_length_2"></td>
                                    <td data-limit-name="max_shortlinks_per_year_with_length_2"></td>
                                </tr>
                            </tbody>
                        </table>
                    </li>

                    <li data-permission-needed="create_shortlinks_with_length_1">
                        Links com 1 caractere<br><small>(depois da barra)</small>:
                        <table class="limit-table">
                            <thead>
                                <tr>
                                    <th>Total</th>
                                    <th>Por Dia</th>
                                    <th>Por Mês</th>
                                    <th>Por Ano</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td data-limit-name="max_shortlinks_with_length_1"></td>
                                    <td data-limit-name="max_shortlinks_per_day_with_length_1"></td>
                                    <td data-limit-name="max_shortlinks_per_month_with_length_1"></td>
                                    <td data-limit-name="max_shortlinks_per_year_with_length_1"></td>
                                </tr>
                            </tbody>
                        </table>
                    </li>


                </ul>
            </div>
        </div>


        @if ($isAdmin)
            <div
                class="form-box overlay"
                id="pa-view" style="display: none"
            >
                <div id="pa-view-loading" class="loading-overlay" style="display: none"></div>
                <div class="form-box-title">Painel de Administração</div>
                <div class="close-form-box" id="pa-view-close-btn">X</div>
                <div class="dashboard-item-container">
                    <div class="dashboard-item" id="pa-stats" style="display: none">
                        <div class="dashboard-item-img"><img src="{{ asset('/img/stats-icon.png') }}"></div>
                        <div class="dashboard-item-name">Estátisticas</div>
                    </div>
                    <div class="dashboard-item" id="pa-permission-groups" style="display: none">
                        <div class="dashboard-item-img"><img src="{{ asset('/img/permission-groups-icon.png') }}"></div>
                        <div class="dashboard-item-name">Grupos de Permissões</div>
                    </div>
                    <div class="dashboard-item" id="pa-users" style="display: none">
                        <div class="dashboard-item-img"><img src="{{ asset('/img/users-icon.png') }}"></div>
                        <div class="dashboard-item-name">Lista de Utilizadores</div>
                    </div>
                </div>
                <div class="dashboard-back-button" id="dashboard-back-button" style="display: none">Voltar</div>

                <div id="dashboard-edit" style="display: none">
                    <div id="dashboard-edit-title"></div>
                    <div id="dashboard-edit-fields"></div>
                    <div id="dashboard-edit-feedback" class="form-box-feedback" style="display: none; margin-bottom: 8px;"></div>
                    <div>
                        <div id="dashboard-edit-save" class="button">Guardar</div>
                        <div id="dashboard-edit-cancel" class="button red">Fechar</div>
                    </div>
                    <div id="dashboard-edit-other-options" class="other-options" style="display: none">
                        <a href="javascript:void(0);" id="dashboard-edit-remove" style="display: none"></a>
                        <div id="dashboard-edit-remove-confirmation" style="display: none">
                            <p id="dashboard-edit-remove-confirmation-message"></p>
                            <div id="dashboard-edit-remove-confirm" class="button">Sim</div>
                            <div id="dashboard-edit-remove-cancel" class="button red">Não</div>
                            <div id="dashboard-edit-remove-feedback" style="display: none"></div>
                        </div>
                    </div>
                </div>

                <div class="option-buttons-container" id="option-buttons" style="display: none">
                    <div id="create-new" class="button inline" style="display: none">Criar novo</div>
                </div>

                <div class="dashboard-list-items-container" id="pa-stats-views" style="margin-top: 14px; display: none">
                </div>

                <div id="dashboard-filter-container" class="dashboard-filter-container" style="display: none">
                    <div class="input-container" style="display: none;" id="dashboard-filter-date-since">
                        Desde:<br>
                        <input type="date" id="dashboard-filter-date-since-input">
                    </div>

                    <div class="input-container" style="display: none;" id="dashboard-filter-date-until">
                        Até:<br>
                        <input type="date" id="dashboard-filter-date-until-input">
                    </div>

                    <div class="input-container" style="display: none;" id="dashboard-filter-view">
                        Vista:<br>
                        <select id="dashboard-filter-view-input"></select>
                    </div>

                    <div class="input-container" style="display: none;" id="dashboard-filter-order">
                        Ordenação:<br>
                        <select id="dashboard-filter-order-input"></select>
                    </div>

                    <div class="input-container" id="free-text-search-container" style="display: none; margin-right: 0;">
                        <div class="input-label" id="free-text-search-label">
                            Pesquisar..
                        </div>
                        <input type="text" id="free-text-search"/>
                    </div>

                    <div class="button btn-color-hover-only" id="refresh-dashboard-results" style="display: none">Atualizar resultados</div>
                </div>

                <div id="dashboard-loading" style="display: none">A carregar...</div>
                <div id="dashboard-no-results" style="display: none">Sem nenhum resultado para mostrar..</div>
                <div id="dashboard-results-table-container">
                    <table id="dashboard-results-container" class="dashboard-results-container" style="display: none">
                        <thead>
                            <tr id="dashboard-results-container-columns">
                            </tr>
                        </thead>
                        <tbody id="dashboard-results-container-rows">
                        </tbody>
                    </table>
                </div>
                <div id="dashboard-results-pagination"></div>
            </div>
        @endif

        <div
            class="form-box overlay"
            id="email-confirmed" style="display: none"
        >
            <div class="form-box-title">Email confirmado!</div>
            <p>A sua conta agora está activa.</p>
            <div class="close-form-box" id="email-confirmed-close-btn">X</div>
            <div class="button" id="email-confirmed-login">Entrar na minha conta</div>
        </div>

        <div class="form-box" id="shorten-urls" style="display: none">
            <div class="input-container">
                <div class="input-label" id="long-url-label">
                    Cole aqui o seu URL loongo..
                </div>
                <input type="text" id="long-url" />
            </div>

            <div class="input-container" style="display: none" id="destination-email-container">
                <div class="input-label" id="destination-email-label">
                    Email destino
                </div>
                <input type="text" id="destination-email" />
            </div>

            <div class="button disabled" id="generate-shortlink">Gerar Link Curto!</div>
            <div id="form-box-feedback" class="form-box-feedback" style="display: none"></div>
            <a href="javascript:void(0);" id="create-personalized-link" class="form-link" style="display: none">Criar link personalizado</a>
            <a href="javascript:void(0);" id="generate-shorter-shortlinks" style="display: none" class="form-link">Escolher tamanho personalizado</a>
            <div class="personalized-shortstring-length" id="choose-shortstring-length" style="display: none">

                <div class="radio-input-container" id="generate_with_length_4_container">
                    <input type="radio" id="generate_with_length_4" name="shortstring_length" value="4">
                    <label for="generate_with_length_4">4 caracteres <small>(depois da barra)</small></label>
                </div>

                <div class="radio-input-container" id="generate_with_length_3_container">
                    <input type="radio" id="generate_with_length_3" name="shortstring_length" value="3">
                    <label for="generate_with_length_3">3 caracteres <small>(depois da barra)</small></label>
                </div>

                <div class="radio-input-container" id="generate_with_length_2_container">
                    <input type="radio" id="generate_with_length_2" name="shortstring_length" value="2">
                    <label for="generate_with_length_2">2 caracteres <small>(depois da barra)</small></label>
                </div>

                <div class="radio-input-container" id="generate_with_length_1_container">
                    <input type="radio" id="generate_with_length_1" name="shortstring_length" value="1">
                    <label for="generate_with_length_1">1 caractere <small>(depois da barra)</small></label>
                </div>

                <a href="javascript:void(0);" class="form-link" id="cancel-and-generate-normally">Cancelar / gerar link normalmente</a>
            </div>
        </div>

        <div
            class="form-box"
            id="form-box-with-shortlink"
            style="display: none"
        >
            <div class="form-box-title">O seu link está pronto!</div>
            <div class="input-container">
                <input type="text" id="shortlink" readonly />
            </div>

            <div class="button" id="save-shortlink" style="display: none">Guardar na minha lista de links</div>
            <div class="button" id="go-to-my-links" style="display: none">Ver minha lista de links</div>
            <a href="javascript:void(0);" id="generate-another-shortlink" class="form-link">Criar outro link</a>
        </div>

        <div
            class="form-box"
            id="form-box-shortlink-requested"
            style="display: none"
        >
            <div class="form-box-title">
                Criar link personalizado
            </div>
            <div class="close-form-box" id="custom-shortlink-close">X</div>


            <div class="input-container">
                <input type="text" id="requested-shortstring" value="{{ isset($shortstring) ? $shortstring : '' }}"/>
                <div>
                    <small>
                        {{$domain}}/<span id="custom-url-shortstring">{{ isset($shortstring) ? $shortstring : '' }}</span>
                    </small>
                    @if (isset($shortlink_available) && $shortlink_available === true)
                        <div style="color: green;" id="custom-url-shortstring-feedback">Link disponível!</div>
                    @endif
                </div>
            </div>

            <div>Para onde quer apontar este link?</div>
            <div class="input-container">
                <input type="text" id="requested-shortlink-long-url"/>
            </div>

            <div id="requested-shortlink-register-feedback" class="form-box-feedback" style="display: none"></div>

            <div class="button disabled" id="shortlink-register">Continuar</div>
        </div>

        @if(isset($view))
            <script>
                window.App.currentView = '{{ $view }}';
            </script>
        @endif

        @if (
            isset($authToken)
            &&
            isset($isLoggedIn)
            &&
            isset($userPermissions)
            &&
            isset($userData)
        )
            <script>
                window._authManager.at = '{{ $authToken }}';
                window._authManager.isLoggedIn = {{ $isLoggedIn }};
                window._authManager.isAuthenticated = true;
                window._authManager.userPermissions = {!! $userPermissions !!};
                window._authManager.userData = {!! $userData !!};
            </script>
        @endif


        <script>
            if (
                typeof window.App.currentView !== 'undefined'
                &&
                typeof window.App.Views[window.App.currentView] !== 'undefined'
                &&
                typeof window.App.Views[window.App.currentView].show === 'function'
            ) {
                window.App.Views[window.App.currentView].show();
            }
        </script>

        <div class="cookie-notice-bar" id="cookie-notice-bar" style="display: none">
            Nós usamos cookies para lhe proporcionar uma melhor experiência online.
            <div class="close-cookie-notice">Ok</div>
        </div>

        <script>
            function setCookie(name,value,days) {
                var expires = "";
                if (days) {
                    var date = new Date();
                    date.setTime(date.getTime() + (days*24*60*60*1000));
                    expires = "; expires=" + date.toUTCString();
                }
                document.cookie = name + "=" + (value || "")  + expires + "; path=/";
            }

            function getCookie(name) {
                var nameEQ = name + "=";
                var ca = document.cookie.split(';');
                for(var i=0;i < ca.length;i++) {
                    var c = ca[i];
                    while (c.charAt(0)==' ') c = c.substring(1,c.length);
                    if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
                }
                return null;
            }

            const cookieNoticeBar = document.getElementById('cookie-notice-bar');

            if (!getCookie('user-has-been-informed-about-cookies')) {
                cookieNoticeBar.style.display = 'block';
            }

            cookieNoticeBar.onclick = function (e) {
                cookieNoticeBar.style.display = 'none';
                setCookie('user-has-been-informed-about-cookies', true);
            };
        </script>


        @if (
            isset($authToken)
            &&
            isset($isLoggedIn)
            &&
            isset($userPermissions)
            &&
            isset($userData)
        )
            <script>
                document.dispatchEvent(
                                window._authManager.customEvents
                                    .userAuthenticatedEvent
                            );
            </script>
         @else
            <script>
                window._authManager.authenticate();
            </script>
        @endif
    </body>
</html>
