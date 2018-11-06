<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use Prewk\XmlStringStreamer;
use Prewk\XmlStringStreamer\Stream\File;
use Prewk\XmlStringStreamer\Parser\StringWalker;
use Guzzle\Http\Client;
use Guzzle\Http\Ring\Exception\ConnectException;
use Auth;
use Config;

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
  //echo "import disabled"; die;
  $user_id = Auth::user()->getId();
  $adminID_session = $user_id;
  $largest_thumbnail_pixels = Config::get('app.instrument_thumb_max');
  $sessiondata = session()->all();
  $role = \Session::get('role');

  // these are set in the session from the form to set up the import
  $legalBodyName_import = \Session::get('legalBodyName_import');
  $legalBodyID_import = \Session::get('legalBodyID_import');
  $import_type_import = \Session::get('import_type_import');
  $MDA_code_import = \Session::get('MDA_code_import');
  $importXMLfile = \Session::get('importXMLfile');

  // import loops uses the variables below - map them to the session ones...
  $legalbody_session = $MDA_code_import;
  $legalbody_name_session = $legalBodyName_import;
  $legalbody_id_session = $legalBodyID_import;
  $file = public_path().'/instrument_resources/xml/'.$legalbody_session.'/'.$importXMLfile;

  // truncate tables for test 
  /*
  \DB::table('user_activity')->truncate();
  \DB::table('instruments')->truncate();
  \DB::table('repositories')->truncate();
  \DB::table('descriptions')->truncate();
  \DB::table('events')->truncate();
  \DB::table('eventactors')->truncate();
  \DB::table('resources')->truncate();
  \DB::table('measurements')->truncate();
  \DB::table('eventactors')->truncate();
  \DB::table('resources')->truncate();
  \DB::table('rights')->truncate();
  */
 
  // check if folders exist
   $imagepath = public_path().'/instrument_resources/images/'.$legalbody_session;
   $imagethumbpath = public_path().'/instrument_resources/images/'.$legalbody_session.'/thumbnails/';
   $soundpath = public_path().'/instrument_resources/sound/'.$legalbody_session;
   $videopath = public_path().'/instrument_resources/video/'.$legalbody_session;
   $raw_local_path = public_path().'/instrument_resources/raw_local/'.$legalbody_session;

  if (!file_exists($raw_local_path)) 
  {    // create folder..
      \File::makeDirectory($raw_local_path, $mode = 0777, true, true);
  }

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

  //$chunksize = 16384; // chunk size (16k)
  //$chunksize = 8192; // chunk size (8k)
  //$chunksize = 4096; // chunk size (4k)
  //$chunksize = 2048; // chunk size (2k)
  // chunk size (1k)
  $chunksize = 1024;

  // for script execution time...
  $time_start = microtime(true);

  // load one mimo file to show structure..
  //$xml = simplexml_load_file(public_path().'/xml/one_mimo_record.xml');
  //foreach( $xml as $element )
  //{
     // echo"<pre>"; print_r( $element ); echo "</pre>";
  //}

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

  // initialise log to store progress of import
  $log="";
  $filesize = filesize($file); // filesize in bytes of rcm xml
  $file_last_modified = (new \DateTime())
      ->setTimestamp(\File::lastModified($file))
      ->format('D, d M Y H:i:s T');

  // add import job to database
  $import_jobID = \DB::table('import_jobs')->insertGetId(
     // legalbodyid should be in session
     ['userID' => $user_id, 'legalBodyID' => $legalbody_id_session, 'fileName' => basename($file), 'fileSize' => $filesize, 'fileLastModified' => $file_last_modified, 'time_started' => \Carbon\Carbon::now()]
  );

$non_destructive_import_counter = 0;

  try {
    // begin import
      echo "<span style='color:red;'><strong>Total file size: </strong>".$filesize." bytes.<br/><br/></span>";
      // Total = XML file size
      //  $this->progressbarClass->setTotalCount(filesize($file));

      $stream = new File($file, $chunksize, function($chunk, $readBytes) use ($filesize, $import_jobID) {
      // This closure will be called every time the streamer requests a new chunk of data from the XML file
      // $this->progressbarClass->reportProgress($readBytes);
       
      $percent = round($readBytes / $filesize * 100,2);  
      $log = "<br/><span style='color:red;'><strong>".$readBytes."</strong> bytes read out of <strong>".$filesize."</strong> total. <span style='font-size:50px;'><strong>".$percent."%</strong></span></span><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>";

      // update job progress every $chunksize of bytes as defined above closure
      \DB::table('import_jobs')
      ->where('id', $import_jobID)
      ->update(array('percentComplete' => $percent, 'currentStatus' => $readBytes.' bytes / '.$filesize.' bytes processed.' ));
      });


     if($import_type_import == "overwrite_all")
     {
          // delete everything for this collection in dbase
          //\DB::table('user_activity')->truncate()
          \DB::table('instruments')->where('legalBodyID', '=', $legalBodyID_import)->delete();
          \DB::table('repositories')->where('legalBodyID', '=', $legalBodyID_import)->delete();
          \DB::table('descriptions')->where('legalBodyID', '=', $legalBodyID_import)->delete();
          \DB::table('events')->where('legalBodyID', '=', $legalBodyID_import)->delete();
          \DB::table('eventactors')->where('legalBodyID', '=', $legalBodyID_import)->delete();
          \DB::table('resources')->where('legalBodyID', '=', $legalBodyID_import)->delete();
          \DB::table('measurements')->where('legalBodyID', '=', $legalBodyID_import)->delete();
          \DB::table('eventactors')->where('legalBodyID', '=', $legalBodyID_import)->delete();
          \DB::table('rights')->where('legalBodyID', '=', $legalBodyID_import)->delete();

          // delete all the folders        
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

          // sleep 1 second between folder deletion and creation
          sleep(1);

          // recreate all the folders
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

          //echo "xit"; exit;

   } // end if delete all



     // file streamer
     $parser = new StringWalker;
     $streamer = new XmlStringStreamer($parser, $stream);
     $counter=1;
     $imported=0;
     $log .="Import begun by userID: ".$user_id."\r\n";





     while ($node = $streamer->getNode()) { // $node is one <lido>...</lido> record...
     
     
     // Nullify Variables At The Beginning Of The Loop... 
     $descriptionType = null;
     $eventType = null;
     $instrumentRightsType = null;
     $rightsType = null;
     $measurementType = null;
     $measurementUnit = null;
     $measurementValue = null;
     $decorativeElementsString = null;                   
     $inscriptionString = null;
     $serialString = null;
     $inventoryNumber = null;
     $materialsText = null;

     // Load Node...
     $simpleXmlNode = simplexml_load_string($node);
     $log .="Record ".$counter."".(string)$simpleXmlNode->identifier."\r\n";

     // Default to not importing this instrument
     $importme = 0;
     if(isset($simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectClassificationWrap->classificationWrap->classification))
     {  
        // loop through classification(s) until we find an instrument name  
          $j=0;
          foreach( $simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectClassificationWrap->classificationWrap->classification as $element )
          {        
                if (isset($simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectClassificationWrap->classificationWrap->classification[$j]->term))
                {
                  // instrument name is set, can import
                   $importme = 1;
                }
                $j++;
            }
            if($importme == 0) {
                  // this record will be ignored is OK to import, pending import type
                  $log .="[IGNORED]"."\r\n";
            } 
        }    

        if(isset($simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectClassificationWrap->objectWorkTypeWrap->objectWorkType->term)) {
            $log.= "Base Category".(string)$simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectClassificationWrap->objectWorkTypeWrap->objectWorkType->term."\r\n";
        }

        // INSTRUMENT CAT COULD BE IN classification(1) or (0)
        if(isset($simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectClassificationWrap->classificationWrap->classification[0]->term)) {
            $log.="title category (d-2.1)".(string)$simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectClassificationWrap->classificationWrap->classification[0]->term."\r\n";
            $instrument['title_preferred'] = (string)$simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectClassificationWrap->classificationWrap->classification[0]->term;
        }

        // INSTRUMENT CAT COULD BE IN classification(1) or (0)
        if(isset($simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectClassificationWrap->classificationWrap->classification[1]->term)) {
            $log.="title category (d-2.1)".(string)$simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectClassificationWrap->classificationWrap->classification[1]->term."\r\n";
            $instrument['title_preferred'] = (string)$simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectClassificationWrap->classificationWrap->classification[1]->term;
        }

        // instrument actual name
        if(isset($simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectIdentificationWrap->titleWrap->titleSet->appellationValue)) {
            $log.="title preferred (d-2.1)".(string)$simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectIdentificationWrap->titleWrap->titleSet->appellationValue."\r\n";
            $instrument['title_preferred_actual'] = (string)$simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectIdentificationWrap->titleWrap->titleSet->appellationValue;
        }

        // HORNBOSTEL COULD BE IN classification(1) or (0)
        if(isset($simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectClassificationWrap->classificationWrap->classification[0]->conceptID)) {      
           $att = 'type';
           $attribute = $simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectClassificationWrap->classificationWrap->classification[0]->conceptID->attributes()->$att;
           $log.= "conceptID class attribute:".$attribute."\r\n";    
           $log.= "conceptID  (hornsbostel cat d-1.2.1):".$simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectClassificationWrap->classificationWrap->classification[0]->conceptID."\r\n";  
           $instrument['hornsbostel'] = $simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectClassificationWrap->classificationWrap->classification[0]->conceptID;         
        } else {
           $instrument['hornsbostel'] = '';
        }

        // HORNBOSTEL COULD BE IN classification(1) or (0)
        if(isset($simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectClassificationWrap->classificationWrap->classification[1]->conceptID)) {        
           $att = 'type';
           $attribute = $simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectClassificationWrap->classificationWrap->classification[1]->conceptID->attributes()->$att;
           $log.= "conceptID class attribute:".$attribute."\r\n";    
           $log.= "conceptID  (hornsbostel cat d-1.2.1):".$simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectClassificationWrap->classificationWrap->classification[1]->conceptID."<br/>";  
           $instrument['hornsbostel'] = $simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectClassificationWrap->classificationWrap->classification[1]->conceptID;        
        } 

        // INSCRIPTIONS
         $inscriptionString="";
        if(isset($simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectIdentificationWrap->inscriptionsWrap->inscriptions[0]->inscriptionDescription->descriptiveNoteValue)) {
            $log.="There are ".$simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectIdentificationWrap->inscriptionsWrap->inscriptions->count()." inscriptions. (d2.3)"."\r\n";
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
                $log.="Inscription string for dbase: </strong>".$inscriptionString."\r\n";
                $inscriptionString = rtrim($inscriptionString, "|"); 
        } else {
            //echo "<br/><strong>There are no inscriptions.</strong>";
            $log.="There are no inscriptions."; 
        }




        // SERIAL NUMBERS
         $serialString="";
        if(isset($simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectIdentificationWrap->displayStateEditionWrap[0]->displayEdition)) {
            $log.="There are ".$simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectIdentificationWrap->displayStateEditionWrap->count()." serial edition numbers."."\r\n";
                $j=0;
                foreach( $simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectIdentificationWrap->displayStateEditionWrap as $element )
                {
                    if (strlen(trim($simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectIdentificationWrap->displayStateEditionWrap[$j]->displayEdition)) > 0) {
                        $serialString .= $simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectIdentificationWrap->displayStateEditionWrap[$j]->displayEdition.'|';
                    } else {
                        $serialString = '';
                    }
                    $j++;
                }
                $log.="Serial edition string: ".$serialString."\r\n";
                $serialString = rtrim($serialString, "|"); 
        } else {
            //echo "<br/><strong>There are no serial edition numbers .</strong>";
            $log.="There are no serial edition numbers."; 
        }



   






        // MEASUREMENTS FREE TEXT
        if(isset($simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectIdentificationWrap->objectMeasurementsWrap->objectMeasurementsSet[0]->displayObjectMeasurements)) {
                $j=0;
                foreach( $simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectIdentificationWrap->objectMeasurementsWrap->objectMeasurementsSet as $element )
                {
                    // MEASUREMENTS FREE TEXT
                    echo $simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectIdentificationWrap->objectMeasurementsWrap->objectMeasurementsSet[$j]->displayObjectMeasurements."<br/>";
                    $instrument['measurementsFreeText'] = trim($simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectIdentificationWrap->objectMeasurementsWrap->objectMeasurementsSet[$j]->displayObjectMeasurements);
                    $j++;
                }
        } else {
            echo "<br/><strong>There are no measurements.</strong>";
            $instrument['measurementsFreeText'] = '';
        }

        // SPECIFIC MEASUREMENTS
        if(isset($simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectIdentificationWrap->objectMeasurementsWrap->objectMeasurementsSet[0]->objectMeasurements->measurementsSet->measurementType)) {
           // echo "<br/><strong>There are ".$simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectIdentificationWrap->objectMeasurementsWrap->objectMeasurementsSet[0]->objectMeasurements->measurementsSet->count()." specific measurements measurements.</strong> ";
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
                       // echo '<br/>'.$measurementType[$j].' '.$measurementUnit[$j].' '.$measurementValue[$j];
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
            $log.= "There are ".$simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectIdentificationWrap->repositoryWrap->repositorySet->count()." repositories."."\r\n";
                $j=0;
                foreach( $simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectIdentificationWrap->repositoryWrap->repositorySet as $element )
                {
                    $log.="LegalBodyName (A-1.3.2)".$simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectIdentificationWrap->repositoryWrap->repositorySet[$j]->repositoryName->legalBodyName->appellationValue."\r\n";
                    $log.= "Inventory Number (D-2.2.1)".$simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectIdentificationWrap->repositoryWrap->repositorySet[$j]->workID."\r\n";
                    $inventoriesBodyName[$j] = $simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectIdentificationWrap->repositoryWrap->repositorySet[$j]->repositoryName->legalBodyName->appellationValue;
                    $inventoryNumber[$j] = $simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectIdentificationWrap->repositoryWrap->repositorySet[$j]->workID;
                    $j++;
                }
        } else {
            $log.="There are no repositories.";
        }

        // DESCRIPTIONS
        if(isset($simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectIdentificationWrap->objectDescriptionWrap->objectDescriptionSet[0]->descriptiveNoteValue)) {
            $log.="There are ".$simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectIdentificationWrap->objectDescriptionWrap->objectDescriptionSet->count()." descriptions."."\r\n";
                $labelled_general_description = 0;
                $j=0;
                    foreach( $simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectIdentificationWrap->objectDescriptionWrap->objectDescriptionSet as $element )
                    {
                    // DESCRIPTION TYPE
                    $att = 'type';
                    if(isset($simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectIdentificationWrap->objectDescriptionWrap->objectDescriptionSet[$j]->attributes()->$att)) {                  
                          $attribute = $simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectIdentificationWrap->objectDescriptionWrap->objectDescriptionSet[$j]->attributes()->$att;
                          // echo "<br/><strong>desc type (d-2.4.1) </strong>".$attribute."<br/>";
                          $log.="Description Type (d-2.4.1): ".$attribute."\r\n";
                          // set the descriptiontion type to the attribute  
                          $descriptionType[$j] = $attribute;
                          if ($attribute == "general description")
                          {
                            $labelled_general_description = 1;
                          }  
                    } else {
                        $descriptionType[$j] = 'general description';
                    }
                    // DESCRIPTION   
                    $log.="Description Text (d-2.4.2): ".$simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectIdentificationWrap->objectDescriptionWrap->objectDescriptionSet[$j]->descriptiveNoteValue."\r\n";
                    $descriptionText[$j] = trim($simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectIdentificationWrap->objectDescriptionWrap->objectDescriptionSet[$j]->descriptiveNoteValue);


                    // D-2.4.3: Description Source
                    if(isset($simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectIdentificationWrap->objectDescriptionWrap->objectDescriptionSet[$j]->sourceDescriptiveNote))
                    {
                      $descriptionTextSource[$j] = trim($simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectIdentificationWrap->objectDescriptionWrap->objectDescriptionSet[$j]->sourceDescriptiveNote);
                      $log .= "Description Source (d-2.4.3): ".trim($simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectIdentificationWrap->objectDescriptionWrap->objectDescriptionSet[$j]->sourceDescriptiveNote)."\r\n";
                    } else {
                      $descriptionTextSource[$j] = '';
                    } 

                $j++;
            }
        } else {
            $log.="There are no descriptions.";  
            $descriptionText = '';
            $descriptionType = '';

        }

        // EVENTS       
        if(isset($simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet[0]->event->eventType->term)) {
            $log.="There are ".$simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet->count()." events."."\r\n";
                $j=0;
                foreach( $simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet as $element )
                {
                    $log.="event type (d-3.2)".$simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet[$j]->event->eventType->term."\r\n";
                    $log.="This event has ".$simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet[$j]->event->eventActor->count()." actors."."\r\n";
                    // get event materials text, if present
                    if(isset($simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet[$j]->event->eventMaterialsTech->displayMaterialsTech)) {
                      $materialsText[$j] = trim($simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet[$j]->event->eventMaterialsTech->displayMaterialsTech);
                    } else {
                      $materialsText[$j] = '';
                    }
                        // SPECIFIC EVENT MATERIALS
                        $materialsString[$j]="";
                        if(isset($simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet[$j]->event->eventMaterialsTech->materialsTech->termMaterialsTech[0]->term))
                        {
                             // echo "<br/><strong>There are ".$simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet[$j]->event->eventMaterialsTech->materialsTech->termMaterialsTech->count()." specific materials for this event. (d2.3)</strong>";
                             $log.="There are ".$simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet[$j]->event->eventMaterialsTech->materialsTech->termMaterialsTech->count()." specific materials for this event. (d2.3)"."\r\n";
                                $mat=0;
                                foreach( $simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet[$j]->event->eventMaterialsTech->materialsTech->termMaterialsTech as $element )
                                {
                                    if (strlen(trim($simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet[$j]->event->eventMaterialsTech->materialsTech->termMaterialsTech[$mat]->term)) > 0) {
                                        $materialsString[$j] .= trim($simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet[$j]->event->eventMaterialsTech->materialsTech->termMaterialsTech[$mat]->term).'|';
                                    } else {
                                        $materialsString[$j] = '';
                                    }
                                    $mat++;
                                }
                                $log.="Event materials string for dbase: </strong>".$materialsString[$j]."\r\n";
                                $materialsString[$j] = rtrim($materialsString[$j], "|"); 
                        } else {
                            $materialsString[$j] = '';
                            $log.="There are no specific materials for this event."; 
                        }


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
                                $log.="actor name (d-3.4.3.1): ".$simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet[$j]->event->eventActor[$inc]->actorInRole->actor->nameActorSet->appellationValue."<br/>role: ".$simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet[$j]->event->eventActor[$inc]->actorInRole->roleActor->term."\r\n";

                                // EVENT ACTOR TYPE
                                  $eventActorName[$j][$inc] = $simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet[$j]->event->eventActor[$inc]->actorInRole->actor->nameActorSet->appellationValue;
                            $inc++;
                            }


                          if (isset($simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet[$j]->event->eventType->term))
                          {
                            $eventType[$j] = $simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet[$j]->event->eventType->term;
                          } else {
                            $eventType[$j] ='';
                          }


                          if (isset($simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet[$j]->event->eventName->appellationValue))
                          {
                            $eventName[$j] = $simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet[$j]->event->eventName->appellationValue;
                          } else {
                            $eventName[$j] ='';
                          }

                          if (isset($simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet[$j]->event->eventDate->displayDate))
                            {
                            $eventDateText[$j] = $simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet[$j]->event->eventDate->displayDate;
                            $log.="Event Date Text (D-3.6.1): ".$simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet[$j]->event->eventDate->displayDate."\r\n";

                          } else {
                            $eventDateText[$j] ='';
                          }
                          if (isset($simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet[$j]->event->eventDate->date->earliestDate))
                            {
                            $eventEarliestDate[$j] = $simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet[$j]->event->eventDate->date->earliestDate;
                            $log.="Event Earliest Date (D-3.6.2): ".$simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet[$j]->event->eventDate->date->earliestDate."\r\n";
                          } else {
                            $eventEarliestDate[$j] = '';
                          }
                          if (isset($simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet[$j]->event->eventDate->date->latestDate))
                            {
                            $eventLatestDate[$j] = $simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet[$j]->event->eventDate->date->latestDate;
                            $log.="Event Latest Date (D-3.6.3): ".$simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet[$j]->event->eventDate->date->latestDate."\r\n";
                          } else {
                            $eventLatestDate[$j] = '';
                          }
                           
                          // PERIOD NAME
                          if (isset($simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet[$j]->event->periodName->term))
                            {
                            $eventPeriodName[$j] = $simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet[$j]->event->periodName->term;
                            $log.="<strong>Period Name (D-3.6.4): ".$simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet[$j]->event->periodName->term."\r\n";
                          } else {
                            $eventPeriodName[$j] = '';
                          }

                          // EVENT PLACE  
                          if (isset($simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet[$j]->event->eventPlace->displayPlace))
                            {
                            $eventPlaceText[$j] = $simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet[$j]->event->eventPlace->displayPlace;
                            $log.="Event Place Free Text (D-3.7.1): ".$simpleXmlNode->lidoWrap->lido->descriptiveMetadata->eventWrap->eventSet[$j]->event->eventPlace->displayPlace."\r\n";
                          } else {
                            $eventPlaceText[$j] = '';
                          }
                            
                    $j++;
                }

        } else {
            $log.="There are no events"."\r\n";
        }

        // DECORATIVE ELEMENTS
        $decorativeElementsString = '';
        if(isset($simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectRelationWrap->subjectWrap->subjectSet[0]->displaySubject))
        {  
                $z=0;
                foreach( $simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectRelationWrap->subjectWrap->subjectSet as $element )    
                {
                   if(isset($simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectRelationWrap->subjectWrap->subjectSet[$z]->displaySubject)) {
                      $decorativeElementsString .= trim($simpleXmlNode->lidoWrap->lido->descriptiveMetadata->objectRelationWrap->subjectWrap->subjectSet[$z]->displaySubject).'|';
                    } else {
                      $decorativeElementsString = '';
                    }  
                    $z++;
                }  
              $decorativeElementsString = rtrim($decorativeElementsString, "|"); 
              $log.="Decorative elements string for dbase: </strong>".$decorativeElementsString."\r\n";
        }

        // ADMIN DATA RECORDINFO
        $instrument['legalBodyID'] = '';
        if(isset($simpleXmlNode->lidoWrap->lido->administrativeMetadata->resourceWrap)) {
            $log.="administrativeMetadata:\r\n";
                $w=0;
                foreach( $simpleXmlNode->lidoWrap->lido->administrativeMetadata->recordWrap as $element )
                {
                      $log.="recordID: ".$simpleXmlNode->lidoWrap->lido->administrativeMetadata->recordWrap->recordID."\r\n";
                      $log.="recordType: ".$simpleXmlNode->lidoWrap->lido->administrativeMetadata->recordWrap->recordType->term."\r\n";
                      $log.="recordSource: ".$simpleXmlNode->lidoWrap->lido->administrativeMetadata->recordWrap->recordSource->legalBodyID."\r\n";
                      $instrument['legalBodyID'] = $simpleXmlNode->lidoWrap->lido->administrativeMetadata->recordWrap->recordSource->legalBodyID;
                    $w++;
                }
        } else {
            $log.="There is no administrativeMetadata.";   
        }

        // DEAL WITH INSTRUMENT RIGHTS
        $instrumentRightsType[0] ='';
        if(isset($simpleXmlNode->lidoWrap->lido->administrativeMetadata->recordWrap->recordRights->rightsType->term)) {
            $log.="rights:\r\n";
                $w=0;
                foreach( $simpleXmlNode->lidoWrap->lido->administrativeMetadata->recordWrap->recordRights as $element )
                {
                        // get rightsType if present
                          if(isset($simpleXmlNode->lidoWrap->lido->administrativeMetadata->recordWrap->recordRights[$w]->rightsType->term)) {
                              $instrumentRightsType[$w] = trim($simpleXmlNode->lidoWrap->lido->administrativeMetadata->recordWrap->recordRights[$w]->rightsType->term);
                          } else {
                              $instrumentRightsType[$w] = '';
                          }
                        // get rights earliest date for this instrument, if present    
                          if(isset($simpleXmlNode->lidoWrap->lido->administrativeMetadata->recordWrap->recordRights[$w]->rightsDate->earliestDate)) {
                            $instrumentRightsEarliestDate[$w] = trim($simpleXmlNode->lidoWrap->lido->administrativeMetadata->recordWrap->recordRights[$w]->rightsDate->earliestDate);
                          } else {
                            $instrumentRightsEarliestDate[$w] ='';
                          }
                        // get rights latest date for this instrument, if present    
                          if(isset($simpleXmlNode->lidoWrap->lido->administrativeMetadata->recordWrap->recordRights[$w]->rightsDate->earliestDate)) {
                            $instrumentRightsLatestDate[$w] = trim($simpleXmlNode->lidoWrap->lido->administrativeMetadata->recordWrap->recordRights[$w]->rightsDate->latestDate);
                          } else {
                            $instrumentRightsLatestDate[$w] ='';
                          }
                        // get rights legalbodyID for this instrument, if present    
                          if(isset($simpleXmlNode->lidoWrap->lido->administrativeMetadata->recordWrap->recordRights[$w]->rightsHolder->legalBodyID)) {
                            $instrumentRightsLegalBodyID[$w] = trim($simpleXmlNode->lidoWrap->lido->administrativeMetadata->recordWrap->recordRights[$w]->rightsHolder->legalBodyID);
                          } else {
                            $instrumentRightsLegalBodyID[$w] ='';
                          }
                        // get rights legalBodyName for this instrument, if present    
                          if(isset($simpleXmlNode->lidoWrap->lido->administrativeMetadata->recordWrap->recordRights[$w]->rightsHolder->legalBodyName->appellationValue)) {
                            $instrumentRightsLegalBodyName[$w] = trim($simpleXmlNode->lidoWrap->lido->administrativeMetadata->recordWrap->recordRights[$w]->rightsHolder->legalBodyName->appellationValue);
                          } else {
                            $instrumentRightsLegalBodyName[$w] ='';
                          }
                        // get rights legalBody Web Link for this instrument, if present    
                          if(isset($simpleXmlNode->lidoWrap->lido->administrativeMetadata->recordWrap->recordRights[$w]->rightsHolder->legalBodyWeblink)) {
                            $instrumentRightsLegalBodyWeblink[$w] = trim($simpleXmlNode->lidoWrap->lido->administrativeMetadata->recordWrap->recordRights[$w]->rightsHolder->legalBodyWeblink);
                          } else {
                            $instrumentRightsLegalBodyWeblink[$w] ='';
                          }
                        // get rights creditLine for this instrument, if present    
                          if(isset($simpleXmlNode->lidoWrap->lido->administrativeMetadata->recordWrap->recordRights[$w]->creditLine)) {
                            $instrumentRightsCreditLine[$w] = trim($simpleXmlNode->lidoWrap->lido->administrativeMetadata->recordWrap->recordRights[$w]->creditLine);
                          } else {
                            $instrumentRightsCreditLine[$w] ='';
                          }
                    $w++;
                }
        } else {
            $log.="There are no instrument rights information.";   
        }





















if ($importme == 1) {

   // the instrument is there ready to import




  // if import type === ETC ETC
  if($import_type_import == "overwrite_existing")
  {  
    // import is set to overwrite all
    //echo $import_type_import;

       $importinc=0;
       while($importinc < sizeof($inventoryNumber))
       {
         
        // check for this repository in dbase
        $repositoryCheck = \DB::table('repositories')
        ->where('legalBodyID', '=', $legalBodyID_import)          
        ->where('inventoryNumber', '=', $inventoryNumber[$importinc])  
        ->where('repositoryName', '=', $inventoriesBodyName[$importinc])         
        ->take(1)->get();

          if (sizeof($repositoryCheck) > 0)
          {
             // we need to delete this instrument from MINIM before it is imported again fresh 
               $this_instrumentID = $repositoryCheck[0]->instrumentID;  

                   // Fetch instrument data
                       $this_instrument = \DB::table('instruments')
                      ->where('instrumentID', '=', $this_instrumentID)  
                      ->take(1)->get();

                if (sizeof($this_instrument) > 0) 
                {
                       $legalBodyID = $this_instrument[0]->legalBodyID;
                       $titlePreferred = $this_instrument[0]->titlePreferred;

                        // Fetch legal body data
                         $legalbody = \DB::table('legalbodies')
                        ->where('legalBodyID', '=', $legalBodyID)  
                        ->take(1)->get();

                        $legalBodyMDAcode = $legalbody[0]->legalBodyMDAcode;  
                        $legalBodyName = $legalbody[0]->legalBodyName;  

                      // delete production event actors
                      \DB::table('eventactors')->where('instrumentID', '=', $this_instrumentID)->delete();
                     
                      // delete the actors for other instrument events
                       $this_events = \DB::table('events')
                      ->where('instrumentID', '=', $this_instrumentID)  
                      ->get();

                         if (sizeof($this_events) > 0) {
                            foreach ($this_events as $event)
                            {
                               // delete the actors for this event
                                \DB::table('eventactors')->where('eventID', '=', $event->eventID)->delete();
                            }
                         }   

                      // now delete the other events      
                      \DB::table('events')->where('instrumentID', '=', $this_instrumentID)->delete();

                      // delete repositories
                      \DB::table('repositories')->where('instrumentID', '=', $this_instrumentID)->delete();

                      // delete descriptions
                      \DB::table('descriptions')->where('instrumentID', '=', $this_instrumentID)->delete();

                      // delete measurements
                      \DB::table('measurements')->where('instrumentID', '=', $this_instrumentID)->delete();

                      // delete any rights info for this instrument event actors
                      \DB::table('rights')->where('instrumentID', '=', $this_instrumentID)->delete();

                      // iterate through instrument's resources and delete them
                       $this_resources = \DB::table('resources')
                      ->where('instrumentID', '=', $this_instrumentID)  
                      ->get();

                            foreach ($this_resources as $resource)
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
                      \DB::table('resources')->where('instrumentID', '=', $this_instrumentID)->delete();

                      // delete the actual instrument
                      \DB::table('instruments')->where('instrumentID', '=', $this_instrumentID)->delete();
            
                      // delete user activity belonging to this instrument
                       \DB::table('user_activity')->where('instrumentID', '=', $this_instrumentID)->delete();

                      // delete all rights information for this instrument, if present (will delete instrument and resource rights for this instrument)
                      \DB::table('rights')->where('instrumentID', '=', $this_instrumentID)->delete();

                 } // end if instrument exists     

          }  // end if match
         
         $importinc++;
       } //




    $import_instrument=1; 
    $imported++; 


  } elseif ($import_type_import == "non_destructive")
  {
    // import is set to non-destructive
    // the import must skip this instrument is it exists in the database...
   // echo $import_type_import;
    // check for instrument
       //$inventoriesBodyName[$j] 
       //$inventoryNumber[$j]

       $importinc=0;
       while($importinc < sizeof($inventoryNumber))
       {
         
        // check for this repository in dbase
        $repositoryCheck = \DB::table('repositories')
        ->where('legalBodyID', '=', $legalBodyID_import)          
        ->where('inventoryNumber', '=', $inventoryNumber[$importinc])  
        ->where('repositoryName', '=', $inventoriesBodyName[$importinc])         
        ->take(1)->get();

          if (sizeof($repositoryCheck) > 0)
          {
           // echo "DO NOT IMPORT - EXISTS IN DBASE".sizeof($repositoryCheck)."<br/";
            $import_instrument=0;

          } else {

        //    echo "IMPORT - NOT IN DBASE".sizeof($repositoryCheck)."<br/";
            $import_instrument=1;
            $imported++; 
            $non_destructive_import_counter++;
          } 

         $importinc++;
       } 




  } elseif ($import_type_import == "overwrite_all")
  {
    // import is set to overwrite existing
    //echo $import_type_import;
    $import_instrument=1;
    $imported++; 
  }



  if($import_instrument == 1)
  {

      // pull resources in for this instrument
          $resourceType = '';
          $resourceFileName = '';
          $thumbGenerated = 'no';
          $p=0;
          if(isset($simpleXmlNode->lidoWrap->lido->administrativeMetadata->resourceWrap->resourceSet[0]->resourceID)) {
              $log.="There are ".$simpleXmlNode->lidoWrap->lido->administrativeMetadata->resourceWrap->resourceSet->count()." resources."."\r\n";
                  foreach( $simpleXmlNode->lidoWrap->lido->administrativeMetadata->resourceWrap->resourceSet as $element )
                  {
                      $log.="Type (A-3.1.3): ".strtolower($simpleXmlNode->lidoWrap->lido->administrativeMetadata->resourceWrap->resourceSet[$p]->resourceType->term)."\r\n";
                      $log.="resourceFileName (A-3.1.2): ".$simpleXmlNode->lidoWrap->lido->administrativeMetadata->resourceWrap->resourceSet[$p]->resourceID."\r\n";
                      $resourceType[$p] = trim(strtolower($simpleXmlNode->lidoWrap->lido->administrativeMetadata->resourceWrap->resourceSet[$p]->resourceType->term));
                      $resourceFileName[$p] = $simpleXmlNode->lidoWrap->lido->administrativeMetadata->resourceWrap->resourceSet[$p]->resourceID;

                      // get resource caption, if present
                        if(isset($simpleXmlNode->lidoWrap->lido->administrativeMetadata->resourceWrap->resourceSet[$p]->resourceDescription)) {
                          $resourceCaption[$p] = trim($simpleXmlNode->lidoWrap->lido->administrativeMetadata->resourceWrap->resourceSet[$p]->resourceDescription);
                        } else {
                          $resourceCaption[$p] = '';
                        }  



                      $rightsInc=0;  
                      $rightsType = '';
                      if(isset($simpleXmlNode->lidoWrap->lido->administrativeMetadata->resourceWrap->resourceSet[$p]->rightsResource->rightsType->term)) { 
                      // there is at least 1 set of resource rights  
                      $log.= "This resource has ".$simpleXmlNode->lidoWrap->lido->administrativeMetadata->resourceWrap->resourceSet[$p]->rightsResource->count()." sets of rights information."."\r\n";

                      // iterate rights for this resource
                         foreach( $simpleXmlNode->lidoWrap->lido->administrativeMetadata->resourceWrap->resourceSet[$p]->rightsResource as $element )
                         { 

                              // get rights type for this resource, if present    
                                if(isset($simpleXmlNode->lidoWrap->lido->administrativeMetadata->resourceWrap->resourceSet[$p]->rightsResource[$rightsInc]->rightsType->term)) {
                                  $rightsType[$p][$rightsInc] = trim($simpleXmlNode->lidoWrap->lido->administrativeMetadata->resourceWrap->resourceSet[$p]->rightsResource[$rightsInc]->rightsType->term);
                                  $log.= "rights type rights inc: ".$rightsInc." ".$simpleXmlNode->lidoWrap->lido->administrativeMetadata->resourceWrap->resourceSet[$p]->rightsResource[$rightsInc]->rightsType->term."\r\n";
                                } else {
                                  $rightsType[$p][$rightsInc] ='';
                                }
                              // get rights earliest date for this resource, if present    
                                if(isset($simpleXmlNode->lidoWrap->lido->administrativeMetadata->resourceWrap->resourceSet[$p]->rightsResource[$rightsInc]->rightsDate->earliestDate)) {
                                  $resourceRightsEarliestDate[$p][$rightsInc] = trim($simpleXmlNode->lidoWrap->lido->administrativeMetadata->resourceWrap->resourceSet[$p]->rightsResource[$rightsInc]->rightsDate->earliestDate);
                                } else {
                                  $resourceRightsEarliestDate[$p][$rightsInc] ='';
                                }
                              // get rights latest date for this resource, if present    
                                if(isset($simpleXmlNode->lidoWrap->lido->administrativeMetadata->resourceWrap->resourceSet[$p]->rightsResource[$rightsInc]->rightsDate->latestDate)) {
                                  $resourceRightsLatestDate[$p][$rightsInc] = trim($simpleXmlNode->lidoWrap->lido->administrativeMetadata->resourceWrap->resourceSet[$p]->rightsResource[$rightsInc]->rightsDate->latestDate);
                                } else {
                                  $resourceRightsLatestDate[$p][$rightsInc] ='';
                                }
                              // get rights legalbodyID for this resource, if present    
                                if(isset($simpleXmlNode->lidoWrap->lido->administrativeMetadata->resourceWrap->resourceSet[$p]->rightsResource[$rightsInc]->rightsHolder->legalBodyID)) {
                                  $resourceRightsLegalBodyID[$p][$rightsInc] = trim($simpleXmlNode->lidoWrap->lido->administrativeMetadata->resourceWrap->resourceSet[$p]->rightsResource[$rightsInc]->rightsHolder->legalBodyID);
                                } else {
                                  $resourceRightsLegalBodyID[$p][$rightsInc] ='';
                                }
                              // get rights legalBodyName for this resource, if present    
                                if(isset($simpleXmlNode->lidoWrap->lido->administrativeMetadata->resourceWrap->resourceSet[$p]->rightsResource[$rightsInc]->rightsHolder->legalBodyName->appellationValue)) {
                                  $resourceRightsLegalBodyName[$p][$rightsInc] = trim($simpleXmlNode->lidoWrap->lido->administrativeMetadata->resourceWrap->resourceSet[$p]->rightsResource[$rightsInc]->rightsHolder->legalBodyName->appellationValue);
                                } else {
                                  $resourceRightsLegalBodyName[$p][$rightsInc] ='';
                                }
                              // get rights legalBody Web Link for this resource, if present    
                                if(isset($simpleXmlNode->lidoWrap->lido->administrativeMetadata->resourceWrap->resourceSet[$p]->rightsResource[$rightsInc]->rightsHolder->legalBodyWeblink)) {
                                  $resourceRightsLegalBodyWeblink[$p][$rightsInc] = trim($simpleXmlNode->lidoWrap->lido->administrativeMetadata->resourceWrap->resourceSet[$p]->rightsResource[$rightsInc]->rightsHolder->legalBodyWeblink);
                                } else {
                                  $resourceRightsLegalBodyWeblink[$p][$rightsInc] ='';
                                }
                              // get rights creditLine for this resource, if present    
                                if(isset($simpleXmlNode->lidoWrap->lido->administrativeMetadata->resourceWrap->resourceSet[$p]->rightsResource[$rightsInc]->creditLine)) {
                                  $resourceRightsCreditLine[$p][$rightsInc] = trim($simpleXmlNode->lidoWrap->lido->administrativeMetadata->resourceWrap->resourceSet[$p]->rightsResource[$rightsInc]->creditLine);
                                } else {
                                  $resourceRightsCreditLine[$p][$rightsInc] ='';
                                }

                                $rightsInc++;

                       } // end for each iterating resource rights         

                     } // end if resource has at least 1 set of rights info   


                      // now get info for this resource
                        if(isset($simpleXmlNode->lidoWrap->lido->administrativeMetadata->resourceWrap->resourceSet[$p]->resourceRepresentation->linkResource)) {
                              $log.="resourceCollectionLink (A-3.1.1): ".$simpleXmlNode->lidoWrap->lido->administrativeMetadata->resourceWrap->resourceSet[$p]->resourceRepresentation->linkResource."\r\n";
                              $resourceCollectionLink[$p] = trim($simpleXmlNode->lidoWrap->lido->administrativeMetadata->resourceWrap->resourceSet[$p]->resourceRepresentation->linkResource);
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

                          } else { // no remote path for resource
         
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

                              $resourceCollectionLink[$p] = "";
                              if ($thumbGenerated != 'yes')
                               {
                                // thumb generation set to yes in all cirmstances except in the case where
                                // the resource is an image but thub not generated... ie where the resourcelink
                                // is not specified... for RCM and others we may put the main images in the 'raw_local' folder
                                // where the resource collection link is not specified. In this case, this check here will look for the image
                                // in the 'raw_local' folder and create a thumbnail
                                              // create instance
                                              $img = \Image::make($raw_local_path.'/'.$resourceFileName[$p]);
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


                                             // and now copy the image from raw_local
                                              $img = \Image::make($raw_local_path.'/'.$resourceFileName[$p]);
                                              $img->save($imagepath.'/'.$resourceFileName[$p]);

                               }   
                          }   // end if link to external resource
                      $p++;
                  }
          } else {
              $log.="There are no resources.";
              $resourceType[$p] = "";
              $resourceFileName[$p] = "";
              $resourceCollectionLink[$p] = "";
          }
      // end dealing with resources  






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
               $instrument_tags = '';  // this instrument is not found in the thesaurus
           } 

          if (strlen(trim($instrument['title_preferred_actual'])) > 0) {
              $instrumentName = $instrument['title_preferred_actual']; // this is the actual value if set for specific instrument name
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
             ['legalBodyID' => $legalbody_id_session, 'adminID' => $adminID_session, 'creationType' => 'import', 'status' => 'imported', 'thesaurusID' => $thesaurusID, 'hornbostelID' => $hornbostelID, 'Level_0' => $Level_0, 'Level_1' => $Level_1, 'Level_2' => $Level_2, 'Level_3' => $Level_3, 'tags' => $instrument_tags, 'hornbostelCat' => $instrument['hornsbostel'], 'decorativeElements' => $decorativeElementsString, 'titlePreferred' => $instrumentName, 'inscriptions' => $inscriptionString, 'serialEditionNumbers' => $serialString, 'measurementsFreeText' => $instrument['measurementsFreeText'], 'created_at' => \Carbon\Carbon::now()]
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
    
        // ADD RIGHTS FOR THIS INSTRUMENT  
        $x=0;
        while ($x < sizeof($instrumentRightsType))
        {
              if (strlen($instrumentRightsType[$x]) > 0)
              {  
                          // add rights
                          \DB::table('rights')->insert(
                              ['instrumentID' => $instrumentID, 'legalBodyID' => $legalbody_id_session, 'rightsFlag' => 'instrument', 'rightsType' => $instrumentRightsType[$x], 'rightsEarliestDate' => $instrumentRightsEarliestDate[$x], 'rightsLatestDate' => $instrumentRightsLatestDate[$x], 'rightsHolderID' => $instrumentRightsLegalBodyID[$x], 'rightsHolderName' => $instrumentRightsLegalBodyName[$x], 'rightsHolderWebsite' => $instrumentRightsLegalBodyWeblink[$x], 'rightsCreditLine' => $instrumentRightsCreditLine[$x]   ]
                          );
              }  
              $x++; 
        }  

        // ADD DESCRIPTIONS   
        if (sizeof($descriptionType) > 1)
        {    
          // more than one description possible, need to iterate them and make sure we use a general description, if available, as the main description stored with instrument 
          // $labelled_general_description set to 1 if general description discovered
            $x=0;
            while ($x < sizeof($descriptionType))
            {
                    if($labelled_general_description == 1) // then we have a general description, iterate and add general as main
                    {    
                        if (strtolower($descriptionType[$x]) == "general description")
                        {  
                            // add as main description in instrument table
                            \DB::table('instruments')
                             ->where('instrumentID', $instrumentID)
                             ->update(array('mainDescriptionType' => $descriptionType[$x], 'mainDescriptionText' => $descriptionText[$x], 'mainDescriptionSource' => $descriptionTextSource[$x]));
                        } else {
                             // other description(s) in descriptions table
                              \DB::table('descriptions')->insert(
                                  ['instrumentID' => $instrumentID, 'legalBodyID' => $legalbody_id_session, 'descriptionType' => $descriptionType[$x], 'descriptionText' => $descriptionText[$x], 'descriptionTextSource' => $descriptionTextSource[$x]]
                              );
                        } 
                    } else { // multiple descriptions, none labelled as general description, we'll just use the first description as the main one
                        if ($x == 0)
                        {  
                            // add as main description in instrument table
                            \DB::table('instruments')
                             ->where('instrumentID', $instrumentID)
                             ->update(array('mainDescriptionType' => $descriptionType[$x], 'mainDescriptionText' => $descriptionText[$x], 'mainDescriptionSource' => $descriptionTextSource[$x]));
                        } else {
                             // other description(s) in descriptions table
                              \DB::table('descriptions')->insert(
                                  ['instrumentID' => $instrumentID, 'legalBodyID' => $legalbody_id_session, 'descriptionType' => $descriptionType[$x], 'descriptionText' => $descriptionText[$x], 'descriptionTextSource' => $descriptionTextSource[$x]]
                              );
                        } 
                    }         
                $x++;
            }
        } else { // there is not more than one description, add the only description as the main description
                        \DB::table('instruments')
                         ->where('instrumentID', $instrumentID)
                         ->update(array('mainDescriptionType' => $descriptionType[0], 'mainDescriptionText' => $descriptionText[0]));
        }      

        // ADD EVENTS AND ACTORS
        $x=0;
        while ($x < sizeof($eventType))
        {
           if (strtolower($eventType[$x]) == "production") {
              // If the event is a production event, then we store it in the instruments table - AND UPDATE instrument rather than INSERT
                  \DB::table('instruments')
                   ->where('instrumentID', $instrumentID)
                   ->update(array('productionEventLocation' => $eventPlaceText[$x], 'productionEventName' => $eventName[$x], 'productionEventDateText' => $eventDateText[$x], 'productionEventEarliestDate' => $eventEarliestDate[$x], 'productionEventLatestDate' => $eventLatestDate[$x], 'productionMaterialsFreeText' => $materialsText[$x], 'productionMaterials' => $materialsString[$x] ));
                      $actinc=0; // add production event actors
                      if (isset($eventActorName[$x]))
                      {  
                          while ($actinc < sizeof($eventActorName[$x]))
                          {
                                  echo sizeof($eventActorName[$x])."actor for proodcution in ins no event id ".$actinc.$eventActorName[$x][$actinc]."<br/>";
                                  if ($eventActorName[$x][$actinc] != "") {       // exclude blank actors
                                          \DB::table('eventactors')->insert(
                                              ['instrumentID' => $instrumentID, 'legalBodyID' => $legalbody_id_session, 'eventActorName' => $eventActorName[$x][$actinc], 'eventActorRole' => $eventActorRole[$x][$actinc], 'eventDisplayActorRole' => $displayActorInRole[$x][$actinc]]
                                          );
                                   }
                              $actinc++;
                          }
                      } // end if actor name set    
           } else {         // If the event is NOT a production event, we store it in the events table..
                  
                  if(isset($eventType[$x]))
                  {  
                      $eventID = \DB::table('events')->insertGetId(
                          ['instrumentID' => $instrumentID, 'legalBodyID' => $legalbody_id_session, 'location' => $eventPlaceText[$x], 'eventType' => $eventType[$x], 'eventName' => $eventName[$x], 'eventDateText' => $eventDateText[$x], 'eventEarliestDate' => $eventEarliestDate[$x], 'eventLatestDate' => $eventLatestDate[$x], 'materialsText' => $materialsText[$x], 'materials' => $materialsString[$x]]
                      );

                      $actinc=0;  // add non production event actors 
                      while ($actinc < sizeof($eventActorName[$x]))
                      {
                              if ($eventActorName[$x][$actinc] != "") {       // exclude blank actors
                                      \DB::table('eventactors')->insert(
                                          ['eventID' => $eventID, 'legalBodyID' => $legalbody_id_session, 'eventActorName' => $eventActorName[$x][$actinc], 'eventActorRole' => $eventActorRole[$x][$actinc], 'eventDisplayActorRole' => $displayActorInRole[$x][$actinc]]
                                      );
                               }
                          $actinc++;
                      }
                    }  // end if event type set  
                  } // end if the event is NOT a production event..    
              $x++;
          }

          // ADD RESOURCES
          $x=0;
          while ($x < sizeof($resourceType))
          {
              // add resource
              $resourceID = \DB::table('resources')->insertGetId(
                  ['instrumentID' => $instrumentID, 'legalBodyID' => $legalbody_id_session, 'resourceStatus' => 'imported', 'resourceType' => strtolower($resourceType[$x]), 'resourceFileName' => $resourceFileName[$x], 'resourceCollectionLink' => $resourceCollectionLink[$x], 'resourceCaption' => $resourceCaption[$x]]
              );
                  

                $rightsInc = 0;
                while ($rightsInc < sizeof($rightsType[$x])) 
                {  
                      if (isset($rightsType[$x][$rightsInc]) > 0)
                      { 
                           // add rights for this resource if any   
                              \DB::table('rights')->insert(
                                  ['instrumentID' => $instrumentID, 'resourceID' => $resourceID, 'legalBodyID' => $legalbody_id_session, 'rightsFlag' => $resourceType[$x], 'rightsType' => $rightsType[$x][$rightsInc], 'rightsEarliestDate' => $resourceRightsEarliestDate[$x][$rightsInc], 'rightsLatestDate' => $resourceRightsLatestDate[$x][$rightsInc], 'rightsHolderID' => $resourceRightsLegalBodyID[$x][$rightsInc], 'rightsHolderName' => $resourceRightsLegalBodyName[$x][$rightsInc], 'rightsHolderWebsite' => $resourceRightsLegalBodyWeblink[$x][$rightsInc], 'rightsCreditLine' => $resourceRightsCreditLine[$x][$rightsInc] ]
                              );
                      } // end if rights info present for this resource

                  $rightsInc++;

               } // end iterating resource rights      

              $x++;


          } // end iterate resources


  }// end if $import_instrument == 1





} // end dbase import...





// Bail Out After X Number Of Instruments For Testing...
//if ($counter == 10) { break; }





              //echo "<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>";
              $log.="\r\n\r\n\r\n";  







              $counter++;

        } // end looping through nodes

} catch( ConnectException $ex ) {

  /*
  errors we're dealing with
  cURL error 56: Recv failure: Connection was reset (see http://curl.haxx.se/libcurl/c/libcurl-errors.html)
  cURL error 6: Could not resolve host: www.horniman.ac.uk (see http://curl.haxx.se/libcurl/c/libcurl-errors.html)
   */

    switch ( $ex->getMessage() ) {
        case '6': // to be verified (CURL ERROR 6 - Could not resolve host)

                  // HANDLE ERROR HERE
                      echo "CURL ERROR 6: Could not resolve host.";
                
        break; 

        case '7': // to be verified (CURL ERROR 7 - losing connection to remote source.)
                  // This can happen when remote source of resource is not available...
                  // handle by adding error to dbase to be shown on frontend and breaking out of import.
                  // User will have to try again laer.
                  // 
                  // http://stackoverflow.com/questions/29617480/how-to-handle-fatal-error-curl-error-7-failed-to-connect-to-xxxx-port-443
                  // HANDLE ERROR HERE
                      echo "CURL ERROR 7: Lost connection to remote source.";

        break;

        case '56': // to be verified (CURL ERROR 56 - connection was reset)

                  // HANDLE ERROR HERE 
                      echo "CURL ERROR 56: Connection was reset.";
                
        break;
    }
} // end dealing with curl errors


    $counter--;
    $time_end = microtime(true);
    $execution_time = round(($time_end - $time_start)/60,2);

    if ($import_type_import == "non_destructive") 
    {  
      $records_ignorded_as_already_exist = $counter - $non_destructive_import_counter;
      $log.="Total Records: ".$counter."\r\n\r\nTotal Imported:".$non_destructive_import_counter."\r\n"."\r\nTotal Ignored As Already In MINIM-UK: \n".$records_ignorded_as_already_exist."\r\n";
      $short_log ="<p style='font-size:16px;'>Total Records: ".$counter."<br/>Total Ignored As Already In MINIM-UK: ".$records_ignorded_as_already_exist."<br/>Total Imported: <span style='color:green;'>".$imported."</span><br/><br/>Total Execution Time: ".$execution_time." minutes</p>";
    } else {
      $log.="Total Records: ".$counter."\r\nTotal Imported: ".$imported."\r\n";      
      $short_log ="<p style='font-size:16px;'>Total Records: ".$counter."<br/>Total Imported: <span style='color:green;'>".$imported."</span><br/>Total Execution Time: <span style='color:green;'>".$execution_time."</span> minutes</p>";
    }

    $log.="Total Execution Time: ".$execution_time." minutes";


    // update current import job with completion
    \DB::table('import_jobs')
     ->where('id', $import_jobID)
     ->update(array('currentStatus' => 'completed', 'log' => $log, 'short_log' => $short_log, 'time_ended' => \Carbon\Carbon::now() ));

    // WRITE LOG TXT FILE
   // $file = public_path().'/instrument_resources/logs/'.$legalbody_session.'/log'.date('Y_m_d_H_i_s').'.txt';
      $file = public_path().'/instrument_resources/logs/'.$import_jobID.'.txt';
   
   // Add File To Server
     \File::put($file, $log);

    
     if($import_type_import == "overwrite_all")
     {
        if($imported > 0)
        {  
            // ADD IMPORT ACTIVITY TO ACTIVITY TABLE
            \DB::table('user_activity')->insert(
                ['userID' => $adminID_session, 'legalBodyID' => $legalbody_id_session, 'legalBodyName' => $legalbody_name_session, 'type' => 'admin_import', 'activity' => "imported ".$imported." instruments", 'activityDate' => \Carbon\Carbon::now() ]
             );
        }    

     } elseif($import_type_import == "non_destructive") {
     
        if($non_destructive_import_counter > 0)
        {  

          // ADD IMPORT ACTIVITY TO ACTIVITY TABLE
          \DB::table('user_activity')->insert(
              ['userID' => $adminID_session, 'legalBodyID' => $legalbody_id_session, 'legalBodyName' => $legalbody_name_session, 'type' => 'admin_import', 'activity' => "imported ".$non_destructive_import_counter." instruments", 'activityDate' => \Carbon\Carbon::now() ]
           );

       }   


     } elseif($import_type_import == "overwrite_existing") {

        if($imported > 0)
        {  
            // ADD IMPORT ACTIVITY TO ACTIVITY TABLE
            \DB::table('user_activity')->insert(
                ['userID' => $adminID_session, 'legalBodyID' => $legalbody_id_session, 'legalBodyName' => $legalbody_name_session, 'type' => 'admin_import', 'activity' => "imported ".$imported." instruments", 'activityDate' => \Carbon\Carbon::now() ]
             );
        }  

     }  

    // echo short import log
    echo $log;
}   // end import










































   public function importlisten($lastImportIDForm)
   {
        $lastImportIDForm = (int) $lastImportIDForm; // force int
        $sessiondata = session()->all();
        $role = \Session::get('role');
        $user_id = Auth::user()->getId();

        // get import variables from session
        $legalBodyID_import = \Session::get('legalBodyID_import');
        $legalBodyName_import = \Session::get('legalBodyName_import');
        $import_type_import = \Session::get('import_type_import');
        $MDA_code_import = \Session::get('MDA_code_import');
        $importXMLfile = \Session::get('importXMLfile');

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

              echo "<center>";
                   // import is in progress   
                   echo '<span style="color:green; font-size:100px; line-height:90px;">'.$percent.'%</span><br/>';
                   echo '<strong>STATUS:&nbsp;</strong>'.$current_status."<br/>";
              echo "</center>";    

            } else {
                // import complete
                if ($import_type_import == "non_destructive")
                {
                  $display_import = "Non-Destructive Import Complete";
                } elseif ($import_type_import == "overwrite_existing")
                {
                  $display_import = "Import To Overwrite Existing Records Complete";
                } elseif ($import_type_import == "overwrite_all")
                {
                  $display_import = "Import To Delete All Instruments In Collection And Import From XML Complete";
                } 

                // get most recent import from logged in user..
                $lastImport = \DB::table('import_jobs')
                ->where('userID', '=', $user_id)          
                ->orderBy('id', 'desc')         
                ->take(1)->get();

                $this_import_id = $lastImport[0]->id; 


                $short_log = $importquery[0]->short_log;             
                echo "<center><h2>".$display_import."</h2>";

                $file = '/instrument_resources/logs/'.$this_import_id.'.txt';

                echo $short_log."<br/>";
                echo "<a href='/dashboard'>Go to dashboard</a> | ";
          //      echo "Go to ".$legalBodyName_import."'s <a href='instruments/".$legalBodyID_import."'>Instruments</a>. | <a href='import-log/".$this_import_id."'>View Full Import Log</a>.";
                echo "Go to ".$legalBodyName_import."'s <a href='/instruments/".$legalBodyID_import."'>Instruments</a>. | <a href='".$file."' target='_blank'>View Full Import Log</a>.";



                //echo "<br/><br/>legalBodyID_import: ".$legalBodyID_import."<br/>";
                //echo "legalBodyName_import: ".$legalBodyName_import."<br/>";
               // echo "import_type_import: ".$import_type_import."<br/>";
                //echo "MDA_code_import: ".$MDA_code_import."<br/>";
                //echo "importXMLfile: ".$importXMLfile."<br/>";
                echo "</center>";
            
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