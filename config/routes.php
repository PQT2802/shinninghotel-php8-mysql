<?php

declare(strict_types=1);

use App\Controllers\Web\HomeController;
use App\Controllers\Web\PageController;
use App\Controllers\Web\NewsController;
use App\Controllers\Web\ContactController;
use App\Controllers\Web\RoomController;
use App\Controllers\Web\BookingController;
use App\Controllers\Web\LocaleController;
use App\Controllers\Admin\AuthController;
use App\Controllers\Admin\DashboardController;
use App\Controllers\Admin\PageController as AdminPageController;
use App\Controllers\Admin\NewsController as AdminNewsController;
use App\Controllers\Admin\BannerController;
use App\Controllers\Admin\MediaController;
use App\Controllers\Admin\MenuController;
use App\Controllers\Admin\SettingController;
use App\Controllers\Admin\UserController;
use App\Controllers\Admin\ContactMessageController;
use App\Controllers\Admin\RoomController as AdminRoomController;
use App\Controllers\Admin\RoomCategoryController;
use App\Controllers\Admin\BookingController as AdminBookingController;

/** @var App\Core\Router $router */

// ——— Public WebUI ———
$router->get('/switch-locale/{targetLocale}', [LocaleController::class, 'switch']);
$router->get('/', [HomeController::class, 'index']);
$router->get('/about', [PageController::class, 'about']);
$router->get('/location', [PageController::class, 'location']);
$router->get('/page/{slug}', [PageController::class, 'show']);
$router->get('/rooms', [RoomController::class, 'index']);
$router->get('/rooms/{slug}', [RoomController::class, 'show']);
$router->get('/book', [BookingController::class, 'stepDates']);
$router->post('/book/dates', [BookingController::class, 'saveDates'], ['Csrf']);
$router->get('/book/rooms', [BookingController::class, 'stepRooms']);
$router->post('/book/room', [BookingController::class, 'saveRoom'], ['Csrf']);
$router->get('/book/guest', [BookingController::class, 'stepGuest']);
$router->post('/book/guest', [BookingController::class, 'saveGuest'], ['Csrf']);
$router->get('/book/review', [BookingController::class, 'stepReview']);
$router->post('/book/confirm', [BookingController::class, 'confirm'], ['Csrf']);
$router->get('/book/complete/{id}', [BookingController::class, 'complete']);
$router->get('/news', [NewsController::class, 'index']);
$router->get('/news/{slug}', [NewsController::class, 'show']);
$router->get('/contact', [ContactController::class, 'index']);
$router->post('/contact', [ContactController::class, 'store'], ['Csrf']);

// ——— Admin Auth ———
$router->get('/admin/login', [AuthController::class, 'login'], ['Guest']);
$router->post('/admin/login', [AuthController::class, 'authenticate'], ['Guest', 'Csrf']);
$router->post('/admin/logout', [AuthController::class, 'logout'], ['Auth', 'Csrf']);

// ——— Admin CMS ———
$router->group('/admin', ['Auth'], function ($router) {
    $router->get('', [DashboardController::class, 'index']);

    $router->get('/pages', [AdminPageController::class, 'index']);
    $router->get('/pages/create', [AdminPageController::class, 'create']);
    $router->post('/pages', [AdminPageController::class, 'store'], ['Csrf']);
    $router->get('/pages/edit/{id}', [AdminPageController::class, 'edit']);
    $router->post('/pages/update/{id}', [AdminPageController::class, 'update'], ['Csrf']);
    $router->post('/pages/delete/{id}', [AdminPageController::class, 'delete'], ['Csrf']);

    $router->get('/news', [AdminNewsController::class, 'index']);
    $router->get('/news/create', [AdminNewsController::class, 'create']);
    $router->post('/news', [AdminNewsController::class, 'store'], ['Csrf']);
    $router->get('/news/edit/{id}', [AdminNewsController::class, 'edit']);
    $router->post('/news/update/{id}', [AdminNewsController::class, 'update'], ['Csrf']);
    $router->post('/news/delete/{id}', [AdminNewsController::class, 'delete'], ['Csrf']);

    $router->get('/banners', [BannerController::class, 'index']);
    $router->get('/banners/create', [BannerController::class, 'create']);
    $router->post('/banners', [BannerController::class, 'store'], ['Csrf']);
    $router->get('/banners/edit/{id}', [BannerController::class, 'edit']);
    $router->post('/banners/update/{id}', [BannerController::class, 'update'], ['Csrf']);
    $router->post('/banners/delete/{id}', [BannerController::class, 'delete'], ['Csrf']);

    $router->get('/media', [MediaController::class, 'index']);
    $router->post('/media/upload', [MediaController::class, 'upload'], ['Csrf']);
    $router->post('/media/delete/{id}', [MediaController::class, 'delete'], ['Csrf']);

    $router->get('/menus', [MenuController::class, 'index']);
    $router->get('/menus/edit/{id}', [MenuController::class, 'edit']);
    $router->post('/menus/update/{id}', [MenuController::class, 'update'], ['Csrf']);

    $router->get('/settings', [SettingController::class, 'index']);
    $router->post('/settings', [SettingController::class, 'update'], ['Csrf']);

    $router->get('/users', [UserController::class, 'index']);
    $router->get('/users/create', [UserController::class, 'create']);
    $router->post('/users', [UserController::class, 'store'], ['Csrf']);
    $router->get('/users/edit/{id}', [UserController::class, 'edit']);
    $router->post('/users/update/{id}', [UserController::class, 'update'], ['Csrf']);
    $router->post('/users/delete/{id}', [UserController::class, 'delete'], ['Csrf']);

    $router->get('/contact-messages', [ContactMessageController::class, 'index']);
    $router->get('/contact-messages/show/{id}', [ContactMessageController::class, 'show']);
    $router->post('/contact-messages/read/{id}', [ContactMessageController::class, 'markRead'], ['Csrf']);
    $router->post('/contact-messages/unread/{id}', [ContactMessageController::class, 'markUnread'], ['Csrf']);
    $router->post('/contact-messages/delete/{id}', [ContactMessageController::class, 'delete'], ['Csrf']);

    $router->get('/room-categories', [RoomCategoryController::class, 'index']);
    $router->get('/room-categories/create', [RoomCategoryController::class, 'create']);
    $router->post('/room-categories', [RoomCategoryController::class, 'store'], ['Csrf']);
    $router->get('/room-categories/edit/{id}', [RoomCategoryController::class, 'edit']);
    $router->post('/room-categories/update/{id}', [RoomCategoryController::class, 'update'], ['Csrf']);
    $router->post('/room-categories/delete/{id}', [RoomCategoryController::class, 'delete'], ['Csrf']);

    $router->get('/rooms', [AdminRoomController::class, 'index']);
    $router->get('/rooms/create', [AdminRoomController::class, 'create']);
    $router->post('/rooms', [AdminRoomController::class, 'store'], ['Csrf']);
    $router->get('/rooms/edit/{id}', [AdminRoomController::class, 'edit']);
    $router->post('/rooms/update/{id}', [AdminRoomController::class, 'update'], ['Csrf']);
    $router->post('/rooms/delete/{id}', [AdminRoomController::class, 'delete'], ['Csrf']);
    $router->post('/rooms/toggle-status/{id}', [AdminRoomController::class, 'toggleStatus'], ['Csrf']);

    $router->get('/bookings', [AdminBookingController::class, 'index']);
    $router->get('/bookings/show/{id}', [AdminBookingController::class, 'show']);
    $router->post('/bookings/status/{id}', [AdminBookingController::class, 'updateStatus'], ['Csrf']);
});
