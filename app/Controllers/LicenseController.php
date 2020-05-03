<?php

namespace App\Controllers;

use DB;
use Exception;
use App\Helpers\SessionHelper;
use App\Helpers\GumroadHelper;
use App\Validators\LicenseValidator;
use Zend\Diactoros\ServerRequest;

class LicenseController extends Controller
{
    /**
     * Open popup to buy license
     *
     * @param string $id
     * @param string $offer_code
     * 
     * @return HtmlResponse
     */
    public function popup($id, $offer_code)
    {
        return $this->respond('license/popup.twig', [
            'id' => $id,
            'offer_code' => $offer_code
        ]);
    }

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
            'user_id' => SessionHelper::get('id')
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
            $gumroad = GumroadHelper::license($id, $license);
        } catch (Exception $e) {
            return $this->respondJson(
                'License Invalid',
                [],
                400
            );
        }

        DB::create('licenses', [
            'app_id' => $id,
            'user_id' => SessionHelper::get('id'),
            'variant' => str_replace(array('(', ')'), '', $gumroad->variants),
            'license' => $license
        ]);

        return $this->respondJson(
            'License Added',
            ['reload' => true]
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
            'License Removed',
            ['reload' => true]
        );
    }
}
