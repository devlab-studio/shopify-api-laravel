<?php

namespace Devlab\ShopifyApiLaravel\Http\Controllers;

use Devlab\ShopifyApiLaravel\ShopifyAPI\ShopifySso;
use Devlab\LaravelLogs\Models\ModelsLog;
use Devlab\ShopifyApiLaravel\Models\MiddlewareSession;
use Devlab\ShopifyApiLaravel\Models\Store;
use Illuminate\Http\Request;

class SooController extends Controller
{
    public function login(Request $request, $store_id)
    {
        $store = Store::dlGet($store_id);

        if ($store) {
            ModelsLog::doLog(dl_get_procedure($this, __FUNCTION__), [
                'shopify_api_session' => $request->cookie('shopify_api_session'),
                'session_id' => session()->getId(),
            ]);

            $shopify_api_session = $request->cookie('shopify_api_session', session()->getId());

            $mw_session = MiddlewareSession::dlGet(records_in_page: -1, filters: ['session_id' => $shopify_api_session]);
            $shopify_data = [];
            if (count($mw_session) == 0) {
                $mw_session = new MiddlewareSession();
                $mw_session->session_id = $shopify_api_session;
            } else {
                $mw_session = $mw_session->first();
                $shopify_data = $mw_session->shopify_data;
            }
            $shopify_data['redirect_url'] = $request->input('redirect_url', null);
            $shopify_data['store_id'] = $store_id;
            $mw_session->shopify_data = $shopify_data;
            $mw_session->save();

            $response = ShopifySso::getAuthorize($store);
            return $response;
        } else {
            return response()->json([
                'error' => 'store_not_found',
                'error_description' => 'Store not found',
            ]);
        }
    }
    public function getCode(Request $request)
    {
        $input = $request->all();

        ModelsLog::doLog(dl_get_procedure($this, __FUNCTION__), [
            'input' => $input,
            'session_id' => $request->cookie('shopify_api_session'),
        ]);

        if (isset($input['code'])) {
            $store = Store::dlGet($input['store_id']);
            $response = ShopifySso::getUserToken($store, $input['code']);
            $mw_session = MiddlewareSession::dlGet(records_in_page: -1, filters: ['session_id' => $request->cookie('shopify_api_session')]);
            if (count($mw_session) > 0 && isset($response['access_token'])) {
                $mw_session = $mw_session->first();
                $mw_session->sso_data = $response;
                $mw_session->save();
                $user_info = ShopifySso::getUserInfo($store, $response['access_token']);



            }
        } else {
            $response = [
                'error' => 'no_code',
                'error_description' => 'No code provided',
            ];
        }

        return response()->json($response);
    }

    public function appLogin(Request $request, $store_id)
    {
        $store = Store::dlGet($store_id);

        if ($store) {
            ModelsLog::doLog(dl_get_procedure($this, __FUNCTION__), [
                'shopify_api_session' => $request->cookie('shopify_api_session'),
                'session_id' => session()->getId(),
            ]);

            $shopify_api_session = $request->cookie('shopify_api_session', session()->getId());

            $mw_session = MiddlewareSession::dlGet(records_in_page: -1, filters: ['session_id' => $shopify_api_session]);
            $shopify_data = [];
            if (count($mw_session) == 0) {
                $mw_session = new MiddlewareSession();
                $mw_session->session_id = $shopify_api_session;
            } else {
                $mw_session = $mw_session->first();
                $shopify_data = $mw_session->shopify_data;
            }
            $shopify_data['redirect_url'] = $request->input('redirect_url', null);
            $shopify_data['store_id'] = $store_id;
            $mw_session->shopify_data = $shopify_data;
            $mw_session->save();

            $response = ShopifySso::getAppAuthorize($store);
            return $response;
        } else {
            return response()->json([
                'error' => 'store_not_found',
                'error_description' => 'Store not found',
            ]);
        }
    }
    public function getAppCode(Request $request)
    {
        $input = $request->all();

        ModelsLog::doLog(dl_get_procedure($this, __FUNCTION__), [
            'input' => $input,
            'session_id' => $request->cookie('shopify_api_session'),
        ]);

        if (isset($input['code'])) {
            // $store = Store::dlGet($input['store_id']);
            $store = Store::whereJsonContains('store_urls', [$input['shop']])->get()->first();
            $response = ShopifySso::getAppToken($store, $input['code']);
            $mw_session = MiddlewareSession::dlGet(records_in_page: -1, filters: ['session_id' => $request->cookie('shopify_api_session')]);
            if (count($mw_session) > 0 && isset($response['access_token'])) {
                $mw_session = $mw_session->first();
                $mw_session->sso_data = $response;
                $mw_session->save();
            }
        } else {
            $response = [
                'error' => 'no_code',
                'error_description' => 'No code provided',
            ];
        }

        return response()->json($response);
    }
}
