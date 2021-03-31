<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Locations extends Model
{
    protected $table = 'locations';

    public $timestamps = false;

    protected $fillable = [            
        'id', 'name', 'county', 'country', 'grid_reference', 'easting',
        'northing', 'latitude', 'longitude','elevation', 'postcode_sector',
        'local_government_area', 'nuts_region', 'type'
    ];
}
