<?php

namespace App\Controllers;

use DB;
use Exception;
use App\Helpers\SessionHelper;
use App\Helpers\LicenseHelper;
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

        $license = $request->data->license;
        $gumroad_id = $request->data->gumroad_id;

        $app_id = DB::select('apps', 'id', ['gumroad_id' => $gumroad_id])[0];
        if (!$app_id) {
            return $this->respondJson(
                false,
                'License invalid', // app not found
                [],
                400
            );
        }

        if (DB::has('licenses', [
            'app_id' => $app_id,
            'user_id' => SessionHelper::get('id')
        ])) {
            return $this->respondJson(
                false,
                'App already licensed',
                [],
                400
            );
        }

        if (DB::has('licenses', ['license' => $license])) {
            return $this->respondJson(
                false,
                'License already used on another account',
                [],
                400
            );
        }

        if (!LicenseHelper::validate($gumroad_id, $license)) {
            return $this->respondJson(
                false,
                'License invalid',
                [],
                400
            );
        }

        DB::create('licenses', [
            'app_id' => $app_id,
            'user_id' => SessionHelper::get('id'),
            'license' => $license
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
