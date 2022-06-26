<?php

namespace App\Http\Controllers;

use App\Role;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    use RegistersUsers;

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'phone' => 'required|numeric|unique:users',
            'password' => 'required|min:6',
            'logo' => 'image|mimes:jpeg,png,jpg,gif,svg',
        ]);
    }

    public function revokeToken(Request $request){
        return $request->user();
        if(auth()->guest()){
            return "FALSE1";
        }
        if(request()->has('token')){
            $token = request()->user()->token();
            if(!empty($token)){
                $token->revoke();
                return "TRUE";
            }else{
                return "FALSE2";
            }
        }else{
            return "FALSE3";
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('user.create', ['roles' => Role::all()]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $inputs = request()->input();
        $this->validator($inputs)->validate();
        $inputs['name'] = str_replace(" ","", $inputs['name']);
        $inputs['password'] = bcrypt($inputs['password']);
        $user = User::create(array_except($inputs,['_token']));

        // Upload logo
        if(request()->hasFile('logo')){
            $logo = request()->file('logo');
            $filename = $user->id.'.'.$logo->getClientOriginalExtension();
            $destinationPath = public_path(User::DIR_LOGO);
            $logo->move($destinationPath, $filename);

            $user->logo = User::DIR_LOGO.$filename;
            $user->save();
        }

        return redirect()
            ->route('user.show', ['id' => $user->id])
            ->with("status", $this->savedMessage);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $inputs = request()->input();
        if(array_key_exists('role_id', $inputs)){
            $role = Role::find($inputs['role_id']);
            if(isset($role))
            return view('user.list', [ 'users' => User::where('role_id', $inputs['role_id'])->with('role')->paginate(10), 'role_id' => $inputs['role_id'], 'role_name' => $role->name]);
        }

        //No role is selected
        //So will show users for all roles
        return view('user.list', [ 'users' => User::with('role')->paginate(10), 'role_id' => -1, 'role_name' => '']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return view('user.show', compact('user'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function updateRolePage(User $user)
    {
        return view('user.updateRole', [ 'user' => $user, 'roles' => Role::all()]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function storeUpdatedRole(Request $request, User $user)
    {
        $validator = Validator::make(request()->input(), [
            'role_id' => 'required|Integer',
        ]);

        if($validator->fails()){
            return redirect()->route('user.show', $user)
                ->with("status", $this->failedToUpdateMessage);
        }

        $user->update([ 'role_id' => request()->input()['role_id']]);
        return redirect()->route('user.show', $user)
            ->with("status", $this->updatedMessage);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function updatePasswordPage(User $user)
    {
        return view('user.updatePassword', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function storeUpdatedPassword(Request $request, User $user)
    {
        $validator = Validator::make(request()->input(), [
            'password' => 'required|min:6',
        ]);

        if($validator->fails()){
            return redirect()->route('user.show', $user)
                ->with("status", $this->failedToUpdateMessage . ". Password length must be of 6 length.");
        }

        $user->update([ 'password' => bcrypt(request()->input()['password'])]);
        return redirect()->route('user.show', $user)
            ->with("status", $this->updatedMessage);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function updateEmailPage(User $user)
    {
        return view('user.updateEmail', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function storeUpdatedEmail(Request $request, User $user)
    {
        $validator = Validator::make(request()->input(), [
            'email' => 'required|email|max:255|unique:users',
        ]);

        if($validator->fails()){
            return redirect()->route('user.show', $user)
                ->with("status", $this->failedToUpdateMessage . ". It must be unique.");
        }

        $user->update([ 'email' => request()->input()['email']]);
        return redirect()->route('user.show', $user)
            ->with("status", $this->updatedMessage);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function updatePhonePage(User $user)
    {
        return view('user.updatePhone', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function storeUpdatedPhone(Request $request, User $user)
    {
        $validator = Validator::make(request()->input(), [
            'phone' => 'required|numeric|unique:users',
        ]);

        if($validator->fails()){
            return redirect()->route('user.show', $user)
                ->with("status", $this->failedToUpdateMessage . ". It must be unique.");
        }

        $user->update([ 'phone' => request()->input()['phone']]);
        return redirect()->route('user.show', $user)
            ->with("status", $this->updatedMessage);
    }

    public function updateBankIdsPage(User $user)
    {
        return view('user.updateBankIds', compact('user'));
    }

    public function storeUpdatedBankIds(Request $request, User $user)
    {
        $user->update(
            [
                // City Bank
                'tcb_id' => request()->input()['tcb_id'],
                
                // DBBL Bank
                'dbbl_id' => request()->input()['dbbl_id'],
                'dbbl_terminal_id' => request()->input()['dbbl_terminal_id'],
                'dbbl_name' => request()->input()['dbbl_name'],
                'dbbl_fullname' => request()->input()['dbbl_fullname'],
                
                // EBL Bank
                'ebl_id' => request()->input()['ebl_id'],
                'ebl_password' => request()->input()['ebl_password'],
                
            ]);
        return redirect()->route('user.show', $user)
            ->with("status", $this->updatedMessage);
    }

    /**
     * Update Invoice Settings Form.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function updateInvoiceSettingsPage(User $user)
    {
        return view('user.updateInvoiceSettings', compact('user'));
    }

    /**
     * Store Invoice Settings  in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function storeUpdatedInvoiceSettings(Request $request, User $user)
    {
        $validator = Validator::make(request()->input(), [
            'logo' => 'image|mimes:jpeg,png,jpg,gif,svg',
        ]);

        if($validator->fails()){
            return redirect()->route('user.updateInvoiceSettings', $user)
                ->with("status", $this->failedToUpdateMessage . ". It must be unique.");
        }

        // Upload logo
        if($request->hasFile('logo')){
            $logo = $request->file('logo');
            $filename = request()->input()['id'].'.'.$logo->getClientOriginalExtension();
            $destinationPath = public_path(User::DIR_LOGO);
            $logo->move($destinationPath, $filename);

            $user->logo = User::DIR_LOGO.$filename;
        }

        $user->invoice_address = request()->input()['invoice_address'];
        $user->invoice_item = request()->input()['invoice_item'];
        $user->invoice_email = isset(request()->input()['invoice_email']) ? 1 : null;

        // save
        $user->save();

        return redirect()->route('user.show', $user)
            ->with("status", $this->updatedMessage);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(User $user)
    {
        if($user->id == 1){
            return redirect()->route('users')
                ->with("status", $this->cannotDisableMessage . " a Super Admin.");
        }

        if(Auth::user()->id == $user->id){
            return redirect()->route('users')
                ->with("status", $this->cannotDisableMessage . " youself.");
        }

        $user->update();
        return redirect()->route('users')
            ->with("status", $user->active == 1 ? $this->enabledMessage : $this->disabledMessage);
    }




    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function delete(User $user)
    {
        return view('user.delete', compact('user'));
    }


    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy()
    {
        $user = User::find(request()->get('id'));
        $user->delete();
        return redirect()
            ->route('user.index')
            ->with("status", $this->deletedMessage);
    }
}

