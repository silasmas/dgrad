<?php

namespace App\Http\Resources;

use App\Models\User as client;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class contreventionUser extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $u = client::where('matricule', $this->matricule)->first();

        return [
            'id' => $this->id,
            'contrevention' => contrevention::make($this->contrevention),
            'agent' => contrevention::make($this->agent),
            'user' => $u,
            'reference' => $this->reference,
            'payerPar' => $this->payerPar,
            'phone' => $this->phone,
            'etat' => $this->etat,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
