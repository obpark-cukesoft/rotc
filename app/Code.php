<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Code extends Model
{
    const CACHE_SECONDS = 60;
    const SCHOOL_CODE_ID = 2;

    public static function rebuildCodes($parentId, $rightId) {
        $leftId = $rightId+1;
        $codeIds = Code::select('id')->where('parent_id', $parentId)->orderBy('id','asc')->get();

        foreach($codeIds as $id) {
            $leftId = Code::rebuildCodes($id->id, $leftId);
        }

        Code::where('id', $parentId)->update(['left_id' => $leftId, 'right_id' => $rightId]);
        return $leftId+1;
    }

    public static function getCodeByParentId($parentId) {
        $codes = Code::findOrFail($parentId);
        $codes = Code::where('id', '!=', $codes->id)
            ->whereBetween('right_id', [$codes->right_id, $codes->left_id])
            ->orderBy('right_id', 'asc')->get();
        $results = [];
        foreach($codes as $code) {
            $results[] = ["value"=> $code->id, "text"=> $code->name_ko];
        }
        return $results;
    }

    public static function getCodes($codeId) {
        return Cache::remember('codes_'.$codeId, self::CACHE_SECONDS, function () use ($codeId) {
            $code = Code::findOrFail($codeId);
            return Code::selectRaw("id as value, name_ko as text")->where('id', '!=', $code->id)
                ->whereBetween('right_id', [$code->right_id, $code->left_id])
                ->orderBy('right_id', 'asc')->get();
        });
    }

    public static function getCodeByValue($codes, $value, $key = 'value') {
        $idx = array_search($value, array_column($codes, $key));
        return $codes[$idx];
    }

}
