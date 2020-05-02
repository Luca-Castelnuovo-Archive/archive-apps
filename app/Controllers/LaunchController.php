<?php

namespace App\Controllers;

// use DB;
// use Exception;
// use App\Helpers\SessionHelper;
// use App\Helpers\LicenseHelper;
// use App\Validators\LicenseValidator;
// use Zend\Diactoros\ServerRequest;

class LaunchController extends Controller
{

    /**
     * Launch access to app
     *
     * @return RedirectResponse
     */
    public function launch($id)
    {
        // TODO: build launch app
        $this->redirect("https://exmaple.com/{$id}");
    }
}
