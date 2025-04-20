<?php

namespace App\Http\Controllers;

use App;
use App\Http\Requests\UserAddRequest;
use App\Models\User;
use App\Models\Category;
use App\Models\UserCatgory;
use Illuminate\Http\Request;
use App\Models\Utility;
use App\Models\LoginDetails;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rule;


class UserController extends Controller
{
    public function __construct()
    {
        // $this->authorizeResource(User::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        if($user->can('manage-users'))
        {
            $users = User::with(['categories'])->where('parent', Auth::user()->getCreatedBy())->get();
            return view('admin.users.index', compact('users'));
        }
        else
        {
            return view('403');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = \Auth::user();
        $categories = Category::where('created_by',\Auth::user()->createId())->get()->pluck('name','id');
        $user->categories  = explode(',', $user->categories);
        if($user->can('create-users'))
        {
            $roles = Role::get();
            return view('admin.users.create', compact('roles','categories'));
        }
        else
        {
            return view('403');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = \Auth::user();
        if($user->can('create-users'))
        {
            $validator = \Validator::make(
                $request->all(), [

                                    'name'    => 'required|string|max:255',
                                    'email'   => 'required|string|email|max:255|unique:users',
                                   'password' => 'required|confirmed|same:password_confirmation',
                                   'categories' => 'required',
                               ]
            );
            if($request->avatar)
            {
                $validation['avatar'] = 'required|image';
            }

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }



            $post = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'parent' => Auth::user()->getCreatedBy(),
            ];

            if($request->avatar)
            {
               $filenameWithExt = $request->file('avatar')->getClientOriginalName();
               $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
               $extension       = $request->file('avatar')->getClientOriginalExtension();
               $fileNameToStore = $filename . '_' . time() . '.' . $extension;
               $url = '';
               $dir        = 'public/';
               $path = Utility::upload_file($request,'avatar',$fileNameToStore,$dir,[]);
               if($path['flag'] == 1){
                   $url = $path['url'];
               }else{
                   return redirect()->back()->with('error', __($path['msg']));
               }
               $post['avatar'] = $fileNameToStore;
            }



            $user = User::create($post);
            foreach($request->categories as $value)
            {


                $category = UserCatgory::create([
                'user_id' => $user->id,
                'category_id' => $value
                ]);
            }
            $role = Role::find(2);
            if($role)
            {
                $user->assignRole($role);
            }

            $user_role = Role::where('name',Auth::user()->getCreatedBy())->pluck('id');
            $role = Role::find($user_role);
            if($role)
            {
                $user->assignRole($role);
                $user->userDefaultDataRegister($user->id);
            }

            // slack //

            $settings  = Utility::settings(\Auth::user()->createId());
            if(isset($settings['user_notification']) && $settings['user_notification'] ==1){
                $uArr = [
                    'email' => $user->email,
                    'password' => $request->password,
                    'user_name'  => \Auth::user()->name,
                ];
                Utility::send_slack_msg('new_user', $uArr);
            }

            // telegram //

            $settings  = Utility::settings(\Auth::user()->createId());
            if(isset($settings['telegram_user_notification']) && $settings['telegram_user_notification'] ==1){
                $uArr = [
                    'email' => $user->email,
                    'password' => $request->password,
                    'user_name'  => \Auth::user()->name,
                ];
                Utility::send_telegram_msg('new_user', $uArr);
            }



            $uArr = [
                'email' => $user->email,
                'password' => $request->password,
            ];


            $module = 'New User';
            $webhook =  Utility::webhookSetting($module,$user->created_by);

            if ($webhook) {
                $parameter = json_encode($user);
                // 1 parameter is  URL , 2 parameter is data , 3 parameter is method
                $status = Utility::WebhookCall($webhook['url'], $parameter, $webhook['method']);
                if ($status == true) {

                    return redirect()->back()->with('success', __('user successfully created!'));
                } else {
                    return redirect()->back()->with('error', __('Webhook call failed.'));
                }
            }

            $resp = Utility::sendEmailTemplate('new_user', [$user->id => $user->email], $uArr);
            // dd($resp);

            return redirect()->route('admin.users')->with('success', __('User created successfully'));
        }
        else
        {
            return view('403');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        $userObj = \Auth::user();
        $categories = Category::where('created_by',\Auth::user()->createId())->get()->pluck('name','id');
        $userCatgory = UserCatgory::where('user_id',$user->id)->get()->pluck('category_id');
        $categories->prepend(__('Select Category'), '');
        $userObj->categories  = explode(',', $userObj->categories);
        if($userObj->can('edit-users') || $user->id == $userObj->id)
        {
            $roles = Role::get();

            return view('admin.users.edit', compact('user', 'userObj', 'roles','categories','userCatgory'));
        }
        else
        {
            return view('403');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $userObj = \Auth::user();

        $validator = \Validator::make(
            $request->all(), [
                               'name'    => 'required|string|max:255',
                              'password' => 'required|confirmed|same:password_confirmation',
                              'email' => ['required',
                              Rule::unique('users')->where(function ($query)  use ($user) {
                              return $query->whereNotIn('id',[$user->id])->where('parent',  \Auth::user()->createId());
                            })
                            ],
                           ]
        );

        if($validator->fails())
        {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

            $user->name  = $request->name;
            $user->email = $request->email;
            if($request->categories)
            {
                UserCatgory::where('user_id',$user->id)->delete();
                foreach($request->categories as $value)
                {
                    $category = UserCatgory::create([
                    'user_id' => $user->id,
                    'category_id' => $value
                    ]);
                }
            }
            if($request->password)
            {
                $user->update(['password' => Hash::make($request->password)]);
            }

            if($request->avatar)
            {
                $request->validate(['avatar' => 'required|image']);

                // $avatarName = 'avatar-' . time() . '.' . $request->avatar->getClientOriginalExtension();
                // $request->avatar->storeAs('public', $avatarName);
                // $user->update(['avatar' => $avatarName]);

                $filenameWithExt = $request->file('avatar')->getClientOriginalName();
                $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                $extension       = $request->file('avatar')->getClientOriginalExtension();
                $fileNameToStore = $filename . '_' . time() . '.' . $extension;
                $url = '';
                $dir        = 'public/';

                $path = Utility::upload_file($request,'avatar',$fileNameToStore,$dir,[]);
                if($path['flag'] == 1){
                    $url = $path['url'];
                }else{
                    return redirect()->back()->with('error', __($path['msg']));
                }
                $user->update(['avatar' => $fileNameToStore]);
            }

            if($request->role && $request->user()->can('edit-users') && !$user->isme)
            {
                $role = Role::find($request->role);
                if($role)
                {
                    $user->syncRoles([$role]);
                }
            }
            $user->save();
            // return redirect()->back()->with('success', __('User updated successfully'));
            return redirect()->route('admin.users')->with('success', __('User updated successfully'));

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $objUser = \Auth::user();
        if($objUser->can('delete-users'))
        {
            $user->delete();

            return redirect()->route('admin.users')->with('success', __('User deleted successfully'));
        }
        else
        {
            return view('403');
        }
    }

    public function roles()
    {
        return response()->json(Role::get());
    }


    public function userPassword($id)
    {
        $eId  = \Crypt::decrypt($id);
        $user = User::find($eId);

        $employee = User::where('id', $eId)->first();

        return view('admin.users.reset', compact('user', 'employee'));
    }

    public function userPasswordReset(Request $request, $id)
    {
        $validator = \Validator::make(
            $request->all(), [
                               'password' => 'required|confirmed|same:password_confirmation',
                           ]
        );

        if($validator->fails())
        {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        $user      = User::where('id', $id)->first();
        $user->forceFill([
            'password' => Hash::make($request->password),
        ])->save();

        return redirect()->route('admin.users')->with('success', 'User Password successfully updated.');
    }

    public function userlog(Request $request)
    {

       $objUser = \Auth::user();
       $time = date_create($request->month);
       $firstDayofMOnth = (date_format($time, 'Y-m-d'));
       $lastDayofMonth =    \Carbon\Carbon::parse($request->month)->endOfMonth()->toDateString();

       $usersList = User::where('parent', '=', $objUser->createId())->get()->pluck('name', 'id');
       $usersList->prepend('All User', '');

       if ($request->month == null) {
           $users = DB::table('login_details')
               ->join('users', 'login_details.user_id', '=', 'users.id')
               ->select(DB::raw('login_details.*, users.name as user_name , users.email as user_email'))
               ->where(['login_details.created_by' => $objUser->id]);

       } else {
           $users = DB::table('login_details')
               ->join('users', 'login_details.user_id', '=', 'users.id')
               ->select(DB::raw('login_details.*, users.name as user_name , users.email as user_email'))
               ->where(['login_details.created_by' => $objUser->id]);
       }

       if (!empty($request->month)) {
           $users->where('date', '>=', $firstDayofMOnth);
           $users->where('date', '<=', $lastDayofMonth);
       }
       if (!empty($request->user)) {
           $users->where(['user_id'  => $request->user]);
       }
      $users = $users->orderBy('id','desc')->get();

        return view('admin.users.userLog',compact('users' , 'usersList'));
    }


    public function userlogview($id){
        $userlog = LoginDetails::find($id);
        return view('admin.users.viewUserLog', compact('userlog'));
    }

    public function userlogDestroy($id){
        $userlog = LoginDetails::find($id);
        $userlog->delete();
        return redirect()->back()->with('success', 'User Log Deleted Successfully.');
    }

}

