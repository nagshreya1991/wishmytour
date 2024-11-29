<?php

namespace Modules\Package\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Package\Database\factories\PackageFactory;
use Carbon\Carbon;


class Package extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
//    protected $fillable = [
//        'id',
//        'user_id',
//        'name',
//        'total_days',
//        'starting_price',
//        'origin_state_id',
//        'origin_city_id',
//        'destination_state_id',
//        'destination_city_id',
//        'keywords',
//        'transportation_id',
//        'hotel_star_id',
//        'religion_id',
//        'trip_id',
//        'type_of_tour_packages_id',
//        'themes_id',
//        'featured_image_path',
//        'inclusions_in_package',
//        'overview',
//        'payment_policy',
//        'cancellation_policy',
//        'terms_and_condition',
//        'featured',
//        'tour_circuit',
//        'region_id',
//        'child_discount',
//        'single_occupancy_cost',
//        'offseason_from_date',
//        'offseason_to_date',
//        'offseason_price',
//        'onseason_from_date',
//        'onseason_to_date',
//        'onseason_price',
//        'total_seat',
//        'bulk_no_of_pax',
//        'pax_discount_percent',
//        'status',
//        'is_transport',
//        'is_flight',
//        'is_train',
//        'is_hotel',
//        'is_meal',
//        'is_sightseeing',
//        'triple_sharing_discount',
//        'admin_verified',
//        'vendor_verified',
//
//    ];

    protected $fillable = [
        'user_id',
        'name',
        'total_days',
        'starting_price',
        'child_price',
        'infant_price',
        'single_occupancy_price',
        'triple_occupancy_price',
        'platform_charges',
        'origin_state_id',
        'origin_city_id',
        'destination_state_id',
        'destination_city_id',
        'region_id',
        'keywords',
        'hotel_star_id',
        'religion_id',
        'tour_type',
        'trip_type',
        'themes_id',
        'overview',
        'payment_policy',
        'cancellation_policy',
        'terms_and_condition',
        'featured',
        'tour_circuit',
        'total_seat',
        'is_transport',
        'is_flight',
        'is_train',
        'is_hotel',
        'is_meal',
        'is_sightseeing',
        'status',
        'admin_verified',
        'vendor_verified',
    ];

    protected $casts = [
        'featured_image_path' => 'string',
    ];

    /**
     * Define the relationship with the Religion model.
     */
    public function religion()
    {
        return $this->belongsTo(Religion::class);
    }

    /**
     * Define the relationship with the Themes model.
     */
    public function themes()
    {
        return $this->belongsTo(Themes::class);
    }





    /**
     * Define the relationship with the galleryImages model.
     */


    public function getGalleryImagesAttribute()
    {
        //return $this->gallery_images()->pluck('path')->toArray();
        return $this->gallery_images()->select('id', 'path')->get()->toArray();
    }

    public function gallery_images()
    {
        return $this->hasMany(PackageImage::class, 'package_id', 'id');
    }

    public function fetchStayPlans()
    {
        return $this->stayPlans()->with('city:id,city')->get()->groupBy('city.city')->map(function ($stayPlans, $cityName) {
            return (object)[
                'city_name' => $cityName,
                'total_nights' => $stayPlans->count(),
            ];
        })->values();
    }

//    public function fetchStayPlans()
//    {
//        return $this->stayPlans()->with('city:id,city')->get()->map(function ($stayPlan) {
//            return [
//                'city_name' => $stayPlan->city->city,
//                'total_nights' => $stayPlan->count(),
//            ];
//        });
//    }

    public function stayPlans()
    {
        return $this->hasMany(StayPlan::class, 'package_id');
    }

    public function trains()
    {
        return $this->hasMany(PackageTrain::class, 'package_id');
    }

    public function flights()
    {
        return $this->hasMany(PackageFlight::class, 'package_id');
    }

    // Add a new relationship to fetch city name
    public function stayPlanCities()
    {
        return $this->hasMany(StayPlan::class, 'package_id')
            ->join('cities', 'stay_plan.cities', '=', 'cities.id')
            ->select('stay_plan.*', 'cities.city as city_name');
    }

    public function cityStayPlans()
    {
        return $this->hasMany(StayPlan::class, 'package_id')
            ->join('cities', 'stay_plan.cities', '=', 'cities.id')
            ->select(
                'cities.city as city_name',
                DB::raw('COUNT(stay_plan.id) as total_nights')
            )
            ->groupBy('stay_plan.cities');
    }

    public function itineraries()
    {
        return $this->hasMany(Itinerary::class, 'package_id');
    }

    public function getItineraries($id)
    {
        $results = Itinerary::join('cities', 'itineraries.place_name', '=', 'cities.id')
            ->leftJoin('package_train', 'itineraries.id', '=', 'package_train.itinerary_id')
            ->leftJoin('package_flight', 'itineraries.id', '=', 'package_flight.itinerary_id')
            ->join('package_hotel', 'itineraries.id', '=', 'package_hotel.itinerary_id')
            ->where('itineraries.package_id', $id)
            ->select(
                'package_train.number_of_nights as train_nights',
                'package_flight.number_of_nights as flight_nights',
                'cities.city as city_name',
                'package_hotel.name as hotel_name',
                'itineraries.id as itinerary_id'
            )
            ->orderBy('itineraries.id')
            ->get();

        $aggregatedResults = [];
        $previousCity = null;
        $previousHotel = null;
        $currentEntry = null;

        foreach ($results as $result) {
            if ($result->train_nights !== null && $result->train_nights > 0) {
                $currentEntry = [
                    'city_name' => 'Train',
                    'hotel_name' => '',
                    'total_nights' => $result->train_nights,
                    'itinerary_id' => $result->itinerary_id
                ];
            } elseif ($result->flight_nights !== null && $result->flight_nights > 0) {
                $currentEntry = [
                    'city_name' => 'Flight',
                    'hotel_name' => '',
                    'total_nights' => $result->flight_nights,
                    'itinerary_id' => $result->itinerary_id
                ];
            } else {
                if ($currentEntry && $previousCity === $result->city_name) {
                    if ($previousHotel === $result->hotel_name) {
                        $currentEntry['total_nights']++;
                    } else {
                        $currentEntry['total_nights']++;
                        $currentEntry['hotel_name'] .= ", {$result->hotel_name}";
                    }
                } else {
                    if ($currentEntry) {
                        $aggregatedResults[] = $currentEntry;
                    }
                    $currentEntry = [
                        'city_name' => $result->city_name,
                        'hotel_name' => $result->hotel_name,
                        'total_nights' => 1,
                        'itinerary_id' => $result->itinerary_id
                    ];
                }

                $previousCity = $result->city_name;
                $previousHotel = $result->hotel_name;
            }
        }

        // Add the last aggregated entry to the results
        if ($currentEntry) {
            $aggregatedResults[] = $currentEntry;
        }

        return collect($aggregatedResults);
    }


    public function mediaLinksAttribute()
    {
        return $this->mediaLinks()->pluck('media_link')->toArray();
    }

    public function mediaLinks()
    {
        return $this->hasMany(PackageMedia::class, 'package_id', 'id');
    }

    public function addons()
    {
        return $this->hasMany(PackageAddon::class, 'package_id', 'id');
    }

    public function seatAvailability()
    {
        return $this->hasMany(PackageSeatAvailability::class, 'package_id', 'id');
    }

    public function seatUnavailable()
    {
        return $this->hasMany(PackageUnavailableDate::class, 'package_id', 'id');
    }

    public function itinerary()
    {
        return $this->hasMany(Itinerary::class, 'package_id', 'id');
    }

    public function inclusions()
    {
        return $this->hasMany(PackageInclusion::class, 'package_id', 'id');
    }

    public function exclusions()
    {
        return $this->hasMany(PackageExclusion::class, 'package_id', 'id');
    }

    /**
     * Get all rates associated with the package that start after the current date.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function rates()
    {
        return $this->packageRates()
            ->where('start_date', '>', Carbon::now())
            ->orderBy('start_date')
            ->get();
    }

    public function packageRates()
    {
        return $this->hasMany(PackageRate::class, 'package_id', 'id');
    }

    public function bulkDiscounts()
    {
        return $this->hasMany(PackageBulkDiscount::class);
    }

    public function packageMessage()
    {
        return $this->hasMany(PackageMessage::class, 'package_id', 'id');
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($package) {
            $package->package_code = self::generatePackageId($package->user_id, $package->id);
            $package->save();
        });
    }

    private static function generatePackageId($vendorId, $packageId)
    {
        $prefix = 'WMTH'; // Fixed prefix for package ID
        $randomAlphanumeric = strtoupper(substr(md5(mt_rand()), 0, 5)); // 5 random alphanumeric characters
        return $prefix . '-' . $vendorId . '-' . $packageId . '-' . $randomAlphanumeric;
    }
} 
