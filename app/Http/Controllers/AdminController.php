<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Input;
use Response;
use App\Http\Requests;
use Illuminate\Http\Request;
use Auth;
use Config;

class AdminController extends Controller
{
/**
* Create a new controller instance.
*
* @return void
*/
public function __construct()
{  
        $this->middleware('auth');
            
           if (Auth::check()) // if they are logged in, pull their unique user_id from users table..
           {
             $user_id = Auth::user()->getId();

                       session()->put('user_id', $user_id);  

                       // get users admin account details
                       $admin_user_info = \DB::select('select * from users where id = ?', [$user_id]);

                       // now that scaffolding is there, add other vars to session...
                       session()->put('forename', $admin_user_info[0]->name);
                       session()->put('surname', $admin_user_info[0]->surname);
                       session()->put('role', $admin_user_info[0]->role);
                       session()->put('email', $admin_user_info[0]->email);
                       session()->put('avatar', $admin_user_info[0]->avatar);    
                       $last_login = new \Carbon\Carbon($admin_user_info[0]->last_login);
                       $last_login = $last_login->format('l jS \\of F Y h:i:s A'); 
                       session()->put('last_login', $last_login);                   
                       session()->put('legalBodyID', $admin_user_info[0]->legalBodyID);

                       // get this user's legal body info
                       $legal_body_info = \DB::select('select * from legalbodies where legalBodyID = ?', [$admin_user_info[0]->legalBodyID]);

                       if (sizeof($legal_body_info) < 1) { // Can't find admin user's collection, refuse continue:exit
                         echo "Can't find collection. Please email your administrator. <a href='/logout'>Logout</a>"; exit;
                       }

                       session()->put('legalBodyName', $legal_body_info[0]->legalBodyName);
                       session()->put('legalBodyShortName', $legal_body_info[0]->legalBodyShortName);  
          };

} // end construct



/**
 * Show the MINIM admin dashboard.
 *
 * @return \Illuminate\Http\Response
 */
public function index()
{
        $user_id = Auth::user()->getId();
        $sessiondata = session()->all();

        // Fetch last 20 activity data for this user
         $admin_user_activity = \DB::table('user_activity')
        ->where('userID', '=', $user_id)  
         ->orderBy('activityDate', 'desc')   
        ->take(20)->get();

        // initialise var in case no activity
        $activity_time_ago="";

        $i=0; // loop to create time ago array for activities
        foreach($admin_user_activity as $user_activity){
                $created_at = new \Carbon\Carbon($user_activity->activityDate);
                $now = \Carbon\Carbon::now();
                $timeago[$i]=$created_at->diffForHumans(\Carbon\Carbon::now()); // get difference between now and activity_date
                $timeago[$i] = str_replace("before","ago",$timeago[$i]); // carbon function returns 'before', let's replace it with 'ago'...
                $activity_time_ago[$i] = $timeago[$i]; // user activity
           $i++;
        } // end foreach

        return view('admin.dashboard')->with([          
           'page' => 'dashboard',
           'role' => \Session::get('role'),
           'sessiondata' => $sessiondata,
           'activity_time_ago' => $activity_time_ago,
           'admin_user_activity' => $admin_user_activity
        ]);    

} // end dashboard function



public function addinstrument($legalBodyID='')
{
        $sessiondata = session()->all();

        // Fetch collection choice to add instrument to 
        $legalBodyID = (int) $legalBodyID; // force int

        if ($legalBodyID < 1)  // go with users default from session if not sent to function
        {
            $legalBodyID = \Session::get('legalBodyID');
        }

        $thislegalbody = \DB::table('legalbodies')
        ->where('legalBodyID', '=', $legalBodyID)  
        ->take(1)->get();

        if (sizeof($thislegalbody) < 1) { // redirect if no result for collection
                  return \Redirect::action('AdminController@movedordeleted');
        }

        $defaultRepositoryName = $thislegalbody[0]->legalBodyDefaultRepository;            

        $legalbodies = \DB::table('legalbodies') // get all collections
        ->orderBy('legalBodyName', 'asc')
        ->get();

        return view('admin.addinstrument')->with([          
           'page' => 'addinstrument',
           'role' => \Session::get('role'),
           'legalbodies' => $legalbodies,
           'legalBodyID' => $legalBodyID,
           'defaultRepositoryName' => $defaultRepositoryName,
           'sessiondata' => $sessiondata
        ]);    

} // end add instrument function



      
// function to store newly added instruments
public function storeinstrument(Request $request)
    {
      $this->validate($request, [
        'mainDescriptionType' => 'required',
        'mainDescriptionText' => 'required|min:10|max:5000',
        'productionEventEarliestDate' => 'required|numeric', // year
        'productionEventLatestDate' => 'required|numeric',   // year
        'productionEventLocation' => 'required',
      ]);

      $sessiondata = session()->all();
      $user_id = Auth::user()->getId();
    
      // get all input values  
      $input = Input::all();      
      $legalBodyID = (int) Input::get('legalBodyID');      
      $instrument_name = Input::get('instrument_name');
      $instrument_classifiedTitlePreferred = Input::get('instrument_classifiedTitlePreferred');
      $measurements_freetext = Input::get('measurements_freetext');
      $thesaurusID = (int) Input::get('thesaurusID');   // hidden input from instrument thesaurus autocomplete
      $hornbostelID = (int) Input::get('hornbostelID'); // hidden input from hornbostel autocomplete
      $cityID = Input::get('cityID');                   // hidden input from city autocomplete   
      $repositoryName = Input::get('repositoryName');
      $inventoryNumber = Input::get('inventoryNumber');
      $hornbostelCat = Input::get('hornbostelCat');
      $mainDescriptionType = Input::get('mainDescriptionType');
      $mainDescriptionSource = Input::get('mainDescriptionSource');
      $mainDescriptionText = Input::get('mainDescriptionText');
      $productionEventLocation = htmlentities(Input::get('productionEventLocation')); // html entities due to characters in thesaurus
      $productionEventCulture = Input::get('productionEventCulture');
      $productionEventEarliestDate = Input::get('productionEventEarliestDate');
      $productionEventLatestDate = Input::get('productionEventLatestDate');
      $productionPeriodName = Input::get('productionPeriodName');
      //$productionMaterialsFreeText = Input::get('productionMaterialsFreeText');
        
      // check collection exists
      $legalbody = \DB::table('legalbodies')
      ->where('legalBodyID', '=', $legalBodyID)  
      ->take(1)->get();

      if (sizeof($legalbody) < 1) {
                return \Redirect::action('AdminController@movedordeleted'); // redirect if no collection
      }

      $legalBodyName = $legalbody[0]->legalBodyName;  

      // generate serial pipe separated string
      $serialEditionNumbersString='';
      foreach ((Input::get('serialnumbers')) as $key => $value) {
            $serialEditionNumbersString = $serialEditionNumbersString . $value['serialnumber'].'|';
      }

      // generate inscriptions pipe separated string
      $inscriptionsString='';
      foreach ((Input::get('inscriptions')) as $key => $value) {
            $inscriptionsString = $inscriptionsString . $value['inscription'].'|';
      }

      // generate descriptive elements pipe separated string
      $decorativesString='';
      foreach ((Input::get('decoratives')) as $key => $value) {
          $decorativesString = $decorativesString . $value['decorative'].'|';
      }
      
      // remove last pipes for dbase.
      $serialEditionNumbersString = rtrim($serialEditionNumbersString, "|"); 
      $inscriptionsString = rtrim($inscriptionsString, "|"); 
      $decorativesString = rtrim($decorativesString, "|");   

      if ($thesaurusID) // approved instrument, get thesaurus info
      {  
             $thesaurus_info = \DB::select('select Level_0, Level_1, Level_2, Level_3 from thesauruses where thesaurusID = ?', [$thesaurusID]);
             $Level_0 = $thesaurus_info[0]->Level_0;
             $Level_1 = $thesaurus_info[0]->Level_1;
             $Level_2 = $thesaurus_info[0]->Level_2;
             $Level_3 = $thesaurus_info[0]->Level_3;
             $instrument_name = ""; // reset instrument name as this is going to use thesaurus and we will get topmost defined level in thesaurus 
             $instrument_tags = $Level_0.','.$Level_1.','.$Level_2.','.$Level_3; // all contextual hierarchy from thesaurus as this is an approved term

              // then level 0 is instrument name
              $instrument_name = $Level_0;
              if (strlen($Level_1) > 0) 
              {
                $instrument_name = $Level_1; // then level 1 is instrument name
              }
              
              if (strlen($Level_2) > 0) 
              {
                $instrument_name = $Level_2; // then level 2 is instrument name 
              }
             
              if (strlen($Level_3) > 0) 
              {
                $instrument_name = $Level_3; // then level 3 is instrument name
              }              
            
              // this is a classified instrument, if user has specified a more specific name for this instrument, use that instead..
              if (strlen($instrument_classifiedTitlePreferred) > 0) 
              {
                 $instrument_name = ucfirst($instrument_classifiedTitlePreferred);
              }  

              // insert instrument basics with thesaurus id
              $instrumentID = \DB::table('instruments')->insertGetId(
                 ['adminID' => $user_id, 'creationType' => 'created', 'status' => 'not live', 'legalBodyID' => $legalBodyID, 'cityID' => $cityID, 'thesaurusID' => $thesaurusID, 'hornbostelID' => $hornbostelID, 'hornbostelCat' => $hornbostelCat,  'titlePreferred' => $instrument_name, 'Level_0' => $Level_0, 'Level_1' => $Level_1, 'Level_2' => $Level_2, 'Level_3' => $Level_3, 'tags' => $instrument_tags, 'mainDescriptionType' => $mainDescriptionType, 'mainDescriptionSource' => $mainDescriptionSource, 'mainDescriptionText' => $mainDescriptionText, 'productionEventLocation' => $productionEventLocation, 'productionEventCulture' => $productionEventCulture, 'productionEventEarliestDate' => $productionEventEarliestDate, 'productionEventLatestDate' => $productionEventLatestDate, 'productionPeriodName' => $productionPeriodName, 'measurementsFreeText' => $measurements_freetext, 'inscriptions' => $inscriptionsString, 'serialEditionNumbers' => $serialEditionNumbersString, 'decorativeElements' => $decorativesString, 'created_at' => \Carbon\Carbon::now()]
              );

        } else { // insert instrument basics without thesaurus id
            
                $instrument_name = ucfirst($instrument_name); // make sure the submitted instrument name is capitalised
                $instrumentID = \DB::table('instruments')->insertGetId(
                   ['adminID' => $user_id, 'creationType' => 'created', 'status' => 'not live', 'legalBodyID' => $legalBodyID, 'cityID' => $cityID, 'hornbostelID' => $hornbostelID, 'hornbostelCat' => $hornbostelCat, 'titlePreferred' => $instrument_name, 'mainDescriptionType' => $mainDescriptionType, 'mainDescriptionSource' => $mainDescriptionSource, 'mainDescriptionText' => $mainDescriptionText, 'productionEventLocation' => $productionEventLocation, 'productionEventCulture' => $productionEventCulture, 'productionEventEarliestDate' => $productionEventEarliestDate, 'productionEventLatestDate' => $productionEventLatestDate, 'productionPeriodName' => $productionPeriodName, 'measurementsFreeText' => $measurements_freetext, 'inscriptions' => $inscriptionsString, 'serialEditionNumbers' => $serialEditionNumbersString, 'decorativeElements' => $decorativesString, 'created_at' => \Carbon\Carbon::now()]
                );

        }

        // insert repository
        \DB::table('repositories')->insert(
            ['instrumentID' => $instrumentID, 'legalBodyID' => $legalBodyID, 'inventoryNumber' => $inventoryNumber, 'repositoryName' => $repositoryName ]
        );

        // insert add instrument activity
        \DB::table('user_activity')->insert(
            ['userID' => $user_id, 'instrumentID' => $instrumentID, 'instrumentName' => $instrument_name, 'legalBodyID' => $legalBodyID, 'legalBodyName' => $legalBodyName, 'activity' => "You added an instrument", 'activityDate' => \Carbon\Carbon::now() ]
        );

        // iterate and insert specific measurements
        foreach ((Input::get('measurements')) as $key => $value) {
            if (strlen($value['value']) > 0)
            {  
                // insert this measurement into the database 
                  \DB::table('measurements')->insert(
                      ['instrumentID' => $instrumentID, 'legalBodyID' => $legalBodyID, 'unit' => $value['unit'], 'type' => $value['type'], 'value' => $value['value']]
                  );
            }      
        }

        // redirect to editing this instrument now that basics are added..
        return \Redirect::action('AdminController@editinstrument', array('instrumentID' => $instrumentID));
    
} // end function to store newly added instruments



// store edited instrument
public function storeinstrumentedited()
{
      $sessiondata = session()->all();
      $user_id = Auth::user()->getId();

      // get all input values  
      $input = Input::all();      
      $legalBodyID = (int) Input::get('legalBodyID');          // force int
      $instrumentID = (int) Input::get('instrumentID');        // force int
      $instrument_name = Input::get('instrument_name');        
      $mainDescriptionType = Input::get('mainDescriptionType');        
      $mainDescriptionText = Input::get('mainDescriptionText');        
      $mainDescriptionSource = Input::get('mainDescriptionSource');        
      $instrument_classifiedTitlePreferred = Input::get('instrument_classifiedTitlePreferred');
      $measurements_freetext = Input::get('measurements_freetext');
      $thesaurusID = (int) Input::get('thesaurusID');          // force int on hidden field instrument thesaurus autocomplete
      $hornbostelID = (int) Input::get('hornbostelID');        // force int on hidden field hornbostel thesaurus autocomplete
      $repositoryName = Input::get('repositoryName');
      $inventoryNumber = Input::get('inventoryNumber');
      $hornbostelCat = Input::get('hornbostelCat');

      // check collection exists
       $legalbody = \DB::table('legalbodies')
      ->where('legalBodyID', '=', $legalBodyID)  
      ->take(1)->get();

        if (sizeof($legalbody) < 1) {
                  return \Redirect::action('AdminController@movedordeleted'); // redirect if no collection
        }
        $legalBodyName = $legalbody[0]->legalBodyName;  

       // generate serial edition numbers pipe separated string
       $serialEditionNumbersString='';
        foreach ((Input::get('serialnumbers')) as $key => $value) {
            $serialEditionNumbersString = $serialEditionNumbersString . $value['serialnumber'].'|';
        }

       // generate inscriptions pipe separated
       $inscriptionsString='';
        foreach ((Input::get('inscriptions')) as $key => $value) {
            $inscriptionsString = $inscriptionsString . $value['inscription'].'|';
        }

       // generate descriptive elements pipe separated string
       $decorativesString='';
        foreach ((Input::get('decoratives')) as $key => $value) {
            $decorativesString = $decorativesString . $value['decorative'].'|';
        }
      
        // remove trailing pipes
        $serialEditionNumbersString = rtrim($serialEditionNumbersString, "|"); 
        $inscriptionsString = rtrim($inscriptionsString, "|"); 
        $decorativesString = rtrim($decorativesString, "|"); 

        if ($thesaurusID)  // this is an approved term instrument, get the name from the instrument thesaurus
        {  
             $thesaurus_info = \DB::select('select Level_0, Level_1, Level_2, Level_3 from thesauruses where thesaurusID = ?', [$thesaurusID]);
             $Level_0 = $thesaurus_info[0]->Level_0;
             $Level_1 = $thesaurus_info[0]->Level_1;
             $Level_2 = $thesaurus_info[0]->Level_2;
             $Level_3 = $thesaurus_info[0]->Level_3;
             $instrument_tags = $Level_0.','.$Level_1.','.$Level_2.','.$Level_3; // all contextual hierarchy from thesaurus as this is an approved term
             $instrument_name = $Level_0; // default level 0 is instrument name, going to use topmost defined level in thesaurus  

              if (strlen($Level_1) > 0) 
              {
                $instrument_name = $Level_1; // then level 1 is instrument name
              }
              
              if (strlen($Level_2) > 0) 
              {
                $instrument_name = $Level_2;  // then level 2 is instrument name   
              }
              
              if (strlen($Level_3) > 0) 
              {
                $instrument_name = $Level_3; // then level 3 is instrument name
              }              

              // this is a classified instrument, if user has specified a more specific name for this instrument, use that instead..
              if (strlen($instrument_classifiedTitlePreferred) > 0) 
              {
                $instrument_name = ucfirst($instrument_classifiedTitlePreferred);
              }  

                 // update instrument basics
                  \DB::table('instruments')
                   ->where('instrumentID', $instrumentID)
                   ->where('legalBodyID', $legalBodyID)
                   ->update(array('legalBodyID' => $legalBodyID, 'thesaurusID' => $thesaurusID, 'hornbostelID' => $hornbostelID, 'hornbostelCat' => $hornbostelCat,  'titlePreferred' => $instrument_name, 'Level_0' => $Level_0, 'Level_1' => $Level_1, 'Level_2' => $Level_2, 'Level_3' => $Level_3, 'tags' => $instrument_tags, 'mainDescriptionType' => $mainDescriptionType, 'mainDescriptionSource' => $mainDescriptionSource, 'mainDescriptionText' => $mainDescriptionText, 'measurementsFreeText' => $measurements_freetext, 'inscriptions' => $inscriptionsString, 'serialEditionNumbers' => $serialEditionNumbersString, 'decorativeElements' => $decorativesString, 'updated_at' => \Carbon\Carbon::now()));

        } else {
        
                // this is not an approved term thesaurus classified instrument
                $instrument_name = ucfirst($instrument_name); // make sure the submitted instrument name is capitalised
                if (strlen($instrument_name) < 1)
                {
                  $instrument_name = ucfirst($instrument_classifiedTitlePreferred);
                } 

                 // update instrument basics
                  \DB::table('instruments')
                   ->where('instrumentID', $instrumentID)
                   ->where('legalBodyID', $legalBodyID)
                   ->update(array('legalBodyID' => $legalBodyID, 'hornbostelID' => $hornbostelID, 'hornbostelCat' => $hornbostelCat, 'titlePreferred' => $instrument_name, 'mainDescriptionType' => $mainDescriptionType, 'mainDescriptionSource' => $mainDescriptionSource, 'mainDescriptionText' => $mainDescriptionText, 'measurementsFreeText' => $measurements_freetext, 'inscriptions' => $inscriptionsString, 'serialEditionNumbers' => $serialEditionNumbersString, 'decorativeElements' => $decorativesString, 'updated_at' => \Carbon\Carbon::now()));

        } // end if not an instrument in the thesaurus

        // delete existing repositories for this instrument
        \DB::table('repositories')->where('instrumentID', '=', $instrumentID)->delete();

        // add repositories for this instrument
            \DB::table('repositories')->insert(
                ['instrumentID' => $instrumentID, 'legalBodyID' => $legalBodyID, 'inventoryNumber' => $inventoryNumber, 'repositoryName' => $repositoryName ]
            );

        // add update instrument activity
            \DB::table('user_activity')->insert(
                ['userID' => $user_id, 'instrumentID' => $instrumentID, 'instrumentName' => $instrument_name, 'legalBodyID' => $legalBodyID, 'legalBodyName' => $legalBodyName, 'activity' => "You updated an instrument", 'activityDate' => \Carbon\Carbon::now() ]
            );

        // delete existing descriptions for this instrument
        \DB::table('descriptions')->where('instrumentID', '=', $instrumentID)->delete();

        // add descriptions
        foreach ((Input::get('desc')) as $key => $value) {
            if (strlen($value['text']) > 0)
            {  
                  \DB::table('descriptions')->insert(
                      ['instrumentID' => $instrumentID, 'legalBodyID' => $legalBodyID, 'descriptionType' => $value['type'], 'descriptionText' => $value['text'], 'descriptionTextSource' => $value['textsource'] ]
                  );
            }      
        }

        // delete existing measurements for this instrument
        \DB::table('measurements')->where('instrumentID', '=', $instrumentID)->delete();
 
        // add specific measurements
        foreach ((Input::get('measurements')) as $key => $value) {
            if (strlen($value['value']) > 0)
            {  
                // insert this measurement into the database
                  \DB::table('measurements')->insert(
                      ['instrumentID' => $instrumentID, 'legalBodyID' => $legalBodyID, 'unit' => $value['unit'], 'type' => $value['type'], 'value' => $value['value']]
                  );
            }      
        }
      
        session()->flash('flashdata', 'Instrument updated successfully!');

        // redirect to editing this instrument 
        return \Redirect::action('AdminController@editinstrument', array('instrumentID' => $instrumentID));
       
} // end store edited ins



    // edit instrument
    public function editinstrument($instrumentID)
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
        $inscriptions = $instrument[0]->inscriptions;
        $serialEditionNumbers = $instrument[0]->serialEditionNumbers;        
        $decorativeElements = $instrument[0]->decorativeElements;
        $thesaurusID = $instrument[0]->thesaurusID;  
        $hornbostelID = $instrument[0]->hornbostelID;          
        $inscriptions = explode('|', $inscriptions);
        $serialEditionNumbers = explode('|', $serialEditionNumbers);
        $decorativeElements = explode('|', $decorativeElements);        

        // get specific measurements for this instrument
         $measurements = \DB::table('measurements')
        ->where('instrumentID', '=', $instrumentID)  
        ->get();

        // Fetch description(s) data
         $descriptions = \DB::table('descriptions')
        ->where('instrumentID', '=', $instrumentID)  
        ->get();

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


        return view('admin.editinstrument_new')->with([          
           'page' => 'editinstrument',
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
           'productionEventLocation' => htmlentities($instrument[0]->productionEventLocation),
           'productionEventCulture' => $instrument[0]->productionEventCulture,
           'productionEventEarliestDate' => $instrument[0]->productionEventEarliestDate,
           'productionEventLatestDate' => $instrument[0]->productionEventLatestDate,
           'productionPeriodName' => $instrument[0]->productionPeriodName,
           'descriptions' => $descriptions,
           'repositoryName' => $repositoryName,  
           'inventoryNumber' => $inventoryNumber,
           'hornbostelCat' => $hornbostelCat,
           'inscriptions' => $inscriptions,
           'serialEditionNumbers' => $serialEditionNumbers,
           'decorativeElements' => $decorativeElements,
           'legalBodyID' => $legalBodyID, 
           'legalBodyName' => $legalBodyName, 
           'legalBodyMDAcode' => $legalBodyMDAcode,
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
           'measurements' => $measurements,
           'measurementsFreeText' => $instrument[0]->measurementsFreeText,
           'insimage' => $insimage,
           'instrument_creation_timeago' => $instrument_creation_timeago,
           'rights' => $rights,
           'creator_avatar' => $creator_avatar,
           'creator_name' => $creator_name,
           'creationType' => $creationType,
           'sessiondata' => $sessiondata
        ]);  

    }





    public function instrumentrights($instrumentID)
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
        $instrumentName = $instrument[0]->titlePreferred;  

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
           'instrumentName' => $instrumentName,
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
           'eventType' => '',
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



    public function storeinstrumentrights() {

      
      $sessiondata = session()->all();
      $user_id = Auth::user()->getId();

      // get all input values  
      $input = Input::all();  

      $instrumentID = Input::get('instrumentID');
      $legalBodyID = Input::get('legalBodyID');

          // delete existing rights information for this instrument
          \DB::table('rights')->where('instrumentID', '=', $instrumentID)->where('rightsFlag', '=', "instrument")->delete();

          // add descriptions
          foreach ((Input::get('rights')) as $key => $value) {
              if (strlen($value['rightsType']) > 0)
              {  
                   \DB::table('rights')->insert(
                        ['rightsCreditLine' => $value['rightsCreditLine'], 'instrumentID' => $instrumentID, 'rightsFlag' => 'instrument', 'legalBodyID' => $legalBodyID, 'rightsType' => $value['rightsType'], 'rightsEarliestDate' => $value['rightsEarliestDate'], 'rightsLatestDate' => $value['rightsLatestDate'], 'rightsHolderName' => $value['rightsHolderName'], 'rightsHolderWebsite' => $value['rightsHolderWebsite'], 'rightsHolderID' => $value['rightsHolderID'] ]
                    );
                    
              }      
          }
        
        session()->flash('flashdata', 'Instrument rights updated successfully!');

        // redirect to editing this instrument 
        return \Redirect::action('AdminController@instrumentrights', array('instrumentID' => $instrumentID));
    }







 public function resourcerights($instrumentID = '', $resourceID = '')
    {
        $sessiondata = session()->all();
        $role = \Session::get('role');
        $instrumentID = (int) $instrumentID; // force int
        $resourceID = (int) $resourceID; // force int


        // echo "resourceID: ".$resourceID." instrumentID: ".$instrumentID; exit;

      // fetch resource for this instrument
       $resource = \DB::table('resources')
      ->where('resourceID', '=', $resourceID)  
      ->where('instrumentID', '=', $instrumentID)       
      ->take(1)->get();

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
        $instrumentName = $instrument[0]->titlePreferred;  


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

       // get rights information for this instrument, if exists
       $resourceRights = \DB::table('rights')
      ->where('instrumentID', '=', $instrumentID)  
      ->where('resourceID', '=', $resourceID)  
      ->get();

        // Fetch description(s) data
         $descriptions = \DB::table('descriptions')
        ->where('instrumentID', '=', $instrumentID)  
        ->get();


        return view('admin.resourcerights')->with([          
           'page' => 'resourcerights',
           'role' => \Session::get('role'),
           'instrumentID' => $instrumentID, 
           'thesaurusID' => $thesaurusID,   
           'hornbostelID' => $hornbostelID,
           'titlePreferred' => $titlePreferred,  
           'titleSingle' => $titleSingle,  
           'instrument_classifiedTitlePreferred' => $instrument_classifiedTitlePreferred,
           'instrumentName' => $instrumentName,
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
           'eventType' => '',      
           'prod_event_actors' => $prod_event_actors,
           'other_event_actors' => $other_event_actors,
           'status' => $status,
           'insimage' => $insimage,
           'instrument_creation_timeago' => $instrument_creation_timeago,
           'rights' => $rights,
           'resourceRights' => $resourceRights,           
           'creator_avatar' => $creator_avatar,
           'creator_name' => $creator_name,
           'creationType' => $creationType,
           'resource' => $resource,
           'sessiondata' => $sessiondata
        ]);  

    }






    public function storeresourcerights() {
     
      $sessiondata = session()->all();
      $user_id = Auth::user()->getId();

      // get all input values  
      $input = Input::all();  

      $instrumentID = Input::get('instrumentID');
      $legalBodyID = Input::get('legalBodyID');
      $resourceID = Input::get('resourceID');
      $resourceType = Input::get('resourceType');


          // delete existing rights information for this resource
          \DB::table('rights')->where('instrumentID', '=', $instrumentID)->where('resourceID', '=', $resourceID)->delete();

          // add resource rights
          foreach ((Input::get('rights')) as $key => $value) {
              if (strlen($value['rightsType']) > 0)
              {  
                   \DB::table('rights')->insert(
                        ['rightsCreditLine' => $value['rightsCreditLine'], 'instrumentID' => $instrumentID, 'resourceID' => $resourceID, 'rightsFlag' => $resourceType, 'legalBodyID' => $legalBodyID, 'rightsType' => $value['rightsType'], 'rightsEarliestDate' => $value['rightsEarliestDate'], 'rightsLatestDate' => $value['rightsLatestDate'], 'rightsHolderName' => $value['rightsHolderName'], 'rightsHolderWebsite' => $value['rightsHolderWebsite'], 'rightsHolderID' => $value['rightsHolderID'] ]
                    );
                    
              }      
          }
        
        session()->flash('flashdata', 'Resource rights updated successfully!');

        // redirect to editing this instrument 
        return \Redirect::action('AdminController@resourcerights', array('instrumentID' => $instrumentID, 'resourceID' => $resourceID));
    }







    public function eventmaterials($instrumentID = '', $eventID = '')
    {
        $sessiondata = session()->all();
        $role = \Session::get('role');
        $instrumentID = (int) $instrumentID; // force int
        $eventID = (int) $eventID; // force int

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

        if ($eventID > 0)
        {  
          // this is not a production event

           $thisevent = \DB::table('events')
          ->where('eventID', '=', $eventID)  
          ->take(1)->get();

            $materialsFreeText = $thisevent[0]->materialsText;  
            $materials = $thisevent[0]->materials;  
            $materials = explode('|', $materials);
            $eventType = $thisevent[0]->eventType;  

        } else {  
          // this is a production event  
          $eventType = "Production";

            $materialsFreeText = $instrument[0]->productionMaterialsFreeText;  
            $materials = $instrument[0]->productionMaterials;  
            $materials = explode('|', $materials);

        }  

        // instrument details
        $status = $instrument[0]->status;
        $thesaurusID = $instrument[0]->thesaurusID;  
        $hornbostelID = $instrument[0]->hornbostelID;          
        $instrumentName = $instrument[0]->titlePreferred;  


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




        return view('admin.eventmaterials_new')->with([          
           'page' => 'eventmaterials',
           'role' => \Session::get('role'),
           'instrumentID' => $instrumentID, 
           'eventID' => $eventID,   
           'eventType' => $eventType,         
           'thesaurusID' => $thesaurusID,   
           'hornbostelID' => $hornbostelID,
           'titlePreferred' => $titlePreferred,  
           'titleSingle' => $titleSingle,  
           'instrument_classifiedTitlePreferred' => $instrument_classifiedTitlePreferred,
           'instrumentName' => $instrumentName,
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
           'prod_event_actors' => $prod_event_actors,
           'other_event_actors' => $other_event_actors,
           'status' => $status,
           'insimage' => $insimage,
           'instrument_creation_timeago' => $instrument_creation_timeago,
           'rights' => $rights,    
           'creator_avatar' => $creator_avatar,
           'creator_name' => $creator_name,
           'creationType' => $creationType,
           'materialsFreeText' => $materialsFreeText,
           'materials' => $materials,
           'sessiondata' => $sessiondata
        ]);  

    }





    public function store_eventmaterials() {
        $sessiondata = session()->all();
        $role = \Session::get('role');

        // get all input values  
        $input = Input::all();
        $legalBodyID = (int) Input::get('legalBodyID'); // force int
        $instrumentID = (int) Input::get('instrumentID'); // force int
        $eventID = (int) Input::get('eventID'); // force int
        $eventType = Input::get('eventType'); 
        $materialsFreeText = Input::get('materialsFreeText'); 

        // generate materials pipe separated string
        $materialsString='';
        foreach ((Input::get('materials')) as $key => $value) {
              $materialsString = $materialsString . $value['material'].'|';
        }

        // remove last pipes for dbase.
        $materialsString = rtrim($materialsString, "|"); 
        
        if ($eventType == "Production")
        {  
             // update prodution event materials
              \DB::table('instruments')
               ->where('instrumentID', $instrumentID)
               ->where('legalBodyID', $legalBodyID)
               ->update(array('productionMaterialsFreeText' => $materialsFreeText, 'productionMaterials' => $materialsString));

                session()->flash('flashdata', 'Production event materials updated successfully!');

                // redirect to editing this instrument 
                return \Redirect::action('AdminController@eventmaterials', array('instrumentID' => $instrumentID, 'eventID' => 0));

        } else {

             // update event materials
              \DB::table('events')
               ->where('instrumentID', $instrumentID)
               ->where('eventID', $eventID)
               ->where('legalBodyID', $legalBodyID)
               ->update(array('materialsText' => $materialsFreeText, 'materials' => $materialsString));

                session()->flash('flashdata', 'Event materials updated successfully!');

                // redirect to editing this instrument 
                return \Redirect::action('AdminController@eventmaterials', array('instrumentID' => $instrumentID, 'eventID' => $eventID));

        }       





    } // end store event materials






    public function deleteinstrument($instrumentID)
    {
        $sessiondata = session()->all();
        $user_id = Auth::user()->getId();
        $instrumentID = (int) $instrumentID;  // force int

        $role = \Session::get('role');
        if ($role != "SuperAdmin" && $role != "Cataloguer" && $role != "Admin")
        {
            return \Redirect::action('AdminController@index');  // redirect to dashboard if not SuperAdmin, Cataloguer or Admin
        }

        // Fetch instrument data
         $instrument = \DB::table('instruments')
        ->where('instrumentID', '=', $instrumentID)  
        ->take(1)->get();
         
        if (sizeof($instrument) < 1) {
                  return \Redirect::action('AdminController@movedordeleted'); // redirect to deleted if no instrument found
        }

        $legalBodyID = $instrument[0]->legalBodyID;

        if($role == "Admin")
        { 
            // admins can only delete instruments belonging to their own collection
             $user =  \DB::table('users')
            ->where('id', '=', $user_id)  
            ->take(1)->get();

             $user_legalBodyID = $user[0]->legalBodyID;

               if ($user_legalBodyID != $legalBodyID)
               { 
                    // redirect to dash 
                    return \Redirect::action('AdminController@index');
               } 
        }   

        // fetch instrument details
        $status = $instrument[0]->status;
        $measurementsFreeText = $instrument[0]->measurementsFreeText;
        $inscriptions = $instrument[0]->inscriptions;
        $serialEditionNumbers = $instrument[0]->serialEditionNumbers;        
        $decorativeElements = $instrument[0]->decorativeElements;

        if(strlen($inscriptions) > 0)
        {
             $inscriptions = explode('|', $inscriptions);
             $num_inscriptions = sizeof($inscriptions);
        } else {
             $num_inscriptions = 0;
        }
       
        if(strlen($serialEditionNumbers) > 0)
        {
             $serialEditionNumbers = explode('|', $serialEditionNumbers);
             $num_serial = sizeof($serialEditionNumbers);
        } else {
             $num_serial = 0;
        }

        if(strlen($decorativeElements) > 0)
        {
             $decorativeElements = explode('|', $decorativeElements); 
             $num_decorative = sizeof($decorativeElements);
        } else {
             $num_decorative = 0;
        }

        // get specific measurements for this instrument
         $measurements = \DB::table('measurements')
        ->where('instrumentID', '=', $instrumentID)  
        ->get();

        // Fetch description(s) data
         $descriptions = \DB::table('descriptions')
        ->where('instrumentID', '=', $instrumentID)  
        ->get();

        // get events others than production for this instrument
         $events = \DB::table('events')
        ->where('instrumentID', '=', $instrumentID)  
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

        // Fetch collection data
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
              // $Level_0 = $thesaurus[0]->Level_0;   // not required  (always 'Instrument')
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

        // iterate through other events
        foreach ($events as $key => $otherevent)
        {    
             $thisEventID = $otherevent->eventID;
             $other_event_actors[$thisEventID] = \DB::table('eventactors')->select('eventActorID', 'eventActorName')->where('eventID', '=', $thisEventID)->get();
        }     

        return view('admin.deleteinstrument')->with([          
           'page' => 'deleteinstrument',
           'role' => \Session::get('role'),
           'instrumentID' => $instrumentID,
           'legalBodyID' => $legalBodyID,
           'legalBodyName' => $legalBodyName,
           'legalBodyMDAcode' => $legalBodyMDAcode,
           'titlePreferred' => $titlePreferred,
           'instrument_classifiedTitlePreferred' => $instrument_classifiedTitlePreferred,
           'insimage' => $insimage,
           'imageCount' => $imageCount,
           'audioCount' => $audioCount,
           'videoCount' => $videoCount,
           'num_inscriptions' => $num_inscriptions,
           'num_serial' => $num_serial,
           'num_decorative' => $num_decorative,
           'legalBodyID' => $legalBodyID, 
           'legalBodyName' => $legalBodyName,  
           'legalBodyMDAcode' => $legalBodyMDAcode,
           'creator_avatar' => $creator_avatar,
           'creator_name' => $creator_name,
           'sessiondata' => $sessiondata
        ]);  

    }





// shows add actor form form an event
public function addactor($instrumentID='', $eventID='')
{
    $role = \Session::get('role');
    $instrumentID = (int) $instrumentID; // force int
    $eventID = (int) $eventID;           // force int
    $sessiondata = session()->all();

      if ($eventID != '0') // then non production event
      {  
            // check that this event belongs to this instrument... if not, something is wrong, redirect...
             $eventvalid = \DB::table('events')
            ->where('instrumentID', '=', $instrumentID)  
            ->where('eventID', '=', $eventID)        
            ->take(1)->get();

            if (sizeof($eventvalid) < 1) {
                      return \Redirect::action('AdminController@movedordeleted');
            } 
      } 
      
      // Fetch instrument data
      $instrument = \DB::table('instruments')
      ->where('instrumentID', '=', $instrumentID)  
      ->take(1)->get();
      
      if (sizeof($instrument) < 1) {
                return \Redirect::action('AdminController@movedordeleted'); // redirect if no result for instrument
      }

      $thesaurusID = $instrument[0]->thesaurusID;  
      $legalBodyID = $instrument[0]->legalBodyID;  

      // if user is 'Admin', is this instrument in their collection?
      if ($role == "Admin") {
            $mylegalbody = \Session::get('legalBodyID');
               if ($mylegalbody != $legalBodyID) {
                  return \Redirect::action('AdminController@movedordeleted'); // this is not an instrument this Admin can edit
               }
      }

      // get events others than production for this instrument
      $events = \DB::table('events')
      ->where('instrumentID', '=', $instrumentID)  
      ->get();

      // get this event
      $thisevent = \DB::table('events')
      ->where('eventID', '=', $eventID)  
      ->get();


      $eventType ='Production'; // default
    
      if (sizeof($thisevent) > 0) { // otherwise it's production
        $eventType = $thisevent[0]->eventType;
      } 

      $status = $instrument[0]->status;  // instrument status

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

        if (sizeof($repositories) != 0 )    // get repository
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

        return view('admin.addactor')->with([          
           'page' => 'addactor',
           'role' => \Session::get('role'),
           'instrumentID' => $instrumentID, 
           'thesaurusID' => $thesaurusID,   
           'titlePreferred' => $titlePreferred,  
           'titleSingle' => $titleSingle,  
           'legalBodyID' => $legalBodyID, 
           'legalBodyName' => $legalBodyName, 
           'legalBodyMDAcode' => $legalBodyMDAcode,
           'imageCount' => $imageCount, 
           'audioCount' => $audioCount, 
           'videoCount' => $videoCount, 
           'actorID' => '',
           'eventType' => $eventType,
           'thisevent' => $thisevent,
           'events' => $events,
           'eventID' => $eventID,
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
}



// edit actor for events other than production (in events table rather than instruments)
public function editactor($eventID='',$actorID='')
{
    $eventID = (int) $eventID;           // force int
    $actorID = (int) $actorID;           // force int
    $sessiondata = session()->all();

    // check that this actor belongs to this event... if not, something is wrong, redirect...
     $actorquery = \DB::table('eventactors')
    ->where('eventID', '=', $eventID)  
    ->where('eventActorID', '=', $actorID)        
    ->take(1)->get();

    if (sizeof($actorquery) < 1) {
              return \Redirect::action('AdminController@movedordeleted'); // redirect if no results for this actor
    } 

    $actorThesaurusID = $actorquery[0]->actorThesaurusID;
    $eventActorType = $actorquery[0]->eventActorType;
    $eventActorName = $actorquery[0]->eventActorName;
    $eventActorSource = $actorquery[0]->eventActorSource;
    $eventActorNationality = $actorquery[0]->eventActorNationality;
    $eventActorBirthDate = $actorquery[0]->eventActorBirthDate;
    $eventActorDeathDate = $actorquery[0]->eventActorDeathDate;
    $eventActorGender = $actorquery[0]->eventActorGender;
    $eventActorRole = $actorquery[0]->eventActorRole;
    $eventDisplayActorRole = $actorquery[0]->eventDisplayActorRole;

    // get instrumentID from event now that we know that is valid
    $instrument = \DB::table('events')
    ->where('eventID', '=', $eventID)  
    ->take(1)->get();
         
    if (sizeof($instrument) < 1) {
              return \Redirect::action('AdminController@movedordeleted'); // redirect if no result for this instrument
    }

     $instrumentID = $instrument[0]->instrumentID; 

    // Fetch instrument data
    $instrument = \DB::table('instruments')
    ->where('instrumentID', '=', $instrumentID)  
    ->take(1)->get();

    // get events others than production for this instrument
    $events = \DB::table('events')
    ->where('instrumentID', '=', $instrumentID)  
    ->get();

    // get this event
    $thisevent = \DB::table('events')
    ->where('eventID', '=', $eventID)  
    ->get();
 
    $eventType ='';
    
    if (sizeof($thisevent) > 0) {       
        $eventType = $thisevent[0]->eventType;
    } 

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

        //echo sizeof($repositories); 
        $repositoryName = '';

        if (sizeof($repositories) != 0 )            // get repository
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

        return view('admin.editactor')->with([          
           'page' => 'editactor',
           'role' => \Session::get('role'),
           'instrumentID' => $instrumentID, 
           'thesaurusID' => $thesaurusID,   
           'titlePreferred' => $titlePreferred,  
           'titleSingle' => $titleSingle,  
           'legalBodyID' => $legalBodyID, 
           'legalBodyName' => $legalBodyName, 
           'legalBodyMDAcode' => $legalBodyMDAcode,
           'imageCount' => $imageCount, 
           'audioCount' => $audioCount, 
           'videoCount' => $videoCount, 
           'eventType' => $eventType,
           'thisevent' => $thisevent,
           'events' => $events,
           'eventID' => $eventID,
           'actorID' => $actorID,
           'prod_event_actors' => $prod_event_actors,
           'other_event_actors' => $other_event_actors,           
           'status' => $status,
           'insimage' => $insimage,
           'actorThesaurusID' => $actorThesaurusID, 
           'eventActorType' => $eventActorType,
           'eventActorName' => $eventActorName,
           'eventActorSource' => $eventActorSource, 
           'eventActorNationality' => $eventActorNationality, 
           'eventActorBirthDate' => $eventActorBirthDate,
           'eventActorDeathDate' => $eventActorDeathDate,
           'eventActorGender' => $eventActorGender, 
           'eventActorRole' => $eventActorRole, 
           'eventDisplayActorRole' => $eventDisplayActorRole, 
           'instrument_creation_timeago' => $instrument_creation_timeago,
           'creator_avatar' => $creator_avatar,
           'creator_name' => $creator_name,
           'creationType' => $creationType,
           'rights' => $rights,
           'sessiondata' => $sessiondata
        ]);  
}
// end edit actor



// edit actor for events other than production
public function editproductionactor($instrumentID='',$actorID='')
{
    $instrumentID = (int) $instrumentID;           // force int
    $actorID = (int) $actorID;           // force int
    $sessiondata = session()->all();

    // check that this actor belongs to this instrument... if not, something is wrong, redirect...
     $actorquery = \DB::table('eventactors')
    ->where('instrumentID', '=', $instrumentID)  
    ->where('eventActorID', '=', $actorID)        
    ->take(1)->get();

    if (sizeof($actorquery) < 1) {
              return \Redirect::action('AdminController@movedordeleted'); // actor doesn't belong...
    } 

    $eventID = $actorquery[0]->eventID;
    $actorThesaurusID = $actorquery[0]->actorThesaurusID;       
    $eventActorType = $actorquery[0]->eventActorType;
    $eventActorName = $actorquery[0]->eventActorName;
    $eventActorSource = $actorquery[0]->eventActorSource;
    $eventActorNationality = $actorquery[0]->eventActorNationality;
    $eventActorBirthDate = $actorquery[0]->eventActorBirthDate;
    $eventActorDeathDate = $actorquery[0]->eventActorDeathDate;
    $eventActorGender = $actorquery[0]->eventActorGender;
    $eventActorRole = $actorquery[0]->eventActorRole;
    $eventDisplayActorRole = $actorquery[0]->eventDisplayActorRole;

        // Fetch instrument data
         $instrument = \DB::table('instruments')
        ->where('instrumentID', '=', $instrumentID)  
        ->take(1)->get();

        if (sizeof($instrument) < 1) {
                  return \Redirect::action('AdminController@movedordeleted'); // REDIRECT IF NO RESULT FOR THIS INSRUMENT IF NO OBJECT
        }

        // get events others than production for this instrument
         $events = \DB::table('events')
        ->where('instrumentID', '=', $instrumentID)  
        ->get();

        // get this event
         $thisevent = \DB::table('events')
        ->where('eventID', '=', $eventID)  
        ->get();
             
        if (sizeof($thisevent) > 0)
        {
         $eventType = $thisevent[0]->eventType;
        } else {
          $eventType =''; 
        }

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

        if (sizeof($repositories) != 0 )            // get repository 
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
              // $Level_0 = $thesaurus[0]->Level_0;   // not required  (always 'Instrument')
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

        return view('admin.editactor')->with([          
           'page' => 'editactor',
           'role' => \Session::get('role'),
           'instrumentID' => $instrumentID, 
           'thesaurusID' => $thesaurusID,   
           'titlePreferred' => $titlePreferred,  
           'titleSingle' => $titleSingle,  
           'legalBodyID' => $legalBodyID, 
           'legalBodyName' => $legalBodyName, 
           'legalBodyMDAcode' => $legalBodyMDAcode,
           'imageCount' => $imageCount, 
           'audioCount' => $audioCount, 
           'videoCount' => $videoCount, 
           'eventType' => "Production",
           'thisevent' => $thisevent,
           'events' => $events,
           'eventID' => $eventID,
           'actorID' => $actorID,
           'prod_event_actors' => $prod_event_actors,
           'other_event_actors' => $other_event_actors,           
           'status' => $status,
           'insimage' => $insimage,
           'actorThesaurusID' => $actorThesaurusID, 
           'eventActorType' => $eventActorType,
           'eventActorName' => $eventActorName,
           'eventActorSource' => $eventActorSource, 
           'eventActorNationality' => $eventActorNationality, 
           'eventActorBirthDate' => $eventActorBirthDate,
           'eventActorDeathDate' => $eventActorDeathDate,
           'eventActorGender' => $eventActorGender, 
           'eventActorRole' => $eventActorRole, 
           'eventDisplayActorRole' => $eventDisplayActorRole, 
           'instrument_creation_timeago' => $instrument_creation_timeago,
           'creator_avatar' => $creator_avatar,
           'creator_name' => $creator_name,
           'creationType' => $creationType,
           'rights' => $rights,
           'sessiondata' => $sessiondata
        ]);  
}
// end edit actor



// delete an instrument's actor 
function deleteactor($actorID='', $eventID ='', $instrumentID ='') 
{
      $user_id = Auth::user()->getId();
      $actorID = (int) $actorID; // force actorID to int for security
      $eventID = (int) $eventID; // force eventID to int for security
      $instrumentID = (int) $instrumentID; // force instrumentID to int for security

      if ($eventID != '0') // if this is not a production event
      {  
            // ensure this actor belongs to the event  
                 $actorquery = \DB::table('eventactors')
                ->where('eventActorID', '=', $actorID)  
                ->where('eventID', '=', $eventID)  
                ->take(1)->get();

              if (sizeof($actorquery) < 1) {
                        return \Redirect::action('AdminController@movedordeleted');
              }

            // get instrument id from event
               $eventquery = \DB::table('events')
              ->where('eventID', '=', $eventID)  
              ->take(1)->get();

              $instrumentID = $eventquery[0]->instrumentID;  

            // now delete the actor
              \DB::table('eventactors')->where('eventActorID', '=', $actorID)->where('eventID', '=', $eventID)->delete();

      } else { // this is a production event

            // now delete the actor
              \DB::table('eventactors')->where('eventActorID', '=', $actorID)->where('instrumentID', '=', $instrumentID)->delete();
     }
      

      // now set the flash data to show the user has been updated...
      session()->flash('flashdata', 'Event actor deleted successfully!');


      // add update account activity
      \DB::table('user_activity')->insert(
          ['userID' => $user_id, 'activity' => "You deleted an actor from an instrument's event", 'instrumentID' => $instrumentID, 'eventID' => $eventID, 'activityDate' => \Carbon\Carbon::now() ]
        );

      if ($eventID != '0') // if this is not a production event
      {  
          // redirect to edit event
          return \Redirect::action('AdminController@editevent', array('instrumentID' => $instrumentID, 'eventID' => $eventID));
      } else {
          // redirect to production event
          return \Redirect::action('AdminController@productionevent', array('instrumentID' => $instrumentID));
      }    
}



// function to show delete event form
function delete_event($eventID='',$instrumentID='')
{
        $user_id = Auth::user()->getId();
        $role = \Session::get('role');
        if ($role != "SuperAdmin" && $role != "Cataloguer" && $role != "Admin")
        {          
            return \Redirect::action('AdminController@index');   // redirect to dash if not SuperAdmin, Cataloguer or Admin
        }

        $eventID = (int) $eventID; // force int
        $instrumentID = (int) $instrumentID; // force int

        // check instrument
         $instrument = \DB::table('instruments')
        ->where('instrumentID', '=', $instrumentID)  
        ->take(1)->get();

        if (sizeof($instrument) < 1) {
                  return \Redirect::action('AdminController@movedordeleted'); // redirect if no results for this instrument
        }

        $instrumentName = $instrument[0]->titlePreferred;
        $legalBodyID = $instrument[0]->legalBodyID;

        // check collection
        $thislegalbody = \DB::table('legalbodies')
        ->where('legalBodyID', '=', $legalBodyID)  
        ->take(1)->get();

        if (sizeof($thislegalbody) < 1) { 
                  return \Redirect::action('AdminController@movedordeleted'); // redirect if no result for this collection
        }

        $legalBodyName = $thislegalbody[0]->legalBodyName;

        // check that this event belongs to this instrument... if not, something is wrong, redirect...
         $eventvalid = \DB::table('events')
        ->where('instrumentID', '=', $instrumentID)  
        ->where('eventID', '=', $eventID)        
        ->take(1)->get();

        if (sizeof($eventvalid) < 1) {
                  return \Redirect::action('AdminController@movedordeleted');
        } 


      // delete the event
      \DB::table('events')->where('instrumentID', '=', $instrumentID)->where('eventID', '=', $eventID)->delete();

      // delete actors belonging to this event
      \DB::table('eventactors')->where('eventID', '=', $eventID)->delete();

      // now set the flash data to show the user has been updated...
      session()->flash('flashdata', 'Instrument event deleted successfully!');

      // add delete event activity
      \DB::table('user_activity')->insert(
          ['userID' => $user_id, 'activity' => "You deleted an event from an instrument", 'legalBodyID' => $legalBodyID, 'instrumentID' => $instrumentID, 'legalBodyName' => $legalBodyName, 'instrumentName' => $instrumentName, 'eventID' => $eventID, 'activityDate' => \Carbon\Carbon::now() ]
        );

      // redirect to editing instrument
      return \Redirect::action('AdminController@editinstrument', array('instrumentID' => $instrumentID));
}




// used to store new actors other than production event actors
function storeactor() 
{
      $user_id = Auth::user()->getId();
      $sessiondata = session()->all();
      $role = \Session::get('role');

      if ($role != "SuperAdmin" && $role != "Cataloguer" && $role != "Admin")
      {         
          return \Redirect::action('AdminController@index'); // redirect to dashboard if not SuperAdmin or Cataloguer
      }

      // get all input values  
      $input = Input::all();
      $instrumentID = (int) Input::get('instrumentID');       // forcing the id into an int       
      $eventID = (int) Input::get('eventID');                 // forcing the id into an int 
      $eventActorID = (int) Input::get('eventActorID');       // forcing the id into an int 
      $mode = Input::get('mode');    

      if ($eventID != '0') // then this is not a production event
      { 
            // check that this event belongs to this instrument... if not, something is wrong, redirect...
             $eventvalid = \DB::table('events')
            ->where('instrumentID', '=', $instrumentID)  
            ->where('eventID', '=', $eventID)        
            ->take(1)->get();

            if (sizeof($eventvalid) < 1) {
                      return \Redirect::action('AdminController@movedordeleted');
            } 
      }      

      $actorID = Input::get('actorID');
      $eventActorType = Input::get('eventActorType');
      $actorname = Input::get('actorname');      
      $eventActorSource = Input::get('eventActorSource');
      $eventActorNationality = Input::get('eventActorNationality');     
      $eventActorBirthDate = Input::get('eventActorBirthDate');           
      $eventActorDeathDate = Input::get('eventActorDeathDate');     
      $eventActorGender = Input::get('eventActorGender'); 
      //$eventActorRole = Input::get('eventActorRole');     
      $eventActorRole ='';
      $eventDisplayActorRole = Input::get('eventDisplayActorRole'); 
      $newEventActorID = 0;

      if ($mode == "new") // add new actor
      {  
            if($eventID == '0') // production event
            {            
              // insert production actor
                $newEventActorID = \DB::table('eventactors')->insertGetId(
                   ['instrumentID' => $instrumentID, 'actorThesaurusID' => $actorID, 'eventActorType' => $eventActorType, 'eventActorName' => $actorname, 'eventActorSource' => $eventActorSource, 'eventActorNationality' => $eventActorNationality, 'eventActorBirthDate' => $eventActorBirthDate, 'eventActorDeathDate' => $eventActorDeathDate, 'eventActorGender' => $eventActorGender, 'eventActorRole' => $eventActorRole, 'eventDisplayActorRole' => $eventDisplayActorRole ]
                );
            } else {
              // insert other event actor
                $newEventActorID = \DB::table('eventactors')->insertGetId(
                   ['eventID' => $eventID, 'actorThesaurusID' => $actorID, 'eventActorType' => $eventActorType, 'eventActorName' => $actorname, 'eventActorSource' => $eventActorSource, 'eventActorNationality' => $eventActorNationality, 'eventActorBirthDate' => $eventActorBirthDate, 'eventActorDeathDate' => $eventActorDeathDate, 'eventActorGender' => $eventActorGender, 'eventActorRole' => $eventActorRole, 'eventDisplayActorRole' => $eventDisplayActorRole ]
                );
            }

            // now set the flash data to show the user has been updated...
            session()->flash('flashdata', 'Event actor added successfully!');

            // add add actor account activity
            \DB::table('user_activity')->insert(
              ['userID' => $user_id, 'activity' => "You added an actor to an instrument's event", 'instrumentID' => $instrumentID, 'eventID' => $eventID, 'activityDate' => \Carbon\Carbon::now() ]
            );
      } // end inserting new actors     

      if ($mode == "update") // update existing actor
      {  
            if($eventID == '0') // production event
            {         
              // update production event actor
                    \DB::table('eventactors')
                     ->where('instrumentID', $instrumentID)
                     ->where('eventActorID', $eventActorID)
                     ->update(array('actorThesaurusID' => $actorID, 'eventActorType' => $eventActorType, 'eventActorName' => $actorname, 'eventActorSource' => $eventActorSource, 'eventActorNationality' => $eventActorNationality, 'eventActorBirthDate' => $eventActorBirthDate, 'eventActorDeathDate' => $eventActorDeathDate, 'eventActorGender' => $eventActorGender, 'eventActorRole' => $eventActorRole, 'eventDisplayActorRole' => $eventDisplayActorRole));

            } else {
              // update other event actor
                    \DB::table('eventactors')
                     ->where('eventActorID', $eventActorID)
                     ->where('eventID', $eventID)
                     ->update(array('actorThesaurusID' => $actorID, 'eventActorType' => $eventActorType, 'eventActorName' => $actorname, 'eventActorSource' => $eventActorSource, 'eventActorNationality' => $eventActorNationality, 'eventActorBirthDate' => $eventActorBirthDate, 'eventActorDeathDate' => $eventActorDeathDate, 'eventActorGender' => $eventActorGender, 'eventActorRole' => $eventActorRole, 'eventDisplayActorRole' => $eventDisplayActorRole));
            }

            // now set the flash data to show the user has been updated...
            session()->flash('flashdata', 'Event actor updated successfully!');

            // add update account activity
            \DB::table('user_activity')->insert(
              ['userID' => $user_id, 'activity' => "You updated an actor in an instrument's event", 'instrumentID' => $instrumentID, 'eventID' => $eventID, 'activityDate' => \Carbon\Carbon::now() ]
            );
     } // end inserting new actors 

     if ($eventID != '0') // then this is not a production event
     { 
          if ($newEventActorID != '0')
          {  
              // redirect to edit new actor
              return \Redirect::action('AdminController@editactor', array('eventID' => $eventID, 'actorID' => $newEventActorID));         
          } else {
              // redirect to edit actor
              return \Redirect::action('AdminController@editactor', array('eventID' => $eventID, 'actorID' => $eventActorID));
          }    
     } else {
          if ($newEventActorID != '0')
          {  
          // redirect to edit new production actor
          return \Redirect::action('AdminController@editproductionactor', array('instrumentID' => $instrumentID, 'eventActorID' => $newEventActorID));
          } else {
          // redirect to edit production actor
          return \Redirect::action('AdminController@editproductionactor', array('instrumentID' => $instrumentID, 'eventActorID' => $eventActorID));
          }   
     }     

} // end storeactor




// shows form for adding event
public function addevent($instrumentID) {
      $role = \Session::get('role');
      $sessiondata = session()->all();
      $instrumentID = (int) $instrumentID; // force insID to int for security

      // Fetch instrument data
       $instrument = \DB::table('instruments')
      ->where('instrumentID', '=', $instrumentID)  
      ->take(1)->get();

      if (sizeof($instrument) < 1) {
                return \Redirect::action('AdminController@movedordeleted'); // REDIRECT IF NO RESULT FOR THIS INSRUMENT IF NO OBJECT
      }

      $status = $instrument[0]->status; // instrument status
      $legalBodyID = $instrument[0]->legalBodyID;  
      $instrumentName = $instrument[0]->titlePreferred;  

      // if user is 'Admin', is this instrument in their collection?
      if ($role == "Admin") {
            $mylegalbody = \Session::get('legalBodyID');
               if ($mylegalbody != $legalBodyID) {
                  return \Redirect::action('AdminController@movedordeleted'); // REDIRECT, THIS IS NOT AN INSTRUMENT THIS ADMIN CAN EDIT
               }
        }

      // Fetch legal body data
       $legalbody = \DB::table('legalbodies')
      ->where('legalBodyID', '=', $legalBodyID)  
      ->take(1)->get();

      $legalBodyName = $legalbody[0]->legalBodyName;  
      $legalBodyMDAcode = $legalbody[0]->legalBodyMDAcode;  

        // get events others than production for this instrument
        $events = \DB::table('events')
        ->where('instrumentID', '=', $instrumentID)  
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

        if (sizeof($repositories) != 0 )  // get repository
        {
           $repositoryName = $repositories[0]->repositoryName;  
           $inventoryNumber = $repositories[0]->inventoryNumber;   
        }   

        if ($thesaurusID > 0) // approved instrument name
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

        return view('admin.addevent')->with([          
           'instrumentName' => $instrumentName,   
           'page' => 'addevent',
           'role' => \Session::get('role'),
           'instrumentID' => $instrumentID, 
           'thesaurusID' => $thesaurusID,   
           'titlePreferred' => $titlePreferred,  
           'titleSingle' => $titleSingle,  
           'repositoryName' => $repositoryName,  
           'inventoryNumber' => $inventoryNumber,
           'legalBodyID' => $legalBodyID, 
           'legalBodyName' => $legalBodyName, 
           'legalBodyMDAcode' => $legalBodyMDAcode,
           'actorID' => '',
           'imageCount' => $imageCount, 
           'audioCount' => $audioCount, 
           'videoCount' => $videoCount, 
           'events' => $events,
           'prod_event_actors' => $prod_event_actors,
           'other_event_actors' => $other_event_actors,
           'eventID' => '',     
           'eventType' => '', 
           'status' => $status,
           'insimage' => $insimage,
           'instrument_creation_timeago' => $instrument_creation_timeago,
           'creator_avatar' => $creator_avatar,
           'creator_name' => $creator_name,
           'creationType' => $creationType,
           'rights' => $rights,
           'sessiondata' => $sessiondata
        ]);   
} // end show addevent form function



// shows form for updating instrument's production event
public function productionevent($instrumentID)
{
        $role = \Session::get('role');
        $sessiondata = session()->all();
        $instrumentID = (int) $instrumentID; // force int

        // Fetch instrument data
         $instrument = \DB::table('instruments')
        ->where('instrumentID', '=', $instrumentID)  
        ->take(1)->get();
        
        if (sizeof($instrument) < 1) {
                  return \Redirect::action('AdminController@movedordeleted'); // redirect if no result for this instrument
        }

        // get production event details
        $status = $instrument[0]->status;
        $thesaurusID = $instrument[0]->thesaurusID;  
        $cityID = $instrument[0]->cityID;          
        $legalBodyID = $instrument[0]->legalBodyID;  
        $productionEventLocation = $instrument[0]->productionEventLocation;
        $productionEventName = $instrument[0]->productionEventName;
        $productionEventNameSource = $instrument[0]->productionEventNameSource;
        $productionEventCulture = $instrument[0]->productionEventCulture;
        $productionEventDateText = $instrument[0]->productionEventDateText;
        $productionEventEarliestDate = $instrument[0]->productionEventEarliestDate;
        $productionEventLatestDate = $instrument[0]->productionEventLatestDate;
        $productionPeriodName = $instrument[0]->productionPeriodName;
        $productionMaterialsFreeText = $instrument[0]->productionMaterialsFreeText;
        $productionMaterials = $instrument[0]->productionMaterials;
        $prod_event_actors=''; $other_event_actors='';

        // if user is 'Admin', is this instrument in their collection?
        if ($role == "Admin") {
            $mylegalbody = \Session::get('legalBodyID');
               if ($mylegalbody != $legalBodyID) {
                  return \Redirect::action('AdminController@movedordeleted'); // redirect if instrument is not in Admin's collection
               }
        }

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
         $legalBodyID = $legalbodies[0]->legalBodyID;  
         $legalBodyName = $legalbodies[0]->legalBodyName;  
         $legalBodyMDAcode = $legalbodies[0]->legalBodyMDAcode;  

        // Fetch repository data
         $repositoryName = '';
         $repositories = \DB::table('repositories')
        ->where('instrumentID', '=', $instrumentID)  
        ->take(1)->get();

        if (sizeof($repositories) != 0 )    // get repository 
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
          
       // get rights information for this instrument, if exists
       $rights = \DB::table('rights')
      ->where('instrumentID', '=', $instrumentID)  
      ->where('rightsFlag', '=', "instrument")  
      ->get();

        return view('admin.productionevent')->with([          
           'page' => 'productionevent',
           'role' => \Session::get('role'),
           'instrumentID' => $instrumentID, 
           'thesaurusID' => $thesaurusID,   
           'cityID' => $cityID,
           'titlePreferred' => $titlePreferred,  
           'titleSingle' => $titleSingle,  
           'legalBodyID' => $legalBodyID, 
           'legalBodyName' => $legalBodyName, 
           'legalBodyMDAcode' => $legalBodyMDAcode,
           'imageCount' => $imageCount, 
           'audioCount' => $audioCount, 
           'videoCount' => $videoCount, 
           'prod_event_actors' => $prod_event_actors,
           'other_event_actors' => $other_event_actors,
           'events' => $events,
           'eventID' => '',          
           'status' => $status,
           'insimage' => $insimage,
           'actorID' => '',
           'productionEventLocation' => $productionEventLocation, 
           'productionEventName' => $productionEventName, 
           'productionEventNameSource' => $productionEventNameSource,
           'productionEventCulture' => $productionEventCulture, 
           'productionEventDateText' => $productionEventDateText, 
           'productionEventEarliestDate' => $productionEventEarliestDate,
           'productionEventLatestDate' => $productionEventLatestDate, 
           'productionPeriodName' => $productionPeriodName, 
           'productionMaterialsFreeText' => $productionMaterialsFreeText,
           'productionMaterials' => $productionMaterials,
           'instrument_creation_timeago' => $instrument_creation_timeago,
           'creator_avatar' => $creator_avatar,
           'creator_name' => $creator_name,
           'rights' => $rights,
           'creationType' => $creationType,
           'sessiondata' => $sessiondata
        ]);  

} // end show production event update form



// function to store editing production event
public function store_productionevent() {
      $sessiondata = session()->all();
      $role = \Session::get('role');
      $user_id = Auth::user()->getId();

      if ($role != "SuperAdmin" && $role != "Cataloguer" && $role != "Admin")
      {          
          return \Redirect::action('AdminController@index'); //redirect to dash if no SuperAdmin, Cataloguer or Admin
      }

      // get all input values
      $input = Input::all();
      $instrumentID = (int) Input::get('instrumentID'); // forcing the id into an int   
      $cityID = Input::get('cityID');             // forcing the id into an int            
      $productionEventName = Input::get('productionEventName');  
      $productionEventLocation = htmlentities(Input::get('productionEventLocation')); // html entities due to values from thesauru
      $productionEventEarliestDate = Input::get('productionEventEarliestDate');      
      $productionEventLatestDate = Input::get('productionEventLatestDate');
      $productionEventDateText = Input::get('productionEventDateText');      
      $productionEventCulture = Input::get('productionEventCulture');     
      $productionPeriodName = Input::get('productionPeriodName');     
      $productionMaterialsFreeText = Input::get('productionMaterialsFreeText');           

      // Fetch instrument data
       $instrument = \DB::table('instruments')
      ->where('instrumentID', '=', $instrumentID)  
      ->take(1)->get();

      $legalBodyID = $instrument[0]->legalBodyID;  

      if (sizeof($instrument) < 1) {
                return \Redirect::action('AdminController@movedordeleted'); // redirect if no instrument
      }


      if ($role == "Admin")
      {
          $usercollection = \Session::get('legalBodyID');
          if ($legalBodyID != $usercollection) {
                return \Redirect::action('AdminController@movedordeleted');    // redirect if 'Admin' and production event doesn't belong to their collection
          }
      }


      $instrumentName = $instrument[0]->titlePreferred;  

      $legalbodies = \DB::table('legalbodies')
      ->where('legalBodyID', '=', $legalBodyID)  
      ->take(1)->get();


      if (sizeof($legalbodies) < 1) {
                return \Redirect::action('AdminController@movedordeleted'); // redirect if no result for this collection
      }

      $legalBodyName = $legalbodies[0]->legalBodyName;  

          // update event
          \DB::table('instruments')
          ->where('instrumentID', '=', $instrumentID)    
          ->update(array('cityID' => $cityID, 'productionEventName' =>$productionEventName, 'productionEventLocation' =>$productionEventLocation, 'productionEventEarliestDate' =>$productionEventEarliestDate, 'productionEventLatestDate' =>$productionEventLatestDate, 'productionEventDateText' => $productionEventDateText, 'productionEventCulture' => $productionEventCulture, 'productionPeriodName' => $productionPeriodName, 'productionMaterialsFreeText' => $productionMaterialsFreeText));

          // now set the flash data to show the user has been updated...
          session()->flash('flashdata', 'Production Event updated successfully!');

          // add production event activity
          \DB::table('user_activity')->insert(
            ['userID' => $user_id, 'activity' => "You updated an instrument's production event", 'legalBodyID' => $legalBodyID, 'legalBodyName' => $legalBodyName, 'instrumentID' => $instrumentID, 'instrumentName' => $instrumentName, 'activityDate' => \Carbon\Carbon::now() ]
          );

        // redirect to editing this event
        return \Redirect::action('AdminController@productionevent', array('instrumentID' => $instrumentID));

} // end store production event



// edit event other than production
public function editevent($instrumentID, $eventID)
{
      $instrumentID = (int) $instrumentID;          // force int
      $eventID = (int) $eventID;                    // force int
      $sessiondata = session()->all();

      // Fetch instrument data
       $instrument = \DB::table('instruments')
      ->where('instrumentID', '=', $instrumentID)  
      ->take(1)->get();

      if (sizeof($instrument) < 1) {
                return \Redirect::action('AdminController@movedordeleted'); // redirect if no result for this instrument
      }

      // get events others than production for this instrument
       $events = \DB::table('events')
      ->where('instrumentID', '=', $instrumentID)  
      ->get();

      // get this event
       $thisevent = \DB::table('events')
      ->where('eventID', '=', $eventID)  
      ->get();

      // redirect if no event
      if (sizeof($thisevent) < 1) {
                return \Redirect::action('AdminController@movedordeleted');
      }

      // get instrument details
      $eventType = $thisevent[0]->eventType;
      $status = $instrument[0]->status;
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

      if (sizeof($repositories) != 0 )   // get repository 
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
             // $Level_0 = $thesaurus[0]->Level_0;   // not required  (always 'Instrument')
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

      return view('admin.editevent')->with([          
         'page' => 'editevent',
         'role' => \Session::get('role'),
         'instrumentID' => $instrumentID, 
         'thesaurusID' => $thesaurusID,   
         'titlePreferred' => $titlePreferred,  
         'titleSingle' => $titleSingle,  
         'legalBodyID' => $legalBodyID, 
         'legalBodyName' => $legalBodyName, 
         'legalBodyMDAcode' => $legalBodyMDAcode,
         'imageCount' => $imageCount, 
         'audioCount' => $audioCount, 
         'videoCount' => $videoCount, 
         'actorID' => '',
         'eventType' => $eventType,
         'thisevent' => $thisevent,
         'events' => $events,
         'eventID' => $eventID,
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
} // end editevent



// preview instrument
public function previewinstrument($instrumentID)
{
    $sessiondata = session()->all();
    $instrumentID = (int) $instrumentID;    // force int

    // Fetch instrument data
    $instrument = \DB::table('instruments')
    ->where('instrumentID', '=', $instrumentID)  
    ->take(1)->get();

    if (sizeof($instrument) < 1) { 
              return \Redirect::action('AdminController@movedordeleted');    // redirect if no result for instrument
    }

    $creationType = $instrument[0]->creationType; 
    $legalBodyID = $instrument[0]->legalBodyID; 
    $adminID = $instrument[0]->adminID; 
    $status = $instrument[0]->status; 
    $hornbostelCat = $instrument[0]->hornbostelCat; 
    $tags = $instrument[0]->tags; 

    // Fetch measurements(s) data, if available
    $measurements = \DB::table('measurements')
    ->where('instrumentID', '=', $instrumentID)      
    ->get();

    $inscriptions = $instrument[0]->inscriptions;
    $productionMaterialsFreeText = $instrument[0]->productionMaterialsFreeText;
    $productionMaterials = $instrument[0]->productionMaterials;
    $serialEditionNumbers = $instrument[0]->serialEditionNumbers;        
    $decorativeElements = $instrument[0]->decorativeElements;

    // get rights information for this instrumnt, if it exists
    $rights = \DB::table('rights')
    ->where('rightsFlag', '=', 'instrument')  
    ->where('instrumentID', '=', $instrumentID)          
    ->get();

    $productionMaterials = str_replace('|', ', ', $productionMaterials);
    $inscriptions = explode('|', $inscriptions);
    $serialEditionNumbers = str_replace("|",", ",$serialEditionNumbers);
    $decorativeElements = explode('|', $decorativeElements);     
    $created_at = $instrument[0]->created_at; 
    $updated_at = $instrument[0]->updated_at; 

    // get user details and time for creation...
    $instrument_created_at = new \Carbon\Carbon($created_at);
    $now = \Carbon\Carbon::now();

    // get difference between now and collection creation
    $instrument_creation_timeago=$instrument_created_at->diffForHumans(\Carbon\Carbon::now());
    $instrument_creation_timeago = str_replace("before","ago",$instrument_creation_timeago);
    $instrument_created_at = $instrument_created_at->formatLocalized('%A %d %B %Y');  

    $admin_creation_user = \DB::table('users')
    ->where('id', '=', $adminID)  
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

    // Fetch description(s) data
     $descriptions = \DB::table('descriptions')
    ->where('instrumentID', '=', $instrumentID)  
    ->get();
    
    // get events others than production for this instrument
     $events = \DB::table('events')
    ->where('instrumentID', '=', $instrumentID)  
    ->get();

    // Fetch repository data
     $repositoryName = '';
     $repositories = \DB::table('repositories')
    ->where('instrumentID', '=', $instrumentID)  
    ->take(1)->get();

    // get repository
    $repositoryName = $repositories[0]->repositoryName;  
    $inventoryNumber = $repositories[0]->inventoryNumber;  

    // get resources for this instrument into separate arrays for images, sound and video
     $images = \DB::table('resources')
    ->where('instrumentID', '=', $instrumentID) 
    ->where('resourceType', 'image') 
    ->get();

     $videos = \DB::table('resources')
    ->where('instrumentID', '=', $instrumentID) 
    ->where('resourceType', 'video') 
    ->get();

     $sounds = \DB::table('resources')
    ->where('instrumentID', '=', $instrumentID) 
    ->where('resourceType', 'sound') 
    ->get();


    if ($thesaurusID > 0) 
    {
       $thesaurus = \DB::table('thesauruses')
      ->where('thesaurusID', '=', $thesaurusID)  
      ->take(1)->get();

           // get levels for approved term for title
           // $Level_0 = $thesaurus[0]->Level_0;   // not required  
           $Level_1 = ucfirst($thesaurus[0]->Level_1);  
           $Level_2 = ucfirst($thesaurus[0]->Level_2);  
           $Level_3 = ucfirst($thesaurus[0]->Level_3);  
           //$titlePreferred = $Level_1." > ".$Level_2." > ".$Level_3;
           $titlePreferred = ucfirst($Level_1)." > ".ucfirst($Level_2)." > ".ucfirst($instrument[0]->titlePreferred);
           $titleSingle = ucfirst($instrument[0]->titlePreferred);

     } else {

           // not an approved term - we can use the value straight from the database...
           $titleSingle = ucfirst($instrument[0]->titlePreferred);
           $titlePreferred = ucfirst($instrument[0]->titlePreferred);
           $Level_3 = ucfirst($instrument[0]->titlePreferred);
     }

    $legalbody = \DB::table('legalbodies')
    ->where('legalBodyID', '=', $legalBodyID) 
    ->take(1)->get();

     $legalBodyName = $legalbody[0]->legalBodyName;  
     $legalBodyMDAcode = $legalbody[0]->legalBodyMDAcode;  

    return view('admin.previewinstrument')->with([          
       'role' => \Session::get('role'),
       'instrumentID' => $instrumentID, 
       'thesaurusID' => $thesaurusID,   
       'titlePreferred' => $titlePreferred,  
       'titleSingle' => $titleSingle,  
       'repositoryName' => $repositoryName, 
       'legalBodyName' => $legalBodyName, 
       'legalBodyMDAcode' => $legalBodyMDAcode,  
       'images' => $images,    
       'videos' => $videos,    
       'sounds' => $sounds,    
       'creationType' => $creationType,   
       'adminID' => $adminID,   
       'status' => $status,   
       'hornbostelCat' => $hornbostelCat,   
       'tags' => $tags,   
       'repositoryName' => $repositoryName,   
       'inventoryNumber' => $inventoryNumber,  
       'instrument_creation_timeago' => $instrument_creation_timeago,  
       'creator_avatar' => $creator_avatar,  
       'creator_name' => $creator_name,  
       'instrument_created_at' => $instrument_created_at,
       'mainDescriptionType' => $instrument[0]->mainDescriptionType,
       'mainDescriptionSource' => $instrument[0]->mainDescriptionSource,
       'mainDescriptionText' => $instrument[0]->mainDescriptionText,
       'productionEventLocation' => $instrument[0]->productionEventLocation,
       'productionEventName' => $instrument[0]->productionEventName,
       'productionEventNameSource' => $instrument[0]->productionEventNameSource,
       'productionEventCulture' => $instrument[0]->productionEventCulture,
       'productionEventDateText' => $instrument[0]->productionEventDateText,
       'productionEventEarliestDate' => $instrument[0]->productionEventEarliestDate,
       'productionEventLatestDate' => $instrument[0]->productionEventLatestDate,
       'events' => $events,
       'productionPeriodName' => $instrument[0]->productionPeriodName,
       'productionMaterialsFreeText' => $instrument[0]->productionMaterialsFreeText,
       'productionMaterials' => $instrument[0]->productionMaterials,
       'measurementsFreeText' => $instrument[0]->measurementsFreeText, 
       'measurements' => $measurements,
       'sourceWebsite' => $instrument[0]->sourceWebsite,         
       'serialEditionNumbers' => $serialEditionNumbers, 
       'decorativeElements' => $decorativeElements,
       'inscriptions' => $inscriptions,
       'productionMaterials' => $productionMaterials,
       'productionMaterialsFreeText' => $productionMaterialsFreeText,
       'rights' => $rights,
       'created_at' => $created_at,
       'updated_at' => $updated_at,
       'descriptions' => $descriptions,
       'sessiondata' => $sessiondata
    ]);  

} // end preview instrument



// used to store all events, edited or added, except production events...
public function store_event() 
{
      $sessiondata = session()->all();
      $role = \Session::get('role');
      $user_id = Auth::user()->getId();

      if ($role != "SuperAdmin" && $role != "Cataloguer" && $role != "Admin") // REDIRECT TO DASHBOARD IF NOT SUPERADMIN, CATALOGUER OR ADMIN
      {
          return \Redirect::action('AdminController@index');
      }

      // get all input values  
      $input = Input::all();
      $instrumentID = (int) Input::get('instrumentID'); // forcing the id into an int       
      $eventID = Input::get('eventID');                 // forcing the id into an int 

      if ($eventID != "NEWEVENT")
      {  
          // check that this event belongs to this instrument... if not, something is wrong, redirect...
           $eventvalid = \DB::table('events')
          ->where('instrumentID', '=', $instrumentID)  
          ->where('eventID', '=', $eventID)        
          ->take(1)->get();

          if (sizeof($eventvalid) < 1) {
                    return \Redirect::action('AdminController@movedordeleted');
          } 
      }    

      $eventName = Input::get('eventName');
      $eventType = Input::get('eventType');
      $cityID = (int) Input::get('cityID');
      $location = Input::get('location');
      $earliestDate = Input::get('earliestDate');      
      $latestDate = Input::get('latestDate');
      $eventDateText = Input::get('eventDateText'); 
      $eventCulture = Input::get('eventCulture');
      $periodName = Input::get('periodName');     
      $materialsText = Input::get('materialsText');           
   

     // get legalbody from instrument
         $instrument = \DB::table('instruments')
        ->where('instrumentID', '=', $instrumentID)  
        ->take(1)->get();

          if (sizeof($instrument) < 1) {
                    return \Redirect::action('AdminController@movedordeleted'); // can't find instument
          }

         $legalBodyID = $instrument[0]->legalBodyID;
         $instrumentName = $instrument[0]->titlePreferred;

         $legalbodies = \DB::table('legalbodies')
        ->where('legalBodyID', '=', $legalBodyID)  
        ->take(1)->get();

        if (sizeof($legalbodies) < 1) {
                  return \Redirect::action('AdminController@movedordeleted');   // can't find collection
        }

        $legalBodyName = $legalbodies[0]->legalBodyName;



      if ($eventID == "NEWEVENT")
       { 
            // add this new event
            $eventID = \DB::table('events')->insertGetId(
            ['instrumentID' => $instrumentID, 'legalBodyID' => $legalBodyID, 'legalBodyID' => $legalBodyID, 'cityID' => $cityID, 'eventName' => $eventName, 'eventType' => $eventType, 'location' =>$location, 'eventEarliestDate' =>$earliestDate, 'eventLatestDate' => $latestDate, 'eventDateText' => $eventDateText, 'eventCulture' => $eventCulture, 'periodName' =>$periodName, 'materialsText' => $materialsText ]
            );

            session()->flash('flashdata', 'Event added successfully!');

            // add new event activity
            \DB::table('user_activity')->insert(
              ['userID' => $user_id, 'activity' => "You added an instrument event", 'instrumentID' => $instrumentID, 'instrumentName' => $instrumentName, 'legalBodyID' => $legalBodyID, 'legalBodyName' => $legalBodyName, 'eventID' => $eventID, 'eventType' =>$eventType,'activityDate' => \Carbon\Carbon::now() ]
            );
            
      } else {

            // update this existing event
            \DB::table('events')
            ->where('instrumentID', '=', $instrumentID)  
            ->where('eventID', '=', $eventID)     
            ->update(array('eventName' => $eventName, 'eventType' =>$eventType,  'cityID' => $cityID, 'location' =>$location, 'eventEarliestDate' =>$earliestDate, 'eventLatestDate' => $latestDate, 'eventDateText' => $eventDateText, 'eventCulture' => $eventCulture, 'periodName' =>$periodName, 'materialsText' => $materialsText));

            session()->flash('flashdata', 'Event updated successfully!');

            // add update existing event activity
            \DB::table('user_activity')->insert(
              ['userID' => $user_id, 'activity' => "You updated an instrument event", 'instrumentID' => $instrumentID, 'instrumentName' => $instrumentName, 'legalBodyID' => $legalBodyID, 'legalBodyName' => $legalBodyName, 'eventID' => $eventID, 'eventType' =>$eventType, 'activityDate' => \Carbon\Carbon::now() ]
            );
       }     

       // redirect to editing this event
       return \Redirect::action('AdminController@editevent', array('instrumentID' => $instrumentID, 'eventID' => $eventID));

} // end store event



// function to show delete collection form
public function deletecollection($legalBodyID) 
    {
        $role = \Session::get('role');
        $sessiondata = session()->all();
        $legalBodyID = (int) $legalBodyID; // force int
        $user_id = Auth::user()->getId();

        if ($role != "SuperAdmin" && $role != "Cataloguer")
        {
            return \Redirect::action('AdminController@index');  // REDIRECT TO DASHBOARD IF NOT SUPERADMIN OR CATALOGUER
        }

        // Get all the legalbody info for this collection
         $legalbodies = \DB::table('legalbodies')
        ->where('legalBodyID', '=', $legalBodyID)  
        ->take(1)->get();

        if (sizeof($legalbodies) < 1) {
                  return \Redirect::action('AdminController@movedordeleted');        // REDIRECT IF NO RESULT FOR THIS COLLECTION
        }

         $insCount="0";
         $insCount = \DB::table('instruments') // number of instruments in collection
        ->where('legalBodyID', '=', $legalBodyID)  
        ->count();

        // can't delete collection if admins exist that are tied to it...
         $collectionusers = \DB::table('users')
        ->where('legalBodyID', '=', $legalBodyID)  
        ->get();

        // get user details and time for creation...
        $creator_id = $legalbodies[0]->creatorAdminID;
        $collection_created_at = new \Carbon\Carbon($legalbodies[0]->created_at);
        $now = \Carbon\Carbon::now();

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

                    // get difference between now and activity_date
                    $timeago[$i]=$created_at->diffForHumans(\Carbon\Carbon::now());

                    // carbon function returns 'before', let's replace it with 'ago'...
                    $timeago[$i] = str_replace("before","ago",$timeago[$i]);

                    // user activity
                   if (sizeof($admin_user) > 0) {
                      $admin_pic[$i] = $admin_user[0]->avatar;
                      $admin_name[$i] = $admin_user[0]->name;
                   } else {
                      $admin_pic[$i] = 'defaults/deleted_user.jpg';
                      $admin_name[$i] = 'Deleted User';
                   }
          $i++;
        } // end for activity foreach

        return view('admin.deletecollection')->with([          
           'page' => 'deletecollection',
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
           'collectionusers' => $collectionusers,
           'insCount' => $insCount,
           'sessiondata' => $sessiondata
        ]);    

} // end show delete collection form



// function to immediately delete items (instruments,collections,users) and return user to correct list
public function delete_now()
{ 
      $role = \Session::get('role');
      if ($role != "SuperAdmin" && $role != "Cataloguer" && $role != "Admin")
      {
          return \Redirect::action('AdminController@index'); // redirect to dash if not super, cataloguer, or admin
      }

      // get all input values  
      $input = Input::all();
      $deletiontype = Input::get('deletiontype'); // the type of deletion...
      $user_id = Auth::user()->getId();

   
      if ($deletiontype == "collection") // user is deleting a collection
      {

          if ($role != "SuperAdmin" && $role != "Cataloguer")
          {
              return \Redirect::action('AdminController@index'); // redirect to dash if not super or cataloguer
          }

              $legalBodyID = (int) Input::get('legalBodyID'); // force int

              // Fetch collection data
               $legalbody = \DB::table('legalbodies')
              ->where('legalBodyID', '=', $legalBodyID)  
              ->take(1)->get();

                if (sizeof($legalbody) < 1) {
                          return \Redirect::action('AdminController@movedordeleted'); // can't find legalbody
                }

              // can't delete collection if admins exist that are tied to it...
               $collectionusers = \DB::table('users')
              ->where('legalBodyID', '=', $legalBodyID)  
              ->get();

                if (sizeof($collectionusers) > 0) {
                          return \Redirect::action('AdminController@movedordeleted'); // this collection has users...can't delete it
                }

                $legalBodyMDAcode = $legalbody[0]->legalBodyMDAcode;
                $legalBodyName = $legalbody[0]->legalBodyName;

                // delete collection's folders 
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

                  if (file_exists($imagepath)) 
                  {    // delete it..
                      \File::deleteDirectory($imagepath);
                  }

                  if (file_exists($imagethumbpath)) 
                  {    // delete it..
                      \File::deleteDirectory($imagethumbpath);
                  }

                  if (file_exists($soundpath)) 
                  {    // delete it..
                      \File::deleteDirectory($soundpath);
                  }

                  if (file_exists($videopath)) 
                  {    // delete it..
                      \File::deleteDirectory($videopath);
                  }

                  if (file_exists($xmlpath)) 
                  {    // delete it..
                      \File::deleteDirectory($xmlpath);
                  }

                  if (file_exists($raw_local_path)) 
                  {    // delete it..
                      \File::deleteDirectory($raw_local_path);
                  }


                  // delete all instruments in this collection
                   \DB::table('instruments')->where('legalBodyID', '=', $legalBodyID)->delete();

                  // actually delete the collection
                   \DB::table('legalbodies')->where('legalBodyID', '=', $legalBodyID)->delete();

                  // delete descriptions of instruments belonging to this collection
                   \DB::table('descriptions')->where('legalBodyID', '=', $legalBodyID)->delete();

                  // delete event actors of instruments belonging to this collection
                   \DB::table('eventactors')->where('legalBodyID', '=', $legalBodyID)->delete();

                  // delete events of instruments belonging to this collection
                   \DB::table('events')->where('legalBodyID', '=', $legalBodyID)->delete();

                  // delete measurements of instruments belonging to this collection
                   \DB::table('measurements')->where('legalBodyID', '=', $legalBodyID)->delete();

                  // delete repositories of instruments belonging to this collection
                   \DB::table('repositories')->where('legalBodyID', '=', $legalBodyID)->delete();

                  // delete user activity belonging to this collection
                   \DB::table('user_activity')->where('legalBodyID', '=', $legalBodyID)->delete();

                  // have deleted the resources folders, remove all from dbase
                   \DB::table('resources')->where('legalBodyID', '=', $legalBodyID)->delete();

                  // now set the flash data to show the user has been updated...
                   session()->flash('flashdata', 'Collection deleted successfully!');

                  // add delete collection to activity
                  \DB::table('user_activity')->insert(
                    ['userID' => $user_id, 'activity' => "You deleted a collection", 'legalBodyID' => $legalBodyID, 'legalBodyName' => $legalBodyName, 'activityDate' => \Carbon\Carbon::now() ]
                  );

                 // redirect to collections list
                 return \Redirect::action('EloquentController@existingcollections');

        } // end if deleting a collection...        



    
      if ($deletiontype == "instrument") // user is deleting an instrument    
      {
              $instrumentID = (int) Input::get('instrumentID'); // force int

              // Fetch instrument data
               $instrument = \DB::table('instruments')
              ->where('instrumentID', '=', $instrumentID)  
              ->take(1)->get();

                if (sizeof($instrument) < 1) {
                          return \Redirect::action('AdminController@movedordeleted'); // redirect if no result for instrument
                }

               $legalBodyID = $instrument[0]->legalBodyID;
               $titlePreferred = $instrument[0]->titlePreferred;

                // Fetch legal body data
                 $legalbody = \DB::table('legalbodies')
                ->where('legalBodyID', '=', $legalBodyID)  
                ->take(1)->get();

                $legalBodyMDAcode = $legalbody[0]->legalBodyMDAcode;  
                $legalBodyName = $legalbody[0]->legalBodyName;  

              // delete production event actors
              \DB::table('eventactors')->where('instrumentID', '=', $instrumentID)->delete();
             
              // delete the actors for other instrument events
               $events = \DB::table('events')
              ->where('instrumentID', '=', $instrumentID)  
              ->get();

                 if (sizeof($events) > 0) {
                    foreach ($events as $event)
                    {
                       // delete the actors for this event
                        \DB::table('eventactors')->where('eventID', '=', $event->eventID)->delete();
                    }
                 }   

              // now delete the other events      
              \DB::table('events')->where('instrumentID', '=', $instrumentID)->delete();

              // delete repositories
              \DB::table('repositories')->where('instrumentID', '=', $instrumentID)->delete();

              // delete descriptions
              \DB::table('descriptions')->where('instrumentID', '=', $instrumentID)->delete();

              // delete measurements
              \DB::table('measurements')->where('instrumentID', '=', $instrumentID)->delete();

              // delete any rights info for this instrument event actors
              \DB::table('rights')->where('instrumentID', '=', $instrumentID)->delete();

              // iterate through instrument's resources and delete them
               $resources = \DB::table('resources')
              ->where('instrumentID', '=', $instrumentID)  
              ->get();

                    foreach ($resources as $resource)
                    {
                           if ($resource->resourceType == "image")
                           {
                            // delete the image
                              \File::Delete('instrument_resources/images/'.$legalBodyMDAcode.'/'.$resource->resourceFileName);
                             // ... and delete thumbnail if it is there
                              \File::Delete('instrument_resources/images/'.$legalBodyMDAcode.'/thumbnails/'.$resource->resourceFileName);
                           }   

                           if ($resource->resourceType == "sound")
                           {
                            // delete the audio
                              \File::Delete('instrument_resources/sound/'.$legalBodyMDAcode.'/'.$resource->resourceFileName);
                           }   

                           if ($resource->resourceType == "video")
                           {
                            // delete the video
                              \File::Delete('instrument_resources/video/'.$legalBodyMDAcode.'/'.$resource->resourceFileName);
                           }   

                          // delete any rights info for this resource 
                          \DB::table('rights')->where('resourceID', '=', $resource->resourceID)->delete();

                    }              

              // delete the resources in the dbase for this instrument
              \DB::table('resources')->where('instrumentID', '=', $instrumentID)->delete();

              // delete the actual instrument
              \DB::table('instruments')->where('instrumentID', '=', $instrumentID)->delete();
    
              // delete user activity belonging to this instrument
               \DB::table('user_activity')->where('instrumentID', '=', $instrumentID)->delete();

              // delete all rights information for this instrument, if present (will delete instrument and resource rights for this instrument)
              \DB::table('rights')->where('instrumentID', '=', $instrumentID)->delete();

              // deleted instrument user flashdata
              session()->flash('flashdata', 'Instrument deleted successfully!');
      
              // add delete instrument to activity table
              \DB::table('user_activity')->insert(
                ['userID' => $user_id, 'activity' => "You deleted an instrument", 'instrumentName' => $titlePreferred, 'legalBodyID' => $legalBodyID, 'legalBodyName' => $legalBodyName, 'activityDate' => \Carbon\Carbon::now() ]
              );              

              // redirect to collection's instrument list
              return \Redirect::action('AdminController@viewedit', array('collectionID' => $legalBodyID));

        } // end if deleting a collection...  



     
      if ($deletiontype == "user")  // user is deleting a user  
      {
              if ($role != "SuperAdmin")
              {
                  return \Redirect::action('AdminController@index'); // redirect to dash if not SuperAdmin
              }

              // get inputs
              $id = (int) Input::get('id'); // force int for user's id
       
              // update the deleted user's activity with user deletion flag
              \DB::table('user_activity')
               ->where('userID', $id)
               ->update(array('user_deleted' => '1'));

              // check user
               $user =  \DB::table('users')
               ->where('id', '=', $id)  
               ->take(1)->get();

               if (sizeof($user) < 1) {
                       return \Redirect::action('AdminController@movedordeleted');
               }
 
               $this_userName = $user[0]->name." ".$user[0]->surname;

              if ($id != 1) { // don't delete main account
                    // delete the user
                      \DB::table('users')->where('id', '=', $id)->delete();

                    // flash data
                      session()->flash('flashdata', 'User deleted successfully!');

                    // add delete user to activity table
                      \DB::table('user_activity')->insert(
                      ['userID' => $user_id, 'activity' => "You deleted an admin user", 'this_userName' => $this_userName,'activityDate' => \Carbon\Carbon::now() ]
                     );              
              }

              // redirect to existing users
              return \Redirect::action('EloquentController@existingusers');

        } // end if deleting a user.  

} // end delete now







// restricted function for Admins for editing collection profile (can't change collection name, shortname, or MDACode)
public function legalbodyprofile()
{
        $sessiondata = session()->all();
        $user_id = Auth::user()->getId();
        $role = \Session::get('role');

        if ($role == "SuperAdmin")
        {
            return \Redirect::action('EloquentController@existingcollections');  // redirect to existing collection (SuperAdmins use edit collection)
        }

        // get the info for the legal body that the user is assigned to 
         $legalbodies = \DB::table('legalbodies')
        ->where('legalBodyID', '=', \Session::get('legalBodyID'))  
        ->take(1)->get();

        return view('admin.legalbodyprofile')->with([          
           'page' => 'legalbodyprofile',
           'role' => \Session::get('role'),
           'legalbodies' => $legalbodies,
           'sessiondata' => $sessiondata
        ]);    

} // end legalbodyprofile function



// function to store edited collection for admins (can't change name, shortname or MDAcode)
public function storelegalbodyprofile()
{
      // the largest size to contstrain either width or height for thumbnails (should be in config...)
      $largest_thumbnail_pixels = "100"; 

      // user_id
      $user_id = Auth::user()->getId();

      // get all input values  
      $input = Input::all();
      $legalBodyWebsite = Input::get('legalBodyWebsite');
      $legalBodyImage = Input::get('legalBodyImage');
      $longitude = Input::get('longitude');
      $latitude = Input::get('latitude');
      $legalBodyDescription = Input::get('legalBodyDescription');
      $legalBodyDefaultRepository = Input::get('legalBodyDefaultRepository');
      $legalBodyOrigImage = Input::get('legalBodyOrigImage'); // original image   
      
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

           // save thumb 
           $img->save('images/legalBodyImages/thumbnails/'.$fileName);

           // delete the original resource as they have uploaded a new one
            \File::Delete('images/legalBodyImages/'.$legalBodyOrigImage);

           // ... and delete thumbnail if it is there
            \File::Delete('images/legalBodyImages/thumbnails/'.$legalBodyOrigImage);

      } else {
            $fileName = $legalBodyOrigImage; // not uploaded anything, keep image same as orig sent in form
      }   

      // update collection
        \DB::table('legalbodies')
         ->where('legalBodyID', \Session::get('legalBodyID'))
         ->update(array('legalBodyWebsite' =>$legalBodyWebsite, 'legalBodyImage' =>$fileName, 'legalBodyDescription' =>$legalBodyDescription, 'legalBodyDefaultRepository' => $legalBodyDefaultRepository, 'longitude' => $longitude, 'latitude' => $latitude, 'updated_at' => \Carbon\Carbon::now() ));

      // now set the flash data to show the collection has been successfully updated...
      session()->flash('flashdata', 'You updated a collection!');

      // Fetch legal body data
      $legal_body_info = \DB::table('legalbodies')
      ->where('legalBodyID', '=', \Session::get('legalBodyID'))  
      ->take(1)->get();
       $legalBodyID = $legal_body_info[0]->legalBodyID;  
       $legalBodyName = $legal_body_info[0]->legalBodyName; 

       if (sizeof($legal_body_info) < 1) { 
         return \Redirect::action('AdminController@index');  // can't find legalbody, redirect..
       }

       // add update legal body activity
       \DB::table('user_activity')->insert(
         ['userID' => $user_id, 'activity' => "You updated a collection", 'legalBodyID' => \Session::get('legalBodyID'), 'legalBodyName' => $legalBodyName, 'activityDate' => \Carbon\Carbon::now() ]
       );

       // redirect to legal body
       return \Redirect::action('AdminController@legalbodyprofile');

} // end store legalbody




// function to show instruments list
public function viewedit($collectionID = "")
{
        $sessiondata = session()->all();
        $adminlegalBodyName = "";
        $role = \Session::get('role');
        $legalBodyID = \Session::get('legalBodyID');

        $admin_legal_body = \DB::table('legalbodies')
        ->where('legalBodyID', '=', $legalBodyID)   
        ->take(1)->get();
        
        if (sizeof($admin_legal_body) < 1) {
                  return \Redirect::action('AdminController@movedordeleted'); // redirect if no result for collection
        } else {
          $adminlegalBodyName = $admin_legal_body[0]->legalBodyName;
        }


        if ($role == "Admin")
        {
            // user is restricted to specified collection
            $adminlegalBodyName = $admin_legal_body[0]->legalBodyName;
        } else {
            // user not restricted to specified collection
            $admin_legal_body = "";
        }


        if ($role == "SuperAdmin" || $role == "Cataloguer") {
       
          if ($collectionID > 0) { // check collection exists
             
              $admin_legal_body = \DB::table('legalbodies')
              ->where('legalBodyID', '=', $collectionID)   
              ->take(1)->get();

                  if (sizeof($admin_legal_body) < 1) {
                            return \Redirect::action('AdminController@movedordeleted'); // redirect if no result for this collection
                  } else {
                    $adminlegalBodyName = $admin_legal_body[0]->legalBodyName;
                  }
              
          } else {

            $adminlegalBodyName = "All";

          }

        } // end if super or cataloguer

        $legalbodies = \DB::table('legalbodies')
        ->orderBy('legalBodyName', 'asc')
        ->get();

        return view('datatables.eloquent.basic-object')->with([          
           'page' => 'viewedit',
           'role' => \Session::get('role'),
           'adminlegalBodyName' => $adminlegalBodyName,
           'legalbodies' => $legalbodies,
           'collectionID' => $collectionID,
           'sessiondata' => $sessiondata
        ]);  

} // end function to show instrument list



// SuperAdmin only function to select criteria to import instruments into a collection
public function importintocollection($import_legalBodyID = '')
{
      $sessiondata = session()->all();
      $role = \Session::get('role');
      $user_id = Auth::user()->getId();

      if ($role != "SuperAdmin")
      {
          return \Redirect::action('AdminController@index'); // redirect to dash if not SuperAdmin
      }

      // if default_legalBodyID is set from the route, then user has come from collection page so make that collection selected
      if (strlen($import_legalBodyID) > 0)
      {
        $collectionID = $import_legalBodyID;
      } else { // otherwise go with their default legal body from their session
        $collectionID = \Session::get('legalBodyID');
      } 


      // Get legal body info for this collection
       $legalbodies = \DB::table('legalbodies')
      ->where('legalBodyID', '=', $collectionID)  
      ->take(1)->get();

      // redirect if no result for this collection
      if (sizeof($legalbodies) < 1) {
                return \Redirect::action('AdminController@movedordeleted'); 
      }

        $legalbodies = \DB::table('legalbodies')
        ->orderBy('legalBodyName', 'asc')
        ->get();

        return view('admin.importintocollection')->with([          
           'page' => 'importintocollection',
           'role' => \Session::get('role'),
           'legalbodies' => $legalbodies,
           'collectionID' => $collectionID, // default admin user's import selection to their collection
           'sessiondata' => $sessiondata
        ]);    
} // end SuperAdmin only function to import instruments into a collection






// SuperAdmin only function to import instruments into a collection
public function importintocollection_go()
{
        $sessiondata = session()->all();
        $role = \Session::get('role');
        $user_id = Auth::user()->getId();

        if ($role != "SuperAdmin")
        {
            return \Redirect::action('AdminController@index'); // redirect to dash if not SuperAdmin
        }

        // get posted paramaters selected for import
        $input = Input::all();      
        $legalBodyID = (int)Input::get('legalBodyID');      
        $import_type = Input::get('import_type');  

        // Fetch legal body data
         $legalbodies = \DB::table('legalbodies')
        ->where('legalBodyID', '=', $legalBodyID)  
        ->take(1)->get();
         $legalBodyID = $legalbodies[0]->legalBodyID;  
         $legalBodyName = $legalbodies[0]->legalBodyName;  
         $legalBodyMDAcode = $legalbodies[0]->legalBodyMDAcode;  
         $importXMLfile = $legalbodies[0]->importXMLfile; 

        // file to import from
        $file = public_path().'/instrument_resources/xml/'.$legalBodyMDAcode.'/'.$importXMLfile;

        // check xml file exists
        if (\File::exists($file))
        {
            $filesize = filesize($file); // filesize in bytes of rcm xml
            $filesize_k = round(($filesize / 1024));
            $file_last_modified = (new \DateTime())
             ->setTimestamp(\File::lastModified($file))
             ->format('D, d M Y H:i:s T');

        } else {
          // xml set in the database, but can't find the actual file :(
          $filesize=0; $file_last_modified=''; $filesize_k = '';
        }

        // add to session for import to pick up
        session()->put('legalBodyID_import', $legalBodyID);
        session()->put('legalBodyName_import', $legalBodyName);        
        session()->put('import_type_import', $import_type);
        session()->put('MDA_code_import', $legalBodyMDAcode);
        session()->put('importXMLfile', $importXMLfile);

        // get the last import performed by current SuperAdmin, so that the function can listen for the next one
        $importquery=''; 
        $lastImportID = \DB::table('import_jobs')
        ->where('userID', $user_id)
        ->orderBy('id', 'desc')
        ->take(1)->get();

        if (isset($lastImportID[0]->id)) // user may have not performed an import before
        {
            $lastImportID = (int) $lastImportID[0]->id; // force int on last import (if any) for this user
        } else {
            $lastImportID = 0;
        }

        return view('admin.importintocollection_go')->with([          
           'page' => 'importintocollection_go',
           'role' => \Session::get('role'),
           'lastImportID' => $lastImportID, 
           'import_type_import' => $import_type,
           'legalBodyID_import' => $legalBodyID,
           'legalBodyName_import' => $legalBodyName,
           'legalBodyMDAcode_import' => $legalBodyMDAcode,
           'importXMLfile' => $importXMLfile,
           'filesize' => $filesize,
           'filesize_k' => $filesize_k,
           'file_last_modified' => $file_last_modified,
           'sessiondata' => $sessiondata
        ]);    

} // end SuperAdmin only function to import instruments into a collection










// instrument autocomplete function 
public function autocomplete(){
      $term = Input::get('term');
      $results = array();
      
        // this will query all levels of the thesaurus table matching the term   
        $queries = \DB::table('thesauruses')
        ->where('Level_1', 'LIKE', '%'.$term.'%')
        ->orWhere('Level_2', 'LIKE', '%'.$term.'%')
        ->orWhere('Level_3', 'LIKE', '%'.$term.'%')      
        ->orWhere('Synonyms', 'LIKE', '%'.$term.'%')
        ->orWhere('Identifier', 'LIKE', '%'.$term.'%')      
        ->take(120)->get(); // get 120 instrument names at a time (lots of trumpets)
      
        foreach ($queries as $query)
        {
           // get instrument names from thesuarus
           $results[] = [ 'thesaurusID' => $query->thesaurusID, 'value' => ''.html_entity_decode($query->Level_1)." > ".html_entity_decode($query->Level_2)." > ".html_entity_decode($query->Level_3).' | '.html_entity_decode($query->Synonyms)." [".($query->Identifier)."]" ]; 
        }

    return Response::json($results);
}  // end instrument autocomplete function...







// hornbostel autocomplete function 
public function hornbostel_autocomplete(){
          $term = Input::get('term');
          $results = array();

            // this will query actor thesaurus
            $queries = \DB::table('hornbostel')
          ->where('HornbostelCat', 'LIKE', '%'.$term.'%')
        ->orWhere('label', 'LIKE', '%'.$term.'%') 
          ->take(120)->get(); // get 120 hornbostel definitions at a time 
          
          foreach ($queries as $query)
          {
              $display_text = $query->label;
              $results[] = [ 'hornbostelID' => $query->id, 'value' => $display_text ]; 
          }

        return Response::json($results);
}  // end hornbostel autocomplete function 



// cities autocomplete function 
public function cities_autocomplete(){
          $term = Input::get('term');
          $results = array();

          // this will query actor thesaurus
          $queries = \DB::table('geonames_cities1000')
          ->where('name', 'LIKE', $term.'%')
          ->distinct('name')
          ->take(100)->get(); // get 100 cities at a time 

          foreach ($queries as $query)
          {
              $results[] = [ 'cityID' => $query->cityID, 'value' => $query->name." (Timezone: ".$query->timezone.")" ]; 
          }

        return Response::json($results);
}  // end cities autocomplete function 



// actor autocomplete function */
public function actorautocomplete()
{
          $term = Input::get('term');
          $results = array();

          // this will query actor thesaurus
          $queries = \DB::table('event_actors_thesaurus')
            ->where('actorName', 'LIKE', '%'.$term.'%')    
            ->take(100)->get(); // get 100 actors at a time
          
          foreach ($queries as $query)
          {
            $actor = $query->actorName;
            $actor = str_replace("\n","",$actor);
            $actor = str_replace("\t","",$actor);

              $results[] = [ 'actorID' => $query->id, 'value' => ''.$actor." [".$query->type."]" ]; 
          }

        return Response::json($results);
} // end actorautocomplete function...



// content not found
public function movedordeleted($code='')
{
        $sessiondata = session()->all();

        return view('admin.notfound')->with([          
           'page' => 'notfound',
           'role' => \Session::get('role'),
           'sessiondata' => $sessiondata
        ]);    

} // end content not found



// function for users to report an issue
public function reportproblem()
{
        $sessiondata = session()->all();
        $user_id = Auth::user()->getId();

        // get existing problem reports for user
         $problems = \DB::table('report_problem')
        ->where('userID', '=', $user_id)  
        ->orderBy('problemDate', 'desc')        
        ->take(10)->get();

        return view('admin.reportproblem')->with([          
           'page' => 'reportproblem',
           'role' => \Session::get('role'),
           'previous_url' => \Session::get('_previous[url]'), // previous url default page for problem report in form
           'problems' => $problems,
           'sessiondata' => $sessiondata
        ]);    
} // end function for users to report an issue




// function to store a reported problem
public function storereportproblem()
{
      $user_id = Auth::user()->getId();
      $sessiondata = session()->all();

      // get all input values // print_r($input);
      $input = Input::all();
      $page_name = Input::get('page_name');     
      $comment = Input::get('comment');  

      \DB::table('report_problem')->insert(
        ['userID' => $user_id, 'page_name' => $page_name, 'comment' => $comment, 'problemDate' => \Carbon\Carbon::now() ]  // insert problem report
      );

      // now set the flash data to show problem has been reported
      session()->flash('flashdata', 'Thankyou for reporting a problem');

      // redirect to editing this event
      return \Redirect::action('AdminController@reportproblem');
}
// end function to store a reported problem



// function to delete a user's reported problem
public function deleteproblem($reportProblemID) 
{
        $user_id = Auth::user()->getId();
        $reportProblemID = (int) $reportProblemID; // force int

        $problem = \DB::table('report_problem')        // ensure problem belongs to user
        ->where('reportProblemID', '=', $reportProblemID)  
        ->where('userID', '=', $user_id)         
        ->take(1)->get();

        if (sizeof($problem) < 1) { 
                  return \Redirect::action('AdminController@movedordeleted'); // redirect if problem not theirs or doesn't exist
        }  

        // delete this problem
        \DB::table('report_problem')->where('reportProblemID', '=', $reportProblemID)->delete();
           
        // now set the flash data to show problem deleted
        session()->flash('flashdata', 'Problem report deleted');

        // redirect to report problem    
        return \Redirect::action('AdminController@reportproblem'); 

} // end delete problem




// archived import functions previously used to get data into mysql
public function archived_imports()
{
      /* ACTORS IMPORT */
      /*
              $lines = file('actors/persons.txt');

              foreach ($lines as $line) {

              // deal with respository
                  \DB::table('event_actors')->insert(
                      ['type' => "Person", 'actorName' => $line ]
                  );

                echo $line."<br/>";
              }
      */

      /* Thesaurus Import */
      
      $queries = \DB::table('thesauruses')
        ->take(4000)->get();
      
      $Level_0 = ""; $Level_1 = ""; $Level_2 = ""; $Level_3 = "";
       
        foreach ($queries as $query)
        {

          if ($query->Level_0) 
          {
            $Level_0 = str_replace("&#x27;","'", $query->Level_0);
          }  
          if ($query->Level_1) 
          {
            $Level_1 = str_replace("&#x27;","'", $query->Level_1);
          }  
          if ($query->Level_2) 
          {
            $Level_2 = str_replace("&#x27;","'", $query->Level_2);
          }  
          if ($query->Level_3) {
            $Level_3 = str_replace("&#x27;","'", $query->Level_3);
          }  

          $thesaurusID = $query->thesaurusID;

             echo "thesaurusID ".$query->thesaurusID."<br/>";
             echo "Identifier ".$query->Identifier."<br/>";
             echo "Level_0 ".$Level_0."<br/>";
             echo "Level_1 ".$Level_1."<br/>";
             echo "Level_2 ".$Level_2."<br/>";
             echo "Level_3 ".$Level_3."<br/>";             
             echo "Synonyms ".$query->Synonyms."<br/>";
             echo "URIs ".$query->URIs."<br/>";
             echo "Same_as_HandS ".$query->Same_as_HandS."<br/>";
             echo "Definition ".$query->Definition."<br/>";
             echo "Original_Language ".$query->Original_Language."<br/>";
             echo "<br/><br/>";

              \DB::table('thesauruses')
                ->where('thesaurusID', $thesaurusID)
                ->update(array('Level_0' => $Level_0, 'Level_1' => $Level_1, 'Level_2' => $Level_2, 'Level_3' => $Level_3 ));

      } // end for each
    
} // end archived imports function



// archived geotagging function previously used to get data into mysql (cities.sql from dbase with we are not using - contains inconsistencies and repetition)
public function cities_import_OLD()
{
$lines = file('cities/cities.sql');
$x=0;
foreach ($lines as $line) {

    $parts = preg_split('/\t+/', $line);
    //var_dump($parts);
    $combined = htmlspecialchars($parts['0'].$parts['1']);
    $combined = preg_replace('/[0-9]+/', '', $combined); //remove population numbers
    $country_code = $parts['2'];
    $region = $parts['3'];
    $latitude = $parts['4'];
    $longitude = $parts['5'];


    // check values
    echo "Combined ".$combined."<br/>";
    //echo "country_code ".$country_code."<br/>";
    //echo "region ".$region."<br/>";
    //echo "latitude ".$latitude."<br/>";
    //echo "longitude ".$longitude."<br/>";
    echo "<br/>";

    // insert this city data
    \DB::table('cities')->insert(
                ['combined' => $combined, 'country_code' => $country_code, 'region' => $region, 'latitude' => $latitude, 'longitude' => $longitude ]
    );

    $x++;
   
      // bail out at 100 for test
      //  if ($x == 100) { exit; }    

     }       
}
// end cities import 



// archived import functions previously used to get data into mysql (not using due to deprecation - last maintained 2011)
public function cities_import_OLD_()
{
$lines = file('cities/worldcitiespop.txt');
$x=0;
foreach ($lines as $line) {

    $parts = preg_split('/[,]/', $line);
    //var_dump($parts);
    $Country = htmlspecialchars($parts['0']);
    $City = htmlspecialchars($parts['1']);
    $AccentCity = htmlspecialchars($parts['2']);
    $Region = htmlspecialchars($parts['3']);
    $Population = htmlspecialchars($parts['4']);
    $Latitude = htmlspecialchars($parts['5']);
    $Longitude = htmlspecialchars($parts['6']);    


    // check values
    echo $City."      ;";

    // insert this city data
    \DB::table('cities')->insert(
                ['Country' => $Country, 'City' => $City, 'AccentCity' => $AccentCity, 'Region' => $Region, 'Population' => $Population, 'Latitude' => $Latitude, 'Longitude' => $Longitude ]
    );

    $x++;
   
      // bail out at 100 for test
        //if ($x == 100) { exit; }    

     }       
}
// end cities import



public function cities_import() // geonames cities in the world with a population of 1000+ (this is the dbase we are using now...)
{
$lines = file('cities/geonames_cities1000.txt');
$x=0;
foreach ($lines as $line) {

    $parts = preg_split('/\t/', $line); // match tab
    //var_dump($parts);
    $one = htmlspecialchars($parts['0']);
    $two = htmlspecialchars($parts['1']);
    $three = htmlspecialchars($parts['2']);
    $four = htmlspecialchars($parts['3']);
    $five = htmlspecialchars($parts['4']);
    $six = htmlspecialchars($parts['5']);
    $seven = htmlspecialchars($parts['6']);    
    $eight = htmlspecialchars($parts['7']);  
    $nine = htmlspecialchars($parts['8']);  
    $ten = htmlspecialchars($parts['9']);  
    $eleven = htmlspecialchars($parts['10']);  
    $twelve = htmlspecialchars($parts['11']);  
    $thirteen = htmlspecialchars($parts['12']);  
    $fourteen = htmlspecialchars($parts['12']);  
    $fifteen = htmlspecialchars($parts['13']);  
    $sixteen = htmlspecialchars($parts['14']);  
    $seventeen = htmlspecialchars($parts['15']);  
    $eighteen = htmlspecialchars($parts['16']);  
    $nineteen = htmlspecialchars($parts['17']);  
    $twenty = htmlspecialchars($parts['18']);  

    //echo "number of parts ".sizeof($parts)."<br/>";

    // check values
    echo "One: ".$one."<br/>";
  /*
    echo "Two: ".$two."<br/>";
    echo "Three: ".$three."<br/>";
    echo "Four: ".$four."<br/>";
    echo "Five: ".$five."<br/>";
    echo "Six: ".$six."<br/>";
    echo "Seven: ".$seven."<br/>";
    echo "Eight: ".$eight."<br/>";
    echo "Nine: ".$nine."<br/>";
    echo "Ten: ".$ten."<br/>";
    echo "Eleven: ".$eleven."<br/>";
    echo "Twelve: ".$twelve."<br/>";
    echo "Thirteen: ".$thirteen."<br/>";
    echo "Fourteen: ".$fourteen."<br/>";
    echo "Fifteen: ".$fifteen."<br/>";
    echo "Sixteen: ".$sixteen."<br/>";
    echo "Seventeen: ".$seventeen."<br/>";
    echo "Eighteen: ".$eighteen."<br/>";
    echo "Nineteen: ".$nineteen."<br/>";
    echo "Twenty: ".$twenty."<br/><br/>";
    */

    // insert this city data
    \DB::table('geonames_cities1000')->insert(
                ['geonameid' => $one, 'name' => $two, 'asciiname' => $three, 'alternatenames' => $four, 'latitude' => $five, 'longitude' => $six, 'feature_class' => $seven, 'feature_code' => $eight, 'country_code' => $nine, 'cc2' => $ten, 'population' => $sixteen, 'elevation' => $seventeen, 'dem' => $eighteen, 'timezone' => $nineteen ]
    );
  
    $x++;
   
      // bail out at 100 for test
     // if ($x == 100) { exit; }    

     }       
}
// end cities import function




  public function importlog($import_jobID = '') // show full import log
  {
    echo "test ".$import_jobID;

      // Fetch info for this import job
      $importjob = \DB::table('import_jobs')
      ->where('id', '=', $import_jobID)  
      ->take(1)->get();

      $userID = $importjob[0]->userID;
      $legalBodyID = $importjob[0]->legalBodyID;
      $fileName = $importjob[0]->fileName;
      $fileSize = $importjob[0]->fileSize;
      $fileLastModified = $importjob[0]->fileLastModified;
      $percentComplete = $importjob[0]->percentComplete;
      $currentStatus = $importjob[0]->currentStatus;
      $short_log = $importjob[0]->short_log;
      $log = $importjob[0]->log;
      $currentStatus = $importjob[0]->currentStatus;
      $time_started = $importjob[0]->time_started;
      $time_ended = $importjob[0]->time_ended;

      echo $log;


  }


} // end Admincontroller class