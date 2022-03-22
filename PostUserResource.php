<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class PostUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'nickname' => $this->nickname,
            'cover' => $this->cover,
            'email' => $this->email,
            'avatar' => $this->avatar,
            'subscriptionPrice' => $this->subscription_price,
            'following' => Auth::user() ? Auth::user()->following($this->resource) : false,
            'following_till' => Auth::user() ? Auth::user()->followingTill($this->resource) : null,
            'follower' => Auth::user() ? $this->resource->following(Auth::user()) : false,
            'last_active' => $this->resource->onlineStatus()['last_active'],
            'is_online' => $this->resource->onlineStatus()['is_online'],
            'verification_status' => $this->verification_status->name(),
        ];
    }
}
