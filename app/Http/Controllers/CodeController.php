<?php

namespace App\Http\Controllers;

use App\Code;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CodeController extends Controller
{
    //
    public function rebuild()
    {
        Code::rebuildCodes(1, 1);
    }

    public function index(Request $request, $parentId = 0)
    {

        if ($request->ajax()) {
            //DB::enableQueryLog();
            $codes = DB::transaction(function () use ($parentId) {
                Code::initOrderByParentId($parentId);
                return Code::select('id', 'name_ko', 'order', DB::raw("'' as class"))->where('parent_id',
                    $parentId)->orderBy('order', 'asc')->get();
            });
            //dd(DB::getQueryLog());
            return response()->json($codes);
        }

        return view('setting.code', ['idx' => 1]);
    }

    public function show($id)
    {
        $code = Code::findOrFail($id);
        return response()->json($code);
    }

    public function store(Request $request)
    {
        $code = new Code();
        $code->parent_id = $request->parent_id;
        $code->name_ko = $request->name_ko;
        $code->memo = $request->memo;
        $code->save();
        return response()->json($code);
    }

    public function update(Request $request, $id)
    {
        $code = Code::findOrFail($id);
        $code->name_ko = $request->name_ko;
        $code->memo = $request->memo;
        $code->save();
        return response()->json($code);
    }

    public function setOrder(Request $request)
    {
        try {

            $result = DB::transaction(function () use ($request) {
                $now = Carbon::now();
                $query = '';
                $values = [];

                //dd($request->toArray());
                foreach ($request->toArray() as $k => $v) {
                    $query .= "(?, ?),";
                    $values[] = $v['id'];
                    $values[] = $k + 1;
                }
                $values[] = $now; // updated_at

                //https://www.mysqltutorial.org/mysql-insert-or-update-on-duplicate-key-update/
                $query = "INSERT INTO codes (id, `order`) VALUES ".trim($query, ',')."
                            ON DUPLICATE KEY
                                UPDATE `order` = values(`order`), updated_at = ?";
                //dd($query);
                return DB::insert($query, $values);
            });
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }

        return response()->json($result);
    }
}
