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
        'send_shortlink_by_email_when_generating' => 'boolean',
        'edit_shortlinks_destination_url' => 'boolean',
        'view_shortlinks_total_views' => 'boolean',
        'view_shortlinks_total_unique_views' => 'boolean',
        'create_custom_shortlinks' => 'boolean',
        'create_shortlinks_with_length_1' => 'boolean',
        'create_shortlinks_with_length_2' => 'boolean',
        'create_shortlinks_with_length_3' => 'boolean',
        'create_shortlinks_with_length_4' => 'boolean',
        'default' => 'boolean',
        'guests_permission_group' => 'boolean'
    ];

    public function isDefaultForGuestUsers() {
        return $this->guests_permission_group;
    }

    public function isDefaultForNewRegisteredUsers() {
        return $this->default;
    }

    public function canEditShortlinksDestinationUrl() {
        return $this->edit_shortlinks_destination_url;
    }

    public function canViewShortlinksTotalViews() {
        return $this->view_shortlinks_total_views;
    }

    public function canViewShortlinksTotalUniqueViews() {
        return $this->view_shortlinks_total_unique_views;
    }

    public function canCreateCustomShortlinks() {
        return $this->create_custom_shortlinks;
    }

    public function canCreateShortlinksWithSpecificLength(int $specificLength) {
        $permissionName = 'create_shortlinks_with_length_' . $specificLength;
        return $this->$permissionName;
    }

    public function canSendShortlinkByEmailWhenGenerating() {
        return $this->send_shortlink_by_email_when_generating;
    }

    public function toPermissionsArray()
    {
        return [
            'send_shortlink_by_email_when_generating' => $this->send_shortlink_by_email_when_generating,
            'edit_shortlinks_destination_url' => $this->edit_shortlinks_destination_url,
            'view_shortlinks_total_views' => $this->view_shortlinks_total_views,
            'view_shortlinks_total_unique_views' => $this->view_shortlinks_total_unique_views,
            'create_custom_shortlinks' => $this->create_custom_shortlinks,
            'create_shortlinks_with_length_1' => $this->create_shortlinks_with_length_1,
            'create_shortlinks_with_length_2' => $this->create_shortlinks_with_length_2,
            'create_shortlinks_with_length_3' => $this->create_shortlinks_with_length_3,
            'create_shortlinks_with_length_4' => $this->create_shortlinks_with_length_4,
        ];

    }

}
