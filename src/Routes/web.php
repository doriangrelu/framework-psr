<?php
/**
 * Created by PhpStorm.
 * User: Dorian
 * Date: 01/05/2018
 * Time: 15:29
 */

use App\Controllers\BaseController;
use App\Framework\Facades\Route;

Route::get("/", [BaseController::class, "index"], "base");