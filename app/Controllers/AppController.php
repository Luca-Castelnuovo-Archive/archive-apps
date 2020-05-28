<?php

namespace App\Controllers;

use Exception;
use CQ\DB\DB;
use CQ\Controllers\Controller;
use App\Helpers\Gumroad;

class AppController extends Controller
{
    /**
     * Create/Update App
     *
     * @param string $id
     *
     * @return Json
     */
    public function create($id)
    {
        try {
            $product = Gumroad::product($id);
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
     * @return Json
     */
    public function toggleActive($id)
    {
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
     * @return Json
     */
    public function delete($id)
    {
        DB::delete('apps', ['id' => $id]);
        DB::delete('licenses', ['app_id' => $id]);
        
        return $this->respondJson(
            'App Deleted',
            ['reload' => true]
        );
    }
}
