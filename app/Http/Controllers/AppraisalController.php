<?php

namespace App\Http\Controllers;

use App\Models\Appraisal;
use App\Models\Branch;
use App\Models\Competencies;
use App\Models\Employee;
use App\Models\Indicator;
use App\Models\Performance_Type;
use App\Models\PerformanceType;
use Illuminate\Http\Request;

class AppraisalController extends Controller
{

    public function index()
    {
        if(\Auth::user()->can('manage appraisal'))
        {
            $user = \Auth::user();
            if($user->type == 'Employee')
            {
                $employee   = Employee::where('user_id', $user->id)->first();
                $competencyCount = Competencies::where('created_by', '=', $user->creatorId())->count();
                $appraisals = Appraisal::where('created_by', '=', \Auth::user()->creatorId())->where('branch', $employee->branch_id)->where('employee', $employee->id)->with(['employees','branches'])->get();
            }
            else
            {
                $competencyCount = Competencies::where('created_by', '=', $user->creatorId())->count();

                $appraisals = Appraisal::where('created_by', '=', \Auth::user()->creatorId())->with(['employees','branches'])->get();
            }

            return view('appraisal.index', compact('appraisals','competencyCount'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if(\Auth::user()->can('create appraisal'))
        {

            $performance     = PerformanceType::where('created_by', '=', \Auth::user()->creatorId())->get();
            $brances = Branch::where('created_by', '=', \Auth::user()->creatorId())->get();
            return view('appraisal.create', compact( 'brances', 'performance'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function store(Request $request)
    {

        if(\Auth::user()->can('create appraisal'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'branch' => 'required',
                                   'employee' => 'required',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $appraisal                 = new Appraisal();
            $appraisal->branch         = $request->branch;
            $appraisal->employee       = $request->employee;
            $appraisal->appraisal_date = $request->appraisal_date;
            $appraisal->rating         = json_encode($request->rating, true);
            $appraisal->remark         = $request->remark;
            $appraisal->created_by     = \Auth::user()->creatorId();
            $appraisal->save();

            return redirect()->route('appraisal.index')->with('success', __('Appraisal successfully created.'));
        }
    }

    public function show(Appraisal $appraisal)
    {
        $rating = json_decode($appraisal->rating, true);
        $performance_types     = PerformanceType::where('created_by', '=', \Auth::user()->creatorId())->get();
        $employee = Employee::find($appraisal->employee);

        if ($employee != null) {
            $indicator = Indicator::where('branch',$employee->branch_id)->where('department',$employee->department_id)->where('designation',$employee->designation_id)->first();
        }else {
            $indicator = null;
        }

        if ($indicator != null) {
            $ratings = json_decode($indicator->rating, true);
        }else {
            $ratings = null;
        }

        return view('appraisal.show', compact('appraisal', 'performance_types', 'ratings','rating'));
    }

    public function edit(Appraisal $appraisal)
    {
        if(\Auth::user()->can('edit appraisal'))
        {

            $performance_types     = PerformanceType::where('created_by', '=', \Auth::user()->creatorId())->get();
            $brances = Branch::where('created_by', '=', \Auth::user()->creatorId())->get();
            $ratings = json_decode($appraisal->rating,true);


            return view('appraisal.edit', compact( 'brances', 'appraisal', 'performance_types','ratings'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function update(Request $request, Appraisal $appraisal)
    {
        if(\Auth::user()->can('edit appraisal'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'branch' => 'required',
                                   'employee' => 'required',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $appraisal->branch         = $request->branch;
            $appraisal->employee       = $request->employee;
            $appraisal->appraisal_date = $request->appraisal_date;
            $appraisal->rating         = json_encode($request->rating, true);
            $appraisal->remark         = $request->remark;
            $appraisal->save();

            return redirect()->route('appraisal.index')->with('success', __('Appraisal successfully updated.'));
        }
    }

    public function destroy(Appraisal $appraisal)
    {
        if(\Auth::user()->can('delete appraisal'))
        {
            if($appraisal->created_by == \Auth::user()->creatorId())
            {
                $appraisal->delete();

                return redirect()->route('appraisal.index')->with('success', __('Appraisal successfully deleted.'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function empByStar(Request $request)
    {
        $employee = Employee::find($request->employee);

        $indicator = Indicator::where('branch',$employee->branch_id)->where('department',$employee->department_id)->where('designation',$employee->designation_id)->first();

        $ratings = !empty($indicator)? json_decode($indicator->rating, true):[];

        $performance_types = PerformanceType::where('created_by', '=', \Auth::user()->creatorId())->get();

        $viewRender = view('appraisal.star', compact('ratings','performance_types'))->render();
        return response()->json(array('success' => true, 'html'=>$viewRender));

    }

    public function empByStar1(Request $request)
    {
        $employee = Employee::find($request->employee);

        $appraisal = Appraisal::find($request->appraisal);

        $indicator = Indicator::where('branch',$employee->branch_id)->where('department',$employee->department_id)->where('designation',$employee->designation_id)->first();

        if ($indicator != null) {
            $ratings = json_decode($indicator->rating, true);
        }else {
            $ratings = null;
        }
        $rating = json_decode($appraisal->rating,true);
        $performance_types = PerformanceType::where('created_by', '=', \Auth::user()->creatorId())->get();
        $viewRender = view('appraisal.staredit', compact('ratings','rating','performance_types'))->render();
        return response()->json(array('success' => true, 'html'=>$viewRender));

    }

    public function getemployee(Request $request)
    {
        $data['employee'] = Employee::where('branch_id',$request->branch_id)->get();
        return response()->json($data);
    }
}
