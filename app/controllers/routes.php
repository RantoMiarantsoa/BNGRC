<?php


// Authentication / home
Flight::route('GET /', function() {
    Flight::render('home');
});
