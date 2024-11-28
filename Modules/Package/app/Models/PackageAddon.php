<?php

namespace Modules\Package\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageAddon extends Model
{
    use HasFactory;

    protected $table = "package_addons";

    protected $fillable = [
        'package_id',
        'title',
        'description',
        'price',
    ];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public static function addAddons($packageId, array $addons)
    {
        $addonData = [];

        foreach ($addons as $addon) {
            $addonData[] = [
                'package_id' => $packageId,
                'title' => $addon['title'],
                'description' => $addon['description'],
                'price' => $addon['price'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        return self::insert($addonData);
    }
}
