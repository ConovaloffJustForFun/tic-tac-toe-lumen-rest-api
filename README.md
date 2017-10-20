Install
============

    git clone https://github.com/ConovaloffJustForFun/tic-tac-toe-lumen-rest-api
    cd tic-tac-toe-lumen-rest-api/
    
    # change settings for database
    cp .env.example .env
    vi .env
    
    # get composer and install dependency
    curl -sS https://getcomposer.org/installer | php
    ./composer.phar install
    
    # init database
    php artisan migrate
    
    # if you dont have apache or nginx:
    php -S 0.0.0.0:8000 -t public

TMP
============
Probably you can use test server fot view how this api work:

    85.143.219.32:8003/


API
============

    # Create game
    curl -d '' 85.143.219.32:8003/api/table/
    
    # make a move
    curl -d 'x=0&y=0' 85.143.219.32:8003/api/table/333 # where 333 is your table id 
    
    # get table status
    curl 85.143.219.32:8003/api/table/333 # where 333 is your table id
