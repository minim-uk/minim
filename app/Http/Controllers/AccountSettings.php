<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Input;
use Response;
use App\Http\Requests;
use Illuminate\Http\Request;
use Auth;

class AccountSettings extends Controller
{
/**
* Create a new controller instance.
*
* @return void
*/
public function __construct()
{  
        $this->middleware('auth');
} // end construct


   
  // function to show account settings form
  public function accountsettings()
  {
          $sessiondata = session()->all();

          return view('admin.accountsettings')->with([          
             'page' => 'accountsettings',
             'role' => \Session::get('role'),
             'sessiondata' => $sessiondata
          ]);    

  } // end account settings function



  // function to store updated account settings
  public function storeaccountsettings(Request $request) {

        $this->validate($request, [
          'name' => 'required|alpha',  // must be alphanumeric
          'surname' =>'required|alpha',
          'email' => 'required|email', // email address unique in users table 
          'confirmemail' => 'required|email|same:email', // confirm email must be same as email              
        ]);

        $user_id = Auth::user()->getId();
        $largest_thumbnail_pixels = "150"; // for avatar image - should be in condig

        // get all input values  
        $input = Input::all();
        $name = Input::get('name');
        $surname = Input::get('surname');
        $email = Input::get('email');
        $confirmemail = Input::get('confirmemail');
        $avatar = Input::get('avatar'); // upload of new avatar if any
        $avatarOrig = Input::get('avatarOrig'); // hidden input of original avatar

        if (Input::file('avatar')) {

              $destinationPath = 'images/users'; // upload path
              $extension = strtolower(Input::file('avatar')->getClientOriginalExtension()); // getting image extension
              $fileName = md5($name.microtime()).'.'.$extension; // using md5 and microtime for filenames
              Input::file('avatar')->move($destinationPath, $fileName); // uploading file to given path

              // create instance to make thumbnail from
              $img = \Image::make(public_path().'/images/users/'.$fileName);
              $width = $img->width();
              $height = $img->height();
                                             
              if ($width > $height) { // width larger than height, let's constrain width and auto height
                    $img->resize($largest_thumbnail_pixels, null, function ($constraint) {
                        $constraint->aspectRatio();
                    });
              } else {  // height larger than width, let's constrain height and auto width
                    $img->resize(null, $largest_thumbnail_pixels, function ($constraint) {
                        $constraint->aspectRatio();
                    });
              }

             // save thumb 
             $img->save('images/users/thumbnails/'.$fileName);

             if ($avatarOrig != 'defaults/no_avatar.jpg') 
             {   
                     // delete the original resource as they have uploaded a new one
                      \File::Delete('images/users/'.$avatarOrig);
                     // ... and delete thumbnail if it's there...
                     \File::Delete('images/users/thumbnails/'.$avatarOrig);
             }    

        } else { // the user has not uploaded an avatar 
              
              if (strlen($avatarOrig < 1))
              {    
                  $fileName = 'defaults/no_avatar.jpg';
              } else {
                  $fileName = $avatarOrig;
              }  
        }

        // update logged in user's account with new details..
        \DB::table('users')
        ->where('id', $user_id)
        ->update(array('name' =>$name, 'surname' =>$surname, 'email' =>$email, 'avatar' => $fileName ));
   
        // now update the session with the new values...
        session()->put('forename', $name);  
        session()->put('surname', $surname);  
        session()->put('email', $email);  
        session()->put('avatar', $fileName);  

        // set the flash data to show the user has been updated...
        session()->flash('flashdata', 'Account updated successfully!');

        // add update account to activity
        \DB::table('user_activity')->insert(
           ['userID' => $user_id, 'activity' => "You updated your account details", 'activityDate' => \Carbon\Carbon::now() ]
        );

        // redirect to accountsettings
        return \Redirect::action('AccountSettings@accountsettings');

  } // end store account settings function



  // function to show change password form
  public function changepassword()
  {
          $sessiondata = session()->all();
          $user_id = Auth::user()->getId();

          return view('admin.changepassword')->with([          
             'page' => 'changepassword',
             'role' => \Session::get('role'),
             'user_id' => $user_id,
             'sessiondata' => $sessiondata
          ]);    

  } // end account settings function



  // function to encrypt and store updated password
  public function storechangepassword(Request $request)
  {
        $this->validate($request, [
        'password' => 'required|min:8',  // name must be unique in legalbodies table
        'confirmPassword' =>'required|same:password',
        ]);

        $user_id = Auth::user()->getId();

        // get all input values  
        $input = Input::all();
        $password = Input::get('password');
        $confirmPassword = Input::get('confirmPassword');
        $checkUser = Input::get('checkUser');
  
        if ($checkUser == $user_id) // does hidden user's id in form match session
        {  
                    // update password
                      \DB::table('users')
                       ->where('id', $user_id)
                       ->update(array('password' => bcrypt($password)));
                    
                    // set flash data
                    session()->flash('flashdata', 'Your Password Has Been Successfully Updated.');          

                    // add update password to activity
                    \DB::table('user_activity')->insert(
                      ['userID' => $user_id, 'activity' => "You changed your password", 'activityDate' => \Carbon\Carbon::now() ]
                    );
            
        } else {

                    session()->flash('flashdata', 'Password not updated');  
        }       

        return \Redirect::action('AccountSettings@changepassword');  // redirect back to form 
 
  } // end update password

} // end class