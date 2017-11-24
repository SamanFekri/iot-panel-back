<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB;
use Exception;

class DataController extends Controller
{
    public function getData(Request $request)
    {
        try {
            $key = 1;
            $query = '{\'1\':{$exists: true}}';
            //$user = DB::connection('mongodb')->collection('parsed')->where((string)$key, 'exists', true)->get();
            $datas = DB::connection('mongodb')->collection('parsed')->raw()
                ->find(["2" => array('$exists' => true)], ['sort' => ['_id' => -1], 'limit' => 10]);

            $ret_val = array();
            foreach ($datas as $data) {
                $ret_val[] = $data;
            }
            return response()->json(['success' => true,'name' => $request->get('name'), 'message' => 'ok', 'datas' => $ret_val], 200);

        } catch (Exception $error) {
            return response()->json(['success' => false, 'message' => $error->getMessage()], 400);
        }

    }
}
