# Installation

## Initial application data
When first installing the project we need to seed some data into the database.

So we must run:
- php artisan db:seed

and to generate the available shortlinks,
we must run:

SHORTSTRINGS_LENGTH=3 php artisan db:seed --class=ShortstringSeeder

other examples (using/set multiple env variables/settings):
SHORTSTRING_SEED_METHOD=recursive SHORTSTRINGS_LENGTH=5 TOTAL_SHORTSTRINGS_TO_SEED=1 php artisan db:seed --class=ShortstringSeeder

Where the SHORTSTRINGS_LENGTH value is the length of the strings to be generated.

## Post install application data seeding

We can also run that same command for generating more shortlinks as needed.
If we initially ran with the SHORTSTRINGS_LENGTH with the value of 3,
when running out of shortlinks ( before we run out ) we should probably run it with the value of 4.

SHORTSTRINGS_LENGTH=4 php artisan db:seed --class=ShortstringSeeder
