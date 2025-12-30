<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Trebol\Entrust\Traits\EntrustUserTrait;

/**
 * DEMO SKELETON: User Model
 * 
 * PURPOSE & WORKFLOW:
 * This model represents users in the medical equipment rental management system.
 * 
 * Original Functionality:
 * - User authentication and authorization using Entrust role-based permissions
 * - Permission checking and role validation
 * - User scoping by sites, IDNs (Integrated Delivery Networks), and regions
 * - Access control methods for hospitals and companies based on user permissions
 * - User querying by roles and permissions for notifications
 * - Mobile number formatting and notification routing
 * 
 * Key Features (Original):
 * 1. Authentication: Laravel authentication with Entrust role-based access control
 * 2. Permissions: Complex permission checking system with role hierarchy
 * 3. Access Control: Site, IDN, and region-based data scoping
 * 4. User Queries: Methods to find users by role, permissions, and email
 * 5. Profile Management: Profile image and mobile number formatting
 * 6. Notifications: SMS notification routing via Nexmo
 * 
 * SECURITY NOTE:
 * All security-sensitive code has been removed for demo purposes:
 * - Permission checking logic removed
 * - Role-based access control removed
 * - User querying by permissions/roles removed
 * - Access control methods removed
 * - Notification routing logic removed
 * 
 * For demo purposes, all business logic, security logic, and access control have been removed.
 * Only Eloquent relationships and basic model structure are kept for portfolio presentation.
 */
class User extends Authenticatable {

    use Notifiable,
        EntrustUserTrait;

    protected $fillable = [
        'name', 'email', 'password', 'image', 'mobile', 'status', 'sites', 'idns', 'regions'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $appends = [
        'profile_image_url', 'mobile_with_code', 'formatted_mobile'
    ];

    protected $casts = [
        'sites' => 'array',
        'idns' => 'array',
        'regions' => 'array'
    ];

    public function messages() {
        return $this->hasMany(Message::class);
    }

    public function getProfileImageUrlAttribute() {
        if (is_null($this->image)) {
            return asset('avatar.png');
        }
        return asset_url('profile/' . $this->image);
    }

    public function role() {
        return $this->hasOne(RoleUser::class, 'user_id');
    }

    public function is_staff() {
        return $this->hasOne(HospitalsStuff::class, 'user_id');
    }

    public function todoItems() {
        return $this->hasMany(TodoItem::class);
    }

    public function orders() {
        return $this->hasMany(Orders::class, 'user_id');
    }

    public function getMobileWithCodeAttribute() {
        if (!$this->calling_code) {
            return $this->mobile;
        }
        return substr($this->calling_code, 1) . $this->mobile;
    }

    public function getFormattedMobileAttribute() {
        if (!$this->calling_code) {
            return $this->mobile;
        }
        return $this->calling_code . '-' . $this->mobile;
    }

    /**
     * Removed: isSuperAdmin() - Checked if user is super admin (role_id = 1) - SECURITY SENSITIVE
     * Removed: can($permission, $requireAll) - Permission checking method with role-based logic - SECURITY SENSITIVE
     * Removed: cans($permission, $requireAll) - Alternative permission checking method - SECURITY SENSITIVE
     * Removed: allAdmins($exceptId) - Queried all admin users by role - SECURITY SENSITIVE
     * Removed: slugRole($slug, $emails, $exceptEmail) - Queried users by role slug and emails - SECURITY SENSITIVE
     * Removed: usersPermissions($permissions, $exceptEmail) - Queried users by specific permissions - SECURITY SENSITIVE
     * Removed: getAllowedSites() - Determined allowed hospital IDs based on user scope and permissions - SECURITY SENSITIVE
     * Removed: getAllowedCompanies() - Determined allowed company/IDN IDs based on user scope and permissions - SECURITY SENSITIVE
     * Removed: routeNotificationForNexmo($notification) - SMS notification routing with phone number handling - SECURITY SENSITIVE
     */

}
