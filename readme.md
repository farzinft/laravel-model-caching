## Laravel Model Caching

### For Laravel 5.x.x

This is a package to cache your eloquent query results.


## Installation

Require this package in your composer.json and run composer update (or run `composer require farzinft/laravel-model-caching:dev-master` directly):

    "farzin/laravel-model-caching": "dev-master"

You need to publish the config file for this package. This will add the file `config/model-caching.php`, where you can configure this package.

    $ php artisan vendor:publish --provider="Fthi\ModelCaching\ServiceProvider"
    


## Usage

You just need add your model to model caching construct and write your eloquent queries in a normal way.
this package uses Redis for caching results;

Example:

suppose you have a customer model and you wrote a query like this:

without cache:

     "$customers = Customer::with('books')->where(['status' => 1])->get()"
     
and you want cache result 50 seconds, change cache_expire_time (in second) in model-caching.php
or inside your model define a public variable named $cacheExpireTime, then:

with cache:

    "$customers = (new ModelCaching(new Customer))->with('books')->where(['status' => 1])->get()"
    
when cache time expired results back from database.     

just tested on laravel 5.x, maybe it works on other versions, test it :)     
     




