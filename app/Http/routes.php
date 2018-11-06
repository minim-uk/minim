<?php

//DASHBOARD
Route::get('/dashboard', 'AdminController@index');
Route::get('/', 'AdminController@index');


// ADD INSTRUMENT
Route::get('/addinstrument', 'AdminController@addinstrument');
Route::get('/addinstrument_two', 'AdminController@addinstrument_two');
Route::get('/addinstrument/collection/{legalBodyID}', ['uses' =>'AdminController@addinstrument']);
Route::post('/addinstrument/store', 'AdminController@storeinstrument');
Route::get('/addinstrument_two/store', 'AdminController@storeinstrument_two');
Route::get('/addinstrument/store', function () {
    return redirect('addinstrument'); 
});
Route::get('/addinstrument_two/store', function () {
    return redirect('addinstrument'); 
});


// ACCOUNT SETTINGS
Route::get('/account-settings', 'AccountSettings@accountsettings');
Route::post('/account-settings/store', 'AccountSettings@storeaccountsettings');
Route::get('/account-settings/store', function () {
    return redirect('account-settings');
});
Route::get('/change-password', 'AccountSettings@changepassword');
Route::post('/change-password/store', 'AccountSettings@storechangepassword');
Route::get('/change-password/store', function () {
    return redirect('change-password');
});


// LEGAL BODY PROFILE
Route::get('/legalbody-profile', 'AdminController@legalbodyprofile');
Route::post('/legalbody-profile/store', 'AdminController@storelegalbodyprofile');
Route::get('/legalbody-profile/store', function () {
    return redirect('legalbody-profile');
});


// USERS
Route::get('/add-user', 'Users@adduser');
Route::post('/add-user/store', 'Users@storeuser');
Route::post('/edit-user/store', 'Users@storeuser');
Route::get('/existing-users', 'EloquentController@existingusers');
Route::get('existing-users/delete/{id}', ['uses' =>'Users@deleteuser']);


// ADD COLLECTION
Route::get('/add-collection', 'Collections@addcollection');
Route::post('/add-collection/store', 'Collections@storecollection');


// EXISTING COLLECTIONS
Route::get('/existing-collections', 'EloquentController@existingcollections');


// EDIT EXISTING COLLECTION
Route::get('existing-collections/edit/{legalBodyID}', ['uses' =>'Collections@editcollection']);
Route::get('existing-collections/delete/{legalBodyID}', ['uses' =>'AdminController@deletecollection']);
Route::post('existing-collections/edit/store', ['uses' =>'Collections@storeeditcollection']);
Route::get('/existing-collections/edit/store', function () {
    return redirect('existing-collections');
});


// MANAGE XML FOR COLLECTION
Route::get('existing-collections/xml/{legalBodyID}', ['uses' =>'Collections@managexml']);
Route::post('existing-collections/xml/store', ['uses' =>'Collections@storexml']);



// EDIT EXISTING USERS
Route::get('existing-users/edit/{id}', ['uses' =>'Users@edituser']);
Route::post('existing-users/edit/store', ['uses' =>'Users@storeeuser']);
Route::get('/existing-users/edit/store', function () {
    return redirect('existing-users');
});



//INSTRUMENT LISTS
Route::get('/instruments', 'AdminController@viewedit');
Route::get('/instruments/{collectionID}', ['uses' =>'AdminController@viewedit']);
Route::controllers([
    'eloquent'   => 'EloquentController',
]);



// IMPORT
Route::get('/import-into-collection', 'AdminController@importintocollection');
Route::get('/import-into-collection/{legalbodyID}', 'AdminController@importintocollection'); // if legalbody id set default to importing this one
Route::post('/import-into-collection-go', 'AdminController@importintocollection_go'); // post only for triggering import
Route::get('/import-into-collection-go', 'AdminController@importintocollection'); // if user refreshes posted import page they either repost, or redirect to import choices
Route::get('/importlisten/{lastImportID}', 'ImportController@importlisten');
Route::get('/import-log/{import_jobID}', 'AdminController@importlog');


//Route::post('/import-into-collection-now', 'AdminController@importintocollectionnow');
Route::post('/import-into-collection-now', 'ImportController@import');
Route::get('/cities-import', 'AdminController@cities_import');


// EDIT / DELETE INSTRUMENT
Route::get('edit-instrument/{instrumentID}', ['uses' =>'AdminController@editinstrument']);
Route::post('/edit-instrument/store', 'AdminController@storeinstrumentedited');
Route::get('delete-instrument/{instrumentID}', ['uses' =>'AdminController@deleteinstrument']);


// INSTRUMENT RIGHTS
Route::get('edit-instrument/{instrumentID}/rights', ['uses' =>'AdminController@instrumentrights']);
Route::post('edit-instrument/storerights', ['uses' =>'AdminController@storeinstrumentrights']);

// RESOURCE RIGHTS
Route::get('edit-instrument/{instrumentID}/resource/{resourceID}/rights', ['uses' =>'AdminController@resourcerights']); 
Route::post('edit-resource/storerights', ['uses' =>'AdminController@storeresourcerights']);

// EDIT/DELETE INSTRUMENT IMAGES
Route::get('edit-instrument/{instrumentID}/images', ['uses' =>'Resources@editimages']);
Route::get('edit-instrument/{instrumentID}/resource/{resourceID}', ['uses' =>'Resources@editresource']);
Route::get('edit-instrument/{instrumentID}/resource/{resourceID}/delete', ['uses' =>'Resources@deleteresource']);
Route::post('edit-resource/store', ['uses' =>'Resources@storeresource']);


// EDIT/DELETE INSTRUMENT VIDEO
Route::get('edit-instrument/{instrumentID}/video', ['uses' =>'Resources@editvideo']);
Route::get('edit-instrument/{instrumentID}/resource/{resourceID}', ['uses' =>'Resources@editresource']);


// EDIT/DELETE INSTRUMENT AUDIO
Route::get('edit-instrument/{instrumentID}/audio', ['uses' =>'Resources@editaudio']);
Route::get('edit-instrument/{instrumentID}/resource/{resourceID}', ['uses' =>'Resources@editresource']);


// ADD NEW RESOURCE
Route::get('edit-instrument/{instrumentID}/add-resource', ['uses' =>'Resources@addresource']);
Route::post('/add-resource/store', ['uses' =>'Resources@insertresource']);


// PREVIEW INSTRUMENT
Route::get('preview-instrument/{instrumentID}', ['uses' =>'AdminController@previewinstrument']);


// EVENTS
Route::get('edit-instrument/{instrumentID}/production-event', ['uses' =>'AdminController@productionevent']);
Route::post('/production-event/store', 'AdminController@store_productionevent');
Route::get('edit-instrument/{instrumentID}/events', ['uses' =>'AdminController@events']);
Route::get('edit-instrument/{instrumentID}/events/{eventID}', ['uses' =>'AdminController@editevent']);
Route::post('/event/store', 'AdminController@store_event');
Route::get('edit-instrument/{instrumentID}/add-event', ['uses' =>'AdminController@addevent']);
Route::post('/add-event/store', ['uses' =>'AdminController@addevent']);
Route::get('/production-event/{instrumentID}', ['uses' =>'AdminController@productionevent']);
Route::get('/delete-event/{eventID}/ins/{instrumentID}', ['uses' =>'AdminController@delete_event']);

// EVENT MATERIALS
Route::get('production-event/{instrumentID}/materials', ['uses' =>'AdminController@eventmaterials']);
Route::get('edit-instrument/{instrumentID}/events/{eventID}/materials', ['uses' =>'AdminController@eventmaterials']);
Route::post('/event-materials/store', 'AdminController@store_eventmaterials');


// PRODUCTION EVENT ACTORS
Route::get('/production-event/{instrumentID}/add-actor', ['uses' =>'AdminController@addactor']);
Route::get('/production-event/{instrumentID}/actor/{eventActorID}', ['uses' =>'AdminController@editproductionactor']);
Route::get('production-event/delete-actor/{actorID}/{eventID}/{instrumentID}', ['uses' =>'AdminController@deleteactor']);


// OTHER EVENT ACTORS
Route::get('events/{eventID}/actors', ['uses' =>'AdminController@eventactors']);
Route::get('events/{eventID}/actor/{actorID}', ['uses' =>'AdminController@editactor']);
Route::get('edit-instrument/{instrumentID}/events/{eventID}/add-actor', ['uses' =>'AdminController@addactor']);
Route::post('/actor/store', ['uses' =>'AdminController@storeactor']);
Route::get('delete-actor/{actorID}/event/{eventID}', ['uses' =>'AdminController@deleteactor']);


// THE AUTH PATHS 
Route::auth();


/*for instrument name autocomplete*/
Route::get('search/autocomplete', 'AdminController@autocomplete');
Route::get('search/rinser', 'EloquentController@rinser');
/*for hornbostel autocomplete*/
Route::get('search/hornbostel_autocomplete', 'AdminController@hornbostel_autocomplete');
/*for hornbostel autocomplete*/
Route::get('search/cities', 'AdminController@cities_autocomplete');

//Route::get('autocompletetest', 'AdminController@autocompletetest');


/* for actor autocomplete */
Route::get('search/actorautocomplete', 'AdminController@actorautocomplete');


// FOR GENERAL TESTS
Route::get('general-tests', 'AdminController@archived_imports');


// FEEDDACK / REPORT PROBLEM
Route::get('report-problem', 'AdminController@reportproblem');
Route::get('report-problem/delete/{reportProblemID}', 'AdminController@deleteproblem');
Route::post('/report-problem/store', 'AdminController@storereportproblem');


// MOVED OR DELETED
Route::get('movedordeleted', 'AdminController@movedordeleted');


// MAIN DELETION ROUTES
Route::post('/delete-now', 'AdminController@delete_now');


// IMPORT LIDO
Route::get('importlido', 'ImportController@import');

// IMPORT HORNSBOSTEL 
Route::get('importhornsbostel', 'ImportController@importhornsbostel');


/*php artisan route:list to show all routes including auth ones */