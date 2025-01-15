<?php

declare(strict_types=1);

namespace BasePackage\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


class BasePackageController extends Controller
{
    public function index(Request $request)
    {
        return response(['status' => true, 'message' => 'Package Installed Successfully']);
    }
}
