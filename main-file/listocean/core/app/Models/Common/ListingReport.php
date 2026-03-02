<?php

namespace App\Models\Common;

use App\Models\Backend\Listing;
use App\Models\Backend\ReportReason;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListingReport extends Model
{
    use HasFactory;

    protected $table = 'listing_reports';
    protected $fillable = ['listing_id','user_id', 'reason_id', 'description'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function reason()
    {
        return $this->belongsTo(ReportReason::class, 'reason_id');
    }

    public function listing()
    {
        return $this->belongsTo(Listing::class,'listing_id');
    }

}
