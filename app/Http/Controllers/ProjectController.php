<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB;
use Exception;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function getData(Request $request, string $username = null, string $projectId = null)
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

                return response()->json(['success' => true, 'message' => 'ok', 'datas' => $ret_val], 200);
            }
            return response()->json(['success' => false, 'message' => 'tag is null'], 400);
        } catch (Exception $error) {
            return response()->json(['success' => false, 'message' => $error->getMessage()], 400);
        }

    }

    public function getProjects(Request $request){
        $user = Auth::user();
        if(!empty($user)){
            $projects = DB::table('projects')
                ->select('projects.id')
                ->where('projects.user_id', $user->id)
                ->get()
                ->pluck('id');

            return response()->json($projects,200);
        }
        return response()->json(['forbidden'], 403);
    }

    public function getProject(Request $request, string $projectId = null){
        $user = Auth::user();
        if(!empty($user)){
            if(!empty($projectId)) {
                $project = DB::table('projects')
                    ->select('projects.*')
                    ->where('projects.user_id', $user->id)
                    ->where('projects.id', $projectId)
                    ->get();

                if(empty($project) || $project == []){
                    return response()->json(['success' => false, 'message' => 'project not found'], 404);
                }
                return response()->json($project,200);
            }else{
                return response()->json(['success' => false, 'message' => 'project not found'], 404);
            }
        }
        return response()->json(['success' => false, 'message' => 'forbidden'], 403);
    }
}
