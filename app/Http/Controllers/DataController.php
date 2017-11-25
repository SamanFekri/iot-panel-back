<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB;
use Exception;

class DataController extends Controller
{
    public function getData(Request $request, string $username = null, string $prprojectId = null)
    {
        try {
            $tag = $request->input('tag');

            if (!empty($tag)) {
                $key = 'data' . '.' . $tag;
                $datas = DB::connection('mongodb')->collection('parsed')
                    ->where($key, '$exists', true)
                    ->take(20)
                    ->orderBy('timestamp', 'asc')
                    ->get([$key, 'timestamp']);

//            ->raw()
//                ->find(["timestamp" => array('$exists' => true)]);
//            ['sort' => ['_id' => -1], 'limit' => 10]

                $ret_val = array();
                foreach ($datas as $data) {
                    $tmp['value'] = $data['data'][$tag];
                    $tmp['time'] = $data['timestamp']->__toString();
                    array_push($ret_val, $tmp);
                }

                return response()->json(['success' => true, 'message' => 'ok', 'datas' => $ret_val,
                                        'username' => $username, 'projectId' => $prprojectId], 200);
            }
            return response()->json(['success' => false, 'message' => 'tag is null'], 400);
        } catch (Exception $error) {
            return response()->json(['success' => false, 'message' => $error->getMessage()], 400);
        }

    }
}
