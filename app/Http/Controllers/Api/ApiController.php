<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public $slug = null;
    public $model = null;
    public $table = null;

    /**
     * Class Constructor
     *
     */
    public function __construct()
    {
        if ($this->model) {
            $this->table = $this->model->getTable();
        }
    }

    public function getSlug()
    {
        if (is_null($this->slug)) {
            return getUrlSlug();
        }

        return $this->slug;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (method_exists($this, 'handleIndex')) {
            return $this->handleIndex($request, $this->model);
        }

        $this->authorize('browse', $this->model);

        if ($request->has('per_page') && $request->query('per_page') === 'all') {
            return $this->model->all();
        }

        return $this->model->paginate();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->authorize('read', $this->model);

        return $this->model->find($id);
    }

    /**
     * Display the specified resource with relationship.
     *
     * @param  int  $id
     * @param  any  $model
     * @return \Illuminate\Http\Response
     */
    public function showWith($id, $model = null, $noAuth = false)
    {
        if (!$noAuth) {
            $this->authorize('read', $this->model);
        }

        return $model ? $model->findOrFail($id) : $this->model->find($id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->authorize('delete', $this->model);

        $model = $this->model->findOrFail($id);

        if (auth()->user()->hasRole($model->name)) {
            return api_response('Unable to delete record', false);
        }

        // $model->delete();

        return api_response('Record deleted successfully', true);
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function multiDestroy(Request $request)
    {
        $this->authorize('delete', $this->model);

        $ids = is_array($request->input('ids'))? $request->input('ids') : explode(',', $request->input('ids'));

        $del = $this->model->whereIn(
            'id',
            $ids
        )->delete();

        $message = $del ? Str::plural('Record', $del).' deleted successfully' : 'No record deleted!';

        return api_response($message, $del > 0);
    }
}
