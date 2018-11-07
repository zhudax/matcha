<?php

$app->get('/', 'GetPages:getHome')->setName('root');
$app->get('/contact', 'GetPages:getContact')->setName('contact');
$app->get('/signin', 'GetPages:getSignin')->setName('signin');
$app->get('/signup', 'GetPages:getSignup')->setName('signup');
$app->get('/profil', 'GetPages:getProfil')->setName('profil');
$app->get('/uprofil', 'GetPages:getUprofil')->setName('uprofil');
$app->get('/logout', 'GetPages:getLogout')->setName('logout');
$app->get('/match', 'GetPages:getMatch')->setName('match');
$app->get('/forgot', 'GetPages:getForgot')->setName('forgot');
$app->get('/notifs', 'GetPages:getNotifs')->setName('notifs');
$app->get('/likes', 'GetPages:getLikes')->setName('likes');
$app->get('/view', 'GetPages:getView')->setName('view');
$app->get('/active', 'GetPages:getActive');

$app->post('/contact', 'PostPages:postContact');
$app->post('/signup', 'PostPages:postSignup');
$app->post('/signin', 'PostPages:postSignin');
$app->post('/profil', 'PostPages:postProfil');
$app->post('/uprofil', 'PostPages:postUprofil');
$app->post('/match', 'PostPages:postMatch');
$app->post('/forgot', 'PostPages:postForgot');
