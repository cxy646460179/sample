<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Auth;

class UsersController extends Controller
{
    //用户注册
    public function create(){
        return view('users.create');
    }
//    查询用户表显示内容 compact() 把 user 数组的形式传递给 'users.show'
    public function show(User $user){
        return view('users.show',compact('user'));
    }
//   接收注册信息,进行验证 unique:users 唯一性验证
    public function store(Request $request){
        $this->validate($request,[
            'name' => 'required|max:50',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|confirmed|min:6'
        ]);
//      用户模型 User::create() 创建成功后会返回一个用户对象，并包含新注册用户的所有信息。
//      我们将新注册用户的所有信息赋值给变量 $user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);
//          注册成功后自动登录
        Auth::login($user);
//          当我们想存入一条缓存的数据，让它只在下一次的请求内有效时，则可以使用 flash 方法。
//          flash 方法接收两个参数，第一个为会话的键，第二个为会话的值
        session()->flash('success', '欢迎，您将在这里开启一段新的旅程~');
//        通过路由跳转来进行数据绑定
        return redirect()->route('users.show',[$user]);
    }
}
