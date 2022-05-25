<?php

namespace App\Http\Controllers\API;

use App;
use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Input;
use function redirect;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function index()
    {

        $post = Post::all(); // ->orderBy('id', 'desc'); //->paginate(5);


        $post = Post::with('user')->get();

//        $post->$request->all()

        return response()->json([
            'status'=>'good',
            'data'=> $post
        ],200);

    }

    public function myPost(Request $request)
    {

        $post = Post::where('user_id',$request->user_id)->get(); // ->orderBy('id', 'desc'); //->paginate(5);

//        $post->$request->all()

        return response()->json([
            'status'=>'good',
            'data'=> $post
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

        $validator = Validator::make($data = $request->all(), Post::$rules);

        //  if($request->title == null || $request->body == null){}    --> 위에 Validator랑 같은 기능 코드 예시
        //  해당 기능이 1~2번 쓰이면 상관 없는데 컨트롤러 갯수가 매우 많으면 Validator는 Models\Post에서 규칙($rules)만 수정해 주면 되니까 효율적

        // validator 규칙 확인
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }

        $post = new Post;

        if ($request->hasfile('thumbnail')) {
            $uploadFile = $request->file('thumbnail');
            $fileHashName = time() . '-' . $uploadFile->getClientOriginalName();
            $filePath = $uploadFile->storeAs('public/thumbnails', $fileHashName);
            $thumbnail_url = Storage::disk('local')->url($filePath);
            $post->thumbnail = $thumbnail_url ?? '';
        }

//        $post->user_id = $request['user_id'];
////        $post->user_name = $request['user_name']; // 이거 유저 정보로 수정해야
//        $post->title = $request['title'];
//        $post->body = $request['body'];
//
//        $post->save();

        $post = $post->create([
            'user_id' => $request['user_id'],
            'title'   => $request['title'],
            'body'    => $request['body']
        ]);

        return response()->json([
            'status'=>200,
            'data'=> $post
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
        $post = Post::findOrFail($id);

        return view('post.show', compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $post = Post::find($id);
        return view('post.edit', compact('post'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $post = Post::find($id);
//
//        $validator = Validator::make($request->all(), Post::$rules);
//
//        if ($validator->fails());
//        {
//            return redirect()->back()->withErrors($validator)->withInput();
//        }

        $post->update($request->all());
        return response()->json([
            'status'=>200,
            'data'=>$post
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
        Post::destroy($id);
        return response()->json([
            'status'=>200
        ]);
    }
};
