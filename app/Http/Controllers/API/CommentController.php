<?php

namespace App\Http\Controllers\API;

use App;
use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Input;
use function redirect;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index($id)
    {

        $post = Post::find($id);

        $comment = Comment::with('user')->where('post_id', $post->id)->get();

        return response()->json([
            'status'=>'good',
            'data'=> $comment
        ],200);

    }


    public function myComment($id)
    {

        $user = User::find($id);
        $myComment = Comment::where('user_id', $user->id)->get(); // ->orderBy('id', 'desc'); //->paginate(5);

        return response()->json([
            'status'=>'good',
            'data'=> $myComment
        ],200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    /**
     * Store a newly created resource in storage.
     *
     * // * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
//        $validator = Validator::make($data = $request->all(), Post::$rules);
        $validator = Validator::make($request->all(), Comment::$rules);

        //  if($request->title == null || $request->body == null){}    --> 위에 Validator랑 같은 기능 코드 예시
        //  해당 기능이 1~2번 쓰이면 상관 없는데 컨트롤러 갯수가 매우 많으면 Validator는 Models\Post에서 규칙($rules)만 수정해 주면 되니까 효율적

        // validator 규칙 확인
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }

        $comment = new Comment;

//        $post->user_id = $request['user_id'];
////        $post->user_name = $request['user_name']; // 이거 유저 정보로 수정해야
//        $post->title = $request['title'];
//        $post->body = $request['body'];
//
//        $post->save();

        $comment = $comment->create([
            'post_id' => $request['post_id'],
            'user_id' => $request['user_id'],
            'body'    => $request['body']
        ]);

        return response()->json([
            'status'=>200,
            'data'=> $comment
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $comment = Comment::findOrFail($id);

        return view('comment.show', compact('comment'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, $id)
    {
        $comment = Comment::find($id);
//
//        $validator = Validator::make($request->all(), Post::$rules);
//
//        if ($validator->fails());
//        {
//            return redirect()->back()->withErrors($validator)->withInput();
//        }

        $comment->update($request->all());
        return response()->json([
            'status'=>200,
            'data'=>$comment
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Comment::destroy($id);
        return response()->json([
            'status'=>200
        ]);
    }
};
