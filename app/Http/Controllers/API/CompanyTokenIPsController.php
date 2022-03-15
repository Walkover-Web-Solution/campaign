<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCompanyTokenIPRequest;
use App\Http\Requests\UpdateCompanyTokenIPRequest;
use App\Http\Resources\CustomResource;
use App\Models\CompanyTokenIp;
use App\Models\Token;
use Illuminate\Http\Request;

class CompanyTokenIPsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Token $token, Request $request)
    {
        $itemsPerPage = $request->input('itemsPerPage', 25);

        $paginator = CompanyTokenIp::where('token_id', $token->id)
            ->where(function ($query) use ($request) {
                if ($request->has('ip_type_id')) {
                    $query->where('ip_type_id', $request->ip_type_id);
                }
            })
            ->orderBy('ip_type_id')
            ->paginate($itemsPerPage, ['*'], 'pageNo');

        return response([
            'status' => 'success',
            'hasError' => false,
            'data' => array(
                'data' => $paginator->items(),
                'itemsPerPage' => $itemsPerPage,
                'pageNumber' => $paginator->currentPage(),
                'totalEntityCount' => $token->ips()->count(),
                'totalPageCount' => ceil($paginator->total() / $paginator->perPage())
            )
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Token $token, StoreCompanyTokenIPRequest $request)
    {
        $ip = $token->ips()->where('ip', $request->ip)->first();
        if (empty($ip)) {
            $ip = $token->ips()->create($request->validated());
        } else {
            $ip->update($request->validated());
        }
        return new CustomResource($ip);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Token $token, CompanyTokenIP $ip)
    {
        if ($ip->token_id != $token->id) {
            throw new \Exception("Forbidden, Not Found!");
        }
        $ip->load('type');
        return  new CustomResource($ip);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Token $token, CompanyTokenIP $ip, UpdateCompanyTokenIPRequest $request)
    {
        $ip->update($request->validated());
        return  new CustomResource($ip);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Token $token, CompanyTokenIP $ip)
    {
        $ip->delete();
        return  new CustomResource(['message' => 'deleted successfully']);
    }
}
