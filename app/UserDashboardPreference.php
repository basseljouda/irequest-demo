<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserDashboardPreference extends Model
{
    protected $table = 'user_dashboard_preferences';

    protected $fillable = [
        'user_id',
        'dashboard_type',
        'widget_id',
        'widget_type',
        'position_row',
        'position_col',
        'size',
        'visible',
        'settings'
    ];

    protected $casts = [
        'visible' => 'boolean',
        'settings' => 'array',
        'position_row' => 'integer',
        'position_col' => 'integer'
    ];

    /**
     * Get user for this preference
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get preferences for a user and dashboard type
     * @param int $userId
     * @param string $dashboardType
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getUserPreferences($userId, $dashboardType)
    {
        return static::where('user_id', $userId)
            ->where('dashboard_type', $dashboardType)
            ->get()
            ->keyBy('widget_id');
    }

    /**
     * Save or update user preference
     * @param int $userId
     * @param string $dashboardType
     * @param string $widgetId
     * @param array $data
     * @return static
     */
    public static function savePreference($userId, $dashboardType, $widgetId, $data)
    {
        return static::updateOrCreate(
            [
                'user_id' => $userId,
                'dashboard_type' => $dashboardType,
                'widget_id' => $widgetId
            ],
            array_merge($data, [
                'widget_id' => $widgetId
            ])
        );
    }

    /**
     * Reset user preferences to defaults
     * @param int $userId
     * @param string $dashboardType
     * @return int Number of deleted preferences
     */
    public static function resetToDefaults($userId, $dashboardType)
    {
        return static::where('user_id', $userId)
            ->where('dashboard_type', $dashboardType)
            ->delete();
    }
}
