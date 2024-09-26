
Firebreak week September 2024

Goal: Experiment with Sight and Sound data, ideal outcome would be creating a set of static web pages where data can be explored visually or could be in the format of an infographic.

Laravel 10 standard install process:

Before you import the data, copy or move the files inside /data to /storage/app/*.json

```
composer install
php artisan migrate
php artisan import:results && php artisan import:voters && php artisan import:votes
php artisan serve
```

Json data files have been created by saving data from the D20 sight and sound api and the polls-api application. Notes on this can be found in export-notes.txt.

Styles are from Tailwind cdn and some pages include jQuery and Highcharts.js via cdn.

Webpages can be exported using `php artisan export` which save static files into the /dist folder. This dist folder can then be uploaded to https://amywestlake.github.io/firebreak_week_poll_data/ hosted via github pages in the firebreak_week_poll_data repo. 

Ideas for future work: 
- Introduce data for 2012 polls for comparisons
- Introduce data from the Directors polls
- Extend queries to find correlations between voters and films (critics who voted for X also voted for Y)
- Look at vote weights (how many voters chose the film as their top choice)