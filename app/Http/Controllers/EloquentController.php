<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Post;
use App\User;
use App\Instrument;
use App\Legalbodies;
use Datatables;
use Illuminate\Http\Request;
use DB;
use Auth;

class EloquentController extends Controller
{
    public function __construct()
    {

        $this->middleware('auth');
        view()->share('controller', 'EloquentController.php');
        view()->share('title', "title");
        view()->share('description', "description");
    }

    public function getIndex()
    {
        return view('datatables.eloquent.index');
    }

    public function getBasic()
    {
        return view('datatables.eloquent.basic');
    }

    public function getBasicData()
    {
        $users = User::select(['id', 'name', 'email', 'created_at', 'updated_at']);

        return Datatables::of($users)
            ->editColumn('name', '{{ $name."-name" }}')
            ->make();
    }

    public function getMaster()
    {
        return view('datatables.eloquent.master');
    }

    public function getMasterData()
    {
        $users = User::select();

        return Datatables::of($users)
            ->addColumn('details_url', function ($user) {
                return url('eloquent/details-data/' . $user->id);
            })
            ->make(true);
    }

    public function getDetailsData($id)
    {
        $posts = User::find($id)->posts();

        return Datatables::of($posts)->make(true);
    }



    public function existingcollections()
    {

        $page = 'existingcollections';
        $sessiondata = session()->all();

     
        return view('datatables.eloquent.existing-collections')->with([          
           'page' => 'existingcollections',
           'role' => \Session::get('role'),
           'sessiondata' => $sessiondata
        ]);    

    }





    public function existingusers()
    {

        $page = 'existingusers';
        $sessiondata = session()->all();

      // QUICK SECURITY - REDIRECT IF NOT SUPERADMIN 
      $role = \Session::get('role');
      if ($role != "SuperAdmin")
      {
          // REDIRECTTO DASHBOARD
          return \Redirect::action('AdminController@index');
      }

     
        return view('datatables.eloquent.existing-users')->with([          
           'page' => 'existingusers',
           'role' => \Session::get('role'),
           'sessiondata' => $sessiondata
        ]);    

    }














    // showing instruments
    public function getBasicObjectData()
    {


        // if segment 3 has collectionid in url
        $collectionID = \Request::segment(3);

        $role = \Session::get('role');
        $legalBodyID = \Session::get('legalBodyID');

        if ($role == "Admin")
        {    
            // THIS ADMIN USER CAN ONLY SEE THE INSTRUMENTS FROM THEIR OWN LEGAL BODAY
            $instrument = Instrument::query()->where('legalBodyID', '=', $legalBodyID);
            return Datatables::of($instrument)->make(true);               
       
        } else {
       

            if ($collectionID) {

                // SHOW JUST THIS COLLECTION
                $instrument = Instrument::query()->where('legalBodyID', '=', $collectionID);
                return Datatables::of($instrument)->make(true); 

            } else {

                // SHOW ALL INSTRUMENTS FROM ALL COLLECTIONS
               // $instrument = Instrument::query()->where('legalBodyID', '=', 2);
                $instrument = Instrument::query();
                return Datatables::of($instrument)->make(true); 

            }


        }  // end if role is not Admin

        
    } // end showing instruments



    public function getExistingCollectionsData()
    {

        // table joins to get admin user data
        // https://datatables.yajrabox.com/eloquent/joins
       
        $instrument = Legalbodies::query();

        return Datatables::of($instrument)->make(true);







        
    }



    public function getExistingUsersData()
    {

        $user = User::query();

        return Datatables::of($user)->make(true);
        
    }






/*
    public function getBasicObjectData()
    {
        $users = User::query();

        return Datatables::of($users)->make(true);
    }
*/

    public function getBasicColumns()
    {
        return view('datatables.eloquent.basic-columns');
    }

    public function getBasicColumnsData()
    {
        $users = User::select(['id', 'name', 'email', 'created_at', 'updated_at']);

        return Datatables::of($users)->make();
    }

    public function getIoc()
    {
        return view('datatables.eloquent.ioc');
    }

    public function getIocData()
    {
        $users      = User::select(['id', 'name', 'email', 'created_at', 'updated_at']);
        $datatables = app('datatables');

        return $datatables->eloquent($users)->make(true);
    }

    public function getCount()
    {
        return view('datatables.eloquent.count');
    }

    public function getCountData()
    {
        $users = User::select([
                'users.id',
                'users.name',
                'users.email',
                \DB::raw('count(posts.user_id) as count'),
                'users.created_at',
                'users.updated_at'
        ])->leftJoin('posts', 'posts.user_id', '=', 'users.id')
        ->groupBy('users.id');

        return Datatables::of($users)->make(true);
    }

    public function getAdvanceFilter()
    {
        return view('datatables.eloquent.advance-filter');
    }

    public function getAdvanceFilterData()
    {
        $users = User::select([
                DB::raw("CONCAT(users.id,'-',users.id) as id"),
                'users.name',
                'users.email',
                DB::raw('count(posts.user_id) AS count'),
                'users.created_at',
                'users.updated_at'
        ])->leftJoin('posts', 'posts.user_id', '=', 'users.id')
        ->groupBy('users.id');

        $datatables =  app('datatables')->of($users)
            ->filterColumn('users.id', 'whereRaw', "CONCAT(users.id,'-',users.id) like ? ", ["$1"]);

        // having count search
        if ($datatables->request->get('post') <> '') {
            $datatables->having('count', $datatables->request->get('operator'), $datatables->request->get('post'));
        }

        // additional users.name search
        if ($name = $datatables->request->get('name')) {
            $datatables->where('users.name', 'like', "$name%");
        }

        return $datatables->make(true);
    }

    public function getAddEditRemoveColumn()
    {
        return view('datatables.eloquent.add-edit-remove-column');
    }

    public function getAddEditRemoveColumnData()
    {
        $users = User::select(['id', 'name', 'email', 'password', 'created_at', 'updated_at']);

        return Datatables::of($users)
            ->addColumn('action', function ($user) {
                return '<a href="#edit-'.$user->id.'" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> Edit</a>';
            })
            ->editColumn('id', 'ID: {{$id}}')
            ->removeColumn('password')
            ->make(true);
    }

    public function getDtRow()
    {
        return view('datatables.eloquent.dt-row');
    }

    public function getDtRowData()
    {
        $users = User::select(['id', 'name', 'email', 'password', 'created_at', 'updated_at']);

        return Datatables::of($users)
            ->addColumn('action', function ($user) {
                return '<a href="#edit-'.$user->id.'" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> Edit</a>';
            })
            ->editColumn('id', '{{$id}}')
            ->removeColumn('password')
            ->setRowId('id')
            ->setRowClass(function ($user) {
                return $user->id % 2 == 0 ? 'alert-success' : 'alert-warning';
            })
            ->setRowData([
                'id' => 'test',
            ])
            ->setRowAttr([
                'color' => 'red',
            ])
            ->make(true);
    }

    public function getCustomFilter()
    {
        return view('datatables.eloquent.custom-filter');
    }

    public function getCustomFilterData(Request $request)
    {
        $users = User::select(['id', 'name', 'email', 'created_at', 'updated_at']);

        return Datatables::of($users)
            ->filter(function ($query) use ($request) {
                if ($request->has('name')) {
                    $query->where('name', 'like', "%{$request->get('name')}%");
                }

                if ($request->has('email')) {
                    $query->where('email', 'like', "%{$request->get('email')}%");
                }
            })
            ->make(true);
    }

    public function getCarbon()
    {
        return view('datatables.eloquent.carbon');
    }

    public function getCarbonData()
    {
        $users = User::select(['id', 'name', 'email', 'created_at', 'updated_at']);

        return Datatables::of($users)
            ->editColumn('created_at', '{!! $created_at->diffForHumans() !!}')
            ->editColumn('updated_at', function ($user) {
                return $user->updated_at->format('Y/m/d');
            })
            ->make(true);
    }

    public function getRelationships()
    {
        return view('datatables.eloquent.relationships');
    }

    public function getRelationshipsData()
    {
        $posts = Post::with('user')->select('posts.*');

        return Datatables::of($posts)
            ->editColumn('title', '{!! str_limit($title, 60) !!}')
            ->make(true);
    }

    public function getJoins()
    {
        return view('datatables.eloquent.joins');
    }

    public function getJoinsData()
    {
        $posts = Post::join('users', 'posts.user_id', '=', 'users.id')
            ->select(['posts.id', 'posts.title', 'users.name', 'users.email', 'posts.created_at', 'posts.updated_at']);

        return Datatables::of($posts)
            ->editColumn('title', '{!! str_limit($title, 60) !!}')
            ->editColumn('name', function ($model) {
                return \HTML::mailto($model->email, $model->name);
            })
            ->make(true);
    }

    public function getMultiFilterSelect()
    {
        return view('datatables.eloquent.multi-filter-select');
    }

    public function getMultiFilterSelectData()
    {
        $users = User::select(['id', 'name', 'email', 'created_at', 'updated_at']);

        return Datatables::of($users)->make(true);
    }

    public function getRowDetails()
    {
        return view('datatables.eloquent.row-details');
    }

    public function getRowDetailsData()
    {
        $users = User::select(['id', 'name', 'email', 'created_at', 'updated_at']);

        return Datatables::of($users)->make(true);
    }

    public function getHasMany()
    {
        return view('datatables.eloquent.has-many');
    }

    public function getHasManyData()
    {
        $posts = User::first()->posts()->with('user');

        return Datatables::of($posts)
            ->editColumn('title', '{!! str_limit($title, 60) !!}')
            ->make(true);
    }

    public function getTransformer()
    {
        return view('datatables.eloquent.transformer');
    }

    public function getTransformerData()
    {
        $users = User::select(['id', 'name', 'email', 'created_at', 'updated_at']);

        return Datatables::of($users)
            ->setTransformer('App\Transformers\DatatablesTransformer')
            ->make(true);
    }

    public function getPostColumnSearch()
    {
        return view('datatables.eloquent.post-column-search');
    }

    public function anyColumnSearchData()
    {
        $users = User::select([
            DB::raw("CONCAT(users.id,'-',users.id) as user_id"),
            'name',
            'email',
            'created_at',
            'updated_at',
        ]);

        return Datatables::of($users)
            ->filterColumn('user_id', function($query, $keyword) {
                $query->whereRaw("CONCAT(users.id,'-',users.id) like ?", ["%{$keyword}%"]);
            })
            ->make(true);
    }

    public function getRowNum()
    {
        return view('datatables.eloquent.rownum');
    }

    public function getRowNumData(Request $request)
    {
        DB::statement(DB::raw('set @rownum=0'));
        $users = User::select([
            DB::raw('@rownum := @rownum + 1 AS rownum'),
            'id',
            'name',
            'email',
            'created_at',
            'updated_at']);
        $datatables = Datatables::of($users);

        if ($keyword = $request->get('search')['value']) {
            $datatables->filterColumn('rownum', 'whereRaw', '@rownum + 1 like ?', ["%{$keyword}%"]);
        }

        return $datatables->make(true);
    }

    public function getBlacklist(Request $request)
    {
        if ($request->ajax()) {
            return Datatables::of(User::query())
                ->blacklist(['password', 'name'])
                ->make(true);
        }

        return view('datatables.eloquent.blacklist', ['title' => 'Blacklist Columns']);
    }

    public function getWhitelist(Request $request)
    {
        if ($request->ajax()) {
            return Datatables::of(User::query())
                ->whitelist(['name', 'email'])
                ->make(true);
        }

        return view('datatables.eloquent.whitelist', ['title' => 'Whitelist Columns']);
    }

    public function getOrderColumn(Request $request)
    {
        if ($request->ajax()) {
            return Datatables::of(User::query())
                ->orderColumn('name', 'email $1')
                ->make(true);
        }

        return view('datatables.eloquent.order-column', ['title' => 'Order Column API']);
    }
}
