<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Input;
use Response;
use App\Http\Requests;
use Illuminate\Http\Request;
use Auth;
use Config;

class Collections extends Controller
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


  // function to show add collection form
  public function addcollection()
  {
          $sessiondata = session()->all();
          $role = \Session::get('role');
          if ($role != "SuperAdmin" && $role != "Cataloguer")
          {
              return \Redirect::action('AdminController@index'); // redirect to dash if not superadmin or cataloguer
          }

          return view('admin.addcollection')->with([          
             'page' => 'addcollection',
             'role' => \Session::get('role'),
             'sessiondata' => $sessiondata
          ]);    
  } // end show add collection form function



  // function to store newly created collection
  public function storecollection(Request $request)
  {
        $sessiondata = session()->all();
        $user_id = Auth::user()->getId();
        $this->validate($request, [
        'legalBodyName' => 'required|unique:legalbodies|max:35',   // name must be unique in legalbodies table
        'legalBodyShortName' =>'required|unique:legalbodies|max:14',// shortname must be unique in legalbodies table
        'legalBodyWebsite' => 'url|unique:legalbodies', // website must be unique in legalbodies table
        'legalBodyMDAcode' => 'required|unique:legalbodies|max:10', // folder structure based on MDAcode
        'legalBodyDefaultRepository' => 'required',
        'legalBodyDescription' => 'required',
        'longitude' => 'numeric',
        'latitude' => 'numeric'
        ]);

        $role = \Session::get('role'); // redirect to dash if not superadmin or cataloguer
        if ($role != "SuperAdmin" && $role != "Cataloguer")
        {
            return \Redirect::action('AdminController@index');
        }

        // set in config
        $largest_thumbnail_pixels = Config::get('app.collection_thumb_max');
     
        // get all input values  
        $input = Input::all();
        $legalBodyName = ucfirst(Input::get('legalBodyName'));
        $legalBodyShortName = Input::get('legalBodyShortName');
        $legalBodyWebsite = Input::get('legalBodyWebsite');
        $legalBodyImage = Input::get('legalBodyImage');   
        $legalBodyDefaultRepository = Input::get('legalBodyDefaultRepository');
        $legalBodyDescription = Input::get('legalBodyDescription');
        $longitude = trim(Input::get('longitude'));           
        $latitude = trim(Input::get('latitude'));

        // used for folder name(s) 
        $legalBodyMDAcode = trim(Input::get('legalBodyMDAcode'));
        $legalBodyMDAcode = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $legalBodyMDAcode);
        $legalBodyMDAcode = mb_ereg_replace("([\.]{2,})", '', $legalBodyMDAcode);
        $legalBodyMDAcode = preg_replace('/\s+/', '', $legalBodyMDAcode);      

        // deal with image upload
        if (Input::file('legalBodyImage')) {

              $destinationPath = 'images/legalBodyImages'; // upload path
              $extension = strtolower(Input::file('legalBodyImage')->getClientOriginalExtension()); // getting image extension
              $fileName = md5($legalBodyName.microtime()).'.'.$extension; // using md5 and microtime for filenames
              Input::file('legalBodyImage')->move($destinationPath, $fileName); // uploading file to given path

                                    // create instance to make thumbnail from
                                    $img = \Image::make(public_path().'/images/legalBodyImages/'.$fileName);
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

                                   $img->save('images/legalBodyImages/thumbnails/'.$fileName);

                                   //new collection, no original resources to unlink

        } else {
              // the user has not uploaded an image for this new collection, use default
              $fileName = 'default/no_image.jpg';
        }   

            // insert new collection and get new id
            $legalBodyID = \DB::table('legalbodies')->insertGetId(
               ['creatorAdminID' => $user_id, 'legalBodyName' =>$legalBodyName, 'legalBodyShortName' =>$legalBodyShortName, 'legalBodyMDAcode' =>$legalBodyMDAcode, 'legalBodyWebsite' =>$legalBodyWebsite, 'legalBodyImage' =>$fileName, 'legalBodyDescription' =>$legalBodyDescription, 'legalBodyDefaultRepository' => $legalBodyDefaultRepository, 'longitude' => $longitude, 'latitude' => $latitude, 'created_at' => \Carbon\Carbon::now(), 'updated_at' => \Carbon\Carbon::now()]
            );

            // creation of new legal body folders
            $imagepath = public_path().'/instrument_resources/images/'.$legalBodyMDAcode;
            $imagethumbpath = public_path().'/instrument_resources/images/'.$legalBodyMDAcode.'/thumbnails/';
            $soundpath = public_path().'/instrument_resources/sound/'.$legalBodyMDAcode;
            $videopath = public_path().'/instrument_resources/video/'.$legalBodyMDAcode;
            $xmlpath = public_path().'/instrument_resources/xml/'.$legalBodyMDAcode;
            $raw_local_path = public_path().'/instrument_resources/raw_local/'.$legalBodyMDAcode;

            if (!file_exists($raw_local_path)) 
            {    // create folder..
               \File::makeDirectory($raw_local_path, $mode = 0777, true, true);
            }

            if (!file_exists($imagepath)) 
            {    // create directory..
              \File::makeDirectory($imagepath, $mode = 0777, true, true);
            }

            if (!file_exists($imagethumbpath)) 
            {    // create directory..
              \File::makeDirectory($imagethumbpath, $mode = 0777, true, true);
            }

            if (!file_exists($soundpath)) 
            {    // create directory..
              \File::makeDirectory($soundpath, $mode = 0777, true, true);
            }

            if (!file_exists($videopath)) 
            {    // create directory..
              \File::makeDirectory($videopath, $mode = 0777, true, true);
            }
           
            if (!file_exists($xmlpath)) 
            {    // create directory..
              \File::makeDirectory($xmlpath, $mode = 0777, true, true);
            }
           

            // add to flash data
            session()->flash('flashdata', 'Collection added successfully!');

            // insert add collection to activity
            \DB::table('user_activity')->insert(
                ['userID' => $user_id, 'activity' => "You added a new collection", 'legalBodyID' => $legalBodyID, 'legalBodyName' => $legalBodyName, 'activityDate' => \Carbon\Carbon::now() ]
            );

            return \Redirect::action('Collections@editcollection', array('legalBodyID' => $legalBodyID));
  } // end function to store newly created collection



  // super admin only function to edit legal bodies not their own
  public function editcollection($legalBodyID)
  {
      $role = \Session::get('role');  
      $sessiondata = session()->all();
      $legalBodyID = (int) $legalBodyID; // FORCE INT 
      $user_id = Auth::user()->getId();
      $mylegalbody = \Session::get('legalBodyID');

      $insCount="0";
      $insCount = \DB::table('instruments')
      ->where('legalBodyID', '=', $legalBodyID)  
      ->count();

      if ($role != "SuperAdmin" && $role != "Cataloguer")
      {
          return \Redirect::action('AdminController@index');
      }

      if ($legalBodyID != $mylegalbody)
      {
          if ($role != "SuperAdmin" && $role != "Cataloguer")  //  no access to this collection 
          {
              return \Redirect::action('AdminController@legalbodyprofile'); 
          }
      }

      // Get legal body info for this collection
       $legalbodies = \DB::table('legalbodies')
      ->where('legalBodyID', '=', $legalBodyID)  
      ->take(1)->get();

      // redirect if no result for this collection
      if (sizeof($legalbodies) < 1) {
                return \Redirect::action('AdminController@movedordeleted'); 
      }

      // get user details and time for creation...
      $creator_id = $legalbodies[0]->creatorAdminID;
      $collection_created_at = new \Carbon\Carbon($legalbodies[0]->created_at);
      $now = \Carbon\Carbon::now();

      // check if their is xml set for this collection
      $importXMLfile = $legalbodies[0]->importXMLfile;
      $legalBodyMDAcode = $legalbodies[0]->legalBodyMDAcode; 
      $file = public_path().'/instrument_resources/xml/'.$legalBodyMDAcode.'/'.$importXMLfile;
      $display_file = '/public/instrument_resources/xml/'.$legalBodyMDAcode.'/'.$importXMLfile;

      // check xml file exists
      if (\File::exists($file))
      {
          $filesize = filesize($file); // filesize in bytes of rcm xml
          $filesize_k = round(($filesize / 1024));
          $file_last_modified = (new \DateTime())
           ->setTimestamp(\File::lastModified($file))
           ->format('D, d M Y H:i:s T');

      } else {
        // xml set in the database, but can't find the actual file
        $filesize=0; $file_last_modified=''; $filesize_k = '';
      }

      // get difference between now and collection creation
      $collection_creation_timeago=$collection_created_at->diffForHumans(\Carbon\Carbon::now());
      // carbon function returns 'before', let's replace it with 'ago'...
      $collection_creation_timeago = str_replace("before","ago",$collection_creation_timeago);

        $admin_creation_user = \DB::table('users')
        ->where('id', '=', $creator_id)  
        ->take(1)->get();

       if (sizeof($admin_creation_user) > 0) {
          $creator_avatar = $admin_creation_user[0]->avatar;
          $creator_name = $admin_creation_user[0]->name;
       } else {
          $creator_avatar = 'defaults/deleted_user.jpg';
          $creator_name = 'Deleted User';
       }

      $activity_time_ago[0] = "";
      $admin_pic[0] = "";
      $admin_name[0] = "";

      // Fetch last 20 activity data for this collection
       $collection_activity = \DB::table('user_activity')
      ->where('legalBodyID', '=', $legalBodyID)  
       ->orderBy('activityDate', 'desc')   
      ->take(20)->get();

      // initialise var in case no activity
      $activity_time_ago="";

        $i=0; // loop to create time ago array for activities
        foreach($collection_activity as $user_activity){

          // the adminID 
          $adminID = $user_activity->userID;

          $admin_user = \DB::table('users')
          ->where('id', '=', $adminID)  
          ->take(1)->get();

              $created_at = new \Carbon\Carbon($user_activity->activityDate);
              $now = \Carbon\Carbon::now();
                 
                 $timeago[$i]=$created_at->diffForHumans(\Carbon\Carbon::now()); // get difference between now and activity_date
                 $timeago[$i] = str_replace("before","ago",$timeago[$i]); // carbon function returns 'before', let's replace it with 'ago'...
                 $activity_time_ago[$i] = $timeago[$i]; // user activity

                 if (sizeof($admin_user) > 0) {
                    $admin_pic[$i] = $admin_user[0]->avatar;
                    $admin_name[$i] = $admin_user[0]->name;
                 } else {
                    $admin_pic[$i] = 'defaults/deleted_user.jpg';
                    $admin_name[$i] = 'Deleted User';
                 }

        $i++; 
      } // end for activity foreach

      return view('admin.editcollection')->with([          
         'page' => 'editcollection',
         'role' => \Session::get('role'),
         'legalBodyID' => $legalBodyID,
         'admin_user_activity' => $legalbodies, 
         'activity_time_ago' => $activity_time_ago,
         'admin_pic' => $admin_pic,
         'admin_name' => $admin_name,
         'collection_activity' => $collection_activity,
         'collection_creation_timeago' => $collection_creation_timeago,
         'collection_creation_image' => $collection_creation_timeago,
         'creator_avatar' => $creator_avatar,
         'creator_name' => $creator_name,
         'insCount' => $insCount,
         'importXMLfile' => $importXMLfile,
         'legalBodyMDAcode' => $legalBodyMDAcode,
         'file' => $file,
         'display_file' => $display_file,
         'filesize' => $filesize,
         'filesize_k' => $filesize_k,
         'file_last_modified' => $file_last_modified,
         'sessiondata' => $sessiondata

      ]);    

  } // end edit collection



  // store edited collection
  public function storeeditcollection(Request $request)
      {
        $user_id = Auth::user()->getId();
        $role = \Session::get('role');
        $sessiondata = session()->all();
        $this->validate($request, [
        'legalBodyName' => 'required|max:35',  
        'legalBodyShortName' =>'required|max:14',
        'legalBodyWebsite' => 'url',
        'legalBodyMDAcode' => 'required|max:10',        
        'legalBodyDefaultRepository' => 'required',
        'legalBodyDescription' => 'required',
        'longitude' => 'numeric',
        'latitude' => 'numeric'
        ]);

        // set in config
        $largest_thumbnail_pixels = Config::get('app.collection_thumb_max');

        if ($role != "SuperAdmin" && $role != "Cataloguer")
        {
            return \Redirect::action('AdminController@index'); // REDIRECT TO DASHBOARD IF NOT SUPERADMIN OR CATALOGUER
        }

        // get all input values  
        $input = Input::all();
        $legalBodyID = (int) Input::get('legalBodyID'); // forcing the id into an int       
        $legalBodyName = ucfirst(Input::get('legalBodyName'));
        $legalBodyShortName = Input::get('legalBodyShortName');

        // used for folder name(s) 
        $legalBodyMDAcode = trim(Input::get('legalBodyMDAcode')); 
        $legalBodyMDAcode = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $legalBodyMDAcode);
        $legalBodyMDAcode = mb_ereg_replace("([\.]{2,})", '', $legalBodyMDAcode); 
        $legalBodyMDAcode = preg_replace('/\s+/', '', $legalBodyMDAcode);

        // folder structure based on mda code - use hidden input to detect change in this...
        $legalBodyMDAcodeOrig = Input::get('legalBodyMDAcodeOrig');

        // just ensure all folders are there
        $imagepath = public_path().'/instrument_resources/images/'.$legalBodyMDAcodeOrig;
        $imagethumbpath = public_path().'/instrument_resources/images/'.$legalBodyMDAcodeOrig.'/thumbnails/';
        $soundpath = public_path().'/instrument_resources/sound/'.$legalBodyMDAcodeOrig;
        $videopath = public_path().'/instrument_resources/video/'.$legalBodyMDAcodeOrig;
        $xmlpath = public_path().'/instrument_resources/xml/'.$legalBodyMDAcodeOrig;
        $raw_local_path = public_path().'/instrument_resources/raw_local/'.$legalBodyMDAcode;

        if (!file_exists($raw_local_path)) 
        {    // create folder..
            \File::makeDirectory($raw_local_path, $mode = 0777, true, true);
        }

        if (!file_exists($imagepath)) 
        {    // create directory..
          \File::makeDirectory($imagepath, $mode = 0777, true, true);
        }

        if (!file_exists($imagethumbpath)) 
        {    // create directory..
          \File::makeDirectory($imagethumbpath, $mode = 0777, true, true);
        }

        if (!file_exists($soundpath)) 
        {    // create directory..
          \File::makeDirectory($soundpath, $mode = 0777, true, true);
        }

        if (!file_exists($videopath)) 
        {    // create directory..
          \File::makeDirectory($videopath, $mode = 0777, true, true);
        }
       
        if (!file_exists($xmlpath)) 
        {    // create directory..
          \File::makeDirectory($xmlpath, $mode = 0777, true, true);
        }





        // after removing bad characters, if legalBodyMDAcode is null, use orig
        if (strlen($legalBodyMDAcode) < 1) { $legalBodyMDAcode = $legalBodyMDAcodeOrig; }

        $legalBodyDefaultRepository = Input::get('legalBodyDefaultRepository');      
        $legalBodyWebsite = Input::get('legalBodyWebsite');
        $legalBodyImage = Input::get('legalBodyImage');     
        $legalBodyOrigImage = Input::get('legalBodyOrigImage');           
        $legalBodyDescription = Input::get('legalBodyDescription');
        $longitude = trim(Input::get('longitude'));           
        $latitude = trim(Input::get('latitude'));


        if (Input::file('legalBodyImage')) {

              $destinationPath = 'images/legalBodyImages'; // upload path
              $extension = strtolower(Input::file('legalBodyImage')->getClientOriginalExtension()); // getting image extension
              $fileName = md5($legalBodyName.microtime()).'.'.$extension; // using md5 and microtime for filenames
              Input::file('legalBodyImage')->move($destinationPath, $fileName); // uploading file to given path

              // create instance to make thumbnail from
              $img = \Image::make(public_path().'/images/legalBodyImages/'.$fileName);
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

              $img->save('images/legalBodyImages/thumbnails/'.$fileName);

              if ($legalBodyOrigImage != 'default/no_image.jpg') 
              {                         
                 // delete the original resource as they have uploaded a new one
                  \File::Delete('images/legalBodyImages/'.$legalBodyOrigImage);
                 // ... and delete thumbnail if it is there
                  \File::Delete('images/legalBodyImages/thumbnails/'.$legalBodyOrigImage);
              }      

        } else {
              $fileName = $legalBodyOrigImage; // not uploaded anything, keep image same as orig
        }   



        if ($legalBodyMDAcode != $legalBodyMDAcodeOrig)
        {
          // mda code has changed, we need to rename the folders.

                // existing folders 
                $imagepath = public_path().'/instrument_resources/images/'.$legalBodyMDAcodeOrig;
                $soundpath = public_path().'/instrument_resources/sound/'.$legalBodyMDAcodeOrig;
                $videopath = public_path().'/instrument_resources/video/'.$legalBodyMDAcodeOrig;
                $xmlpath = public_path().'/instrument_resources/xml/'.$legalBodyMDAcodeOrig;

                // renamed folders 
                $new_imagepath = public_path().'/instrument_resources/images/'.$legalBodyMDAcode;
                $new_soundpath = public_path().'/instrument_resources/sound/'.$legalBodyMDAcode;
                $new_videopath = public_path().'/instrument_resources/video/'.$legalBodyMDAcode;
                $new_xmlpath = public_path().'/instrument_resources/xml/'.$legalBodyMDAcode;

                if ( ! \File::move($imagepath, $new_imagepath))
                {
                    die("Couldn't rename image folder");
                }

                if ( ! \File::move($soundpath, $new_soundpath))
                {
                    die("Couldn't rename sound folder");
                }

                if ( ! \File::move($videopath, $new_videopath))
                {
                    die("Couldn't rename video folder");
                }

                if ( ! \File::move($xmlpath, $new_xmlpath))
                {
                    die("Couldn't rename xml folder");
                }

        } // end if mda code change

        // update collection
        \DB::table('legalbodies')
         ->where('legalBodyID', $legalBodyID)
         ->update(array('legalBodyName' =>$legalBodyName, 'legalBodyShortName' =>$legalBodyShortName, 'legalBodyMDAcode' =>$legalBodyMDAcode, 'legalBodyDefaultRepository' => $legalBodyDefaultRepository, 'legalBodyWebsite' =>$legalBodyWebsite, 'legalBodyImage' =>$fileName, 'legalBodyDescription' =>$legalBodyDescription, 'longitude' => $longitude, 'latitude' => $latitude, 'updated_at' => \Carbon\Carbon::now()));

        // now set the flash data to show the user has been updated...
        session()->flash('flashdata', 'Collection updated successfully!');

        // add update update collection activity
        \DB::table('user_activity')->insert(
           ['userID' => $user_id, 'activity' => "You updated a collection", 'legalBodyID' => $legalBodyID, 'legalBodyName' => $legalBodyName, 'activityDate' => \Carbon\Carbon::now() ]
        );

       return \Redirect::action('Collections@editcollection', array('legalBodyID' => $legalBodyID)); // redirect to manage collection

  } // end storeeditcollection function (updating collection not owned by admin)





  // manage xml
  public function managexml($legalBodyID)
  {
      $role = \Session::get('role');  
      $sessiondata = session()->all();
      $legalBodyID = (int) $legalBodyID; // FORCE INT 
      $user_id = Auth::user()->getId();
      $mylegalbody = \Session::get('legalBodyID');

      $insCount="0";
      $insCount = \DB::table('instruments')
      ->where('legalBodyID', '=', $legalBodyID)  
      ->count();

      // Uploading XML is SuperAdmin only
      if ($role != "SuperAdmin")
      {
          return \Redirect::action('AdminController@index');
      }

      // Get legal body info for this collection
       $legalbodies = \DB::table('legalbodies')
      ->where('legalBodyID', '=', $legalBodyID)  
      ->take(1)->get();

      // redirect if no result for this collection
      if (sizeof($legalbodies) < 1) {
                return \Redirect::action('AdminController@movedordeleted'); 
      }

      // get user details and time for creation...
      $creator_id = $legalbodies[0]->creatorAdminID;
      $collection_created_at = new \Carbon\Carbon($legalbodies[0]->created_at);
      $now = \Carbon\Carbon::now();

      // check if their is xml set for this collection
      $importXMLfile = $legalbodies[0]->importXMLfile;
      $legalBodyMDAcode = $legalbodies[0]->legalBodyMDAcode; 
      $file = public_path().'/instrument_resources/xml/'.$legalBodyMDAcode.'/'.$importXMLfile;
      $display_file = '/public/instrument_resources/xml/'.$legalBodyMDAcode.'/'.$importXMLfile;
      $preview_file = '/instrument_resources/xml/'.$legalBodyMDAcode.'/'.$importXMLfile;

      // check xml file exists
      if (\File::exists($file))
      {
          $filesize = filesize($file); // filesize in bytes of rcm xml
          $filesize_k = round(($filesize / 1024));
          $file_last_modified = (new \DateTime())
           ->setTimestamp(\File::lastModified($file))
           ->format('D, d M Y H:i:s T');

      } else {
        // xml set in the database, but can't find the actual file
        $filesize=0; $file_last_modified=''; $filesize_k = '';
      }

      // get difference between now and collection creation
      $collection_creation_timeago=$collection_created_at->diffForHumans(\Carbon\Carbon::now());
      // carbon function returns 'before', let's replace it with 'ago'...
      $collection_creation_timeago = str_replace("before","ago",$collection_creation_timeago);

        $admin_creation_user = \DB::table('users')
        ->where('id', '=', $creator_id)  
        ->take(1)->get();

       if (sizeof($admin_creation_user) > 0) {
          $creator_avatar = $admin_creation_user[0]->avatar;
          $creator_name = $admin_creation_user[0]->name;
       } else {
          $creator_avatar = 'defaults/deleted_user.jpg';
          $creator_name = 'Deleted User';
       }

      $activity_time_ago[0] = "";
      $admin_pic[0] = "";
      $admin_name[0] = "";

      // Fetch last 20 activity data for this collection
       $collection_activity = \DB::table('user_activity')
      ->where('legalBodyID', '=', $legalBodyID)  
       ->orderBy('activityDate', 'desc')   
      ->take(20)->get();

      // initialise var in case no activity
      $activity_time_ago="";

        $i=0; // loop to create time ago array for activities
        foreach($collection_activity as $user_activity){

          // the adminID 
          $adminID = $user_activity->userID;

          $admin_user = \DB::table('users')
          ->where('id', '=', $adminID)  
          ->take(1)->get();

              $created_at = new \Carbon\Carbon($user_activity->activityDate);
              $now = \Carbon\Carbon::now();
                 
                 $timeago[$i]=$created_at->diffForHumans(\Carbon\Carbon::now()); // get difference between now and activity_date
                 $timeago[$i] = str_replace("before","ago",$timeago[$i]); // carbon function returns 'before', let's replace it with 'ago'...
                 $activity_time_ago[$i] = $timeago[$i]; // user activity

                 if (sizeof($admin_user) > 0) {
                    $admin_pic[$i] = $admin_user[0]->avatar;
                    $admin_name[$i] = $admin_user[0]->name;
                 } else {
                    $admin_pic[$i] = 'defaults/deleted_user.jpg';
                    $admin_name[$i] = 'Deleted User';
                 }

        $i++; 
      } // end for activity foreach

      return view('admin.managexml')->with([          
         'page' => 'managexml',
         'role' => \Session::get('role'),
         'legalBodyID' => $legalBodyID,
         'admin_user_activity' => $legalbodies, 
         'activity_time_ago' => $activity_time_ago,
         'admin_pic' => $admin_pic,
         'admin_name' => $admin_name,
         'collection_activity' => $collection_activity,
         'collection_creation_timeago' => $collection_creation_timeago,
         'collection_creation_image' => $collection_creation_timeago,
         'creator_avatar' => $creator_avatar,
         'creator_name' => $creator_name,
         'insCount' => $insCount,
         'importXMLfile' => $importXMLfile,
         'legalBodyMDAcode' => $legalBodyMDAcode,
         'file' => $file,
         'display_file' => $display_file,
         'preview_file' => $preview_file,
         'filesize' => $filesize,
         'filesize_k' => $filesize_k,
         'file_last_modified' => $file_last_modified,
         'sessiondata' => $sessiondata

      ]);    

  } // end manage xml




  // function to store a reported problem
  public function storexml()
  {
        $role = \Session::get('role');  
        $user_id = Auth::user()->getId();
        $sessiondata = session()->all();

        // Uploading XML is SuperAdmin only
        if ($role != "SuperAdmin")
        {
            return \Redirect::action('AdminController@index');
        }

        // get all input values // print_r($input);
        $input = Input::all();
        $lido_xml = Input::get('lido_xml');  
        $legalBodyID = Input::get('legalBodyID');  
        $importXMLfile_orig = Input::get('importXMLfile_orig'); 

        // Get legal body info for this collection
         $legalbodies = \DB::table('legalbodies')
        ->where('legalBodyID', '=', $legalBodyID)  
        ->take(1)->get();

        // redirect if no result for this collection
        if (sizeof($legalbodies) < 1) {
                  return \Redirect::action('AdminController@movedordeleted'); 
        }


        $legalBodyMDAcode = $legalbodies[0]->legalBodyMDAcode; 

        if (Input::file('lido_xml')) {

              $destinationPath = 'instrument_resources/xml/'.$legalBodyMDAcode; // upload path
              $extension = strtolower(Input::file('lido_xml')->getClientOriginalExtension()); // getting image extension
              $fileName = $legalBodyMDAcode.'.'.$extension; // using md5 and microtime for filenames
              Input::file('lido_xml')->move($destinationPath, $fileName); // uploading file to given path

              // now set the flash data to show xml uploaded
              session()->flash('flashdata', 'XML uploaded successfully');


        } else {
              // the user has not uploaded an image for this new collection, use default
              $fileName = $importXMLfile_orig;
        }   

        // update this collection with new xml file
        \DB::table('legalbodies')
         ->where('legalBodyID', $legalBodyID)
         ->update(array('importXMLfile' =>$fileName));

        // redirect to editing this collection
         return \Redirect::action('Collections@editcollection', array('legalBodyID' => $legalBodyID));
  }
  // end function to store xml





} // end Collections class