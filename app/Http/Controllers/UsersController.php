<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Auth;

class UsersController extends Controller
{
//    中间件判断权限 过滤除去 except 以外的动作
    public function __construct()
    {
        $this->middleware('auth',[
            'except' => ['show','create','store','index']
        ]);
//    只让未登录用户访问注册页面：guest 未登录用户
        $this->middleware('guest', [
            'only' => ['create']
        ]);
    }

    //用户注册
    public function create(){
        return view('users.create');
    }
//    查询用户表显示内容 compact() 把 user 数组的形式传递给 'users.show'
    public function show(User $user){
        return view('users.show',compact('user'));
    }
//    接收注册信息,进行验证 unique:users 唯一性验证
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
//    编辑页面渲染
    public function edit(User $user)
    {
//        授权
        $this->authorize('update', $user);

        return view('users.edit',compact('user'));
    }
//    执行编辑
    public function update(User $user, Request $request)
    {
//        验证用户资料
        $this->validate($request,[
            'name' => 'required|max:50',
            'password' => 'nullable|confirmed|min:6'
        ]);
//        授权
        $this->authorize('update', $user);
//        定义用户资料数组 data
        $data = [];
        $data['name'] = $request->name;
//        判断 password 不为空时,加密
        if ($request->password){
            $data['password'] = bcrypt($request->password);
        }
//        更新资料
        $user->update($data);
//        成功提示
        session()->flash('success','个人资料更新成功!');
//        跳转个人页面
        return redirect()->route('users.show',$user->id);
    }
//    列表页
    public function index()
    {
        $users = User::paginate(10);
        return view('users.index', compact('users'));
    }
//    删除用户
    public function destroy(User $user)
    {
        $this->authorize('destroy', $user);
        $user->delete();
        session()->flash('success', '成功删除用户！');
        return back();
    }
}
