<?php

namespace App\Controllers;

use DB;
use App\Helpers\GumroadHelper;
use Exception;

class AppController extends Controller
{
    /**
     * Create/Update App
     *
     * @param string $id
     *
     * @return JsonResponse
     */
    public function create($id)
    {
        if (!$this->isUserAdmin()) {
            return $this->respondJson(
                'Access Denied',
                [],
                403
            );
        }

        try {
            $product = GumroadHelper::product($id);
        } catch (Exception $e) {
            return $this->respondJson(
                'Gumroad ID not found',
                [],
                400
            );
        }

        if (!DB::has('apps', ['id' => $id])) {
            DB::create(
                'apps',
                [
                    'id' => $id,
                    'name' => $product->name,
                    'url' => "https://{$product->name}"
                ]
            );

            return $this->respondJson(
                'App Created',
                ['reload' => true]
            );
        }

        DB::update('apps', [
            'name' => $product->name,
            'url' => "https://{$product->name}"
        ], ['id' => $id]);

        return $this->respondJson(
            'App Updated',
            ['reload' => true]
        );
    }

    /**
     * Toggle acitve state
     *
     * @param string $id
     * 
     * @return JsonResponse
     */
    public function toggleActive($id)
    {
        if (!$this->isUserAdmin()) {
            return $this->respondJson(
                'Access Denied',
                [],
                403
            );
        }

        $app = DB::get('apps', ['active'], ['id' => $id]);

        if (!$app) {
            return $this->respondJson(
                'User not found',
                [],
                400
            );
        }

        DB::update('apps', ['active' => !$app['active']], ['id' => $id]);

        return $this->respondJson(
            'App Toggled',
            ['reload' => true]
        );
    }

    /**
     * Delete App
     *
     * @param string $id
     *
     * @return JsonResponse
     */
    public function delete($id)
    {
        if (!$this->isUserAdmin()) {
            return $this->respondJson(
                'Access Denied',
                [],
                403
            );
        }

        DB::delete('apps', ['id' => $id,]);

        return $this->respondJson(
            'App Deleted',
            ['reload' => true]
        );
    }
}
