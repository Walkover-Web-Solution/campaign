<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssociateTokenToCampaignRequest;
use App\Http\Requests\UpdateTokenRequest;
use App\Http\Resources\CustomResource;
use App\Models\Campaign;
use App\Models\Token;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TokensController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $itemsPerPage = $request->input('itemsPerPage', 25);
        $paginator = Token::select('id', 'name')->where('company_id', $request->company->id)
            ->where(function ($query) use ($request) {
                if ($request->has('name')) {
                    $query->where('name', 'like', '%' . $request->name . '%');
                }
                if ($request->has('is_active')) {
                    $query->where('is_active', (bool)$request->is_active);
                }
                if ($request->has('withTrashed')) {
                    $query->withTrashed();
                }
            })
            ->paginate($itemsPerPage, ['*'], 'pageNo');

        return new CustomResource([
            'data' => $paginator->items(),
            'itemsPerPage' => $itemsPerPage,
            'pageNumber' => $paginator->currentPage(),
            'totalEntityCount' => $paginator->total(),
            'totalPageCount' => ceil($paginator->total() / $paginator->perPage())
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
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:50', 'alpha_dash', Rule::unique('tokens', 'name')->where(function ($query)  use ($request) {
                return $query->where('company_id', $request->company->id);
            })]
        ]);
        $token = $request->company->tokens()->create($request->all());
        return new CustomResource($token);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Token $token)
    {
        if ($token->company_id != $request->company->id) {
            throw new \Exception("Unauthorized", 1);
        }

        $token->load(['campaigns']);
        $token->campaigns->transform(function ($campaign) {
            return array(
                'id' => $campaign->id,
                'name' => $campaign->name,
                'is_active' => $campaign->is_active,
                'is_selected' => true

            );
        });
        return new CustomResource($token);
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
    public function update(UpdateTokenRequest $request, Token $token)
    {
        $input = $request->validated();
        if ($token->is_primary) {
            return new CustomResource(["message" => "Can not update default token"]);
        }
        $token->update($input);
        return new CustomResource($token);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Token $token, Request $request)
    {
        if ($token->company_id != $request->company->id) {
            throw new \Exception("Unauthorized", 1);
        }
        if ($token->is_primary) {
            return new CustomResource(["message" => "Can not delete default token"]);
        }
        $token->delete();
        return new CustomResource(['message' => "Delete successfully"]);
    }

    /**
     * Associate the token with campaign
     */
    public function associate(Token $token, AssociateTokenToCampaignRequest $request)
    {
        $updateData = array("token_id" => $token->id);

        if (isset($request->campaigns)) {
            $primary_token = $request->company->tokens()->select('id')->where('is_primary', true)->first();
            Campaign::where('token_id', $token->id)->update(['token_id' => $primary_token->id]);
            Campaign::whereIn('slug', collect($request->campaigns)->pluck('slug')->toArray())->update($updateData);
        }
        return new CustomResource([
            'message' => 'Associated successfully'
        ]);
    }
}
