# Google reCAPTCHA v3

Use use reCAPTCHA v3 for bot protection.
https://developers.google.com/recaptcha/docs/v3

reCAPTCHA v3 returns a score for each request without user friction. The score is based on interactions with your site and enables you to take an appropriate action for your site. Register reCAPTCHA v3 keys on the reCAPTCHA Admin console. 

Get your token here:
https://www.google.com/recaptcha/admin/create

and update these variables located in the .env file:

ENABLE_NOCAPTCHA=true
NOCAPTCHA_SITEKEY=yourSiteKey
NOCAPTCHA_SECRET=yourSecret


# Login with Github

To enable the Login with Github button,
you must create a new oAuth application in Developer settings:
https://github.com/settings/developers

Use this call back url:
https://<website domain>/auth/github/callback

After that, get your Client ID,
and generate your Client SECRET
and then update the following env variables in the .env file:

ENABLE_LOGIN_WITH_GITHUB=true
GITHUB_CLIENT_ID=yourClientId
GITHUB_CLIENT_SECRET=yourClientSecret


# Login with Facebook

To enable the Login with Facebook button,
you need a Facebook for Developers account,

you can create it here:
https://developers.facebook.com/async/registration/

after creating the developer acc, or login in into one,
you can open the following link to Create a new app:
https://developers.facebook.com/apps/create/

after creating the new App,

Add the following product to your app:

"Facebook login" ( click button Setup where you find this )

after you've added the "Facebook login" to your app,
go to "Facebook login" settings,

and setup the "Valid OAuth Redirect URIs"
to have the following redirect url:

https://<website domain>/auth/facebook/callback


Your also need to go to Your APP "Basic settings"
and setup:
- App domains with the production full url ( https://<your-domain.com>)
- Privacy Policy URL
- Terms of Service URL

You also have to go to -> App Review -> Permissions and Features
and Request Advanced Access for: email, public_profile

After that,  set your application from Development to "LIVE" mode.

Get the App ID and App secret

and setup the following env variables in the .env file:

ENABLE_LOGIN_WITH_FACEBOOK=true
FACEBOOK_CLIENT_ID=412647327691226
FACEBOOK_CLIENT_SECRET=148fcbc4b646cbb3d57d7b16da75feef


# Login with Google

Guide:
https://developers.google.com/identity/gsi/web/guides/get-google-api-clientid

You must go to the Google APIs Console ( https://console.developers.google.com/apis )

then go to "Credentials",
then "Create new project" ( if not yet created ),
then in "Credentials" click "Create credentials" -> oAuth
then you need to "Configure the OAuth consent screen"
select User type: External,

then configure the app details / info,
and make sure to add the app domain in "Authorized Domains",

then in the next steps select at least the following Scopes for the app:

.../auth/userinfo.email	See your primary Google Account email address
.../auth/userinfo.profile	See your personal info, including any personal info you've made publicly available
openid	Associate you with your personal info on Google


after this the OAuth consent screen is configured...go back to "Credentials" again,
then in "Credentials" click "Create credentials" -> oAuth,
select the App type ( web app ),

make sure to add in the "Authorized redirect URIs" the following URL:
https://<your-domain>/auth/google/callback

then click Create / or enter in the keyboard,
and in the next step copy the Client ID and Secret and set it up in
the following env variables in the .env file:

ENABLE_LOGIN_WITH_GOOGLE=true
GOOGLE_CLIENT_ID=yourClientId
GOOGLE_CLIENT_SECRET=yourClientSecret

you also need to open the "OAuth Consent Screen" ( menu item below "Credentials " menu item )
and then "Publish App", some things will be required for the app to be approved and the approval might take some time.



# Login with LinkedIn

Go to https://developers.linkedin.com/
Click "Create App"
follow the steps / fill in necessary info,
then under "Products" tab

where you see "Sign In with LinkedIn" click "Request Access",
then go to the "Auth" tab, 
in the "OAuth 2.0 settings" section edit the "Authorized redirect URLs for your app",
and add the following redirect url:
https://<your-domain>/auth/linkedin/callback

then get the Client ID, and the Client Secret
and update the below env variables in the .env file:

ENABLE_LOGIN_WITH_LINKEDIN=true
LINKEDIN_CLIENT_ID=yourClientId
LINKEDIN_CLIENT_SECRET=yourClientSecret
