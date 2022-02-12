<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Board;
use Validator;
use App\Http\Resources\Board as BoardResource;

class BoardController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //dd('dd');
        $boards = Board::all();

        return $this->sendResponse(BoardResource::collection($boards), 'Boards retrieved successfully.');
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //dd($request->all());
        $input = $request->all();
        $input['created_by'] = auth()->user()->id;

        $validator = Validator::make($input, [
            'board_name' => 'required|unique:boards',
            'board_start_at' => 'required',
            'board_end_at' => 'required',
            'board_final_date' => 'required',
            'board_description' => 'required',
            'created_by' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $board = Board::create($input);

        return $this->sendResponse(new BoardResource($board), 'Board created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $board = Board::find($id);

        if (is_null($board)) {
            return $this->sendError('Board not found.');
        }

        return $this->sendResponse(new BoardResource($board), 'Board retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Board $board)
    {
        $input = $request->all();

        if($board->created_by != auth()->user()->id){
            return $this->sendError('Validation Error.',['created_by' => ['You can modify only your board.']]);
        }

        $validator = Validator::make($input, [
            'board_name' => "required|unique:boards,board_name,$board->id",
            'board_start_at' => 'required',
            'board_end_at' => 'required',
            'board_final_date' => 'required',
            'board_description' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $board->board_name = $input['board_name'];
        $board->board_start_at = $input['board_start_at'];
        $board->board_end_at = $input['board_end_at'];
        $board->board_final_date = $input['board_final_date'];
        $board->board_description = $input['board_description'];
        $board->created_by = auth()->user()->id;
        $board->save();

        return $this->sendResponse(new BoardResource($board), 'Board updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Board $board)
    {
        $user = auth()->user();

        if($board->created_by != auth()->user()->id){
            return $this->sendError('Validation Error.',['id' => ['You can delete only your board.']]);
        }

        $board->delete();

        return $this->sendResponse([], 'Board deleted successfully.');
    }
}
