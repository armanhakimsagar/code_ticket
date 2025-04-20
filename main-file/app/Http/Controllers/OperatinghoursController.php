<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OperatingHours;


class OperatinghoursController extends Controller
{
    //
    public function index(Request $request)
    {

        $opeatings = OperatingHours::where('created_by', \Auth::user()->id)->get();

        return view('admin.operating_hours.index', compact('opeatings'));
    }

    public function create()
    {
        $days = OperatingHours::$days;

        return view('admin.operating_hours.create', compact('days'));
    }




    public function store(Request $request)
    {
        $user = \Auth::user();


            $validation = [
                'name' => 'required|string|max:255',
                'content' => 'required|string|max:255',

            ];
            $days = $data = [];

            if($request->days != null)
            {
                foreach ($request->content as $key => $value) {
                    if(array_key_exists($key , $request->days)){
                        $data[$key] = $value;
                    }
                }
                $opeating = new OperatingHours();
                $opeating->name = $request->name;
                $opeating->content = json_encode($data);
                $opeating->created_by = \Auth::user()->createId();
                $opeating->save();
                return redirect()->route('admin.operating_hours.index')->with('success', __('OperatingHours created successfully'));
            }
            else{
                return redirect()->route('admin.operating_hours.index')->with('error', __('Something went wrong, Please try again,'));
            }


    }



    public function show($id)
    {
        $opeating = OperatingHours::find($id);
        $days = OperatingHours::$days;

        return view('admin.operating_hours.show', compact('opeating', 'days'));
    }

    public function edit($id)
    {
        $user = \Auth::user();

        $opeatings = OperatingHours::find($id);
        $days = OperatingHours::$days;

        return view('admin.operating_hours.edit', compact('opeatings', 'days'));
    }

    public function update(Request $request, $id)
    {

        $user = \Auth::user();

        $days = $data = [];


        foreach ($request->content as $key => $value) {

            if (array_key_exists($key, $request->days)) {
                $data[$key] = $value;
            }
        }
        $opeatings = OperatingHours::find($id);
        $opeatings->name = $request->name;
        $opeatings->content = json_encode($data);
        $opeatings->created_by = \Auth::user()->createId();
        $opeatings->save();
        return redirect()->route('admin.operating_hours.index')->with('success', __('OperatingHours updated successfully'));
    }

    public function destroy($id)
    {
        $user = \Auth::user();
        $opeatings = OperatingHours::find($id);
        $opeatings->delete();

        return redirect()->back()->with('success', __('OperatingHours deleted successfully'));
    }
}
