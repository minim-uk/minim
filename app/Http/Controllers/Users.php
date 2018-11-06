<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Input;
use Response;
use App\Http\Requests;
use Illuminate\Http\Request;
use Auth;
use Config;

class Users extends Controller
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


  // superadmin show adduser form function
  public function adduser()
  {      
          $role = \Session::get('role');
          $sessiondata = session()->all();

          if ($role != "SuperAdmin")
          {
              return \Redirect::action('AdminController@index'); // redirect to dash if not SuperAdmin
          }

          $legalbodies = \DB::table('legalbodies')
          ->orderBy('legalBodyName', 'asc')
          ->get();
          
          return view('admin.adduser')->with([          
             'page' => 'adduser',
             'role' => \Session::get('role'),
             'legalbodies' => $legalbodies,                           
             'sessiondata' => $sessiondata
          ]);    
  } // end add user function




  // superadmin function to store new or edited users
  public function storeuser(Request $request)
  {
        $sessiondata = session()->all();
        $id = (int) Input::get('id'); // hidden form input with user's id
        $largest_thumbnail_pixels = Config::get('app.user_thumb_max');
        $user_id = Auth::user()->getId();

        $role = \Session::get('role');
        if ($role != "SuperAdmin")
        {
            return \Redirect::action('AdminController@index');  // redirect to dash if not SuperAdmin
        }

        if ($id == "1")
        {
            return \Redirect::action('EloquentController@existingusers'); // can't edit main account
        }
     
        // get all input values  
        $input = Input::all();
        $name = ucfirst(Input::get('name'));
        $surname = ucfirst(Input::get('surname'));
        $email = Input::get('email');
        $confirmemail = Input::get('confirmemail');
        $avatar = Input::get('avatar'); // upload of new avatar if any
        $avatarOrig = Input::get('avatarOrig'); // hidden input of original avatar
        $adminLevel = Input::get('adminLevel');
        $password = bcrypt(Input::get('password')); //bcrypt  

        // deal with selected admin level  
        if($adminLevel == "SuperAdmin") {
           $legalBodyID = Input::get('legalBodyID');
           $role = "SuperAdmin";
        } elseif ($adminLevel == "Cataloguer") {
           $legalBodyID = Input::get('legalBodyID');
           $role = "Cataloguer";
        } elseif(strpos($adminLevel, 'admin_') !== false) { // admin for a specfic collection
           $adminLevel = (int) str_replace('admin_', '', $adminLevel);  // form value contains 'admin_[legalBodyID]'
           $legalBodyID = (int) $adminLevel;
           $role = "Admin";
        } 

        // if avatar uploaded        
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

             $img->save('images/users/thumbnails/'.$fileName);

             if ($avatarOrig != 'defaults/no_avatar.jpg') 
             {   
                  // delete the original resource as they have uploaded a new one
                  \File::Delete('images/users/'.$avatarOrig);
                  // ... and delete thumbnail if it is there..
                  \File::Delete('images/users/thumbnails/'.$avatarOrig);
             }          

        } else {
              // the user has not uploaded an image for new user
              if (strlen($avatarOrig < 1))
              {    
                  $fileName = 'defaults/no_avatar.jpg';
              } else {
                  $fileName = $avatarOrig;
              }  
        }



       if ($id == "0") // new user, also dealing with password
       {  
                $this->validate($request, [
                    'name' => 'required|alpha',  // must be alphanumeric
                    'surname' =>'required|alpha',
                    'email' => 'required|email|unique:users', // email address uniue in users table 
                    'confirmemail' => 'required|email|same:email', // confirm email must be same as email
                    'password' => 'required|min:8', // password minimum of 8 characters
                    'confirmPassword' => 'required|same:password'                   
                ]);
         
                //insert new user
                    $newUserID = \DB::table('users')->insertGetId(
                       ['creatorAdminID' => \Session::get('user_id'), 'legalBodyID' =>$legalBodyID, 'name' =>$name, 'surname' =>$surname, 'role' => $role, 'email' =>$email, 'password' => $password, 'avatar' => $fileName, 'created_at' => \Carbon\Carbon::now() ]
                    );

               // update flash data
                  session()->flash('flashdata', 'New user created successfully!');

               // add create user to activity
                  \DB::table('user_activity')->insert(
                    ['userID' => $user_id, 'this_userID' => $newUserID, 'activity' => "You added a new user", 'activityDate' => \Carbon\Carbon::now() ]
                  );
                  
               // redirect to edit user
                  return \Redirect::action('Users@edituser', array('id' => $newUserID));

            } else { // updating existing user

                  $this->validate($request, [
                    'name' => 'required|alpha',  // must be alphanumeric
                    'surname' =>'required|alpha',
                    'email' => 'required|email', 
                    'confirmemail' => 'required|email|same:email', // confirm email must be same as email
                  ]);

                // update existing user
                   \DB::table('users') 
                   ->where('id', $id)
                   ->update(array('legalBodyID' =>$legalBodyID, 'name' =>$name, 'surname' =>$surname, 'role' => $role, 'email' =>$email, 'avatar' => $fileName));

                // update flash data
                  session()->flash('flashdata', 'User updated successfully!');

                // add activity
                  \DB::table('user_activity')->insert(
                    ['userID' => $user_id, 'this_userID' => $id, 'activity' => "You updated a user's admin account", 'activityDate' => \Carbon\Carbon::now() ]
                  );

                // redirect to edit user
                  return \Redirect::action('Users@edituser', array('id' => $id));
          }      
  } // end superadmin function to store new or edited users



  // super admin only function to show edit user form
  public function edituser($id)
  {    
          $id = (int) $id;  // force int
          $sessiondata = session()->all();
          $role = \Session::get('role');

          if ($role != "SuperAdmin") // redirect to dash if not superadmin
          {  
              return \Redirect::action('AdminController@index'); 
          }

          if (\Session::get('user_id') == $id) // redirect to own account settings if admin has chosen own account
          {
             return \Redirect::action('AccountSettings@accountsettings'); 
          }

          $user = \DB::table('users') // get user to edit
          ->where('id', '=', $id)  
          ->take(1)->get();

          if (sizeof($user) < 1) 
          {     
              return \Redirect::action('AdminController@movedordeleted'); // redirect if user to edit doesn't exist
          }

          // get collections for pulldown
          $legalbodies = \DB::table('legalbodies')
          ->orderBy('legalBodyName', 'asc')
          ->get();

          // get edited user's activity
          $result =  \DB::table('user_activity')
          ->where('userID', '=', $id)  
           ->orderBy('activityDate', 'desc')   
          ->take(20)->get();

          $activity_time_ago=""; // init
     
          $i=0; // loop to create time ago array for activities
          foreach($result as $user_activity){
               $created_at = new \Carbon\Carbon($user_activity->activityDate);
               $now = \Carbon\Carbon::now();
               $timeago[$i]=$created_at->diffForHumans(\Carbon\Carbon::now()); // get difference between now and activity_date
               $timeago[$i] = str_replace("before","ago",$timeago[$i]); // carbon function returns 'before', let's replace it with 'ago'...
               $activity_time_ago[$i] = $timeago[$i]; // user activity
                 $i++; 
          } // end for activity foreach

         // Fetch last 20 activity data for this collection
          $user_activity = \DB::table('user_activity')
          ->where('userID', '=', $id)  
          ->orderBy('activityDate', 'desc')   
          ->take(20)->get();

          return view('admin.edituser')->with([          
             'page' => 'edituser',
             'role' => \Session::get('role'),
             'id' => $id,
             'user' => $user,
             'user_activity' => $user_activity,
             'activity_time_ago' => $activity_time_ago,
             'legalbodies' => $legalbodies,
             'sessiondata' => $sessiondata
          ]);   
  } // end show edituser form



  // function to show delete user form
  public function deleteuser($id) 
  {
        $role = \Session::get('role');
        $id = (int) $id; // force int
        $sessiondata = session()->all();

        if (\Session::get('user_id') == $id)
        {
            return \Redirect::action('AccountSettings@accountsettings');      // redirect to account settings if own account
        }

        if ($role != "SuperAdmin")
        {
            return \Redirect::action('AdminController@index');                // redirect to dash if not superadmin
        }

        $user = \DB::table('users')
        ->where('id', '=', $id)  
        ->take(1)->get();

        // no result for user 
        if (sizeof($user) < 1) {
                    return \Redirect::action('AdminController@movedordeleted');
        }

        $name = $user[0]->name;  
        $surname = $user[0]->surname;  

        return view('admin.deleteuser')->with([          
           'page' => 'deleteuser',
           'role' => \Session::get('role'),
           'id' => $id,
           'name' => $name,
           'surname' => $surname,
           'sessiondata' => $sessiondata
        ]);   

  } // end function to show delete user form

} // end Users class