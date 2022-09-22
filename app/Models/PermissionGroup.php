<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermissionGroup extends Model
{

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'edit_shortlinks_destination_url' => 'boolean',
        'view_shortlinks_total_views' => 'boolean',
        'view_shortlinks_total_unique_views' => 'boolean',
    ];

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $visible = [
        'edit_shortlinks_destination_url',
        'view_shortlinks_total_views',
        'view_shortlinks_total_unique_views'
    ];

    public function canEditShortlinksDestinationUrl() {
        return $this->edit_shortlinks_destination_url;
    }

    public function canViewShortlinksTotalViews() {
        return $this->view_shortlinks_total_views;
    }

    public function canViewShortlinksTotalUniqueViews() {
        return $this->view_shortlinks_total_unique_views;
    }

}
