<?php

$route->get('/test', function () {
    return path('resources/databases/clinic/sql');
});
$route->get('/', [
    'controller' => 'Home@index',
    'name' => 'home',
]);
$route->get('/login', [
    'controller' => 'Login@index',
    'name' => 'login',
    'middleware' => 'Login',
]);
// Start Admin Section
$route->post('/patient/reply', [
    'controller' => 'Admin@reply',
    'name' => 'patient.reply',
    'middleware' => 'Admin',
]);
$route->ajax('/admin/complain/delete', [
    'controller' => 'Admin@complainDelete',
    'middleware' => 'Admin',
]);

$route->ajax('/patient/desc/:id', [
    'controller' => 'Admin@desc',
    'middleware' => 'Admin',
    'token' => false
]);
$route->ajax('/admin/user/delete/:id', [
    'controller' => 'Admin@delete',
    'middleware' => 'Admin',
]);
// End Admin Section
// Start Complain Section
$route->post('/complain/new', [
    'controller' => 'Complain@insert',
    'name' => 'complain.new',
    'middleware' => 'Complain',
]);
$route->ajax('/complain/desc/:id', [
    'controller' => 'Complain@desc',
        'token' => false
]);
$route->ajax('/complain/delete/:id', [
    'controller' => 'Complain@delete',
]);
// End Complain Section
// Start Login Section
$route->get('/logout', [
    'controller' => 'Login@logout',
    'name' => 'logout',
]);
$route->post('/signin', [
    'controller' => 'Login@signin',
    'name' => 'sign-in',
]);
$route->post('/signup', [
    'controller' => 'Login@signup',
    'name' => 'sign-up',
    'middleware' => 'Signup',
]);

// End Login Section
$route->get('/contact', [
    'controller' => 'Contact@index',
    'name' => 'contact',
    'middleware' => 'Login',
]);

// Start User Section
$route->get('/user/profile', [
    'controller' => 'User@profile',
    'name' => 'user.profile',
    'middleware' => 'User',
]);
$route->put('/user/update', [
    'controller' => 'User@update',
    'name' => 'user.update',
    'middleware' => 'AccountUpdate',
]);
$route->ajax('/user/delete/', [
    'controller' => 'User@delete',
]);
// End User Section
// Start Message Section
$route->get('/message/report/:id', [
    'controller' => 'Message@report',
    'name' => 'message.report',
]);
$route->ajax('/message/seen/:id', [
    'controller' => 'Message@seen',
    'token' => false
]);
$route->ajax('/message/delete/:id', [
    'controller' => 'Message@delete',
]);
// End Message Section
