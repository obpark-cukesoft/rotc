<?php

namespace App\Http\Controllers;

use App\Board;
use App\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PostController extends Controller
{
    //
    public function index(Request $request, $boardId, $page = 0, $rowsPerPage = 10, $keyword = null)
    {

        if ($request->ajax())
        {
            $result = [];
            $query = Post::where('board_id', $boardId);
            if ($keyword) {
                /*$query->where(function($query) use($keyword){
                    $query->where('name', 'like', '%'.$keyword.'%');
                    $query->orWhere('mobile', 'like', '%'.$keyword.'%');
                });*/
            }
            $result['total'] = $query->count();
            $query->orderBy('id', 'desc');
            if ($rowsPerPage > 0) {
                $offset = ($page - 1) * $rowsPerPage;
                $query->offset($offset)->limit($rowsPerPage);
            }
            $result['items'] = $query->get();

            return response()->json($result);
        }

        $board = Board::find($boardId);
        return view('board.'.$board->skin.'.index', compact('board'));
    }

    public function store(Request $request, $boardId) {
        try {

            $post = new Post;
            $post->board_id = $boardId;
            $post->title = $request->title;
            $post->content = $request->content;
            $post->save();

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json($e->getMessage());
        }

        return response()->json(true);
    }

    public function show($id) {
        $post = Post::findOrFail($id);
        /*$post->count_read++;
        $post->save();*/
        return response()->json($post);
    }

    public function update(Request $request, $id) {
        try {
            $post = Post::findOrFail($id);
            $post->title = $request->title;
            $post->content = $request->content;
            $post->save();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json($e->getMessage());
        }

        return response()->json(true);
    }

    public function destroy($id)
    {
        return Post::destroy($id);
    }

}
