<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnApproval extends Model
{
    protected $fillable = [
        'kd',
        'date',
        'SlpCode',
        'SlpName',
        'SlpCodeSfa',
        'Branch',
        'CardCode',
        'CardName',
        'DocTotal',
        'lat',
        'long',
        'file',
        'users_spv_id',
        'approval_spv_st',
        'users_sbh_id',
        'approval_sbh_st',
    ];

    public function getBranchDetailAttribute()
    {
        return Branch::find($this->Branch);
    }

    public function getStatusLabelAttribute()
    {
        $spv = $this->approval_spv_st;
        $sbh = $this->approval_sbh_st;

        if ($spv==0 && $sbh==0) 
        {
            $label = '<span class="badge badge-primary">Waiting</span>';
        }
        else if ($spv==1 && $sbh==0)
        {
            $label = '<span class="badge badge-primary">Waiting For SBH</span>';
        }
        else if ($spv==1 && $sbh==1)
        {
            $label = '<span class="badge badge-success">Approve</span>';
        }
        else
        {
            $label = '<span class="badge badge-danger">Reject</span>';
        }

        return $label;
    }

    public function getCompanyDetailAttribute()
    {
       return Company::find($this->company_id);
    }
}
