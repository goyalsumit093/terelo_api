<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    public function index(){
        $task = Task::where('user_id',auth()->user()->id)->get();

        return BaseController::sendResponse($task, 'Tasks retrieved successfully.');
    }

    public function store(Request $request)
    {
        $input = $request->all();

        $input['user_id'] = auth()->user()->id;
        $input['status'] = 'Pending';

        $validator = Validator::make($input, [
            'board_id' => 'required|exists:boards,id',
            'task_name' => 'required',
            'description' => 'nullable',
            'task_start_date' => 'required',
            'task_end_date' => 'required',
            'task_final_date' => 'required',
        ]);

        if($validator->fails()){
            return BaseController::sendError('Validation Error.', $validator->errors());
        }

        $task = Task::create($input);
        if($task){
            $insertMapping = DB::table('task_board_mapping')->insert([
                'user_id' => auth()->user()->id,
                'board_id' => $request->input('board_id'),
                'task_id' => $task->id,
                'status' => 0,
            ]);

            $insertMapping = DB::table('task_board_log')->insert([
                'task_id' => $task->id,
                'previous_user' => null,
                'new_user' => auth()->user()->id,
                'created_by' => $task->user_id,
            ]);
        }

        return BaseController::sendResponse($task, 'Tasks retrieved successfully.');
    }

    public function show($id)
    {
        $task = Task::with('board')->find($id);

        if (is_null($task)) {
            return BaseController::sendError('Task not found.');
        }

        return BaseController::sendResponse($task, 'Task retrieved successfully.');
    }

    public function update(Request $request, $id)
    {
        $input = $request->all();

        $task = Task::find($id);

        if($task->user_id != auth()->user()->id){
            return BaseController::sendError('Validation Error.',['user_id' => ['You can modify only your task.']]);
        }

        $validator = Validator::make($input, [
            'board_id' => 'required|exists:boards,id',
            'assign_to' => 'exists:users,id',
            'task_name' => "required",//|unique:task,task_name,$task->id
            'description' => 'nullable',
            'task_start_date' => 'required',
            'task_end_date' => 'required',
            'task_final_date' => 'required'
        ]);

        if($validator->fails()){
            return BaseController::sendError('Validation Error.', $validator->errors());
        }

        $prevBoard = $task->board_id;
        $newBoard = $input['board_id'];

        $task->task_name = $input['task_name'];
        $task->description = $input['description'];
        $task->task_start_date = $input['task_start_date'];
        $task->task_end_date = $input['task_end_date'];
        $task->task_final_date = $input['task_final_date'];
        $task->board_id = $input['board_id'];
        if($task->save()){
            if($prevBoard != $newBoard){
                $insertMapping = DB::table('task_board_mapping')->insert([
                    'user_id' => auth()->user()->id,
                    'board_id' => $request->input('board_id'),
                    'task_id' => $task->id,
                    'status' => 0,
                ]);
            }

            if(isset($input['assign_to']) && $input['assign_to']){
                $insertMapping = DB::table('task_board_mapping')->insert([
                    'user_id' => $input['assign_to'],
                    'board_id' => $task->board_id,
                    'task_id' => $task->id,
                    'status' => 0,
                ]);

                $insertMapping = DB::table('task_board_log')->insert([
                    'task_id' => $task->id,
                    'previous_user' => $task->user_id,
                    'new_user' => $input['assign_to'],
                    'created_by' => $task->user_id,
                ]);
            }
        }

        return BaseController::sendResponse($task, 'Task updated successfully.');
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $userId = $user->id;
        $task = Task::where('id',$id)->where('user_id',$userId)->first();
        if(!$task){
            return BaseController::sendError('id', 'Yo can not delete other\'s task.');
        }

        $task->delete();

        return BaseController::sendResponse([], 'Task deleted successfully.');
    }
}
