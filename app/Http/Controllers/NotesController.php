<?php

namespace App\Http\Controllers;

use App\Notifications\NotesNotify;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Models\Notes;
use App\Models\Status;
use App\Models\User;
use DataTables;

class NotesController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->status = Status::getAllStatus();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('dashboard.notes.notesList')->with(['status' => $this->status]);
    }

    public function dataTable(Request $request)
    {
        if (request()->ajax()) {
            $advices = Notes::query()
                ->with('status')
                ->when((filled(request()->status_id)), function (Builder $builder) {
                    $builder->where('status_id', request()->status_id);
                });

            return Datatables::of($advices)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return [
                        'showUrl' => route('notes.show', $row->id) ?? Null,
                        'EditUrl' => route('notes.edit', $row->id) ?? Null,
                        'deleteUrl' => route('notes.destroy', $row->id) ?? Null
                    ];
                })
                ->addColumn('status', function ($row) {
                    return isset($row->status) ? json_decode($row->status, true) : Null;
                })
                ->addColumn('author', function ($row) {
                    return isset($row->user) ? $row->user->name : Null;
                })
                ->rawColumns(['author'])
                ->make(true);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $statuses = Status::all();
        return view('dashboard.notes.create', [ 'statuses' => $statuses ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title'             => 'required|min:1|max:64',
            'content'           => 'required',
            'status_id'         => 'required',
            'applies_to_date'   => 'required|date_format:Y-m-d',
            'note_type'         => 'required'
        ]);
        $user = auth()->user();
        $note = new Notes();
        $note->title     = $request->input('title');
        $note->content   = $request->input('content');
        $note->status_id = $request->input('status_id');
        $note->note_type = $request->input('note_type');
        $note->applies_to_date = $request->input('applies_to_date');
        $note->users_id = $user->id;
        $note->save();
        //$request->session()->flash('message', 'Successfully created note');
        Notification::send(User::getAdmins(), new NotesNotify($note));
        return redirect(route('notes.index'))->with('success', 'Note created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $note = Notes::with('user')->with('status')->find($id);
        return view('dashboard.notes.noteShow', [ 'note' => $note ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $note = Notes::find($id);
        $statuses = Status::all();
        return view('dashboard.notes.edit', [ 'statuses' => $statuses, 'note' => $note ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //var_dump('bazinga');
        //die();
        $validatedData = $request->validate([
            'title'             => 'required|min:1|max:64',
            'content'           => 'required',
            'status_id'         => 'required',
            'applies_to_date'   => 'required|date_format:Y-m-d',
            'note_type'         => 'required'
        ]);
        $note = Notes::find($id);
        $note->title     = $request->input('title');
        $note->content   = $request->input('content');
        $note->status_id = $request->input('status_id');
        $note->note_type = $request->input('note_type');
        $note->applies_to_date = $request->input('applies_to_date');
        $note->save();
        $request->session()->flash('message', 'Successfully edited note');
        return redirect()->route('notes.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Notes $note)
    {
        $note->delete();

        $data['code'] = 1;
        $data['msg'] = 'Item Deleted successfully';
        return response()->json($data);
    }
}
