<?php

namespace App\Controllers;

use DB;
use Exception;
use App\Validators\AppValidator;
use Ramsey\Uuid\Uuid;
use Zend\Diactoros\ServerRequest;

class AppController extends Controller
{
    /**
     * Create App
     *
     * @param ServerRequest $request
     *
     * @return JsonResponse
     */
    public function create(ServerRequest $request)
    {
        if (!$this->isUserAdmin()) {
            return $this->respondJson(
                'Access Denied',
                [],
                403
            );
        }

        try {
            AppValidator::create($request->data);
        } catch (Exception $e) {
            return $this->respondJson(
                'Provided data was malformed',
                json_decode($e->getMessage()),
                422
            );
        }

        if (DB::has('apps', ['gumroad_id' => $request->data->gumroad_id])) {
            return $this->respondJson(
                'Gumroad ID already used',
                [],
                400
            );
        }

        DB::create(
            'apps',
            [
                'id' => Uuid::uuid4()->toString(),
                'gumroad_id' => $request->data->gumroad_id,
                'name' => $request->data->name,
                'url' => $request->data->url
            ]
        );

        return $this->respondJson(
            'App Created',
            ['reload' => true]
        );
    }

    /**
     * Update App
     *
     * @param ServerRequest $request
     * @param string $id
     *
     * @return JsonResponse
     */
    public function update(ServerRequest $request, $id)
    {
        if (!$this->isUserAdmin()) {
            return $this->respondJson(
                'Access Denied',
                [],
                403
            );
        }

        try {
            AppValidator::update($request->data);
        } catch (Exception $e) {
            return $this->respondJson(
                'Provided data was malformed',
                json_decode($e->getMessage()),
                422
            );
        }

        $app = DB::get(
            'apps',
            [
                'gumroad_id',
                'name',
                'url',
                'active'
            ],
            [
                'id' => $id
            ]
        );

        if (!$app) {
            return $this->respondJson(
                'App not found',
                [],
                404
            );
        }

        DB::update(
            'apps',
            [
                'gumroad_id' => $request->data->gumroad_id ?: $app['gumroad_id'],
                'name' => $request->data->name ?: $app['name'],
                'url' => $request->data->url ?: $app['url'],
                'active' => isset($request->data->active) ? $request->data->active : $app['active']
            ],
            [
                'id' => $id
            ]
        );

        return $this->respondJson(
            'App Updated',
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

        DB::delete('apps', [
            'id' => $id,
        ]);

        return $this->respondJson(
            'App Deleted',
            ['reload' => true]
        );
    }
}
