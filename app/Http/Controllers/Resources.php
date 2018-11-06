<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Input;
use Response;
use App\Http\Requests;
use Illuminate\Http\Request;
use Auth;
use Config;

class Resources extends Controller
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

	// shows form for adding resource
	public function addresource($instrumentID) {
	      $sessiondata = session()->all(); 
	      $instrumentID = (int) $instrumentID; // force instrumentID to int

	      // Fetch instrument data
	       $instrument = \DB::table('instruments')
	      ->where('instrumentID', '=', $instrumentID)  
	      ->take(1)->get();

	        if (sizeof($instrument) < 1) {
	                  return \Redirect::action('AdminController@movedordeleted'); // redirect if no result for instrument
	        }

	        // get events others than production for this instrument
	         $events = \DB::table('events')
	        ->where('instrumentID', '=', $instrumentID)  
	        ->get();

	        $status = $instrument[0]->status; // instrument status

	        // get user details and time for creation...
	        $creationType = $instrument[0]->creationType;
	        $creator_id = $instrument[0]->adminID;
	        $instrument_created_at = new \Carbon\Carbon($instrument[0]->created_at);
	        $now = \Carbon\Carbon::now();

	        $instrument_creation_timeago=$instrument_created_at->diffForHumans(\Carbon\Carbon::now()); // get difference between now and collection creation
	        $instrument_creation_timeago = str_replace("before","ago",$instrument_creation_timeago); // carbon function returns 'before', let's replace it with 'ago'...

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

	        // is this an approved term instrument?
	         $thesaurusID = $instrument[0]->thesaurusID;  
	         $legalBodyID = $instrument[0]->legalBodyID;  

	        // Fetch collection data
	         $legalbodies = \DB::table('legalbodies')
	        ->where('legalBodyID', '=', $legalBodyID)  
	        ->take(1)->get();
	         $legalBodyID = $legalbodies[0]->legalBodyID;  
	         $legalBodyName = $legalbodies[0]->legalBodyName;  
	         $legalBodyMDAcode = $legalbodies[0]->legalBodyMDAcode;  

	        // Fetch repository data
	         $repositoryName = '';
	         $repositories = \DB::table('repositories')
	        ->where('instrumentID', '=', $instrumentID)  
	        ->take(1)->get();

	        if (sizeof($repositories) != 0 )      // get repository
	        {
	           $repositoryName = $repositories[0]->repositoryName;  
	           $inventoryNumber = $repositories[0]->inventoryNumber;   
	        }   

	        if ($thesaurusID > 0) 
	        {
	           $thesaurus = \DB::table('thesauruses')
	          ->where('thesaurusID', '=', $thesaurusID)  
	          ->take(1)->get();

	               // get levels for approved term for title
	               // $Level_0 = $thesaurus[0]->Level_0;   // not required  
	               $Level_1 = $thesaurus[0]->Level_1;  
	               $Level_2 = $thesaurus[0]->Level_2;  
	               $Level_3 = $thesaurus[0]->Level_3;  
	               $titlePreferred = $Level_1." > ".$Level_2." > ".$Level_3;
	               $titleSingle = $instrument[0]->titlePreferred;

	         } else {

	              // not an approved term - we can use the value straight from the database...
	              $titleSingle = $instrument[0]->titlePreferred;
	              $titlePreferred = $instrument[0]->titlePreferred;
	              $Level_3 = $instrument[0]->titlePreferred;
	         }

	           // get 1 image for this instrument if exists
	           $insimage = \DB::table('resources')
	          ->where('instrumentID', '=', $instrumentID)  
	          ->where('resourceType', '=', "image")  
	          ->take(1)->get();

	          if (sizeof($insimage) > 0) {
	            $insimage = $insimage[0]->resourceFileName;
	          } else {
	            $insimage = "none";
	          }  

	           // get number of images for this instrument
	           $imageCount = \DB::table('resources')
	          ->where('instrumentID', '=', $instrumentID)  
	          ->where('resourceType', '=', "image")  
	          ->count();

	           // get number of audio for this instrument
	           $audioCount = \DB::table('resources')
	          ->where('instrumentID', '=', $instrumentID)  
	          ->where('resourceType', '=', "sound")  
	          ->count();

	           // get number of video for this instrument
	           $videoCount = \DB::table('resources')
	          ->where('instrumentID', '=', $instrumentID)  
	          ->where('resourceType', '=', "video")  
	          ->count();


	      $legalBodyID = $instrument[0]->legalBodyID;  
	      $instrumentName = $instrument[0]->titlePreferred;  

	      // Fetch legal body data
	       $legalbody = \DB::table('legalbodies')
	      ->where('legalBodyID', '=', $legalBodyID)  
	      ->take(1)->get();

	      $legalBodyName = $legalbody[0]->legalBodyName;  
	      $legalBodyMDAcode = $legalbody[0]->legalBodyMDAcode;  

	        $prod_event_actors=''; $other_event_actors='';

	        // get production event actors for this instrument
	         $prod_event_actors = \DB::table('eventactors')->select('eventActorID', 'eventActorName')->where('instrumentID', '=', $instrumentID)->get();
	         
	        // get events others than production for this instrument
	         $events = \DB::table('events')
	        ->where('instrumentID', '=', $instrumentID)  
	        ->get();

	        // iterate through other events
	        foreach ($events as $key => $otherevent)
	        {    
	             $thisEventID = $otherevent->eventID;
	             $other_event_actors[$thisEventID] = \DB::table('eventactors')->select('eventActorID', 'eventActorName')->where('eventID', '=', $thisEventID)->get();
	        }     

         // get rights information for this instrument, if exists
         $rights = \DB::table('rights')
        ->where('instrumentID', '=', $instrumentID)  
        ->where('rightsFlag', '=', "instrument")  
        ->get();

	        return view('admin.addresource')->with([          
	           'page' => 'addresource',
	           'role' => \Session::get('role'),
	           'instrumentID' => $instrumentID,
	           'legalBodyName' => $legalBodyName,
	           'legalBodyMDAcode' => $legalBodyMDAcode,
	           'legalBodyID' => $legalBodyID,
	           'instrumentName' => $instrumentName,   
	           'thesaurusID' => $thesaurusID,   
	           'titlePreferred' => $titlePreferred,  
	           'titleSingle' => $titleSingle,  
	           'repositoryName' => $repositoryName,  
	           'inventoryNumber' => $inventoryNumber,
	           'imageCount' => $imageCount, 
	           'audioCount' => $audioCount, 
	           'videoCount' => $videoCount, 
	           'actorID' => '',
	           'events' => $events,
             'eventID' => '',
             'eventType' => '',              
	           'prod_event_actors' => $prod_event_actors,
	           'other_event_actors' => $other_event_actors,
	           'status' => $status,
	           'insimage' => $insimage,
	           'instrument_creation_timeago' => $instrument_creation_timeago,
	           'creator_avatar' => $creator_avatar,
	           'creator_name' => $creator_name,
	           'creationType' => $creationType,
             'rights' => $rights,
	           'sessiondata' => $sessiondata
	        ]);   

	} // end add resource


    // edit images show image list
    public function editimages($instrumentID)
    {
        $role = \Session::get('role');
        $sessiondata = session()->all();
        $instrumentID = (int) $instrumentID;// force insID to int for security

        // Fetch instrument data
         $instrument = \DB::table('instruments')
        ->where('instrumentID', '=', $instrumentID)  
        ->take(1)->get();

        if (sizeof($instrument) < 1) {
                 return \Redirect::action('AdminController@movedordeleted'); // REDIRECT IF NO RESULT FOR THIS INSRUMENT IF NO OBJECT
        }

        $status = $instrument[0]->status;
        $legalBodyID = $instrument[0]->legalBodyID;  
        $instrumentName = $instrument[0]->titlePreferred;  

        // if user is 'Admin', is this instrument in their collection?
        if ($role == "Admin") {
            $mylegalbody = \Session::get('legalBodyID');
               if ($mylegalbody != $legalBodyID) {
                  return \Redirect::action('AdminController@movedordeleted'); // redirect, not an instrument this admin can edit
               }
        }

        // Fetch legal body data
         $legalbody = \DB::table('legalbodies')
        ->where('legalBodyID', '=', $legalBodyID)  
        ->take(1)->get();

        $legalBodyName = $legalbody[0]->legalBodyName;  
        $legalBodyMDAcode = $legalbody[0]->legalBodyMDAcode;  

        // fetch images for this instrument
         $images = \DB::table('resources')
        ->where('instrumentID', '=', $instrumentID)  
        ->where('resourceType', '=', 'image')  
        ->get();

        // get user details and time for creation...
        $creationType = $instrument[0]->creationType;
        $creator_id = $instrument[0]->adminID;
        $instrument_created_at = new \Carbon\Carbon($instrument[0]->created_at);
        $now = \Carbon\Carbon::now();

        $instrument_creation_timeago=$instrument_created_at->diffForHumans(\Carbon\Carbon::now()); // get difference between now and collection creation
        $instrument_creation_timeago = str_replace("before","ago",$instrument_creation_timeago); // carbon function returns 'before', let's replace it with 'ago'...

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

        // is this an approved term instrument?
         $thesaurusID = $instrument[0]->thesaurusID;  
         $legalBodyID = $instrument[0]->legalBodyID;  

        // Fetch legal body data
         $legalbodies = \DB::table('legalbodies')
        ->where('legalBodyID', '=', $legalBodyID)  
        ->take(1)->get();
         $legalBodyID = $legalbodies[0]->legalBodyID;  
         $legalBodyName = $legalbodies[0]->legalBodyName;  
         $legalBodyMDAcode = $legalbodies[0]->legalBodyMDAcode;  

        // Fetch repository data
         $repositoryName = '';
         $repositories = \DB::table('repositories')
        ->where('instrumentID', '=', $instrumentID)  
        ->take(1)->get();

        if (sizeof($repositories) != 0 )     // get repository
        {
           $repositoryName = $repositories[0]->repositoryName;  
           $inventoryNumber = $repositories[0]->inventoryNumber;   
        }   

        if ($thesaurusID > 0) 
        {
           $thesaurus = \DB::table('thesauruses')
          ->where('thesaurusID', '=', $thesaurusID)  
          ->take(1)->get();

              // get levels for approved term for title
              // $Level_0 = $thesaurus[0]->Level_0;   // not required, (always 'Instruments') 
              $Level_1 = $thesaurus[0]->Level_1;  
              $Level_2 = $thesaurus[0]->Level_2;  
              $Level_3 = $thesaurus[0]->Level_3;  
              $titlePreferred = $Level_1." > ".$Level_2." > ".$Level_3;
              $titleSingle = $instrument[0]->titlePreferred;

         } else {

              // not an approved term - we can use the value straight from the database...
              $titleSingle = $instrument[0]->titlePreferred;
              $titlePreferred = $instrument[0]->titlePreferred;
              $Level_3 = $instrument[0]->titlePreferred;
         }

          // get 1 image for this instrument if exists
           $insimage = \DB::table('resources')
          ->where('instrumentID', '=', $instrumentID)  
          ->where('resourceType', '=', "image")  
          ->take(1)->get();

          if (sizeof($insimage) > 0) {
            $insimage = $insimage[0]->resourceFileName;
          } else {
            $insimage = "none";
          }  

          // get number of images for this instrument
           $imageCount = \DB::table('resources')
          ->where('instrumentID', '=', $instrumentID)  
          ->where('resourceType', '=', "image")  
          ->count();

          // get number of audio for this instrument
           $audioCount = \DB::table('resources')
          ->where('instrumentID', '=', $instrumentID)  
          ->where('resourceType', '=', "sound")  
          ->count();

          // get number of video for this instrument
           $videoCount = \DB::table('resources')
          ->where('instrumentID', '=', $instrumentID)  
          ->where('resourceType', '=', "video")  
          ->count();

        $prod_event_actors=''; $other_event_actors='';

        // get production event actors for this instrument
         $prod_event_actors = \DB::table('eventactors')->select('eventActorID', 'eventActorName')->where('instrumentID', '=', $instrumentID)->get();

        // get events others than production for this instrument
         $events = \DB::table('events')
        ->where('instrumentID', '=', $instrumentID)  
        ->get();

        // iterate through other events
        foreach ($events as $key => $otherevent)
        {    
             $thisEventID = $otherevent->eventID;
             $other_event_actors[$thisEventID] = \DB::table('eventactors')->select('eventActorID', 'eventActorName')->where('eventID', '=', $thisEventID)->get();
        }     

       // get rights information for this instrument, if exists
       $rights = \DB::table('rights')
      ->where('instrumentID', '=', $instrumentID)  
      ->where('rightsFlag', '=', "instrument")  
      ->get();

        return view('admin.editimages')->with([          
           'page' => 'editimages',
           'role' => \Session::get('role'),
           'instrumentID' => $instrumentID,
           'legalBodyName' => $legalBodyName,
           'legalBodyMDAcode' => $legalBodyMDAcode,
           'legalBodyID' => $legalBodyID,
           'instrumentName' => $instrumentName,
           'images' => $images,
           'thesaurusID' => $thesaurusID,   
           'titlePreferred' => $titlePreferred,  
           'titleSingle' => $titleSingle,  
           'repositoryName' => $repositoryName,  
           'inventoryNumber' => $inventoryNumber,
           'imageCount' => $imageCount, 
           'audioCount' => $audioCount, 
           'videoCount' => $videoCount, 
           'actorID' => '',
           'events' => $events,
           'eventID' => '',
           'eventType' => '',           
           'insimage' => $insimage,
           'prod_event_actors' => $prod_event_actors,
           'other_event_actors' => $other_event_actors,
           'status' => $status,
           'instrument_creation_timeago' => $instrument_creation_timeago,
           'creator_avatar' => $creator_avatar,
           'creator_name' => $creator_name,
           'creationType' => $creationType,
           'rights' => $rights,
           'sessiondata' => $sessiondata
        ]);   
   } // end edit images function
 


    // editaudio shows audio list
    public function editaudio($instrumentID)
    {
        $role = \Session::get('role');
        $sessiondata = session()->all();
        $instrumentID = (int) $instrumentID;// force insID to int for security
        $soundimage = ""; // image to represent sound 
        $images ='';

        // then we need an image to represent the mp3
         $soundimage = '';
         $resourcequery = \DB::table('resources')
        ->where('resourceType', '=', "image")  
        ->where('instrumentID', '=', $instrumentID)  
        ->take(1)->get();

        if (sizeof($resourcequery) > 0) {
         $soundimage = $resourcequery[0]->resourceFileName;  
        }

        // Fetch instrument data
         $instrument = \DB::table('instruments')
        ->where('instrumentID', '=', $instrumentID)  
        ->take(1)->get();

        if (sizeof($instrument) < 1) {
                  return \Redirect::action('AdminController@movedordeleted'); // redirect if no result for instrument
        }

        $status = $instrument[0]->status;
        $legalBodyID = $instrument[0]->legalBodyID;  
        $instrumentName = $instrument[0]->titlePreferred;  

        // if user is 'Admin', is this instrument in their collection?
        if ($role == "Admin") {
            $mylegalbody = \Session::get('legalBodyID');
               if ($mylegalbody != $legalBodyID) {
                  return \Redirect::action('AdminController@movedordeleted'); // this is not an instrument belonging to this Admin's collection
               }
        }

        // Fetch legal body data
         $legalbody = \DB::table('legalbodies')
        ->where('legalBodyID', '=', $legalBodyID)  
        ->take(1)->get();

        $legalBodyName = $legalbody[0]->legalBodyName;  
        $legalBodyMDAcode = $legalbody[0]->legalBodyMDAcode;  

        // get user details and time for creation...
        $creationType = $instrument[0]->creationType;
        $creator_id = $instrument[0]->adminID;
        $instrument_created_at = new \Carbon\Carbon($instrument[0]->created_at);
        $now = \Carbon\Carbon::now();

        $instrument_creation_timeago=$instrument_created_at->diffForHumans(\Carbon\Carbon::now());  // get difference between now and collection creation
        $instrument_creation_timeago = str_replace("before","ago",$instrument_creation_timeago); // carbon function returns 'before', let's replace it with 'ago'...

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

        // is this an approved term instrument?
         $thesaurusID = $instrument[0]->thesaurusID;  
         $legalBodyID = $instrument[0]->legalBodyID;  

        // Fetch legal body data
         $legalbodies = \DB::table('legalbodies')
        ->where('legalBodyID', '=', $legalBodyID)  
        ->take(1)->get();
         $legalBodyID = $legalbodies[0]->legalBodyID;  
         $legalBodyName = $legalbodies[0]->legalBodyName;  
         $legalBodyMDAcode = $legalbodies[0]->legalBodyMDAcode;  

        // Fetch repository data
         $repositoryName = '';
         $repositories = \DB::table('repositories')
        ->where('instrumentID', '=', $instrumentID)  
        ->take(1)->get();

        if (sizeof($repositories) != 0 ) // get repository 
        {
           $repositoryName = $repositories[0]->repositoryName;  
           $inventoryNumber = $repositories[0]->inventoryNumber;   
        }   

        if ($thesaurusID > 0) 
        {
           $thesaurus = \DB::table('thesauruses')
          ->where('thesaurusID', '=', $thesaurusID)  
          ->take(1)->get();

              // get levels for approved term for title
              // $Level_0 = $thesaurus[0]->Level_0;   // not required  
              $Level_1 = $thesaurus[0]->Level_1;  
              $Level_2 = $thesaurus[0]->Level_2;  
              $Level_3 = $thesaurus[0]->Level_3;  
              $titlePreferred = $Level_1." > ".$Level_2." > ".$Level_3;
              $titleSingle = $instrument[0]->titlePreferred;

         } else {

              // not an approved term - we can use the value straight from the database...
              $titleSingle = $instrument[0]->titlePreferred;
              $titlePreferred = $instrument[0]->titlePreferred;
              $Level_3 = $instrument[0]->titlePreferred;
         }
        
          // get 1 image for this instrument if exists
          $insimage = \DB::table('resources')
          ->where('instrumentID', '=', $instrumentID)  
          ->where('resourceType', '=', "image")  
          ->take(1)->get();

          if (sizeof($insimage) > 0) {
            $insimage = $insimage[0]->resourceFileName;
          } else {
            $insimage = "none";
          }  

           // get number of images for this instrument
           $imageCount = \DB::table('resources')
          ->where('instrumentID', '=', $instrumentID)  
          ->where('resourceType', '=', "image")  
          ->count();

           // get number of audio for this instrument
           $audioCount = \DB::table('resources')
          ->where('instrumentID', '=', $instrumentID)  
          ->where('resourceType', '=', "sound")  
          ->count();

           // get number of videos for this instrument
           $videoCount = \DB::table('resources')
          ->where('instrumentID', '=', $instrumentID)  
          ->where('resourceType', '=', "video")  
          ->count();

           // fetch audio into array for list for this instrument
           $sounds = \DB::table('resources')
          ->where('instrumentID', '=', $instrumentID)  
          ->where('resourceType', '=', 'sound')  
          ->get();

        $prod_event_actors=''; $other_event_actors='';

        // get production event actors for this instrument
         $prod_event_actors = \DB::table('eventactors')->select('eventActorID', 'eventActorName')->where('instrumentID', '=', $instrumentID)->get();
         
        // get events others than production for this instrument
         $events = \DB::table('events')
        ->where('instrumentID', '=', $instrumentID)  
        ->get();

        // iterate through other events
        foreach ($events as $key => $otherevent)
        {    
             $thisEventID = $otherevent->eventID;
             $other_event_actors[$thisEventID] = \DB::table('eventactors')->select('eventActorID', 'eventActorName')->where('eventID', '=', $thisEventID)->get();
        }     

       // get rights information for this instrument, if exists
       $rights = \DB::table('rights')
      ->where('instrumentID', '=', $instrumentID)  
      ->where('rightsFlag', '=', "instrument")  
      ->get();

        return view('admin.editaudio')->with([          
         'page' => 'editaudio',
           'role' => \Session::get('role'),
           'instrumentID' => $instrumentID,
           'legalBodyName' => $legalBodyName,
           'legalBodyMDAcode' => $legalBodyMDAcode,
           'legalBodyID' => $legalBodyID,
           'instrumentName' => $instrumentName,
           'images' => $images,
           'thesaurusID' => $thesaurusID,   
           'titlePreferred' => $titlePreferred,  
           'titleSingle' => $titleSingle,  
           'repositoryName' => $repositoryName,  
           'inventoryNumber' => $inventoryNumber,
           'imageCount' => $imageCount, 
           'audioCount' => $audioCount, 
           'videoCount' => $videoCount, 
           'actorID' => '',
           'events' => $events,
           'eventID' => '',
           'eventType' => '',      
           'prod_event_actors' => $prod_event_actors,
           'other_event_actors' => $other_event_actors,
           'status' => $status,
           'insimage' => $insimage,
           'instrument_creation_timeago' => $instrument_creation_timeago,
           'creator_avatar' => $creator_avatar,
           'creator_name' => $creator_name,
           'creationType' => $creationType,
           'sounds' => $sounds,
           'rights' => $rights,
           'soundimage' => $soundimage,
           'sessiondata' => $sessiondata,
        ]);   

} // end edit audio list



// editvideo shows video list
public function editvideo($instrumentID)
{
        $role = \Session::get('role');
        $sessiondata = session()->all();
        $instrumentID = (int) $instrumentID;// force insID to int for security
        $videoimage = ""; // image to represent sound 

        // then we need an image to represent the mp3
         $resourcequery = \DB::table('resources')
        ->where('resourceType', '=', "image")  
        ->where('instrumentID', '=', $instrumentID)  
        ->take(1)->get();
        
        if (sizeof($resourcequery) > 0) {
         $videoimage = $resourcequery[0]->resourceFileName; 
        }

        // Fetch instrument data
         $instrument = \DB::table('instruments')
        ->where('instrumentID', '=', $instrumentID)  
        ->take(1)->get();

        if (sizeof($instrument) < 1) {
                  return \Redirect::action('AdminController@movedordeleted');   // REDIRECT IF NO RESULT FOR THIS INSRUMENT IF NO OBJECT
        }

        $status = $instrument[0]->status;
        $legalBodyID = $instrument[0]->legalBodyID;  
        $instrumentName = $instrument[0]->titlePreferred;  

        // if user is 'Admin', is this instrument in their collection?
        if ($role == "Admin") {
            $mylegalbody = \Session::get('legalBodyID');
               if ($mylegalbody != $legalBodyID) {
                  return \Redirect::action('AdminController@movedordeleted'); // REDIRECT, THIS IS NOT AN INSTRUMENT THIS ADMIN CAN EDIT
               }
        }

        // Fetch collection data
         $legalbody = \DB::table('legalbodies')
        ->where('legalBodyID', '=', $legalBodyID)  
        ->take(1)->get();

        $legalBodyName = $legalbody[0]->legalBodyName;  
        $legalBodyMDAcode = $legalbody[0]->legalBodyMDAcode;  

        // get user details and time for creation...
        $creationType = $instrument[0]->creationType;
        $creator_id = $instrument[0]->adminID;
        $instrument_created_at = new \Carbon\Carbon($instrument[0]->created_at);
        $now = \Carbon\Carbon::now();

        $instrument_creation_timeago=$instrument_created_at->diffForHumans(\Carbon\Carbon::now()); // get difference between now and collection creation
        $instrument_creation_timeago = str_replace("before","ago",$instrument_creation_timeago);  // carbon function returns 'before', let's replace it with 'ago'...

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

        // is this an approved term instrument?
         $thesaurusID = $instrument[0]->thesaurusID;  
         $legalBodyID = $instrument[0]->legalBodyID;  

        // Fetch legal body data
         $legalbodies = \DB::table('legalbodies')
        ->where('legalBodyID', '=', $legalBodyID)  
        ->take(1)->get();
         $legalBodyID = $legalbodies[0]->legalBodyID;  
         $legalBodyName = $legalbodies[0]->legalBodyName;  
         $legalBodyMDAcode = $legalbodies[0]->legalBodyMDAcode;  

        // Fetch repository data
         $repositories = \DB::table('repositories')
        ->where('instrumentID', '=', $instrumentID)  
        ->take(1)->get();

        $repositoryName = '';

        if (sizeof($repositories) != 0 )     // get repository 
        {
           $repositoryName = $repositories[0]->repositoryName;  
           $inventoryNumber = $repositories[0]->inventoryNumber;   
        }   


         if ($thesaurusID > 0) 
         {
           $thesaurus = \DB::table('thesauruses')
          ->where('thesaurusID', '=', $thesaurusID)  
          ->take(1)->get();

              // get levels for approved term for title
              // $Level_0 = $thesaurus[0]->Level_0;   // not required  
              $Level_1 = $thesaurus[0]->Level_1;  
              $Level_2 = $thesaurus[0]->Level_2;  
              $Level_3 = $thesaurus[0]->Level_3;  
              $titlePreferred = $Level_1." > ".$Level_2." > ".$Level_3;
              $titleSingle = $instrument[0]->titlePreferred;

         } else {

              // not an approved term - we can use the value straight from the database...
              $titleSingle = $instrument[0]->titlePreferred;
              $titlePreferred = $instrument[0]->titlePreferred;
              $Level_3 = $instrument[0]->titlePreferred;
         }
         
           // get 1 image for this instrument if exists
           $insimage = \DB::table('resources')
          ->where('instrumentID', '=', $instrumentID)  
          ->where('resourceType', '=', "image")  
          ->take(1)->get();

          if (sizeof($insimage) > 0) {
            $insimage = $insimage[0]->resourceFileName;
          } else {
            $insimage = "none";
          }  

           // get number of images for this instrument
           $imageCount = \DB::table('resources')
          ->where('instrumentID', '=', $instrumentID)  
          ->where('resourceType', '=', "image")  
          ->count();

           // get number of audio for this instrument
           $audioCount = \DB::table('resources')
          ->where('instrumentID', '=', $instrumentID)  
          ->where('resourceType', '=', "sound")  
          ->count();

           // get number of video for this instrument
           $videoCount = \DB::table('resources')
          ->where('instrumentID', '=', $instrumentID)  
          ->where('resourceType', '=', "video")  
          ->count();

        // Fetch collection data
         $legalbody = \DB::table('legalbodies')
        ->where('legalBodyID', '=', $legalBodyID)  
        ->take(1)->get();

        $legalBodyName = $legalbody[0]->legalBodyName;  
        $legalBodyMDAcode = $legalbody[0]->legalBodyMDAcode;  

        // fetch images for this instrument
         $videos = \DB::table('resources')
        ->where('instrumentID', '=', $instrumentID)  
        ->where('resourceType', '=', 'video')  
        ->get();

        $prod_event_actors=''; $other_event_actors='';

        // get production event actors for this instrument
         $prod_event_actors = \DB::table('eventactors')->select('eventActorID', 'eventActorName')->where('instrumentID', '=', $instrumentID)->get();
         
        // get events others than production for this instrument
         $events = \DB::table('events')
        ->where('instrumentID', '=', $instrumentID)  
        ->get();

        // iterate through other events
        foreach ($events as $key => $otherevent)
        {    
             $thisEventID = $otherevent->eventID;
             $other_event_actors[$thisEventID] = \DB::table('eventactors')->select('eventActorID', 'eventActorName')->where('eventID', '=', $thisEventID)->get();
        }     

       // get rights information for this instrument, if exists
       $rights = \DB::table('rights')
      ->where('instrumentID', '=', $instrumentID)  
      ->where('rightsFlag', '=', "instrument")  
      ->get();

        return view('admin.editvideo')->with([          
           'page' => 'editvideo',
           'role' => \Session::get('role'),
           'instrumentID' => $instrumentID,
           'legalBodyName' => $legalBodyName,
           'legalBodyMDAcode' => $legalBodyMDAcode,
           'legalBodyID' => $legalBodyID,
           'instrumentName' => $instrumentName,
           'videos' => $videos,
           'videoimage' => $videoimage,
           'thesaurusID' => $thesaurusID,   
           'titlePreferred' => $titlePreferred,  
           'titleSingle' => $titleSingle,  
           'repositoryName' => $repositoryName,  
           'inventoryNumber' => $inventoryNumber,
           'imageCount' => $imageCount, 
           'audioCount' => $audioCount, 
           'videoCount' => $videoCount, 
           'actorID' => '',
           'events' => $events,
           'eventID' => '',
           'eventType' => '',            
           'prod_event_actors' => $prod_event_actors,
           'other_event_actors' => $other_event_actors,
           'status' => $status,
           'insimage' => $insimage,
           'instrument_creation_timeago' => $instrument_creation_timeago,
           'creator_avatar' => $creator_avatar,
           'creator_name' => $creator_name,
           'rights' => $rights,
           'creationType' => $creationType,
           'sessiondata' => $sessiondata,
        ]);   
} // end editvideo



// deleteresource deletes the resource
public function deleteresource($instrumentID,$resourceID) {
        $user_id = Auth::user()->getId();
        $instrumentID = (int) $instrumentID; // force instrumentID to int for security
        $resourceID = (int) $resourceID; // force resourceID to int for security

        // then we need an image to represent the mp3
         $resourcequery = \DB::table('resources')
        ->where('resourceID', '=', $resourceID)  
        ->take(1)->get();

        if (sizeof($resourcequery) < 1) {
                  return \Redirect::action('AdminController@movedordeleted'); // redirect if no result for resource
        }

         $resourceType = $resourcequery[0]->resourceType;  
         $resourceFileName = $resourcequery[0]->resourceFileName;  

        // get instrument
         $instrument = \DB::table('instruments')
        ->where('instrumentID', '=', $instrumentID)  
        ->take(1)->get();

        if (sizeof($instrument) < 1) {
                  return \Redirect::action('AdminController@movedordeleted'); // redirect if no result for instrument
        }

         $legalBodyID = $instrument[0]->legalBodyID;  
         $instrumentName = $instrument[0]->titlePreferred; 

        // get collection
         $legalbodies = \DB::table('legalbodies')
        ->where('legalBodyID', '=', $legalBodyID)  
        ->take(1)->get();

        if (sizeof($legalbodies) < 1) {
                  return \Redirect::action('AdminController@movedordeleted'); // redirect if no result for collection
        }

         $legalBodyMDAcode = $legalbodies[0]->legalBodyMDAcode;  
         $legalBodyName = $legalbodies[0]->legalBodyName;  

         // now remove the record from the database...
          \DB::table('resources')->where('resourceID', '=', $resourceID)->delete();

         // now remove any rights info for this resource from the database...
          \DB::table('rights')->where('resourceID', '=', $resourceID)->delete();

         // set flash data to show deletion
          session()->flash('flashdata', "Instrument resource successfully deleted");
          $sessiondata = session()->all();

         // add delete resource activity
          \DB::table('user_activity')->insert(
            ['userID' => $user_id, 'resourceID' => $resourceID, 'activity' => "You deleted a ".$resourceType, 'instrumentID' => $instrumentID, 'instrumentName' => $instrumentName, 'legalBodyID' => $legalBodyID, 'legalBodyName' => $legalBodyName, 'activityDate' => \Carbon\Carbon::now() ]
          );

        // remove the files then redirect...
         if ($resourceType == "image")
         {
            \File::Delete('instrument_resources/images/'.$legalBodyMDAcode.'/'.$resourceFileName); // delete the image
            \File::Delete('instrument_resources/images/'.$legalBodyMDAcode.'/thumbnails/'.$resourceFileName); // delete image thumb if exists
             return \Redirect::action('Resources@editimages', array('instrumentID' => $instrumentID)); // redirect to image list
         }   

         if ($resourceType == "sound")
         {
            \File::Delete('instrument_resources/sound/'.$legalBodyMDAcode.'/'.$resourceFileName);  // delete the audio
             return \Redirect::action('Resources@editaudio', array('instrumentID' => $instrumentID)); // redirect to audio list
         }   

         if ($resourceType == "video")
         {
            \File::Delete('instrument_resources/video/'.$legalBodyMDAcode.'/'.$resourceFileName); // delete the video
             return \Redirect::action('Resources@editvideo', array('instrumentID' => $instrumentID));  // redirect to video list
         }   

} // end delete resource function



// edit resource shows edit form and info for all resources
public function editresource($instrumentID,$resourceID)
  {
      $sessiondata = session()->all();
      $instrumentID = (int) $instrumentID; // force insID to int for security
      $resourceID = (int) $resourceID; // force resourceID to int for security
      $soundimage = ""; // image to represent sound 
      $videoimage = ""; // image to represent video

      // Fetch instrument data
       $instrument = \DB::table('instruments')
      ->where('instrumentID', '=', $instrumentID)  
      ->take(1)->get();

      if (sizeof($instrument) < 1) {
            return \Redirect::action('AdminController@movedordeleted'); // redirect if no result for this instrument
      }

      $legalBodyID = $instrument[0]->legalBodyID;  
      $thesaurusID = $instrument[0]->thesaurusID;  
      $instrumentName = $instrument[0]->titlePreferred;  
      $status = $instrument[0]->status;


      if ($thesaurusID > 0) 
      {
          $thesaurus = \DB::table('thesauruses')
          ->where('thesaurusID', '=', $thesaurusID)  
          ->take(1)->get();

          // get levels for approved term for title
          // $Level_0 = $thesaurus[0]->Level_0;   // not required  
          $Level_1 = $thesaurus[0]->Level_1;  
          $Level_2 = $thesaurus[0]->Level_2;  
          $Level_3 = $thesaurus[0]->Level_3;  
          $titlePreferred = $Level_1." > ".$Level_2." > ".$Level_3;
          $titleSingle = $instrument[0]->titlePreferred;

      } else {

          // not an approved term - we can use the value straight from the database...
          $titleSingle = $instrument[0]->titlePreferred;
          $titlePreferred = $instrument[0]->titlePreferred;
          $Level_3 = $instrument[0]->titlePreferred;
      }

      // Fetch collection data
       $legalbody = \DB::table('legalbodies')
      ->where('legalBodyID', '=', $legalBodyID)  
      ->take(1)->get();

      $legalBodyName = $legalbody[0]->legalBodyName;  
      $legalBodyMDAcode = $legalbody[0]->legalBodyMDAcode;  

      // fetch resource for this instrument
       $resource = \DB::table('resources')
      ->where('resourceID', '=', $resourceID)  
      ->where('instrumentID', '=', $instrumentID)       
      ->take(1)->get();

      if (sizeof($resource) < 1) {
                return \Redirect::action('AdminController@movedordeleted');  // redirect if no resource
      }

      $resourceFileName = $resource[0]->resourceFileName;  
      $resourceType = $resource[0]->resourceType; 

      /* FIND FOLDERS */
      $image_path = public_path()."/instrument_resources/images/".$legalBodyMDAcode."/".$resourceFileName;
      $thumb_path = public_path()."/instrument_resources/images/".$legalBodyMDAcode."/thumbnails/".$resourceFileName;
      $sound_path = public_path()."/instrument_resources/sound/".$legalBodyMDAcode."/".$resourceFileName;
      $video_path = public_path()."/instrument_resources/video/".$legalBodyMDAcode."/".$resourceFileName;

      if ($resourceType == "image") {
            $resourcemessage = ""; //init message
            if (\File::exists($image_path))
            {           
                  $img = \Image::make($image_path);
                  $width = $img->width();
                  $height = $img->height();
                  $resourcemessage = "This image is ".$width." pixels wide and ".$height." pixels tall.";          
            } else {
                 $resourcemessage.= "Can't find this image...";
            }

            if (\File::exists($thumb_path))
            {
                  $img = \Image::make($thumb_path);
                  $width = $img->width();
                  $height = $img->height();
                  $resourcemessage.= " It also has a thumbnail which is ".$width." pixels wide and ".$height." pixels tall.";

            } else {
               $resourcemessage.= " This image has no thumbnail.";
            }
       } // end dealing with image


      if ($resourceType == "sound") { // then we need an image to represent the mp3
             $resourcequery = \DB::table('resources')
            ->where('resourceType', '=', "image")  
            ->where('instrumentID', '=', $instrumentID)  
            ->take(1)->get();

            if (sizeof($resourcequery) > 0) { // if there is an image, take first to represent the sound
             $soundimage = $resourcequery[0]->resourceFileName; 
            }

            if (\File::exists($sound_path))
            {
                  $bytes = \File::size($sound_path);
                  $mb = round($bytes/1000000,2);
                  $resourcemessage =  "This audio file is ".$mb." megabytes.";
            } else {
                  $resourcemessage = "Cannot find audio file.";
            }
       } // end dealing with sound


      if ($resourceType == "video") { // then we need an image to represent the video
         $resourcequery = \DB::table('resources')
        ->where('resourceType', '=', "image")  
        ->where('instrumentID', '=', $instrumentID)  
        ->take(1)->get();

        if (sizeof($resourcequery) > 0) { // if there is an image, take first to represent video
         $videoimage = $resourcequery[0]->resourceFileName;  
        }

            if (\File::exists($video_path))
            {
                  $bytes = \File::size($video_path);
                  $mb = round($bytes/1000000,2);

                 $resourcemessage =  "This video file is ".$mb." megabytes.";
            } else {
                 $resourcemessage = "Cannot find video file.";
            }
       } // end dealing with video

       // get number of images for this instrument
       $imageCount = \DB::table('resources')
      ->where('instrumentID', '=', $instrumentID)  
      ->where('resourceType', '=', "image")  
      ->count();

       // get number of audio for this instrument
       $audioCount = \DB::table('resources')
      ->where('instrumentID', '=', $instrumentID)  
      ->where('resourceType', '=', "sound")  
      ->count();

       // get number of video for this instrument
       $videoCount = \DB::table('resources')
      ->where('instrumentID', '=', $instrumentID)  
      ->where('resourceType', '=', "video")  
      ->count();

      // get user details and time for creation...
       $creationType = $instrument[0]->creationType;
       $creator_id = $instrument[0]->adminID;
       $instrument_created_at = new \Carbon\Carbon($instrument[0]->created_at);
       $now = \Carbon\Carbon::now();

       $instrument_creation_timeago=$instrument_created_at->diffForHumans(\Carbon\Carbon::now()); // get difference between now and collection creation
       $instrument_creation_timeago = str_replace("before","ago",$instrument_creation_timeago); // carbon function returns 'before', let's replace it with 'ago'...

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

        // is this an approved term instrument?
         $thesaurusID = $instrument[0]->thesaurusID;  
         $legalBodyID = $instrument[0]->legalBodyID;  

        // Fetch legal body data
         $legalbodies = \DB::table('legalbodies')
        ->where('legalBodyID', '=', $legalBodyID)  
        ->take(1)->get();
         $legalBodyID = $legalbodies[0]->legalBodyID;  
         $legalBodyName = $legalbodies[0]->legalBodyName;  
         $legalBodyMDAcode = $legalbodies[0]->legalBodyMDAcode;  

        // Fetch repository data
         $repositories = \DB::table('repositories')
        ->where('instrumentID', '=', $instrumentID)  
        ->take(1)->get();

        $repositoryName = ''; // init
        if (sizeof($repositories) != 0 )     // get repository 
        {
           $repositoryName = $repositories[0]->repositoryName;  
           $inventoryNumber = $repositories[0]->inventoryNumber;   
        }   

        // if approved term instrument
        if ($thesaurusID > 0) 
        {
           $thesaurus = \DB::table('thesauruses')
          ->where('thesaurusID', '=', $thesaurusID)  
          ->take(1)->get();

              // get levels for approved term for title
              // $Level_0 = $thesaurus[0]->Level_0;   // not required  
              $Level_1 = $thesaurus[0]->Level_1;  
              $Level_2 = $thesaurus[0]->Level_2;  
              $Level_3 = $thesaurus[0]->Level_3;  
              $titlePreferred = $Level_1." > ".$Level_2." > ".$Level_3;
              $titleSingle = $instrument[0]->titlePreferred;

         } else {

              // not an approved term - we can use the value straight from the database...
              $titleSingle = $instrument[0]->titlePreferred;
              $titlePreferred = $instrument[0]->titlePreferred;
              $Level_3 = $instrument[0]->titlePreferred;
         }
         
          // get 1 image for this instrument if exists
          $insimage = \DB::table('resources')
          ->where('instrumentID', '=', $instrumentID)  
          ->where('resourceType', '=', "image")  
          ->take(1)->get();

          if (sizeof($insimage) > 0) {
            $insimage = $insimage[0]->resourceFileName;
          } else {
            $insimage = "none";
          }  

        $prod_event_actors=''; $other_event_actors=''; // init

        // get production event actors for this instrument
         $prod_event_actors = \DB::table('eventactors')->select('eventActorID', 'eventActorName')->where('instrumentID', '=', $instrumentID)->get();

        // get events others than production for this instrument
         $events = \DB::table('events')
        ->where('instrumentID', '=', $instrumentID)  
        ->get();

        // iterate through other events
        foreach ($events as $key => $otherevent)
        {    
             $thisEventID = $otherevent->eventID;
             $other_event_actors[$thisEventID] = \DB::table('eventactors')->select('eventActorID', 'eventActorName')->where('eventID', '=', $thisEventID)->get();
        }     

       // get rights information for this instrument, if exists
       $rights = \DB::table('rights')
      ->where('instrumentID', '=', $instrumentID)  
      ->where('rightsFlag', '=', "instrument")  
      ->get();

       // get rights information for this instrument, if exists
       $resourceRights = \DB::table('rights')
      ->where('instrumentID', '=', $instrumentID)  
      ->where('resourceID', '=', $resourceID)  
      ->get();

        return view('admin.editresource')->with([          
           'page' => 'editresource',
           'role' => \Session::get('role'),
           'instrumentID' => $instrumentID,
           'legalBodyName' => $legalBodyName,
           'legalBodyMDAcode' => $legalBodyMDAcode,
           'legalBodyID' => $legalBodyID,
           'instrumentName' => $instrumentName,
           'titlePreferred' => $titlePreferred,  
           'titleSingle' => $titleSingle,           
           'resourcemessage' => $resourcemessage,
           'resourceOrigFileName' => $resourceFileName,
           'resourceOrigFileType' => $resourceType,
           'soundimage' => $soundimage,
           'videoimage' => $videoimage,
           'resource' => $resource,
           'imageCount' => $imageCount, 
           'audioCount' => $audioCount, 
           'videoCount' => $videoCount, 
           'actorID' => '',
           'insimage' => $insimage,
           'instrumentName' => $instrumentName,   
           'thesaurusID' => $thesaurusID,   
           'repositoryName' => $repositoryName,  
           'inventoryNumber' => $inventoryNumber,
           'events' => $events,
           'eventID' => '',
           'eventType' => '',                    
           'prod_event_actors' => $prod_event_actors,
           'other_event_actors' => $other_event_actors,
           'status' => $status,
           'instrument_creation_timeago' => $instrument_creation_timeago,
           'creator_avatar' => $creator_avatar,
           'creator_name' => $creator_name,
           'rights' => $rights,
           'resourceRights' => $resourceRights,   
           'creationType' => $creationType,
           'sessiondata' => $sessiondata
        ]);   
    } // end edit resource




	// used to store edited resources
	public function storeresource()
	    {
	      // the largest size to contstrain either width or height for thumbnails (should be in config...)
	      $largest_thumbnail_pixels = Config::get('app.instrument_thumb_max');
	      $role = \Session::get('role');
	      $user_id = Auth::user()->getId(); // needed for activity
	     
	      if ($role != "SuperAdmin" && $role != "Cataloguer" && $role != "Admin")
	      {
	          return \Redirect::action('AdminController@index'); // REDIRECT TO DASHBOARD IF NOT SUPERADMIN,CATALOGUER OR ADMIN
	      }

	      // get all input values  
	      $input = Input::all();
	      $resourceID = (int) Input::get('resourceID'); // forcing the id into an int       
	      $instrumentID = (int) Input::get('instrumentID'); // forcing the id into an int   
	      $resourceOrigFileType = Input::get('resourceOrigFileType'); // get the original file type for this resource from form
	      $resourceOrigFileName = Input::get('resourceOrigFileName'); // get the original filename for this resource from form
	      $resourceCaption = Input::get('resourceCaption'); // get the caption
	      $fileName = $resourceOrigFileName; // default to original resource from form
	      
	      // set activity text
	      if ($resourceOrigFileType == "image") {
	      $activity_text = "You updated an image resource"; // init
	      } elseif ($resourceOrigFileType == "sound") {
	      $activity_text = "You updated an audio resource"; // init
	      } elseif ($resourceOrigFileType == "video") {
	      $activity_text = "You updated a video resource"; // init
	      }
	      
	      // Fetch instrument data
	      $instrument = \DB::table('instruments')
	      ->where('instrumentID', '=', $instrumentID)  
	      ->take(1)->get();

	      if (sizeof($instrument) < 1) {
	                return \Redirect::action('AdminController@movedordeleted'); // redirect if no result for instrument
	      }

	      $legalBodyID = $instrument[0]->legalBodyID;  
	      $instrumentName = $instrument[0]->titlePreferred;  

	      // Fetch legal body data
	      $legalbody = \DB::table('legalbodies')
	      ->where('legalBodyID', '=', $legalBodyID)  
	      ->take(1)->get();

	      $legalBodyMDAcode = $legalbody[0]->legalBodyMDAcode;  
	      $legalBodyName = $legalbody[0]->legalBodyName;  

	      if (sizeof($legalbody) < 1) {
	        return \Redirect::action('AdminController@movedordeleted'); // redirect if collection doesn't exist
	      }

	      $upload_type = "";
	      if (Input::file('resource')) {
	            $extension = strtolower(Input::file('resource')->getClientOriginalExtension()); 
	            if (($extension == "jpg" || $extension == "png" || $extension == "gif"))
	            {
	                $upload_type="image";
	            }

	            if (($extension == "mp3" || $extension == "wav" || $extension == "aiff"))
	            {
	                $upload_type="sound";
	            }

	            if (($extension == "mp4"))
	            {
	                $upload_type="video";
	            }
	      }

	    if ($upload_type == "image")
	    {
	      if (Input::file('resource')) {
	            $destinationPath = 'instrument_resources/images/'.$legalBodyMDAcode; // upload path
	            $extension = strtolower(Input::file('resource')->getClientOriginalExtension());  // getting image extension          
	            $fileName = md5($legalBodyMDAcode.microtime()).'.'.$extension; // using md5 and microtime for filenames
	            Input::file('resource')->move($destinationPath, $fileName); // uploading file to given path

	            // create instance to make thumbnail from
	            $img = \Image::make(public_path().'/instrument_resources/images/'.$legalBodyMDAcode.'/'.$fileName);
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
 
               // save the image			
               $img->save('instrument_resources/images/'.$legalBodyMDAcode.'/thumbnails/'.$fileName);

	           // delete original image 
	            \File::Delete('instrument_resources/images/'.$legalBodyMDAcode.'/'.$resourceOrigFileName);

	           // ... and delete thumbnail if it is there
	            \File::Delete('instrument_resources/images/'.$legalBodyMDAcode.'/thumbnails/'.$resourceOrigFileName);

	      } else {
	            // the user has not uploaded a resource... keep it the same as orig
	            $fileName = $resourceOrigFileName;
	      }    
	    } // end dealing with image....

	    if ($upload_type == "sound")
	    {
	      if (Input::file('resource')) {
	            $destinationPath = 'instrument_resources/sound/'.$legalBodyMDAcode; // upload path
	            $extension = strtolower(Input::file('resource')->getClientOriginalExtension());  // getting image extension
	            $fileName = md5($legalBodyMDAcode.microtime()).'.'.$extension; // using md5 and microtime for filenames
	            Input::file('resource')->move($destinationPath, $fileName); // uploading file to given path

	            // delete original file as they have uploaded a new onme
	            \File::Delete('instrument_resources/sound/'.$legalBodyMDAcode.'/'.$resourceOrigFileName);
	 
	      } else {
	            // the user has not uploaded a resource... keep it the same as orig
	            $fileName = $resourceOrigFileName;
	      }   
	    } // end dealing with sound

	    if ($upload_type == "video")
	    {
	      if (Input::file('resource')) {
	            $destinationPath = 'instrument_resources/video/'.$legalBodyMDAcode; // upload path
	            $extension = strtolower(Input::file('resource')->getClientOriginalExtension()); // getting image extension
	            $fileName = md5($legalBodyMDAcode.microtime()).'.'.$extension; // using md5 and microtime for filenames
	            Input::file('resource')->move($destinationPath, $fileName); // uploading file to given path

	            // delete original resource as they have uploaded a new one
	            \File::Delete('instrument_resources/video/'.$legalBodyMDAcode.'/'.$resourceOrigFileName);
	 
	      } else {
	            // the user has not uploaded a resource... keep it the same as orig
	            $fileName = $resourceOrigFileName;
	      }   
	    } // end dealing with sound

	    // add flash message
	     session()->flash('flashdata', $activity_text); 

	    // update this resource
	    \DB::table('resources')
	     ->where('resourceID', $resourceID)
	     ->where('instrumentID', $instrumentID)
	     ->update(array('resourceCaption' => $resourceCaption, 'resourceFileName' => $fileName, 'updated_at' => \Carbon\Carbon::now()));

	    // add update resource activity
	    \DB::table('user_activity')->insert(
	      ['userID' => $user_id, 'resourceID' => $resourceID, 'activity' => $activity_text, 'instrumentID' => $instrumentID, 'instrumentName' => $instrumentName, 'legalBodyID' => $legalBodyID, 'legalBodyName' => $legalBodyName, 'activityDate' => \Carbon\Carbon::now() ]
	    );

	    // redirect back to the edit resource after update
	    return \Redirect::action('Resources@editresource', array('instrumentID' => $instrumentID, 'resourceID' => $resourceID));

	} // end used to store edited resources



	// function to insert new resource
	public function insertresource()
	    {
	      $role = \Session::get('role');
	      if ($role != "SuperAdmin" && $role != "Cataloguer" && $role != "Admin")
	      {
	          return \Redirect::action('AdminController@index');  // redirect to dash if not SuperAdmin, Cataloguer or Admin
	      }

	      // get all input values  
	      $input = Input::all();       // print_r($input);
	      $instrumentID = (int) Input::get('instrumentID'); // forcing the id into an int   
	      $resourceCaption = Input::get('resourceCaption'); // get the caption
	      $user_id = Auth::user()->getId(); // needed for activity

	      // the largest size to contstrain either width or height for thumbnails (should be in config...)
	      $largest_thumbnail_pixels = Config::get('app.instrument_thumb_max');

	      // Fetch instrument data
	      $instrument = \DB::table('instruments')
	      ->where('instrumentID', '=', $instrumentID)  
	      ->take(1)->get();

	      if (sizeof($instrument) < 1) {
	               return \Redirect::action('AdminController@movedordeleted'); // redirect if no result for instrument
	      }

	      $legalBodyID = $instrument[0]->legalBodyID;  
	      $instrumentName = $instrument[0]->titlePreferred; 

	      // Fetch legal body data
	      $legalbody = \DB::table('legalbodies')
	      ->where('legalBodyID', '=', $legalBodyID)  
	      ->take(1)->get();

	      $legalBodyMDAcode = $legalbody[0]->legalBodyMDAcode;  
	      $legalBodyName = $legalbody[0]->legalBodyName;  

	      if (sizeof($legalbody) < 1) {
	        return \Redirect::action('AdminController@movedordeleted'); // redirect if no result for collection
	      }

	      $upload_type = "";
	      if (Input::file('resource')) { // get extension for uploaded file
	            $extension = strtolower(Input::file('resource')->getClientOriginalExtension()); 
	            if (($extension == "jpg" || $extension == "png" || $extension == "gif"))
	            {
	                $upload_type="image";
	            }

	            if (($extension == "mp3" || $extension == "wav" || $extension == "aiff"))
	            {
	                $upload_type="sound";
	            }

	            if (($extension == "mp4"))
	            {
	                $upload_type="video";
	            }
	      }

	      $activity_text = "";
	      if ($upload_type == "image")
	      {
	            if (Input::file('resource')) {
	                  $destinationPath = 'instrument_resources/images/'.$legalBodyMDAcode; // upload path
	                  $extension = strtolower(Input::file('resource')->getClientOriginalExtension()); // getting image extension                  
	                  $fileName = md5($legalBodyMDAcode.microtime()).'.'.$extension; // using md5 and microtime for filenames
	                  Input::file('resource')->move($destinationPath, $fileName); // uploading file to given path

	                  // create thumbnail for this image
	                  $img = \Image::make(public_path().'/instrument_resources/images/'.$legalBodyMDAcode.'/'.$fileName);

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
	                   
	                  $img->save('instrument_resources/images/'.$legalBodyMDAcode.'/thumbnails/'.$fileName);

	            } else {
	                  // the user has not uploaded a resource... just initialise the filename...
	                  $fileName = "";
	            }    

	            $activity_text = 'You added an image';  // for flashdata
	      } // end dealing with image....

	      if ($upload_type == "sound")
	      {
	            if (Input::file('resource')) {
	                  $destinationPath = 'instrument_resources/sound/'.$legalBodyMDAcode; // upload path
	                  $extension = strtolower(Input::file('resource')->getClientOriginalExtension());  // getting image extension
	                  $fileName = md5($legalBodyMDAcode.microtime()).'.'.$extension; // using md5 and microtime for filenames
	                  Input::file('resource')->move($destinationPath, $fileName); // uploading file to given path

	            } else {
	                  // the user has not uploaded a resource... just initialise the filename
	                  $fileName = "";
	            }   

	            $activity_text = 'You added audio'; // for flashdata
	      } // end dealing with sound

	      if ($upload_type == "video")
	      {
	            if (Input::file('resource')) {
	                  $destinationPath = 'instrument_resources/video/'.$legalBodyMDAcode; // upload path
	                  $extension = strtolower(Input::file('resource')->getClientOriginalExtension()); // getting image extension
	                  $fileName = md5($legalBodyMDAcode.microtime()).'.'.$extension; // using md5 and microtime for filenames
	                  Input::file('resource')->move($destinationPath, $fileName); // uploading file to given path
	       
	            } else {
	                  // the user has not uploaded a resource... just initialise the filename..
	                  $fileName = "";
	            }   

	            $activity_text = 'You added a video'; // for flashdata
	      } // end dealing with video

	      session()->flash('flashdata', $activity_text);

	      if (($upload_type == "") && (strlen($resourceCaption < 1)))
	      {
	                  // they've not uploaded any file, redirect back to the edit resource after update
	                  return \Redirect::action('Resources@addresource', array('instrumentID' => $instrumentID));
	      } else {

	                  // insert new resource
	                  $resourceID = \DB::table('resources')->insertGetId(
	                     ['instrumentID' => $instrumentID, 'legalBodyID' => $legalBodyID, 'resourceType' => $upload_type, 'resourceCaption' => $resourceCaption, 'resourceFileName' => $fileName, 'created_at' => \Carbon\Carbon::now() ]
	                  );

	                  // add insert resource activity
	                  \DB::table('user_activity')->insert(
	                  ['userID' => $user_id, 'resourceID' => $resourceID, 'activity' => $activity_text,  'instrumentID' => $instrumentID, 'instrumentName' => $instrumentName, 'resourceName' => $upload_type, 'legalBodyID' => $legalBodyID, 'legalBodyName' => $legalBodyName, 'activityDate' => \Carbon\Carbon::now() ]
	                  );

	                 // redirect back to the edit resource after update
	                 return \Redirect::action('Resources@editresource', array('instrumentID' => $instrumentID, 'resourceID' => $resourceID));

	        } // end dealing with upload/caption
	} // end insert new resource





    public function resourcerights($resourceID)
    {
        $sessiondata = session()->all();
        $role = \Session::get('role');
        $instrumentID = (int) $instrumentID; // force int

        // Fetch instrument data
         $instrument = \DB::table('instruments')
        ->where('instrumentID', '=', $instrumentID)  
        ->take(1)->get();

        if (sizeof($instrument) < 1) {
                  return \Redirect::action('AdminController@movedordeleted'); // REDIRECT IF NO RESULT FOR THIS INSRUMENT
        }

        $legalBodyID = $instrument[0]->legalBodyID;  

        // if user is 'Admin', is this instrument in their collection?
        if ($role == "Admin") {
            $mylegalbody = \Session::get('legalBodyID');
               if ($mylegalbody != $legalBodyID) {
                  return \Redirect::action('AdminController@movedordeleted'); // REDIRECT, THIS IS NOT AN INSTRUMENT THIS ADMIN CAN EDIT
               }
        }

        // instrument details
        $status = $instrument[0]->status;
        $thesaurusID = $instrument[0]->thesaurusID;  
        $hornbostelID = $instrument[0]->hornbostelID;          


        // get events others than production for this instrument
         $events = \DB::table('events')
        ->where('instrumentID', '=', $instrumentID)  
        ->get();

        // get hornbostel definition text if id is set
         $hornbostel = \DB::table('hornbostel')
        ->where('id', '=', $hornbostelID)  
        ->get();

        // value for hornbostel form field
        if (sizeof($hornbostel) > 0) { // if id already set, and result
           $hornbostelCat = $hornbostel[0]->label; // already has a hornbostel category, use value for field
        } else {
           $hornbostelCat = $instrument[0]->hornbostelCat; // use text field that could have been imported from xml or submitted unclassified
        }

        // get user details and time for creation...
        $creationType = $instrument[0]->creationType;
        $creator_id = $instrument[0]->adminID;
        $instrument_created_at = new \Carbon\Carbon($instrument[0]->created_at);
        $now = \Carbon\Carbon::now();

        $instrument_creation_timeago=$instrument_created_at->diffForHumans(\Carbon\Carbon::now()); // get difference between now and collection creation
        $instrument_creation_timeago = str_replace("before","ago",$instrument_creation_timeago); // carbon function returns 'before', let's replace it with 'ago'...

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

        // Fetch collection data
         $legalbodies = \DB::table('legalbodies')
        ->where('legalBodyID', '=', $legalBodyID)  
        ->take(1)->get();

        // redirect to moved or deleted if no collection
        if (sizeof($legalbodies) < 1) {
                  return \Redirect::action('AdminController@movedordeleted');
        }

        $legalBodyID = $legalbodies[0]->legalBodyID;  
        $legalBodyName = $legalbodies[0]->legalBodyName;  
        $legalBodyMDAcode = $legalbodies[0]->legalBodyMDAcode;  

        // Fetch repository data
        $repositoryName = '';
        $repositories = \DB::table('repositories')
        ->where('instrumentID', '=', $instrumentID)  
        ->take(1)->get();

        if (sizeof($repositories) != 0 ) // get repository 
        {
          $repositoryName = $repositories[0]->repositoryName;  
          $inventoryNumber = $repositories[0]->inventoryNumber;   
        }   

        // is this an approved term instrument?
        if ($thesaurusID > 0) 
        {
           $thesaurus = \DB::table('thesauruses')
          ->where('thesaurusID', '=', $thesaurusID)  
          ->take(1)->get();
               // $Level_0 = $thesaurus[0]->Level_0;   // not required (always 'Instrument') 
               $Level_1 = $thesaurus[0]->Level_1;  
               $Level_2 = $thesaurus[0]->Level_2;  
               $Level_3 = $thesaurus[0]->Level_3;  
               $titlePreferred = $Level_1." > ".$Level_2." > ".$Level_3;
               $titleSingle = $instrument[0]->titlePreferred;
               $instrument_classifiedTitlePreferred = $instrument[0]->titlePreferred;

         } else {
              // not an approved term - we can use the value straight from the database...
              $titleSingle = $instrument[0]->titlePreferred;
              $titlePreferred = $instrument[0]->titlePreferred;
              $Level_3 = $instrument[0]->titlePreferred;
              $instrument_classifiedTitlePreferred = $instrument[0]->titlePreferred;
         }
         
           // get 1 image for this instrument if exists
           $insimage = \DB::table('resources')
          ->where('instrumentID', '=', $instrumentID)  
          ->where('resourceType', '=', "image")  
          ->take(1)->get();

          if (sizeof($insimage) > 0) {
            $insimage = $insimage[0]->resourceFileName;
          } else {
            $insimage = "none";
          }  

           // get number of images for this instrument
           $imageCount = \DB::table('resources')
          ->where('instrumentID', '=', $instrumentID)  
          ->where('resourceType', '=', "image")  
          ->count();

           // get number of audio for this instrument
           $audioCount = \DB::table('resources')
          ->where('instrumentID', '=', $instrumentID)  
          ->where('resourceType', '=', "sound")  
          ->count();

           // get number of video for this instrument
           $videoCount = \DB::table('resources')
          ->where('instrumentID', '=', $instrumentID)  
          ->where('resourceType', '=', "video")  
          ->count();

          $prod_event_actors=''; $other_event_actors='';

         // get production event actors for this instrument
         $prod_event_actors = \DB::table('eventactors')->select('eventActorID', 'eventActorName')->where('instrumentID', '=', $instrumentID)->get();
         
         // get events others than production for this instrument
         $events = \DB::table('events')
        ->where('instrumentID', '=', $instrumentID)  
        ->get();

        // iteratre through other events
        foreach ($events as $key => $otherevent)
        {    
             $thisEventID = $otherevent->eventID;
             $other_event_actors[$thisEventID] = \DB::table('eventactors')->select('eventActorID', 'eventActorName')->where('eventID', '=', $thisEventID)->get();
        }     

       // get rights information for this instrument, if exists
       $rights = \DB::table('rights')
      ->where('instrumentID', '=', $instrumentID)  
      ->where('rightsFlag', '=', "instrument")  
      ->get();

        // Fetch description(s) data
         $descriptions = \DB::table('descriptions')
        ->where('instrumentID', '=', $instrumentID)  
        ->get();


        return view('admin.instrumentrights')->with([          
           'page' => 'instrumentrights',
           'role' => \Session::get('role'),
           'instrumentID' => $instrumentID, 
           'thesaurusID' => $thesaurusID,   
           'hornbostelID' => $hornbostelID,
           'titlePreferred' => $titlePreferred,  
           'titleSingle' => $titleSingle,  
           'instrument_classifiedTitlePreferred' => $instrument_classifiedTitlePreferred,
           'mainDescriptionType' => $instrument[0]->mainDescriptionType,
           'mainDescriptionSource' => $instrument[0]->mainDescriptionSource,
           'mainDescriptionText' => $instrument[0]->mainDescriptionText,
           'descriptions' => $descriptions,
           'repositoryName' => $repositoryName,  
           'inventoryNumber' => $inventoryNumber,
           'hornbostelCat' => $hornbostelCat,
           'legalBodyID' => $legalBodyID, 
           'legalBodyName' => $legalBodyName, 
           'legalBodyMDAcode' => $legalBodyMDAcode,
           'imageCount' => $imageCount, 
           'audioCount' => $audioCount, 
           'videoCount' => $videoCount, 
           'descriptions' => $descriptions,
           'actorID' => '',
           'events' => $events,
           'eventID' => '',
           'prod_event_actors' => $prod_event_actors,
           'other_event_actors' => $other_event_actors,
           'status' => $status,
           'insimage' => $insimage,
           'instrument_creation_timeago' => $instrument_creation_timeago,
           'rights' => $rights,
           'creator_avatar' => $creator_avatar,
           'creator_name' => $creator_name,
           'creationType' => $creationType,
           'sessiondata' => $sessiondata
        ]);  

    }




} // end class    