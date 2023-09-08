## Make an Instagram App

1. Go to https://developers.facebook.com/apps/
2. Click Create App button
3. Select the "Other" use cases
4. Select Consumer type
5. Enter details of your app on the next page
6. In the next page click Setup on "Instagram Basic Display" product
7. In the next page click Create App (button is down below)
8. In the next page add Valid OAuth Redirect URIs with "https://<YOUR-DOMAIN-NAME>/social-media-auth/instagram-redirect"
9. Add in Deauthorize callback URL with "https://<YOUR-DOMAIN-NAME>/social-media-auth/callback"
10. Add in Data Deletion Request URL with "https://<YOUR-DOMAIN-NAME>/social-media-auth/delete"
11. Then hit Save changes, copy Instagram App ID & Secret and replace the app_id and app_secret into the app.yml or mysite.yml
12. Go to "App Settings" to add a "Website" platform
13. Then enter your website URL, and hit "Save changes"