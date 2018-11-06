<?php

/*
QUESTIONS:
----------
- Is a single event ever likely to have multiple places?
- Yearly imports? Will RCM maintain data on this system or the other? Is the missing data on the current main system?
- admin recordID from RCM or other body - are we storing this as part of the repository or just as part of instrument - ie only ever 1 recordID?
- will every single record we deal with at least have 1 event? - 'production'? may move this 1 event into instruments table if so for searching
- what are we doing about data integrity - blank actors in Eninburgh  xml - missing recordID in Edinburgh??
- both rcm and edinburgh only have display measurements... edinburgh has no units reference ie 'length 56.9'
- is it poss to get some records with all possibilities ie resourceCaptions etc?
- will RCM etc maintin their records on this system or somewhere else? will importing again the same instrument always supercede?
- blank record in edinburgh:: MAKE SYSTEM IDENTIFY AND NOT INSERT BLANKS... BLANK ACTORS TOO IN EDINBURGH
 */

/*
PREP FOR RAM IMPORT
- remove 'lido:' find and replace
- remove headers:

  remove header in xml
  (?s)<header(.*?)</header>
  &/






 */


namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use Prewk\XmlStringStreamer;
use Prewk\XmlStringStreamer\Stream\File;
use Prewk\XmlStringStreamer\Parser\StringWalker;
use Guzzle\Http\Client;
use Guzzle\Http\Ring\Exception\ConnectException;
use Auth;

//$getimage = new \GuzzleHttp\Client();

class progressbarClass extends Controller
{
 public function setTotalCount(Request $request)
 {
     echo "foo";
 }

}

class ImportController extends Controller
{
private $progressbarClass;






public function import(Request $request)
{
  
  $user_id = Auth::user()->getId();
  $adminID_session = $user_id;
  $largest_thumbnail_pixels = "250";
  $sessiondata = session()->all();
  $role = \Session::get('role');




  

/*
  $fileName = $_FILES["file1"]["name"]; // The file name
  $fileTmpLoc = $_FILES["file1"]["tmp_name"]; // File in the PHP tmp folder
  $fileType = $_FILES["file1"]["type"]; // The type of file it is
  $fileSize = $_FILES["file1"]["size"]; // File size in bytes
  $fileErrorMsg = $_FILES["file1"]["error"]; // 0 for false... and 1 for true
  if (!$fileTmpLoc) { // if file not chosen
      echo "ERROR: Please browse for a file before clicking the upload button.";
      exit();
  }
  if(move_uploaded_file($fileTmpLoc, public_path().'/images/'.$fileName)){

    //sleep(10);

      echo "$fileName upload is complete";
  } else {
      echo "move_uploaded_file function failed";
  }

*/





// truncate tables for test
//echo "import disabled"; die;



// the largest size to contstrain either width or height for thumbnails



// truncate activity?
//\DB::table('user_activity')->truncate();



\DB::table('instruments')->truncate();
\DB::table('repositories')->truncate();
\DB::table('descriptions')->truncate();
\DB::table('events')->truncate();
\DB::table('eventactors')->truncate();
\DB::table('resources')->truncate();
\DB::table('measurements')->truncate();
\DB::table('eventactors')->truncate();
\DB::table('resources')->truncate();


/*
$legalbody_session = "GB-Lcm";
$legalbody_name_session = "Royal College Of Music";
$legalbody_id_session = "1";
//$file = public_path().'/instrument_resources/xml/'.$legalbody_session.'/all_mimo_records_no_header.xml';
$file = public_path().'/instrument_resources/xml/'.$legalbody_session.'/all_mimo_records_no_header_10.xml';
*/

/*
$legalbody_session = "UEDIN";
$legalbody_name_session = "University Of Edinburgh";
$legalbody_id_session = "2";
//$file = public_path().'/instrument_resources/xml/'.$legalbody_session.'/uni_edinburgh1-100_noheadertest.xml';
$file = public_path().'/instrument_resources/xml/'.$legalbody_session.'/uni_edinburgh1-10_noheadertest.xml';
*/

/*
$legalbody_session = "RAM";
$legalbody_name_session = "Royal Academy of Music";
$legalbody_id_session = "11";
$file = public_path().'/instrument_resources/xml/'.$legalbody_session.'/RAM_no_header_test.xml';
*/


$legalbody_session = "HM";
$legalbody_name_session = "Horniman Museum";
$legalbody_id_session = "37";
$file = public_path().'/instrument_resources/xml/'.$legalbody_session.'/Horniman_no_header_test.xml';





// check if folders exist
 $imagepath = public_path().'/instrument_resources/images/'.$legalbody_session;
 $imagethumbpath = public_path().'/instrument_resources/images/'.$legalbody_session.'/thumbnails/';
 $soundpath = public_path().'/instrument_resources/sound/'.$legalbody_session;
 $videopath = public_path().'/instrument_resources/video/'.$legalbody_session;

if (!file_exists($imagepath)) 
{    // create folder..
    \File::makeDirectory($imagepath, $mode = 0777, true, true);
}

if (!file_exists($imagethumbpath)) 
{    // create folder..
    \File::makeDirectory($imagethumbpath, $mode = 0777, true, true);
}

if (!file_exists($soundpath)) 
{    // create folder..
    \File::makeDirectory($soundpath, $mode = 0777, true, true);
}

if (!file_exists($videopath)) 
{    // create folder..
    \File::makeDirectory($videopath, $mode = 0777, true, true);
}

// chunk size (16k)
$chunksize = 16384;

// for script execution time...
$time_start = microtime(true);

// load one mimo file to show structure..
$xml = simplexml_load_file(public_path().'/xml/one_mimo_record.xml');
foreach( $xml as $element )
{
   // echo"<pre>"; print_r( $element ); echo "</pre>";
}

/*
remove header in xml
(?s)<header(.*?)</header>
&/

 */

/*
echo "streamer<br/>";
$options = array(
    "captureDepth" => 10
);
*/


// initialise log
$log="";

$filesize = filesize($file); // filesize in bytes of rcm xml
$file_last_modified = (new \DateTime())
    ->setTimestamp(\File::lastModified($file))
    ->format('D, d M Y H:i:s T');


// ADD JOB TO DATABASE
$import_jobID = \DB::table('import_jobs')->insertGetId(
   // legalbodyid should be in session
   ['userID' => $user_id, 'fileName' => basename($file), 'fileSize' => $filesize, 'fileLastModified' => $file_last_modified, 'time_started' => \Carbon\Carbon::now()]
);





try {
    // the code which throws the error





        echo "<span style='color:red;'><strong>Total file size: </strong>".$filesize." bytes.<br/><br/></span>";
                // Total = XML file size
              //  $this->progressbarClass->setTotalCount(filesize($file));

                $stream = new File($file, $chunksize, function($chunk, $readBytes) use ($filesize, $import_jobID) {
                    // This closure will be called every time the streamer requests a new chunk of data from the XML file
                      // $this->progressbarClass->reportProgress($readBytes);
               
                $percent = round($readBytes / $filesize * 100,2);  
                  
                echo "<span style='color:red;'><strong>".$readBytes."</strong> bytes read out of <strong>".$filesize."</strong> total. <span style='font-size:50px;'><strong>".$percent."%</strong></span></span><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>";




                         // update  job progress every 16kb in closure
                          \DB::table('import_jobs')
                           ->where('id', $import_jobID)
                           ->update(array('percentComplete' => $percent, 'currentStatus' => $readBytes.' bytes / '.$filesize.' bytes processed.' ));





                });

        // Convenience method for creating a file streamer with the default parser
        $parser = new StringWalker;
        $streamer = new XmlStringStreamer($parser, $stream);

        $counter=1;
        $imported=0;

        $log .="Import begun by: ".$user_id."\n";

        while ($node = $streamer->getNode()) {
        // $node is one <lido>...</lido> record...
        $simpleXmlNode = simplexml_load_string($node);

        $log .="Record ".$counter.(string)$simpleXmlNode->identifier."\n";

        //echo "<br/><span style='font-size:50px; color:green;'>Record ".$counter."</span>".(string)$simpleXmlNode->identifier;


        if(!isset($simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectClassificationWrap->classificationWrap->classification[0]->term)) {

            //echo "<span style='font-size:50px; color:green;'>[IGNORED]</span>";
            $log .="<span style='font-size:50px; color:green;'>[IGNORED]</span>"."\n";
            $importme = 0;

        } else {
            $imported++; // this is assumed ready to import
            $importme = 1;
        }

        if(isset($simpleXmlNode->lidoWrap->lido->lidoRecID)) {
            //echo "<br/><strong>Local Lido RecID? </strong>".(string)$simpleXmlNode->lidoWrap->lido->lidoRecID;
            $log .= "Local Lido RecID?: ".(string)$simpleXmlNode->lidoWrap->lido->lidoRecID."\n";
        }



        if(isset($simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectClassificationWrap->objectWorkTypeWrap->objectWorkType->term)) {
            // CLASSIFICATION CAN BE MANY - NEED FOR EACH FOR THIS...
            //echo "<br/><strong>Basic Category..? </strong>".(string)$simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectClassificationWrap->objectWorkTypeWrap->objectWorkType->term;
            $log.= "Basic Category..?".(string)$simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectClassificationWrap->objectWorkTypeWrap->objectWorkType->term."\n";
        }



        // INSTRUMENT CAT
        if(isset($simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectClassificationWrap->classificationWrap->classification[0]->term)) {
            //echo "<br/><strong>title category (d-2.1) </strong>".(string)$simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectClassificationWrap->classificationWrap->classification[0]->term;

            $log.="title category (d-2.1)".(string)$simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectClassificationWrap->classificationWrap->classification[0]->term."\n";

                $instrument['title_preferred'] = (string)$simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectClassificationWrap->classificationWrap->classification[0]->term;

        }


        // instrument actual name
        if(isset($simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectIdentificationWrap->titleWrap->titleSet->appellationValue)) {
        //    echo "<br/><strong>title preferred (d-2.1) </strong>".(string)$simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectIdentificationWrap->titleWrap->titleSet->appellationValue;
        $log.="title preferred (d-2.1)".(string)$simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectIdentificationWrap->titleWrap->titleSet->appellationValue."\n";


                $instrument['title_preferred_actual'] = (string)$simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectIdentificationWrap->titleWrap->titleSet->appellationValue;

        }



        if(isset($simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectClassificationWrap->classificationWrap->classification[1]->conceptID)) {
         
              $att = 'type';
              $attribute = $simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectClassificationWrap->classificationWrap->classification[1]->conceptID->attributes()->$att;

             // echo "<br/><strong>conceptID class attribute? </strong>".$attribute;
             // echo "<br/><strong>conceptID  (hornsbostel cat d-1.2.1): </strong>".$simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectClassificationWrap->classificationWrap->classification[1]->conceptID;  

         $log.= "conceptID class attribute?:".$attribute."\n";    
         $log.= "conceptID  (hornsbostel cat d-1.2.1):".$simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectClassificationWrap->classificationWrap->classification[1]->conceptID."\n";  

              $instrument['hornsbostel'] = $simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectClassificationWrap->classificationWrap->classification[1]->conceptID;
         
        } else {
          $instrument['hornsbostel'] = '';
        }



        // INSCRIPTIONS
         $inscriptionString="";
        if(isset($simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectIdentificationWrap->inscriptionsWrap->inscriptions[0]->inscriptionDescription->descriptiveNoteValue)) {

            // echo "<br/><strong>There are ".$simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectIdentificationWrap->inscriptionsWrap->inscriptions->count()." inscriptions. (d2.3)</strong>";
            $log.="There are ".$simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectIdentificationWrap->inscriptionsWrap->inscriptions->count()." inscriptions. (d2.3)"."\n";

                $j=0;
                foreach( $simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectIdentificationWrap->inscriptionsWrap->inscriptions as $element )
                {
                    if (strlen(trim($simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectIdentificationWrap->inscriptionsWrap->inscriptions[$j]->inscriptionDescription->descriptiveNoteValue)) > 0) {
                        $inscriptionString .= $simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectIdentificationWrap->inscriptionsWrap->inscriptions[$j]->inscriptionDescription->descriptiveNoteValue.'|';
                    } else {
                        $inscriptionString = '';
                    }

                    $j++;
                }

        //        echo "<br/><strong>Inscription string for dbase: </strong>".$inscriptionString;
                $log.="Inscription string for dbase: </strong>".$inscriptionString."\n";

                $inscriptionString = rtrim($inscriptionString, "|"); 

        } else {
            //echo "<br/><strong>There are no inscriptions.</strong>";
            $log.="There are no inscriptions."; 
        }


        /*
        Add to measurements

        <objectMeasurementsWrap>
           <objectMeasurementsSet>
            <displayObjectMeasurements>Piano: 22071234mm. height: 327mm. height less lid: 303mm. stand: 602mm. height from case top to soundboard: 87mm.</displayObjectMeasurements>
            <objectMeasurements>
             <measurementsSet>
              <measurementType>Piano</measurementType>
              <measurementUnit>22071234</measurementUnit>
              <measurementValue>mm</measurementValue>
             </measurementsSet>
             <measurementsSet>
              <measurementType>height</measurementType>
              <measurementUnit>327</measurementUnit>
              <measurementValue>mm</measurementValue>
             </measurementsSet>
             <measurementsSet>
              <measurementType>height less lid</measurementType>
              <measurementUnit>303</measurementUnit>
              <measurementValue>mm</measurementValue>
             </measurementsSet>
             <measurementsSet>
              <measurementType>stand</measurementType>
              <measurementUnit>602</measurementUnit>
              <measurementValue>mm</measurementValue>
             </measurementsSet>
             <measurementsSet>
              <measurementType>height from case top to soundboard</measurementType>
              <measurementUnit>87</measurementUnit>
              <measurementValue>mm</measurementValue>
             </measurementsSet>
            </objectMeasurements>
           </objectMeasurementsSet>
        </objectMeasurementsWrap>

        */




        // MEASUREMENTS FREE TEXT
        if(isset($simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectIdentificationWrap->objectMeasurementsWrap->objectMeasurementsSet[0]->displayObjectMeasurements)) {

                $j=0;
                foreach( $simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectIdentificationWrap->objectMeasurementsWrap->objectMeasurementsSet as $element )
                {
                   
                    // MEASUREMENTS FREE TEXT
                    echo $simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectIdentificationWrap->objectMeasurementsWrap->objectMeasurementsSet[$j]->displayObjectMeasurements."<br/>";

                    $instrument['measurementsFreeText'] = $simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectIdentificationWrap->objectMeasurementsWrap->objectMeasurementsSet[$j]->displayObjectMeasurements;

                    $j++;
                }

        } else {
            echo "<br/><strong>There are no measurements.</strong>";
            $instrument['measurementsFreeText'] = '';
        }




        // SPECIFIC MEASUREMENTS
        if(isset($simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectIdentificationWrap->objectMeasurementsWrap->objectMeasurementsSet[0]->objectMeasurements->measurementsSet->measurementType)) {

           echo "<br/><strong>There are ".$simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectIdentificationWrap->objectMeasurementsWrap->objectMeasurementsSet[0]->objectMeasurements->measurementsSet->count()." specific measurements measurements.</strong> ";
          
           $log.="There are ".$simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectIdentificationWrap->objectMeasurementsWrap->objectMeasurementsSet[0]->objectMeasurements->measurementsSet->count()." specific measurements measurements";

                $j=0;
                foreach( $simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectIdentificationWrap->objectMeasurementsWrap->objectMeasurementsSet[0]->objectMeasurements->measurementsSet as $element )
                
                  {

                       // <measurementType>
                       if (strlen(trim($simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectIdentificationWrap->objectMeasurementsWrap->objectMeasurementsSet[0]->objectMeasurements->measurementsSet[$j]->measurementType)) > 0) {
                            $measurementType[$j] = $simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectIdentificationWrap->objectMeasurementsWrap->objectMeasurementsSet[0]->objectMeasurements->measurementsSet[$j]->measurementType;
                        } else {
                            $measurementType[$j] ='';
                        }

                       // <measurementUnit>
                       if (strlen(trim($simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectIdentificationWrap->objectMeasurementsWrap->objectMeasurementsSet[0]->objectMeasurements->measurementsSet[$j]->measurementUnit)) > 0) {
                            $measurementUnit[$j] = $simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectIdentificationWrap->objectMeasurementsWrap->objectMeasurementsSet[0]->objectMeasurements->measurementsSet[$j]->measurementUnit;
                        } else {
                            $measurementUnit[$j] ='';
                        }

                       // <measurementValue>
                       if (strlen(trim($simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectIdentificationWrap->objectMeasurementsWrap->objectMeasurementsSet[0]->objectMeasurements->measurementsSet[$j]->measurementValue)) > 0) {
                           $measurementValue[$j] = $simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectIdentificationWrap->objectMeasurementsWrap->objectMeasurementsSet[0]->objectMeasurements->measurementsSet[$j]->measurementValue;
                        } else {
                            $measurementValue[$j] ='';
                        }


                        echo '<br/>'.$measurementType[$j].' '.$measurementUnit[$j].' '.$measurementValue[$j];

                     $j++;
                }

        } else {
            echo "<br/><strong>There are no specific measurements.</strong>";
            $measurementType = '';
            $measurementUnit = '';
            $measurementValue = '';
        }















        // REPOSITORIES
        if(isset($simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectIdentificationWrap->repositoryWrap->repositorySet[0]->repositoryName->legalBodyName->appellationValue)) {

            // echo "<br/><strong>There are ".$simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectIdentificationWrap->repositoryWrap->repositorySet->count()." repositories.</strong>";
            $log.= "There are ".$simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectIdentificationWrap->repositoryWrap->repositorySet->count()." repositories."."\n";


                $j=0;
                foreach( $simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectIdentificationWrap->repositoryWrap->repositorySet as $element )
                {
                   // echo "<br/><strong>LegalBodyName (A-1.3.2) </strong>".$simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectIdentificationWrap->repositoryWrap->repositorySet[$j]->repositoryName->legalBodyName->appellationValue;

                   // echo "<br/><strong>Inventory Number (D-2.2.1) </strong>".$simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectIdentificationWrap->repositoryWrap->repositorySet[$j]->workID."<br/>";
                   
                    $log.="LegalBodyName (A-1.3.2)".$simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectIdentificationWrap->repositoryWrap->repositorySet[$j]->repositoryName->legalBodyName->appellationValue."\n";

                    $log.= "Inventory Number (D-2.2.1)".$simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectIdentificationWrap->repositoryWrap->repositorySet[$j]->workID."\n";

                        $inventoriesBodyName[$j] = $simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectIdentificationWrap->repositoryWrap->repositorySet[$j]->repositoryName->legalBodyName->appellationValue;

                        $inventoryNumber[$j] = $simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectIdentificationWrap->repositoryWrap->repositorySet[$j]->workID;

                    $j++;
                }

        } else {
            //echo "<br/>There are no repositories.";
            $log.="There are no repositories.";
        }



        // DESCRIPTIONS
        if(isset($simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectIdentificationWrap->objectDescriptionWrap->objectDescriptionSet[0]->descriptiveNoteValue)) {

            // echo "<br/><strong>There are ".$simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectIdentificationWrap->objectDescriptionWrap->objectDescriptionSet->count()." descriptions.</strong>";

            $log.="There are ".$simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectIdentificationWrap->objectDescriptionWrap->objectDescriptionSet->count()." descriptions."."\n";


                $j=0;
                    foreach( $simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectIdentificationWrap->objectDescriptionWrap->objectDescriptionSet as $element )
                    {
                      
                    // DESCRIPTION TYPE
                    $att = 'type';
                    if(isset($simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectIdentificationWrap->objectDescriptionWrap->objectDescriptionSet[$j]->attributes()->$att)) {
                   
                          $attribute = $simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectIdentificationWrap->objectDescriptionWrap->objectDescriptionSet[$j]->attributes()->$att;
                          // echo "<br/><strong>desc type (d-2.4.1) </strong>".$attribute."<br/>";
                          $log.="desc type (d-2.4.1): ".$attribute."\n";

                          $descriptionType[$j] = $attribute;

                    } else {
                        $descriptionType[$j] = 'General Description';
                    }

                    // DESCRIPTION   
                    // echo "<strong>desc text (d-2.4.2) </strong>".$simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectIdentificationWrap->objectDescriptionWrap->objectDescriptionSet[$j]->descriptiveNoteValue."<br/>";
                    $log.="desc text (d-2.4.2): ".$simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectIdentificationWrap->objectDescriptionWrap->objectDescriptionSet[$j]->descriptiveNoteValue."\n";

                        $descriptionText[$j] = $simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectIdentificationWrap->objectDescriptionWrap->objectDescriptionSet[$j]->descriptiveNoteValue;

                $j++;
            }

        } else {
            //echo "<br/>There are no descriptions.";
            $log.="There are no descriptions.";    
        }



        // EVENTS       
        if(isset($simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet[0]->event->eventType->term)) {

            //echo "<br/><strong>There are ".$simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet->count()." events.</strong>";
            $log.="There are ".$simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet->count()." events."."\n";

                $j=0;
                foreach( $simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet as $element )
                {
                    //echo "<br/><strong>event type (d-3.2) </strong>".$simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet[$j]->event->eventType->term;
                    //echo "<br/><br/><strong>This event has ".$simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet[$j]->event->eventActor->count()." actors.</strong><br/>.";


                    $log.="event type (d-3.2)".$simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet[$j]->event->eventType->term."\n";
                    $log.="This event has ".$simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet[$j]->event->eventActor->count()." actors."."\n";



                         // EVENT ACTOR
                         $inc=0;
                            foreach( $simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet[$j]->event->eventActor as $element )
                            {
                                // ACTOR TYPE
                                $att = 'type';
                                if(isset($simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet[$j]->event->eventActor[$inc]->actorInRole->actor->attributes()->$att)) {
                               
                                      $attribute = $simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet[$j]->event->eventActor[$inc]->actorInRole->actor->attributes()->$att;
                                      //echo "<br/><strong>actor type (d-3.4.1) </strong>".$attribute;
                                      $log.="actor type (d-3.4.1): ".$attribute;

                                      if(isset($simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet[$j]->event->eventActor[$inc]->actorInRole->actor->attributes()->$att)) {
                                            // eventactortype for dbase
                                            $eventActorType[$j][$inc] = $simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet[$j]->event->eventActor[$inc]->actorInRole->actor->attributes()->$att;
                                      } else {
                                            $eventActorType[$j][$inc] = "";
                                      }
                                }

                                // ACTOR ROLE
                                  if(isset($simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet[$j]->event->eventActor[$inc]->actorInRole->roleActor->term)) {
                                        // eventactortype for dbase
                                        $eventActorRole[$j][$inc] = $simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet[$j]->event->eventActor[$inc]->actorInRole->roleActor->term;
                                  } else {
                                        $eventActorRole[$j][$inc] = "";
                                  }

                                // DISPLAY ACTOR IN ROLE
                                  if(isset($simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet[$j]->event->eventActor[$inc]->displayActorInRole)) {
                                        // eventactortype for dbase
                                        $displayActorInRole[$j][$inc] = $simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet[$j]->event->eventActor[$inc]->displayActorInRole;
                                  } else {
                                        $displayActorInRole[$j][$inc] = "";
                                  }

                                // EVENT ACTOR NAME
                                  // echo "<br/><strong>actor name (d-3.4.3.1) </strong>".$simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet[$j]->event->eventActor[$inc]->actorInRole->actor->nameActorSet->appellationValue."</strong>role: ".$simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet[$j]->event->eventActor[$inc]->actorInRole->roleActor->term."<br/>";

                                  $log.="actor name (d-3.4.3.1): ".$simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet[$j]->event->eventActor[$inc]->actorInRole->actor->nameActorSet->appellationValue."\nrole: ".$simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet[$j]->event->eventActor[$inc]->actorInRole->roleActor->term."\n";

                                // EVENT ACTOR TYPE
                                  $eventActorName[$j][$inc] = $simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet[$j]->event->eventActor[$inc]->actorInRole->actor->nameActorSet->appellationValue;

                            $inc++;
                            }

                         // EVENT DATE
                          //  echo "<strong>Event Date Text (D-3.6.1) </strong>".$simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet[$j]->event->eventDate->displayDate."<br/>";
                          //  echo "<strong>Event Earliest Date (D-3.6.2) </strong>".$simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet[$j]->event->eventDate->date->earliestDate."<br/>";
                          //  echo "<strong>Event Latest Date (D-3.6.3) </strong>".$simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet[$j]->event->eventDate->date->latestDate."<br/>";

                         // PERIOD NAME
                          //  echo "<strong>Period Name (D-3.6.4) </strong>".$simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet[$j]->event->periodName->term."<br/>";

                         // EVENT PLACE
                          //  echo "<strong>Event Place Free Text (D-3.7.1) </strong>".$simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet[$j]->event->eventPlace->displayPlace."<br/>";
                          //  echo "<strong>Event Place Geographical (D-3.7.3.1) </strong>".$simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet[$j]->event->eventPlace->displayPlace." [ <i>- need to work this out...</i> ]<br/>";


                         // EVENT DATE
                            $log.="Event Date Text (D-3.6.1): ".$simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet[$j]->event->eventDate->displayDate."\n";
                            $log.="Event Earliest Date (D-3.6.2): ".$simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet[$j]->event->eventDate->date->earliestDate."\n";
                            $log.="Event Latest Date (D-3.6.3): ".$simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet[$j]->event->eventDate->date->latestDate."\n";

                         // PERIOD NAME
                            $log.="<strong>Period Name (D-3.6.4): ".$simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet[$j]->event->periodName->term."\n";

                         // EVENT PLACE
                            $log.="Event Place Free Text (D-3.7.1): ".$simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet[$j]->event->eventPlace->displayPlace."\n";
                            $log.="Event Place Geographical (D-3.7.3.1): ".$simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet[$j]->event->eventPlace->displayPlace." [ <i>- need to work this out...</i> ]"."\n";

                          if (isset($simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet[$j]->event->eventType->term))
                            {
                            $eventType[$j] = $simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet[$j]->event->eventType->term;
                          } else {
                            $eventType[$j] ='';
                          }
                          if (isset($simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet[$j]->event->eventDate->displayDate))
                            {
                            $eventDateText[$j] = $simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet[$j]->event->eventDate->displayDate;
                          } else {
                            $eventDateText[$j] ='';
                          }
                          if (isset($simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet[$j]->event->eventDate->date->earliestDate))
                            {
                            $eventEarliestDate[$j] = $simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet[$j]->event->eventDate->date->earliestDate;
                          } else {
                            $eventEarliestDate[$j] = '';
                          }
                          if (isset($simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet[$j]->event->eventDate->date->latestDate))
                            {
                            $eventLatestDate[$j] = $simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet[$j]->event->eventDate->date->latestDate;
                          } else {
                            $eventLatestDate[$j] = '';
                          }
                           

                          if (isset($simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet[$j]->event->periodName->term))
                            {
                            $eventPeriodName[$j] = $simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet[$j]->event->periodName->term;
                          } else {
                            $eventPeriodName[$j] = '';
                          }

                            
                          if (isset($simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet[$j]->event->eventPlace->displayPlace))
                            {
                            $eventPlaceText[$j] = $simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet[$j]->event->eventPlace->displayPlace;
                          } else {
                            $eventPlaceText[$j] = '';
                          }
                            
                    $j++;
                }

        } else {
            //echo "<br/>There are no events.";
            $log.="There are no events"."\n";
        }






        // ADMIN DATA RECORDINFO
        $instrument['legalBodyID'] = '';
        if(isset($simpleXmlNode->lidoWrap->lido->administrativeMetadata->resourceWrap)) {

            //echo "<br/><strong>administrativeMetadata [what do we do with this??]</strong><br/>";
            $log.="administrativeMetadata:\n\n";

                $w=0;
                foreach( $simpleXmlNode->lidoWrap->lido->administrativeMetadata->recordWrap as $element )
                {
                   // echo "<strong>recordID </strong>".$simpleXmlNode->lidoWrap->lido->administrativeMetadata->recordWrap->recordID."</strong><br/>";
                   // echo "<strong>recordType </strong>".$simpleXmlNode->lidoWrap->lido->administrativeMetadata->recordWrap->recordType->term."</strong><br/>";
                   // echo "<strong>recordSource </strong>".$simpleXmlNode->lidoWrap->lido->administrativeMetadata->recordWrap->recordSource->legalBodyID."</strong><br/>";

                    $log.="recordID: ".$simpleXmlNode->lidoWrap->lido->administrativeMetadata->recordWrap->recordID."\n";
                    $log.="recordType: ".$simpleXmlNode->lidoWrap->lido->administrativeMetadata->recordWrap->recordType->term."\n";
                    $log.="recordSource: ".$simpleXmlNode->lidoWrap->lido->administrativeMetadata->recordWrap->recordSource->legalBodyID."\n";

                        $instrument['legalBodyID'] = $simpleXmlNode->lidoWrap->lido->administrativeMetadata->recordWrap->recordSource->legalBodyID;

                    $w++;
                }

        } else {
            echo "<br/>There is no administrativeMetadata.";
            $log.="There is no administrativeMetadata.";   
        }



        // DEAL WITH INSTRUMENT RESOURCES
        $resourceType = '';
        $resourceFileName = '';
        $thumbGenerated = 'no';
        $p=0;
        if(isset($simpleXmlNode->lidoWrap->lido->administrativeMetadata->resourceWrap->resourceSet[0]->resourceID)) {

            //echo "<br/><strong>There are ".$simpleXmlNode->lidoWrap->lido->administrativeMetadata->resourceWrap->resourceSet->count()." resources.<br/></strong>";
            $log.="There are ".$simpleXmlNode->lidoWrap->lido->administrativeMetadata->resourceWrap->resourceSet->count()." resources."."\n";

                foreach( $simpleXmlNode->lidoWrap->lido->administrativeMetadata->resourceWrap->resourceSet as $element )
                {
                   // echo "<strong>Type (A-3.1.3) </strong>".strtolower($simpleXmlNode->lidoWrap->lido->administrativeMetadata->resourceWrap->resourceSet[$p]->resourceType->term)."</strong><br/>";
                   // echo "<strong>resourceFileName (A-3.1.2) </strong>".$simpleXmlNode->lidoWrap->lido->administrativeMetadata->resourceWrap->resourceSet[$p]->resourceID."<br/>";

                    $log.="Type (A-3.1.3): ".strtolower($simpleXmlNode->lidoWrap->lido->administrativeMetadata->resourceWrap->resourceSet[$p]->resourceType->term)."\n";
                    $log.="resourceFileName (A-3.1.2): ".$simpleXmlNode->lidoWrap->lido->administrativeMetadata->resourceWrap->resourceSet[$p]->resourceID."\n";


                    $resourceType[$p] = trim(strtolower($simpleXmlNode->lidoWrap->lido->administrativeMetadata->resourceWrap->resourceSet[$p]->resourceType->term));
                    $resourceFileName[$p] = $simpleXmlNode->lidoWrap->lido->administrativeMetadata->resourceWrap->resourceSet[$p]->resourceID;
                   
                        if(isset($simpleXmlNode->lidoWrap->lido->administrativeMetadata->resourceWrap->resourceSet[$p]->resourceRepresentation->linkResource)) {
                            // echo "<strong>resourceCollectionLink (A-3.1.1) </strong>".$simpleXmlNode->lidoWrap->lido->administrativeMetadata->resourceWrap->resourceSet[$p]->resourceRepresentation->linkResource."<br/>";
                            $log.="resourceCollectionLink (A-3.1.1): ".$simpleXmlNode->lidoWrap->lido->administrativeMetadata->resourceWrap->resourceSet[$p]->resourceRepresentation->linkResource."\n";

                            $resourceCollectionLink[$p] = $simpleXmlNode->lidoWrap->lido->administrativeMetadata->resourceWrap->resourceSet[$p]->resourceRepresentation->linkResource;
                  
                                if ($resourceType[$p] == "image") {
                                  $savepath = $imagepath;
                                }
                                if ($resourceType[$p] == "sound") {
                                  $savepath = $soundpath;
                                  $thumbGenerated = 'yes';
                                }
                                if ($resourceType[$p] == "video") {
                                  $savepath = $videopath;
                                  $thumbGenerated = 'yes';
                                }

                                // get extension
                                $extension = \File::extension($resourceFileName[$p]);

                                // have a unique filename
                                $resourceFileName[$p] = md5($resourceFileName[$p].microtime()).'.'.$extension;

                                // get this resource
                                $getimage = new \GuzzleHttp\Client();
                                $getimage->get(
                                    trim($resourceCollectionLink[$p]),
                                    [
                                         'save_to' => $savepath.'/'.$resourceFileName[$p], // save this resource
                                    ]);

                                // now that resource has been created, let's create the thumbnail image if the resource is an image
                                if ($resourceType[$p] == "image") {
                               
                                            // create instance of image
                                            $img = \Image::make($savepath.'/'.$resourceFileName[$p]);
                                            $width = $img->width();
                                            $height = $img->height();
                                   
                                            if ($width > $height) { // width larger than height, let's constrain width and auto height
                                                  // resize the image to a width of 250 and constrain aspect ratio (auto height)
                                                  $img->resize($largest_thumbnail_pixels, null, function ($constraint) {
                                                      $constraint->aspectRatio();
                                                  });
                                            } else {  // height larger than width, let's constrain height and auto width
                                                // resize the image to a height of 250 and constrain aspect ratio (auto width)
                                                  $img->resize(null, $largest_thumbnail_pixels, function ($constraint) {
                                                      $constraint->aspectRatio();
                                                  });
                                            }

                                           $thumbGenerated = 'yes';
                                           $img->save($imagethumbpath.'/'.$resourceFileName[$p]);

                                } // end thumbail generation

                        } else {
                            $resourceCollectionLink[$p] = "";
                            if ($thumbGenerated != 'yes')
                             {
                              // thumb generation set to yes in all cirmstances except in the case where
                              // the resource is an image but thub not generated... ie where the resourcelink
                              // is not specified... for RCM and others we may put the main images in the correct folder
                              // and the resource collection link is not specified. In this case, this check here will look for the image
                              // in the folder and create a thumbnail
                               
                                            // create instance
                                            $img = \Image::make($imagepath.'/'.$resourceFileName[$p]);
                                            $width = $img->width();
                                            $height = $img->height();
                                                                           
                                            if ($width > $height) { // width larger than height, let's constrain width and auto height

                                                  // resize the image to a width of 250 and constrain aspect ratio (auto height)
                                                  $img->resize($largest_thumbnail_pixels, null, function ($constraint) {
                                                      $constraint->aspectRatio();
                                                  });

                                            } else {  // height larger than width, let's constrain height and auto width

                                                // resize the image to a height of 250 and constrain aspect ratio (auto width)
                                                  $img->resize(null, $largest_thumbnail_pixels, function ($constraint) {
                                                      $constraint->aspectRatio();
                                                  });

                                            }

                                           $img->save($imagethumbpath.'/'.$resourceFileName[$p]);

                             }   


                        }   // end if link to external resource

                    $p++;
                }

        } else {
            // echo "<br/>There are no resources.";
            $log.="There are no resources.";

            $resourceType[$p] = "";
            $resourceFileName[$p] = "";
            $resourceCollectionLink[$p] = "";
        }





if ($importme == 1) {
    // instrument has a name, let's import it
        // Let's compare the instrument name against the approved terms
         $thesaurus = \DB::table('thesauruses')
        ->where('Level_3', '=', $instrument['title_preferred'])  
        ->take(1)->get();

        if (isset($thesaurus[0]->thesaurusID))
        {
             $thesaurusID = $thesaurus[0]->thesaurusID;  
             $Level_0 = $thesaurus[0]->Level_0;  
             $Level_1 = $thesaurus[0]->Level_1;  
             $Level_2 = $thesaurus[0]->Level_2;  
             $Level_3 = $thesaurus[0]->Level_3;  
             $instrument_tags = $Level_0.','.$Level_1.','.$Level_2.','.$Level_3; // all contextual hierarchy from thesaurus as this is an approved term
        } else {
             $thesaurusID = '';
             $Level_0 = '';
             $Level_1 = '';
             $Level_2 = ''; 
             $Level_3 = '';
             $instrument_tags = '';  // all contextual hierarchy from thesaurus as this is an approved term
        } 


         if (strlen(trim($instrument['title_preferred_actual'])) > 0) {
            $instrumentName = $instrument['title_preferred_actual']; // this is the actual value if set for specific ins name
         } else {
            $instrumentName = $instrument['title_preferred'];        // this is actually the classification
         }      

        // LOOK UP HORNBOSTEL
         $hornbostel_match = \DB::table('hornbostel')
        ->where('HornbostelCat', '=', $instrument['hornsbostel'])     // look for exact match on hornbostel cat   
        ->take(1)->get();

        if (sizeof($hornbostel_match) < 1) { 
                 
                // no hornbostel match
                $hornbostelID = '0';

        } else {

                // hornbostel match, get id to add with instrument
                $hornbostelID = $hornbostel_match[0]->id; 
        }

        // ADD INSTRUMENT TO DATABASE
        $instrumentID = \DB::table('instruments')->insertGetId(
           // legalbodyid should be in session
           ['legalBodyID' => $legalbody_id_session, 'adminID' => $adminID_session, 'creationType' => 'import', 'status' => 'imported', 'thesaurusID' => $thesaurusID, 'hornbostelID' => $hornbostelID, 'Level_0' => $Level_0, 'Level_1' => $Level_1, 'Level_2' => $Level_2, 'Level_3' => $Level_3, 'tags' => $instrument_tags, 'hornbostelCat' => $instrument['hornsbostel'], 'titlePreferred' => $instrumentName, 'inscriptions' => $inscriptionString, 'measurementsFreeText' => $instrument['measurementsFreeText'], 'created_at' => \Carbon\Carbon::now()]
        );

        // ADD REPOSITORIES
        $x=0;
        while ($x < sizeof($inventoriesBodyName))
        {
            \DB::table('repositories')->insert(
                ['instrumentID' => $instrumentID, 'legalBodyID' => $legalbody_id_session, 'inventoryNumber' => $inventoryNumber[$x], 'repositoryName' => $inventoriesBodyName[$x]]
            );

            $x++;
        }

        // ADD SPECIFIC MEASUREMENTS
        $x=0;
        while ($x < sizeof($measurementType))
        {

            if (isset($measurementValue[$x])) // only add if we have measurement type
            {  
                  // add specific measurement for this instrument
                  \DB::table('measurements')->insert(
                      ['instrumentID' => $instrumentID, 'legalBodyID' => $legalbody_id_session, 'unit' => $measurementUnit[$x], 'type' => $measurementType[$x], 'value' => $measurementValue[$x]]
                  );   

            }      

            $x++;
        }
  



//echo "size of desc ".sizeof($descriptionType);

        // ADD DESCRIPTIONS
        $x=0;
        while ($x < sizeof($descriptionType))
        {
                if($x == 0)
                {    
                    // main description in instrument table
                    \DB::table('instruments')
                     ->where('instrumentID', $instrumentID)
                     ->update(array('mainDescriptionType' => $descriptionType[$x], 'mainDescriptionText' => $descriptionText[$x]));

                } else {
                    // other description(s) in descriptions table
                        \DB::table('descriptions')->insert(
                            ['instrumentID' => $instrumentID, 'legalBodyID' => $legalbody_id_session, 'descriptionType' => $descriptionType[$x], 'descriptionText' => $descriptionText[$x]]
                        );
                }        

            $x++;
        }

        // ADD EVENTS AND ACTORS
        $x=0;
        while ($x < sizeof($eventType))
        {
            if (strtolower($eventType[$x]) == "production") {
            // If the event is a production event, then we store it in the instruments table - AND UPDATE instrument rather than INSERT

                \DB::table('instruments')
                 ->where('instrumentID', $instrumentID)
                 ->update(array('productionEventLocation' => $eventPlaceText[$x], 'productionEventDateText' => $eventDateText[$x], 'productionEventEarliestDate' => $eventEarliestDate[$x], 'productionEventLatestDate' => $eventLatestDate[$x]));

                    echo "<strong>PRODUCTION ".$eventType[$x]."</strong><br/>";

                    // ADD EVENT ACTORS
                    $actinc=0;
                    while ($actinc < sizeof($eventActorName[$x]))
                    {
                            echo sizeof($eventActorName[$x])."actor for proodcution in ins no event id ".$actinc.$eventActorName[$x][$actinc]."<br/>";

                            // EXCLUDE BLANK ACTORS
                            if ($eventActorName[$x][$actinc] != "") {     
                                    \DB::table('eventactors')->insert(
                                        //['eventID' => $eventID, 'eventActorType' => $eventActorType[$x][$actinc], 'eventActorName' => $eventActorName[$x][$actinc]]
                                        ['instrumentID' => $instrumentID, 'legalBodyID' => $legalbody_id_session, 'eventActorName' => $eventActorName[$x][$actinc], 'eventActorRole' => $eventActorRole[$x][$actinc], 'eventDisplayActorRole' => $displayActorInRole[$x][$actinc]]
                                    );
                             }

                        $actinc++;
                    }


            } else {         // If the event is NOT a production event, we store it in the events table..
            
                $eventID = \DB::table('events')->insertGetId(
                    ['instrumentID' => $instrumentID, 'legalBodyID' => $legalbody_id_session,  'eventType' => $eventType[$x], 'eventDateText' => $eventDateText[$x], 'eventEarliestDate' => $eventEarliestDate[$x], 'eventLatestDate' => $eventLatestDate[$x]]
                );


                    echo "<strong>".$eventType[$x]."</strong><br/>";

                    // ADD EVENT ACTORS
                    $actinc=0;
                    while ($actinc < sizeof($eventActorName[$x]))
                    {

                            echo sizeof($eventActorName[$x])."ACTOR!!!!!!!!!!! eventid".$eventID." ".$actinc.$eventActorName[$x][$actinc]."<br/>";

                            // EXCLUDE BLANK ACTORS
                            if ($eventActorName[$x][$actinc] != "") {     
                                    \DB::table('eventactors')->insert(
                                        //['eventID' => $eventID, 'eventActorType' => $eventActorType[$x][$actinc], 'eventActorName' => $eventActorName[$x][$actinc]]
                                        ['eventID' => $eventID, 'legalBodyID' => $legalbody_id_session, 'eventActorName' => $eventActorName[$x][$actinc], 'eventActorRole' => $eventActorRole[$x][$actinc], 'eventDisplayActorRole' => $displayActorInRole[$x][$actinc]]
                                    );
                             }

                        $actinc++;
                    }

                } // end if the event is NOT a production event..    

            $x++;
        }
      
        // ADD RESOURCES
        $x=0;
        while ($x < sizeof($resourceType))
        {
           
            \DB::table('resources')->insert(
                ['instrumentID' => $instrumentID, 'legalBodyID' => $legalbody_id_session, 'resourceType' => strtolower($resourceType[$x]), 'resourceFileName' => $resourceFileName[$x], 'resourceCollectionLink' => $resourceCollectionLink[$x]]
            );

            $x++;
        }





        // ADD IMPORT ACTIVITY FOR THIS INSTRUMENT TO ACTIVITY TABLE
        /*
           \DB::table('user_activity')->insert(
              ['userID' => $adminID_session, 'instrumentID' => $instrumentID, 'type' => 'imported', 'activity' => "blahblah ", 'activityDate' => \Carbon\Carbon::now() ]
            );
        */


} // end dbase import...





// BAIL  OUT AT 10 for now..
//if ($counter == 10) { die(); }





              //echo "<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>";
              $log.="\n\n\n";  







              $counter++;

        } // end looping through nodes

} catch( ConnectException $ex ) {
    switch ( $ex->getMessage() ) {
        case '7': // to be verified (CURL ERROR 7 - losing connection to remote source.)
                  // This can happen when remote source of resource is not available...
                  // handle by adding error to dbase to be shown on frontend and breaking out of import.
                  // User will have to try again laer.
                  // 
                  // http://stackoverflow.com/questions/29617480/how-to-handle-fatal-error-curl-error-7-failed-to-connect-to-xxxx-port-443
                  // HANDLE ERROR HERE
                  // 
                  // 
                  // 
            break;
    }
}


        $counter--;
        $time_end = microtime(true);

        echo "<p style='font-size:40px;'>Total Records: ".$counter."<br/>Total Imported: <span style='color:green;'>".$imported.'</span></p>';
        $log.="Total Records: ".$counter."\nTotal Imported: \n".$imported."\n";

        $execution_time = round(($time_end - $time_start)/60,2);
        echo "<p style='font-size:40px;'>Total Execution Time: ".$execution_time." minutes</p>";
        $log.="Total Execution Time: ".$execution_time." minutes";

        $short_log ="<p style='font-size:16px;'>Total Records: ".$counter."<br/>Total Imported: <span style='color:green;'>".$imported."</span><br/>Total Execution Time: ".$execution_time." minutes</p>";

        //echo "log = ".$log;


//'currentStatus' => 'Total Records: '.$counter.'<br/>Total Imported: '.$imported.'<br/>Total Execution Time: '.$execution_time.' minutes'

                  \DB::table('import_jobs')
                   ->where('id', $import_jobID)
                   ->update(array('currentStatus' => 'completed', 'log' => $log, 'short_log' => $short_log, 'time_ended' => \Carbon\Carbon::now() ));




// not writing txt file... use dbase
//$file = public_path().'/instrument_resources/logs/'.$legalbody_session.'/log'.date('Y_m_d_H_i_s').'.txt';
//\File::put($file, $log);


        // ADD IMPORT ACTIVITY TO ACTIVITY TABLE
            \DB::table('user_activity')->insert(
              ['userID' => $adminID_session, 'legalBodyID' => $legalbody_id_session, 'legalBodyName' => $legalbody_name_session, 'type' => 'admin_import', 'activity' => "imported ".$imported." instruments", 'activityDate' => \Carbon\Carbon::now() ]
            );


 
echo $log;


    }   // end import








































   public function importlisten($lastImportIDForm)
   {
        $lastImportIDForm = (int) $lastImportIDForm; // force int
        $sessiondata = session()->all();
        $role = \Session::get('role');
        $user_id = Auth::user()->getId();

        $importquery='';
        $importquery = \DB::table('import_jobs')
        ->where('userID', $user_id)
        ->orderBy('id', 'desc')
        ->take(1)->get();


        if (isset($importquery[0]->id))
        {
            $lastImportID = (int) $importquery[0]->id;
            $percent = $importquery[0]->percentComplete;
            $current_status = $importquery[0]->currentStatus;

        } else {
            $lastImportID = 0;
        }


        //echo 'last import id from form '.$lastImportIDForm."<br/>";
        //echo 'last import id from dbase '.$lastImportID."<br/>";


        if ($lastImportID <> '0' && $lastImportID <> $lastImportIDForm)
        {
           // echo 'import id from form '.$lastImportID.'<br/>';
           // echo 'latest user import id '.$lastImportID.'<br/>';
           // echo 'session import id'.\Session::get('lastImportID').'<br/>';
            if($current_status != 'completed')
            {    
                 // import is in progress   
                 echo 'percent completed: <span style="color:green; font-size:30px;">'.$percent.'%</span><br/>';
                 echo 'status: '.$current_status."<br/>";

/*
echo '
 <div class="progress">
  <div class="progress-bar" role="progressbar" aria-valuenow="70"
  aria-valuemin="0" aria-valuemax="100" style="width:'.$percent.'">
    <span class="sr-only">70% Complete</span>
  </div>
</div>';
*/

            } else {
                // import complete
                $short_log = $importquery[0]->short_log;

                echo $short_log."<br/>";
                echo "Import complete. <a href='/dashboard'>Go to dashboard</a>.";
             
            }   
        }



   }

























/*
   public function importhornsbostel(Request $request)
    {

       $xml = simplexml_load_file(public_path().'/xml/hornsbostel/hs_one_record.xml');
        foreach( $xml as $element )
        {
            echo"<pre>"; print_r( $element ); echo "</pre>";
        }

        // chunk size (16k)
        $chunksize = 16384;

        // for script execution time displayed at end of function..
        $time_start = microtime(true);

        // find xml file
        $file = public_path().'/xml/hornsbostel/hs.xml';

        // get the total file size of the xml
        $filesize = filesize($file); // filesize in bytes of rcm xml
        echo "<span style='color:red;'><strong>Total file size: </strong>".$filesize." bytes.<br/><br/></span>";
              // progressclass for future function where import built into admin
              //  $this->progressbarClass->setTotalCount(filesize($file));

                $stream = new File($file, $chunksize, function($chunk, $readBytes) use ($filesize) {
                    // This closure will be called every time the streamer requests a new chunk of data from the collection's XML file
                    // $this->progressbarClass->reportProgress($readBytes);
               
                $percent = round($readBytes / $filesize * 100,2);  
                  
                echo "<span style='color:red;'><strong>".$readBytes."</strong> bytes read out of <strong>".$filesize."</strong> total. <span style='font-size:50px;'><strong>".$percent."%</strong></span></span><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>";

                });

                // Convenience method for creating a file streamer with the default parser
                $parser = new StringWalker;
                $streamer = new XmlStringStreamer($parser, $stream);

                $counter=1;

                while ($node = $streamer->getNode()) {
                    // $node is one <lido>...</lido> record...
                    $simpleXmlNode = simplexml_load_string($node);


                    // BAIL  OUT AT 25 for now..
                    //if ($counter == 10) { die(); }


                        echo "<br/><span style='font-size:50px; color:green;'>Record ".$counter."</span><br/>".(string)$simpleXmlNode->eid;
                        $eid = (string)$simpleXmlNode->eid;

                        echo "<br/>".(string)$simpleXmlNode->label;
                        $label = (string)$simpleXmlNode->label;

                        // get hornsbostel number from label (first word before space)
                        $arr = explode(' ',trim((string)$simpleXmlNode->label));
                        echo "<br/>hornsbostel: ".$arr[0]; // will print Test
                        $hornsbostel = $arr[0];

                        echo "<br/>".(string)$simpleXmlNode->definition;
                        $definition = (string)$simpleXmlNode->definition;;

                        echo "<br/>".(string)$simpleXmlNode->language;
                        $language = (string)$simpleXmlNode->language;

                        echo "<br/>".(string)$simpleXmlNode->mimoType;
                        $mimotype = (string)$simpleXmlNode->mimoType;

                        echo "<br/>".(string)$simpleXmlNode->relation->type;
                        $type = (string)$simpleXmlNode->relation->type;

                        echo "<br/>".(string)$simpleXmlNode->relation->label;
                        $relationlabel = (string)$simpleXmlNode->relation->label;

                        echo "<br/>".(string)$simpleXmlNode->relation->eid;
                        $relationeid = (string)$simpleXmlNode->relation->eid;

                        echo "<br/>".(string)$simpleXmlNode->relation->language;
                        $relationlanguage = (string)$simpleXmlNode->relation->language;



    \DB::table('hornsbostel')->insert(
        ['eid' => $eid, 'HornbostelCat' => $hornsbostel, 'label' => $label, 'definition' => $definition, 'mimoType' => $mimotype]
    );


                    echo "<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>";

                  $counter++;

                } // end looping through nodes

        $counter--;
        $time_end = microtime(true);

       // echo "<p style='font-size:40px;'>Total Records: ".$counter."<br/>Total Imported: <span style='color:green;'>".$imported.'</span></p>';

        $execution_time = round(($time_end - $time_start)/60,2);
        echo "<p style='font-size:40px;'>Total Execution Time: ".$execution_time." minutes</p>";

    }   // end import hornbostel
*/




} // end import controller class