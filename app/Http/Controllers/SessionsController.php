<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class SessionsController extends Controller
{
//    只让未登录用户访问登录页面
    public function __construct()
    {
        $this->middleware('guest', [
            'only' => ['create']
        ]);
    }
    //渲染登录页面
     public function create(){
         return view('sessions.create');
     }
//     接收 post 传过来的用户登录信息,并验证
    public function store(Request $request){
        $credentials = $this->validate($request,[
            'email' => 'required|email|max:255',
            'password' => 'required'
        ]);
        /**
         * attempt 方法会接收一个数组来作为第一个参数，该参数提供的值将用于寻找数据库中的用户数据。
         * 因此在上面的例子中，attempt 方法执行的代码逻辑如下：
         * 使用 email 字段的值在数据库中查找；
         * 如果用户被找到：
         * 1). 先将传参的 password 值进行哈希加密，然后与数据库中 password 字段中已加密的密码进行匹配；
         * 2). 如果匹配后两个值完全一致，会创建一个『会话』给通过认证的用户。会话在创建的同时，
         * 也会种下一个名为 laravel_session 的 HTTP Cookie，以此 Cookie 来记录用户登录状态，最终返回 true；
         * 3). 如果匹配后两个值不一致，则返回 false；
         *  如果用户未找到，则返回 false
         * Auth::attempt() 方法可接收两个参数，
         * 第一个参数为需要进行用户身份认证的数组，
         * 第二个参数为是否为用户开启『记住我』功能的布尔值。
         */
        if (Auth::attempt($credentials,$request->has('remember'))) {
            // 登录成功后的相关操作
            session()->flash('success', '欢迎回来！');
            return redirect()->intended(route('users.show', [Auth::user()]));
        } else {
            // 登录失败后的相关操作
            session()->flash('danger', '很抱歉，您的邮箱和密码不匹配');
            return redirect()->back();
        }
    }
//    退出登录
    public function destroy(){
         Auth::logout();
         session()->flash('success','您已成功退出!');
         return redirect('login');
    }
}
