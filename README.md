## How to run project

- composer install.
- copy .env.example to .env and set EXCHANGE_RATE_URL value in .env
- there is sample file in storage folder named input.csv. You can modify it or add another file and use file path command php artisan calculate:fee {filePath}    for sample file you can run php artisan calculate:fee input.csv
