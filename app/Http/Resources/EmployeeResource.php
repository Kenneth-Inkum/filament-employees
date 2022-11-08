<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'firstName' => $this->first_name,
            'lastName' => $this->last_name,
            'email' => $this->email,
            'address' =>$this->address,
            'country'=> $this->country,
            'city' => $this->city,
            'state' => $this->state,
            'zipCode' => $this->zip_code,
            'birthDate' => $this->birthday,
            'dateHired' => $this->date_hired,
        ];

        // return parent::toArray($request);
    }
}
