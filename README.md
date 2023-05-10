## How to run project

- composer install.
- copy .env.example to .env and set EXCHANGE_RATE_URL value in .env
- There is sample file in storage folder named input.csv. You can modify it or add another file and use file path in command: php artisan calculate:fee {filePath}  for sample file you can run php artisan calculate:fee input.csv
- If you would like to test application run this command: php artisan test
