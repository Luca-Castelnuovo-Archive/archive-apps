<?php

namespace App\Controllers;

use DB;
use Exception;
use App\Helpers\SessionHelper;
use App\Validators\LicenseValidator;
use Zend\Diactoros\ServerRequest;

class LicenseController extends Controller
{
    /**
     * Add license to user
     * 
     * @param ServerRequest $request
     *
     * @return JsonResponse
     */
    public function create(ServerRequest $request)
    {
        try {
            LicenseValidator::create($request->data);
        } catch (Exception $e) {
            return $this->respondJson(
                false,
                'Provided data was malformed',
                json_decode($e->getMessage()),
                422
            );
        }

        // check if licensed already used
        // query gumroad api
        // check if user already has license for that app // app id is extracted from license api response

        $gumroad_id = 123;

        $app_id = DB::select('apps', 'id', ['gumroad_id' => $gumroad_id]);

        if (!$app_id) {
            return $this->respondJson(
                false,
                'License invalid', // app not found
                [],
                400
            );
        }

        DB::create('licenses', [
            'app_id' => '',
            'user_id' => SessionHelper::get('id'),
            'license' => $request->data->license
        ]);

        return $this->respondJson(
            true,
            'License added',
            ['redirect' => '/user/dashboard']
        );
    }

    /**
     * Remove license from user
     *
     * @param ServerRequest $request
     * 
     * @return JsonResponse
     */
    public function remove(ServerRequest $request)
    {
        try {
            LicenseValidator::remove($request->data);
        } catch (Exception $e) {
            return $this->respondJson(
                false,
                'Provided data was malformed',
                json_decode($e->getMessage()),
                422
            );
        }

        DB::delete('licenses', [
            'license' => $request->data->license,
            'user_id' => SessionHelper::get('id')
        ]);

        return $this->respondJson(
            true,
            'License removed',
            ['redirect' => '/user/dashboard']
        );
    }
}
