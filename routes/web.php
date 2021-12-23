<?php

use App\Http\Controllers\RosterEventsController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', [RosterEventsController::class, 'show']);
Route::get(
    '/dates/{startDate}/{endDate}',
    [RosterEventsController::class, 'getAllEventsBetweenDates']
);

Route::get(
    '/flight/next-week/{startDate?}',
    [RosterEventsController::class, 'getFlightNextWeek']
);

Route::get(
    '/flight/next-week/{startDate?}',
    [RosterEventsController::class, 'getFlightNextWeek']
);

Route::get(
    '/stand-by/next-week/{startDate?}',
    [RosterEventsController::class, 'getStandbyEventsForNextWeek']
);

Route::get(
    '/location/{location}',
    [RosterEventsController::class, 'getFlightsByLocation']
);
