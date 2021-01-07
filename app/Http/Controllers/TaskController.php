<?php

namespace App\Http\Controllers;

use App\Task;
use App\User;
use App\Repositories\TaskRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller{
	protected $tasks;

	public function __construct()
    {
        $this->middleware('auth');

    }

	public function getTasks(Request $request){
		$user = Auth::user();
		$tasks = Task::where('assigned_to', $user->id)
                    ->orderBy('created_at', 'asc')
                    ->get();
		return response()->json(['tasks' => $tasks], 200);
	}

	public function getAssign(Request $request){
		$user = Auth::user();
		$tasks = Task::where('assigned_by', $user->id)
                    ->get();
		return response()->json(['tasks' => $tasks], 200);

	}

	public function getAllTasks(Request $request){
		if(Auth::user()->roles == 'Admin'){
			$tasks = Task::get();
			return response()->json(['tasks' => $tasks], 200);
		}
		else{
            return response()->json(['message' => 'you do not have the permission to view all tasks']);
        }

	}

	public function addTask(Request $request){
		$this->validate($request, [
            'title' => 'required|string',
            'description' => 'required|string',
            'due_date' => 'required',
            'assigned_to' => 'required',
        ]);
    	$task = new Task;
    	$task->title = $request->input('title');
    	$task->description = $request->input('description');
    	$task->due_date = $request->input('due_date');
    	$task->assigned_to= $request->input('assigned_to');
    	$task->assigned_by= Auth::user()->id;
    	$task->status = 'assigned';

    	$task->save();

    	return response()->json(['task' => $task, 'message' => 'CREATED'], 201);
        
        // catch (\Exception $e) {
        //     //return error message
        //     return response()->json(['message' => 'Failed!'], 409);
        // }

        // return response()->json(['task' => $newTask, 'message' => 'CREATED'], 201);

	}

	public function deleteTask($id){
		$task = Task::find($id);
		$task->delete();
	}


	public function idTask($id){
		$task = Task::find($id);
		return response()->json(['task' => $task], 200);
	}

	public function userTasks($id){
		$tasks = Task::where('assigned_to', $id)
					->get();

		return response()->json(['tasks' => $tasks]);
	}

	public function editTask(Request $request, $id){
		$task = Task::find($id);
		$this->validate($request, [
            'title' => 'required|string',
            'description' => 'required|string',
            'due_date' => 'required',
        ]);

        $task->title = $request->input('title');
        $task->description = $request->input('description');
        $task->due_date = $request->input('due_date');

        $task->save();

        return response()->json(['task' => $task, 'message' => 'Edited Successfully'], 201);
	}

	public function updateTask(Request $request, $id){
		$task = Task::find($id);
		$this->validate($request, [
			'status' => 'required|string',
        ]);

        $task->status =$request->input('status');
        if($task->status ==="completed"){
        	$current_date_time = date('Y-m-d H:i:s');
        	$task->completred_at = $current_date_time;
        }

        $task->save();

        return response()->json(['task' => $task, 'message' => 'Updated Successfully'], 201);
	}

}