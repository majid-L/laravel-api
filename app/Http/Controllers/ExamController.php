<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Exam;
use App\Http\Resources\ExamResource;
use App\Http\Resources\ExamCollection;
use Illuminate\Support\Facades\Gate;

class ExamController extends Controller
{
    public function examsIndex(Request $request)
    {
        return new ExamCollection(Exam::paginate(30));
        /*
        if (!Gate::allows('view-exams')) {
            return response(['msg' => 'Administrator access is required to perform this action.'], 403);
        }

        $order = $request->query('order') ?? 'DESC';
        $limit = $request->query('limit') ?? 30;
        $location = $request->query('location') ?? '';
        $date = $request->query('date') ?? '';

        return new ExamCollection(
            Exam::orderBy('date', $order)
                ->where('location_name', 'like', '%' . $location . '%')
                ->whereDate('date', 'like', '%' . $date . '%')
                ->paginate($limit)
        );*/
    }


    public function store(Request $request)
    {
        Gate::authorize('create-exam');

        $request->validate([
            "title" => 'required|string',
            "description" => 'required|string',
            "candidate_id" => 'required|numeric',
            "candidate_name" => 'required|string',
            "date" => 'required|date',
            "location_name" => 'required|string',
            "latitude" => 'required|decimal:0,25',
            "longitude" => 'required|decimal:0,25'
        ]);
        
        $exams = DB::table('exams')->get();

        if (
            $exams->contains('candidate_name', $request->candidate_name)
            && $exams->contains('date', $request->date)
            && $exams->contains('description', $request->description)
            && $exams->contains('price', $request->price)
            )
        {
            return response()->json([
                'msg' => 'Candidate is already booked in for an exam at this time.'
            ], 400);
        }
        else
        {
            return Exam::create($request->all());
        }
    }

    
    public function show(string $id)
    {
        $exam = Exam::find($id);
        if (!$exam || !Gate::allows('view-exams', $exam->candidate_id)) {
            return response(['msg' => 'Not found.'], 404);
        }

        return new ExamResource(Exam::find($id));
    }

    /**
     * searches for records whose name attribute matches the provided substring
     * @param str $name
     * @return \Illuminate\Http\Response
     */
    public function search(string $name)
    {
        return new ExamCollection(
            Exam::where('candidate_name', 'like', '%' . $name . '%')->get()
        );
    }

    
    public function update(Request $request, string $id)
    {
        $exam = Exam::find($id);

        if (!$exam || !Gate::allows('update-exam', $exam->candidate_id)) {
            return response(['msg' => 'Not found.'], 404);
        }

        $exam->update($request->all());
        return $exam;
    }

    
    public function destroy(string $id)
    {
        $exam = Exam::find($id);

        if (!$exam || !Gate::allows('delete-exam', $exam->candidate_id)) {
            return response(['msg' => 'Not found.'], 404);
        }

        return Exam::destroy($id);
    }
}
