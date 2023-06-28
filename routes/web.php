<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Home

Route::get('/', 'HomeController@show')->name('homepage');
Route::get('api/article/filter', 'HomeController@filter');

// Authentication
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::get('logout', 'Auth\LoginController@logout')->name('logout');
Route::get('signup', 'Auth\RegisterController@showRegistrationForm')->name('signup');
Route::post('signup', 'Auth\RegisterController@register');
Route::get('forgot-password', 'Auth\PasswordResetController@showSendLinkForm')->name('showLinkForm');
Route::post('forgot-password', 'Auth\PasswordResetController@sendLink')->name('sendLink');
Route::get('reset', 'Auth\PasswordResetController@showResetPasswordForm')->name('password.reset');
Route::post('reset', 'Auth\PasswordResetController@reset')->name('password.update');

// Google Auth
Route::get('login/google', 'Auth\AuthAPIController@googleRedirect')->name('googleAuth');
Route::get('login/google/callback', 'Auth\AuthAPIController@loginWithGoogle');

// User
Route::get('user/{id}', 'UserController@show')->name('userProfile')->where(['id' => '[0-9]+']);
Route::get('user/{id}/edit', 'UserController@edit')->name('editUser')->where(['id' => '[0-9]+']);
Route::put('user/{id}', 'UserController@update')->name('editProfile')->where(['id' => '[0-9]+']);
Route::delete('user/{id}', 'UserController@delete')->name('deleteUser')->where(['id' => '[0-9]+']);
Route::post('user/{id}/report', 'UserController@report')->where(['id' => '[0-9]+']);
Route::get('api/user/{id}/suspension', 'UserController@suspension')->where(['id' => '[0-9]+']);
Route::get('user/{id}/followed', 'UserController@followed')->name('followedUsers')->where(['id' => '[0-9]+']);
Route::get('api/user/{id}/articles', 'UserController@articles')->where(['id' => '[0-9]+']);
Route::post('user/{id}/follow', 'UserController@follow')->where(['id' => '[0-9]+']);
Route::post('user/{id}/unfollow', 'UserController@unfollow')->where(['id' => '[0-9]+']);

// Articles
Route::get('article', 'ArticleController@createForm')->name('createArticle');
Route::post('article', 'ArticleController@create');
Route::get('article/{id}', 'ArticleController@show')->name('article')->where(['id' => '[0-9]+']);
Route::get('article/{id}/edit', 'ArticleController@edit')->name('editArticle')->where(['id' => '[0-9]+']);
Route::put('article/{id}/edit', 'ArticleController@update')->where(['id' => '[0-9]+']);
Route::delete('article/{id}', 'ArticleController@destroy')->where(['id' => '[0-9]+']);
Route::get('api/article/{id}/comments', 'ArticleController@comments')->where(['id' => '[0-9]+']);

// Comments
Route::post('comment', 'CommentController@create');
Route::put('comment/{id}', 'CommentController@update')->where(['id' => '[0-9]+']);
Route::delete('comment/{id}', 'CommentController@destroy')->where(['id' => '[0-9]+']);

// Content
Route::delete('content/{id}', 'ContentController@removeFeedback')->where(['id' => '[0-9]+']);
Route::put('content/{id}', 'ContentController@giveFeedback')->where(['id' => '[0-9]+']);

// Admin
Route::get('admin', 'AdminController@show')->name('admin');
Route::get('admin/suspensions', 'AdminController@suspensions');
Route::post('user/{id}/suspend', 'AdminController@suspendUser')->where(['id' => '[0-9]+']);
Route::put('user/{id}/unsuspend', 'AdminController@unsuspendUser')->where(['id' => '[0-9]+']);
Route::get('admin/reports', 'AdminController@reports');
Route::get('admin/tags', 'AdminController@tags');
Route::put('admin/reports/{id}/close', 'AdminController@closeReport')->where(['id' => '[0-9]+']);

// Tag
Route::put('tags/{tag_id}/accept', 'TagController@accept')->where(['tag_id' => '[0-9]+']);
Route::put('tags/{tag_id}/reject', 'TagController@reject')->where(['tag_id' => '[0-9]+']);
Route::put('tags/{tag_id}/add_favorite', 'TagController@addUserFavorite')->where(['tag_id' => '[0-9]+']);
Route::put('tags/{tag_id}/remove_favorite', 'TagController@removeUserFavorite')->where(['tag_id' => '[0-9]+']);
Route::delete('tags/{tag_id}', 'TagController@destroy')->where(['tag_id' => '[0-9]+']);
Route::post('tags/new', 'TagController@propose');

// Search
Route::get('search', 'SearchController@show')->name('search');
Route::get('api/search/users', 'SearchController@searchUsers');
Route::get('api/search/articles', 'SearchController@searchArticles');

// Notifications
Route::get('api/notifications', 'NotificationController@show');
Route::put('notifications', 'NotificationController@readNotifications');

// Static Pages
Route::get('about', 'StaticPagesController@getAboutUs')->name('about');
Route::get('guidelines', 'StaticPagesController@getGuidelines')->name('guidelines');
Route::get('faq', 'StaticPagesController@getFaq')->name('faq');

// Share
Route::post('/api/share_socials', 'ShareController@shareWidget');
