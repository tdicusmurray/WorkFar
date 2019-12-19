# WorkFar
 Start your own job site, remote staffing site.

WorkFar has been tested and used for over 7 years with zero successful hacking attempts.

We recommend you use a DigitalOcean cloud server for $5/month using the Ubuntu 18.04 server.

You can spin up a LAMP stack image from their selection to make the process faster.

Once you have a cloud server up and running, connect using winscp and upload all of the files from this github repository within /var/www/ the apache webserver http root.

Now apt-get install phpmyadmin, or use the command line sql tool to create the database from the sql file within github (Upload coming soon)

Now navigate to controller/controller.php and change the variables for database name, user, and password to match your credentials.

Now you can open jobsitethatsyours.com in the browser, and you will see the site fully functioning without the video conferencing enabled.
'
Future tutorial on implementing video chat in the browser upon award of the $50 feature request found on teddymurray.com/feature_request
