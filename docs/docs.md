1. make a service that will initialize the instagram graph api endpoints. (refer to InstagramApi.php file in the project)
  - then register this service by using the silverstripe injection class, please refer to the _config.php file.

2. make a yml file that will handle the method for the instagram app login and for the instagram access token, please refer to the routes.yml file.

3. then make the 2 controllers named LogInToInstagram and SocialMediaAuthController
  - please refer to the files LogInToInstagram.php and SocialMediaAuthController.php in the project
  - set the redirect uri 'https://yourdomain.com/social-media-auth/instagram-redirect' into your instagram app found in your https://developers.facebook.com/apps/ 
  - LogInToInstagram.php will run the instagram app login to get a code that will be use to get your app access token
    which will be processed in SocialMediaAuthController.php file because the redirect uri function is inside here (the instagram function, please refer to the file).

4. make an extension of the Silvestripe SiteConfig class, (refer to https://docs.silverstripe.org/en/4/developer_guides/extending/extensions/)
  - declare 2 fields in this extension, InstagramToken with Text datatype, and TokenExpires with Varchar datatype (please refer to the SiteConfigExtension.php file)
  - once created, run dev/build and go to the admin Settings menu and run the Instagram app login in the Instagram tab to get the first access token that allows 
    your website to get the instagram feeds to your instagram account
  - in this extension you will see the functions on getting/caching your instagram feeds.

5. to render in the page, call the function '$SiteConfig.getInstagramPosts' into your template and loop it for every records fetched.
  - returned fields are 'ID', 'Username', 'Caption', 'Link', 'Image', 'Timestamp'

6. for refreshing your access token, create a silverstripe build task job, (please refer to file InstagramCacheTask.php)
  - in this task it will refresh the access token set ing the SiteConfig extension only if current token is older than 24 hours but younger than 60 days.
  - you can run this in the cron job on the frequency you desire