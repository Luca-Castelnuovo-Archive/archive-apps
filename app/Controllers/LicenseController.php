<?php

namespace App\Controllers;

use App\Helpers\Gumroad;
use App\Validators\LicenseValidator;
use CQ\Controllers\Controller;
use CQ\DB\DB;
use CQ\Helpers\Session;
use Exception;

class LicenseController extends Controller
{
    /**
     * Open popup to buy license.
     *
     * @param string $id
     * @param string $offer_code
     *
     * @return Html
     */
    public function popup($id, $offer_code)
    {
        return $this->respond('license/popup.twig', [
            'id' => $id,
            'offer_code' => $offer_code,
        ]);
    }

    /**
     * Add license to user.
     *
     * @param object $request
     *
     * @return Json
     */
    public function create($request)
    {
        try {
            LicenseValidator::create($request->data);
        } catch (Exception $e) {
            return $this->respondJson(
                'Provided data was malformed',
                json_decode($e->getMessage()),
                422
            );
        }

        $license = $request->data->license;
        $id = $request->data->id;

        if (!DB::has('apps', ['id' => $id])) {
            return $this->respondJson(
                'License Invalid', // app not found
                [],
                400
            );
        }

        if (DB::has('licenses', [
            'app_id' => $id,
            'user_id' => Session::get('id'),
        ])) {
            return $this->respondJson(
                'App already licensed',
                [],
                400
            );
        }

        if (DB::has('licenses', ['license' => $license])) {
            return $this->respondJson(
                'License already used on another account',
                [],
                400
            );
        }

        try {
            $gumroad = Gumroad::license($id, $license);
        } catch (Exception $e) {
            return $this->respondJson(
                'License Invalid',
                [],
                400
            );
        }

        DB::create('licenses', [
            'app_id' => $id,
            'user_id' => Session::get('id'),
            'variant' => str_replace(['(', ')'], '', $gumroad->variants),
            'license' => $license,
        ]);

        return $this->respondJson(
            'License Added',
            ['reload' => true]
        );
    }

    /**
     * Remove license from user.
     *
     * @param object $request
     *
     * @return Json
     */
    public function remove($request)
    {
        try {
            LicenseValidator::remove($request->data);
        } catch (Exception $e) {
            return $this->respondJson(
                'Provided data was malformed',
                json_decode($e->getMessage()),
                422
            );
        }

        DB::delete('licenses', [
            'license' => $request->data->license,
            'user_id' => Session::get('id'),
        ]);

        return $this->respondJson(
            'License Removed',
            ['reload' => true]
        );
    }
}
