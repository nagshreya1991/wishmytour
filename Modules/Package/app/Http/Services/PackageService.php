<?php

namespace Modules\Package\App\Http\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Modules\Package\app\Models\Package;
use Modules\Package\app\Models\Itinerary;
use Modules\Package\app\Models\PackageImage;
use Modules\Package\app\Models\PackageFlight;
use Modules\Package\app\Models\PackageTrain;
use Modules\Package\app\Models\PackageLocalTransport;
use Modules\Package\app\Models\PackageHotel;
use Modules\Package\app\Models\PackageHotelGalleryImage;
use Modules\Package\app\Models\Themes;
use Modules\Package\app\Models\State;
use Modules\Package\app\Models\City;
use Modules\Package\app\Models\Religion;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Modules\Package\app\Models\PackageAddon;
use Modules\Package\app\Models\PackageMedia;
use Modules\Package\app\Models\PackageSeatAvailability;
use Modules\Package\app\Models\PackageSightseeingGallery;
use Modules\Package\app\Models\PackageUnavailableDate;
use Modules\Package\app\Models\PackageSightseeing;
use Modules\Package\app\Models\PackageInclusion;
use Modules\Package\app\Models\PackageExclusion;
use Modules\Package\app\Models\VendorType;
use Modules\Package\app\Models\StayPlan;
use Modules\Package\app\Models\Region;
use Modules\Package\app\Models\LocationKeyword;
use Modules\Package\app\Models\PackageBulkDiscount;
use Modules\Package\app\Models\PackageRate;
use App\Helpers\Helper;
use App\Models\Config;
use Modules\Package\app\Models\Wishlist;
use Modules\Package\app\Models\PackageMessage;
use Illuminate\Support\Facades\Log;
use Modules\User\app\Models\User;
use App\Rules\CancellationPolicyValidation;
use App\Rules\PaymentPolicyValidation;

class PackageService
{
    protected Package $package;

    public function addPackage(array $request)
    {

        $user = Auth::user();
        $organizationName = '';
        if ($user && $user->vendorDetails) {
            $organizationName = $user->vendorDetails->organization_name;
        }

        $validator = Validator::make($request, [
            'cancellation_policy' => ['required', new CancellationPolicyValidation],
            'payment_policy' => ['required', new PaymentPolicyValidation],
        ]);

        if ($validator->fails()) {
            return ['res' => false, 'msg' => $validator->errors()->first(), 'data' => []];
        }

        $originCityId = $request['origin'];
        $originCity = City::find($originCityId);

        $platformCharges = Helper::getConfig('platform_charges')->value;


        $package = Package::create([
            'user_id' => $user->id,
            'name' => $request['name'],
            'total_days' => $request['total_days'],
            'origin_state_id' => $originCity->state_id,
            'origin_city_id' => $originCityId,
            'destination_state_id' => $request['destination_state_id'],
            'destination_city_id' => $request['destination_city_id'],
            'tour_type' => $request['tour_type'] ?? null,
            'trip_type' => $request['tour_type'] ?? null,
            'themes_id' => $request['themes_id'] ?? null,
            'overview' => $request['overview'] ?? null,
            'terms_and_condition' => $request['terms_and_condition'] ?? null,
            'cancellation_policy' => implode(',', $request['cancellation_policy']) ?? null,
            'payment_policy' => implode(',', $request['payment_policy']) ?? null,
            'keywords' => $request['keywords'] ?? null,
            'total_seat' => $request['total_seat'] ?? null,
            'starting_price' => $request['starting_price'] ?? null,
            'child_price' => $request['child_price'] ?? null,
            'infant_price' => $request['infant_price'] ?? null,
            'single_occupancy_price' => $request['single_occupancy_price'] ?? null,
            'triple_occupancy_price' => $request['triple_occupancy_price'] ?? null,
            'platform_charges' => $platformCharges,
            'religion_id' => $request['religion_id'] ?? null,
            'tour_circuit' => $request['tour_circuit'] ?? null,
            'region_id' => $request['region_id'] ?? null,
            'status' => 2
        ]);

        if (isset($request['bulk_discount_tiers'])) {
            foreach ($request['bulk_discount_tiers'] as $tier) {
                if (isset($tier['pax']) && isset($tier['discount'])) {
                    $package->bulkDiscounts()->create([
                        'min_pax' => $tier['pax'],
                        'discount' => $tier['discount'],
                    ]);
                }
            }

        }
        if ($request['status'] == 1) {
            $originCity->increment('packages', 1);
        }

        // Handle featured image
//        if (isset($request['featured_image_path'])) {
//            $featuredImage = $request['featured_image_path'];
//
//            // Check if the file is an instance of UploadedFile
//            if ($featuredImage instanceof \Illuminate\Http\UploadedFile && $featuredImage->isValid()) {
//                $filename = time() . '_' . $featuredImage->getClientOriginalName();
//                $featuredImage->storeAs('public/uploads', $filename);
//                $package->update(['featured_image' => 'uploads/' . $filename]);
//            } else {
//                // Log or handle the error
//                return response()->json(['error' => 'Invalid or corrupted file for featured image'], 400);
//            }
//        } else {
//            $package->update(['featured_image' => null]);
//            // return response()->json(['error' => 'Featured image not provided'], 400);
//        }

        // Handle gallery images
        if (isset($request['gallery_images'])) {
            foreach ($request['gallery_images'] as $image) {


                $filename = uniqid() . '.' . $image->getClientOriginalExtension();
                $path = 'gallery_images/' . $package->id . '/' . $filename;

                // Manually move the file to the storage path
                File::makeDirectory(storage_path('app/public/' . dirname($path)), 0755, true, true);
                $image->move(storage_path('app/public/' . dirname($path)), $filename);


                // Create a record in the database for the image
                PackageImage::create([
                    'package_id' => $package->id,
                    'path' => $path,
                ]);
            }
        }
        // Handle media links
        $packageMediaLinks = [];
        if (isset($request['media_links'])) {
            foreach ($request['media_links'] as $mediaLink) {
                $packageMediaLinks[] = [
                    'package_id' => $package->id,
                    'media_link' => $mediaLink,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Insert media links into the package_media table
        PackageMedia::insert($packageMediaLinks);


        $addonsData = [];
        // Handle addons

        if (isset($request['addons'])) {
            $addonsData = $request['addons'];
            PackageAddon::addAddons($package->id, $addonsData);
        }

        $packageRates = [];
        if (isset($request['rates'])) {
            foreach ($request['rates'] as $rate) {
                $createdRate = PackageRate::create([
                    'package_id' => $package->id,
                    'start_date' => $rate['start_date'],
                    'end_date' => $rate['end_date'],
                    'price' => $rate['price'],
                ]);
                $packageRates[] = $createdRate->toArray();
            }
        }

        // Handle package_seat_availability
        $packageSeatAvailabilityData = [];
        if (isset($request['seat_availability'])) {
            foreach ($request['seat_availability'] as $availability) {
                $packageSeatAvailabilityData[] = [
                    'package_id' => $package->id,
                    'date' => $availability['date'],
                    'seat' => $availability['seat'],
                    'cost' => $availability['cost'],
                ];
            }
        }
        PackageSeatAvailability::insert($packageSeatAvailabilityData);

        // Handle PackageUnavailableDate
        $packageUnavailableDate = [];
        if (isset($request['seat_unavailable'])) {
            foreach ($request['seat_unavailable'] as $unavailability) {
                $packageUnavailableDate[] = [
                    'package_id' => $package->id,
                    'date' => $unavailability['date'],
                ];
            }
        }
        PackageUnavailableDate::insert($packageUnavailableDate);

        //itinerary
        $itineraryData = [];
        $packageFlightData = [];
        $packageTrainData = [];
        $packageLTData = [];
        $packageHotelData = [];
        $packageHotelGalleryImages = [];
        $packageSTData = [];
        $inclusionList = [];
        $exclusionList = [];
        $packageSiteseeingGalleryImages = [];

        if (isset($request['inclusion_list'])) {
            foreach ($request['inclusion_list'] as $inclusion) {
                $inclusionList[] = [
                    'package_id' => $package->id,
                    'name' => $inclusion['text'] ?? null,
                ];

            }
        }
        if (isset($request['exclusion_list'])) {
            foreach ($request['exclusion_list'] as $exclusion) {
                $exclusionList[] = [
                    'package_id' => $package->id,
                    'name' => $exclusion['text'] ?? null,
                ];

            }
        }

        if (isset($request['itinerary'])) {
            foreach ($request['itinerary'] as $itinerary) {
                $lastInsertedId = DB::table('itineraries')->insertGetId([
                    'package_id' => $package->id,
                    'day' => $itinerary['day'],
                    'place_name' => $itinerary['place_name'] ?? null,
                    'itinerary_title' => $itinerary['itinerary_title'] ?? null,
                    'itinerary_description' => $itinerary['itinerary_description'] ?? null,
                    'meal' => $itinerary['meal'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                //Flight
                if (isset($itinerary['flights'])) {
                    $package->update(['is_flight' => 1]);
                    foreach ($itinerary['flights'] as $flight) {
                        $packageFlightData[] = [
                            'itinerary_id' => $lastInsertedId,
                            'package_id' => $package->id,
                            'depart_destination' => $flight['depart_destination'],
                            'arrive_destination' => $flight['arrive_destination'],
                            'depart_datetime' => $flight['depart_datetime'] ?? null,
                            'arrive_datetime' => $flight['arrive_datetime'] ?? null,
                            'number_of_nights' => $train['number_of_nights'] ?? 0,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }
                //Train
                if (isset($itinerary['trains'])) {
                    $package->update(['is_train' => 1]);
                    foreach ($itinerary['trains'] as $train) {
                        $packageTrainData[] = [
                            'itinerary_id' => $lastInsertedId,
                            'package_id' => $package->id,
                            'train_name' => $train['train_name'],
                            'train_number' => $train['train_number'],
                            'class' => $train['class'],
                            'from_station' => $train['from_station'],
                            'to_station' => $train['to_station'],
                            'number_of_nights' => $train['number_of_nights'] ?? 0,
                            'depart_datetime' => $train['depart_datetime'] ?? null,
                            'arrive_datetime' => $train['arrive_datetime'] ?? null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }
                //Local Transport
                if (isset($itinerary['local_transports'])) {
                    $package->update(['is_transport' => 1]);
                    foreach ($itinerary['local_transports'] as $transport) {
                        $packageLTData[] = [
                            'itinerary_id' => $lastInsertedId,
                            'package_id' => $package->id,
                            'car' => $transport['car'],
                            'model' => $transport['model'],
                            'capacity' => $transport['capacity'],
                            'AC' => $transport['AC'],
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }
                //Hotel
                if (isset($itinerary['hotel'])) {
                    $package->update(['is_hotel' => 1]);
                    foreach ($itinerary['hotel'] as $hotel) {
                        $lasthotelInsertedId = DB::table('package_hotel')->insertGetId([
                            'itinerary_id' => $lastInsertedId,
                            'package_id' => $package->id,
                            'name' => $hotel['name'],
                            'is_other_place' => $hotel['is_other_place'],
                            'place_name' => $hotel['place_name'] ?? null,
                            'distance_from_main_town' => $hotel['distance_from_main_town'] ?? null,
                            'rating' => $hotel['rating'],
                            'created_at' => now(),
                            'updated_at' => now(),
                            // ];
                        ]);
                        if (isset($hotel['gallery_images'])) {
                            foreach ($hotel['gallery_images'] as $image) {
                                $filename = uniqid() . '.' . $image->getClientOriginalExtension();
                                $path = 'hotel_gallery_images/' . $package->id . '/' . $filename;

                                // Manually move the file to the storage path
                                File::makeDirectory(storage_path('app/public/' . dirname($path)), 0755, true, true);
                                $image->move(storage_path('app/public/' . dirname($path)), $filename);

                                // Save information to the database
                                $packageHotelGalleryImages[] = [
                                    'hotel_id' => $lasthotelInsertedId,
                                    'path' => $path,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ];
                            }
                        }
                        $packageHotelData[] = [
                            'itinerary_id' => $lastInsertedId,
                            'name' => $hotel['name'],
                            'rating' => $hotel['rating'],
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }

                //Siteseeing
                if (isset($itinerary['siteseeing'])) {
                    $package->update(['is_sightseeing' => 1]);
                    foreach ($itinerary['siteseeing'] as $siteseeing) {

                        $lastsightseeingInsertedId = DB::table('package_sightseeing')->insertGetId([
                            'package_id' => $package->id,
                            'itinerary_id' => $lastInsertedId,
                            'morning' => $siteseeing['morning'] ?? null,
                            'afternoon' => $siteseeing['afternoon'] ?? null,
                            'evening' => $siteseeing['evening'] ?? null,
                            'night' => $siteseeing['night'] ?? null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        if (isset($siteseeing['gallery_images'])) {
                            foreach ($siteseeing['gallery_images'] as $image) {
                                $filename = uniqid() . '.' . $image->getClientOriginalExtension();
                                $path = 'siteseeing_gallery_images/' . $package->id . '/' . $filename;

                                // Manually move the file to the storage path
                                File::makeDirectory(storage_path('app/public/' . dirname($path)), 0755, true, true);
                                $image->move(storage_path('app/public/' . dirname($path)), $filename);

                                // Save information to the database
                                $packageSiteseeingGalleryImages[] = [
                                    'sightseeing_id' => $lastsightseeingInsertedId,
                                    'path' => $path,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ];
                            }
                        }
                    }
                }
                // Add the current $itinerary to $itineraryData
                $itineraryData[] = [
                    'package_id' => $package->id,
                    'day' => $itinerary['day'],
                    'place_name' => $itinerary['place_name'] ?? null,
                    'itinerary_title' => $itinerary['itinerary_title'] ?? null,
                    'itinerary_description' => $itinerary['itinerary_description'] ?? null,
                    'meal' => $itinerary['meal'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                // Update is_meal if meal is not null
                if (isset($itinerary['meal']) && $itinerary['meal'] !== null) {
                    $package->update(['is_meal' => 1]);
                }
            }
        }

        PackageFlight::insert($packageFlightData);
        PackageTrain::insert($packageTrainData);
        PackageLocalTransport::insert($packageLTData);

        PackageHotelGalleryImage::insert($packageHotelGalleryImages);

        PackageInclusion::insert($inclusionList);
        PackageExclusion::insert($exclusionList);
        PackageSightseeingGallery::insert($packageSiteseeingGalleryImages);


        // Load the gallery_images relationship
        $package->load('gallery_images');
        Helper::sendNotification(34, $organizationName . " added a new package :" . $package->name);
        return ['res' => true, 'msg' => 'Package added successfully', 'data' => [
            'package' => $package->toArray(),
            'itinerary' => $itineraryData,
            'package_flight' => $packageFlightData,
            'package_Train' => $packageTrainData,
            'package_local_transport' => $packageLTData,
            'package_hotel' => $packageHotelData,
            'package_hotel_gallery' => $packageHotelGalleryImages,
            'package_siteseeing' => $packageSTData,
            'package_siteseeing_gallery' => $packageSiteseeingGalleryImages,
            'addons_data' => $addonsData,
            'package_media_links' => $packageMediaLinks,
            'packageSeatAvailabilityData' => $packageSeatAvailabilityData,
            'packageUnavailableDate' => $packageUnavailableDate,
            'inclusionList' => $inclusionList,
            'exclusionList' => $exclusionList,
            'is_transport' => $package->is_transport,
            'is_flight' => $package->is_flight,
            'is_train' => $package->is_train,
            'is_hotel' => $package->is_hotel,
            'is_meal' => $package->is_meal,
            'is_sightseeing' => $package->is_sightseeing,
            'packageRates' => $packageRates,
        ]];

    }

    public function getAllPackages(Request $request)
    {
        $query = Package::with([
            'religion',
            'themes',
        ])
            ->leftJoin('states', 'packages.destination_state_id', '=', 'states.id')
            ->leftJoin('cities as destination_city', 'packages.destination_city_id', '=', 'destination_city.id')
            ->leftJoin('cities as origin_city', 'packages.origin_city_id', '=', 'origin_city.id')
            ->leftJoin('users', 'packages.user_id', '=', 'users.id')
            ->join('vendors', 'packages.user_id', '=', 'vendors.user_id')
            ->select(
                'packages.*',
                'states.name as state_name',
                'destination_city.city as destination_city_name',
                'origin_city.city as origin_city_name',
                'vendors.name as vendor_name',
            )
            ->addSelect(DB::raw('(SELECT path FROM package_images WHERE package_id = packages.id ORDER BY id ASC LIMIT 1) as first_gallery_image'))
            ->where('packages.status', 1)
            ->where('packages.admin_verified', 1)
            ->where('packages.vendor_verified', 1)
            ->where('vendors.status', 1);

        if ($request->has('featured') && $request->featured == 1) {
            // Fetch only featured packages
            $query->where('packages.featured', true);
        }

        // Add DISTINCT to ensure unique records
        $query->distinct();

        $packages = $query->get();


        return $packages;
    }

    public function getComparePackagelist(array $packageIds)
    {
        $compareList = [];


        $packages = Package::with([
            'religion',
            'themes',
        ])
            ->leftjoin('states', 'packages.destination_state_id', '=', 'states.id')
            ->leftjoin('cities', 'packages.destination_city_id', '=', 'cities.id')
            //->leftJoin('package_images', 'packages.id', '=', 'package_images.package_id')
            ->leftjoin('users', 'packages.user_id', '=', 'users.id')
            ->join('vendors', 'packages.user_id', '=', 'vendors.user_id')
            ->leftJoin('package_images', function ($join) {
                $join->on('packages.id', '=', 'package_images.package_id')
                    ->whereRaw('package_images.id = (select min(id) from package_images where package_id = packages.id)');
            })
            ->select(
                'packages.*',
                'states.name as state_name',
                'cities.city as cities_name',
                'vendors.name as vendor_name',
                'package_images.path as featured_image_path'
            )
            ->whereIn('packages.id', $packageIds)
            ->where('packages.status', 1)
            ->where('packages.status', 1)
            ->where('packages.admin_verified', 1)
            ->where('packages.vendor_verified', 1)
            ->where('vendors.status', 1)
            ->get();

        return $packages;

    }

    public function vendorPackageList(Request $request)
    {
        $query = Package::with([
            'religion',
            'themes',
        ])
            ->leftjoin('states', 'packages.destination_state_id', '=', 'states.id')
            ->leftjoin('cities', 'packages.destination_city_id', '=', 'cities.id')
            ->leftjoin('users', 'packages.user_id', '=', 'users.id')
            ->leftJoin('vendors', 'packages.user_id', '=', 'vendors.user_id')
            ->select(
                'packages.*',
                'states.name as state_name',
                'cities.city as cities_name',
                'vendors.name as vendor_name',
            );

        if ($request->has('featured') && $request->featured == 1) {

            $query->where('packages.featured', true);
        }

        if ($request->has('status')) {


            if ($request->status == 2) {
                $query->where('packages.admin_verified', 0);
                $query->where('packages.vendor_verified', 0);
            } elseif ($request->status == 3) {
                $query->where('packages.admin_verified', 1);
                $query->where('packages.vendor_verified', 0);
            } else {
                $query->where('packages.status', $request->status);
            }
        }
        if ($request->has('admin_verified')) {

            $query->where('packages.admin_verified', $request->admin_verified);
        }
        if ($request->has('vendor_verified')) {

            $query->where('packages.vendor_verified', $request->vendor_verified);
        }
        if ($request->has('search')) {

            $query->where('packages.name', 'like', '%' . $request->search . '%');
        }
        if (auth()->check()) {

            $query->where('packages.user_id', auth()->id());
        }
        $query->where('packages.status', '!=', 3);
        $query->orderBy('packages.updated_at', 'desc');
        $packages = $query->get();
        // View the generated SQL query
        // $sql = $query->toSql();
        //  dd($packages); // Output the SQL query to the screen

        return $packages;
    }

    public function fetchFilterPackages($filters)
    {
        $query = Package::with([
            'religion',
            'themes',
        ])
            ->leftjoin('states', 'packages.destination_state_id', '=', 'states.id')
            ->leftjoin('cities as destination_city', 'packages.destination_city_id', '=', 'destination_city.id')
            ->leftjoin('cities as origin_city', 'packages.origin_city_id', '=', 'origin_city.id')
            ->leftjoin('users', 'packages.user_id', '=', 'users.id')
            ->join('vendors', 'packages.user_id', '=', 'vendors.user_id')
            ->select(
                'packages.*',
                'states.name as state_name',
                'destination_city.city as destination_city_name',
                'origin_city.city as origin_city_name',
                'vendors.name as vendor_name',
            )
            ->addSelect(DB::raw('(SELECT path FROM package_images WHERE package_id = packages.id ORDER BY id ASC LIMIT 1) as featured_image_path'))
            ->addSelect(DB::raw('(SELECT MAX(rating) as rating FROM package_hotel WHERE package_id = packages.id LIMIT 1) as rating'))
            ->where('packages.status', 1)
            ->where('packages.admin_verified', 1)
            ->where('packages.vendor_verified', 1)
            ->where('vendors.status', 1);


        $destinationId = $filters['destination_id'] ?? null;
        $destinationType = $filters['destination_type'] ?? null;
        if ($destinationId && $destinationType) {
            switch ($destinationType) {
                case 'region':
                    $details = Region::find($destinationId);
                    $packages = Package::where('region_id', $details->id)->pluck('id');
                    $query->whereIn('packages.id', $packages);
                    break;
                case 'state':
                    $details = State::find($destinationId);
                    $packages = Package::where('destination_state_id', $details->id)->pluck('id');
                    $places = Itinerary::whereIn('package_id', $packages)
                        ->join('cities', 'itineraries.place_name', '=', 'cities.id')
                        ->groupBy('itineraries.place_name')
                        ->pluck('itineraries.place_name');
                    $packages = Itinerary::whereIn('place_name', $places)->groupBy('package_id')->pluck('package_id');
                    $query->whereIn('packages.id', $packages);
                    break;
                case 'city':
                    $details = City::find($destinationId);
                    $packages = Itinerary::where('place_name', $details->id)->groupBy('package_id')->pluck('package_id');
                    $query->whereIn('packages.id', $packages);
                    break;
                default:
                    // Handle the case if destination type is none of the specified types
                    break;
            }


        }

        if (isset($filters['filter_places']) && $filters['filter_places'] !== '') {
            $places = is_array($filters['filter_places']) ? $filters['filter_places'] : [$filters['filter_places']];

            $query->whereHas('itineraries', function ($query) use ($places) {
                $query->whereIn('place_name', $places);
            });
        }
        if (isset($filters['filter_origins']) && $filters['filter_origins'] !== '') {
            $originCityIds = is_array($filters['filter_origins']) ? $filters['filter_origins'] : [$filters['filter_origins']];
            $query->whereIn('origin_city_id', $originCityIds);
        }

        if (isset($filters['filter_keywords'])) {

            $keywords = is_array($filters['filter_keywords']) ? $filters['filter_keywords'] : [$filters['filter_keywords']];
            $query->where(function ($query) use ($keywords) {
                $first = true;
                foreach ($keywords as $keyword) {
                    $locationKeywordName = LocationKeyword::where('id', $keyword)->pluck('name');
                    if ($first) {
                        $query->where(function ($query) use ($locationKeywordName) {
                            $query->orWhereRaw('FIND_IN_SET(?, packages.keywords) > 0', [$locationKeywordName]);
                        });
                        $first = false;
                    } else {
                        $query->orWhere(function ($query) use ($locationKeywordName) {
                            $query->orWhereRaw('FIND_IN_SET(?, packages.keywords) > 0', [$locationKeywordName]);
                        });
                    }
                }
            });
        }

        if (isset($filters['transportations'])) {
            $transportations = $filters['transportations'];

            $query->where(function ($query) use ($transportations) {
                foreach ($transportations as $transportation) {
                    if ($transportation === 'flight') {
                        $query->orWhere('is_flight', 1);
                    } elseif ($transportation === 'train') {
                        $query->orWhere('is_train', 1);
                    }
                }
            });
        }

        if (isset($filters['hotel_stars'])) {
            $hotelStars = $filters['hotel_stars'];

            if (in_array('5', $hotelStars)) {
                $query->orHavingRaw('rating = ?', [5]);
            }
            if (in_array('4', $hotelStars)) {
                $query->orHavingRaw('rating = ?', [4]);
            }
            if (in_array('3', $hotelStars)) {
                $query->orHavingRaw('rating <= ?', [3]);
            }
        }

        if (isset($filters['religion_id'])) {
            $religionId = is_array($filters['religion_id']) ? $filters['religion_id'] : [$filters['religion_id']];
            $query->whereIn('religion_id', $religionId);
        }

        if (isset($filters['trip_id'])) {
            $tripId = is_array($filters['trip_id']) ? $filters['trip_id'] : [$filters['trip_id']];
            $query->whereIn('tour_type', $tripId);
        }

        if (isset($filters['type_of_tour_packages_id'])) {
            $type_of_tour_packages_id = is_array($filters['type_of_tour_packages_id']) ? $filters['type_of_tour_packages_id'] : [$filters['type_of_tour_packages_id']];
            $query->whereIn('trip_type', $type_of_tour_packages_id);
        }

        if (isset($filters['themes_id'])) {
            $themes_id = is_array($filters['themes_id']) ? $filters['themes_id'] : [$filters['themes_id']];
            $query->whereIn('themes_id', $themes_id);
        }

        if (isset($filters['location'])) {
            $location = is_array($filters['location']) ? $filters['location'] : [$filters['location']];
            $query->whereIn('location', $location);
        }

        if (isset($filters['keyword'])) {
            $keywords = explode(',', $filters['keyword']);


            foreach ($keywords as $keyword) {
                $query->orWhere('keywords', 'LIKE', '%' . trim($keyword) . '%');
            }
        }

        if (isset($filters['total_days'])) {
            $totalDaysRange = explode('-', $filters['total_days']);
            $query->whereBetween('total_days', $totalDaysRange);
        }

        if (isset($filters['duration'])) {
            $duration = (int)$filters['duration'];
            $query->where('total_days', $duration);
        }
        if (isset($filters['similar_duration'])) {
            $duration = (int)$filters['similar_duration'];
            $query->where('total_days', '!=', $duration);
        }

        if (isset($filters['starting_price'])) {
            $totalstarting_priceRange = explode('-', $filters['starting_price']);
            $query->whereBetween('starting_price', $totalstarting_priceRange);
        }
        if (isset($filters['is_train'])) {
            $isTrain = $filters['is_train'];
            $query->where('is_train', $isTrain);
        }
        if (isset($filters['is_flight'])) {
            $isFlight = $filters['is_flight'];
            $query->where('is_flight', $isFlight);
        }

        //        if (isset($filters['slot_price']) && is_array($filters['slot_price'])) {
        //            $first = true;
        //            foreach ($filters['slot_price'] as $slotRange) {
        //                $range = explode('-', $slotRange);
        //                if ($first) {
        //                    $query->whereBetween('starting_price', $range);
        //                    $first = false;
        //                } else {
        //                    $query->orWhere(function ($query) use ($range) {
        //                        $query->whereBetween('starting_prices', $range);
        //                    });
        //                }
        //            }
        //        }

        if (isset($filters['slot_price']) && is_array($filters['slot_price'])) {
            $query->where(function ($query) use ($filters) {
                $first = true;
                foreach ($filters['slot_price'] as $slotRange) {
                    $range = explode('-', $slotRange);
                    if ($first) {
                        $query->where(function ($query) use ($range) {
                            $query->whereBetween('starting_price', $range);
                        });
                        $first = false;
                    } else {
                        $query->orWhere(function ($query) use ($range) {
                            $query->whereBetween('starting_price', $range);
                        });
                    }
                }
            });
        }

        if (isset($filters['from_city_id'])) {
            $originCityId = $filters['from_city_id'];
            $query->where('origin_city_id', $originCityId);
            //            $originCity = City::find($originCityId);
            //            $query->where(function ($query) use ($originCityId, $originCity) {
            //                $query->where('origin_city_id', $originCityId)
            //                    ->orWhere('origin_city_id', function ($query) use ($originCityId, $originCity) {
            //                        $query->select('id')
            //                            ->from('cities')
            //                            //->where('state_id', $originCity->state_id)
            //                            ->where('id', '!=', $originCityId)
            //                            ->where('packages', '>', 0)
            //                            ->orderByRaw('SQRT(POW((lat - (SELECT lat FROM cities WHERE id = ?)), 2) + POW((lng - (SELECT lng FROM cities WHERE id = ?)), 2))', [$originCityId, $originCityId])
            //                            ->limit(1);
            //                    });
            //            });
        }

        $packages = $query->get();

        return $packages;
    }

    public function fetchRelatedPackages($packageId)
    {
        $mainPackage = Package::findOrFail($packageId);

        $places = Itinerary::where('package_id', $packageId)
            ->join('cities', 'itineraries.place_name', '=', 'cities.id')
            ->groupBy('itineraries.place_name')
            ->pluck('itineraries.place_name');
        $packageIds = Itinerary::whereIn('place_name', $places)->where('package_id', '!=', $packageId)->groupBy('package_id')->pluck('package_id');

        return Package::with([
            'religion',
            'themes',
        ])
            ->leftjoin('states', 'packages.destination_state_id', '=', 'states.id')
            ->leftjoin('cities', 'packages.destination_city_id', '=', 'cities.id')
            ->leftjoin('users', 'packages.user_id', '=', 'users.id')
            ->join('vendors', 'packages.user_id', '=', 'vendors.user_id')
            ->select(
                'packages.*',
                'states.name as state_name',
                'cities.city as cities_name',
                'vendors.name as vendor_name',
            )
            ->addSelect(DB::raw('(SELECT path FROM package_images WHERE package_id = packages.id ORDER BY id ASC LIMIT 1) as first_gallery_image'))
            ->where('packages.status', 1)
            ->where('packages.admin_verified', 1)
            ->where('packages.vendor_verified', 1)
            ->whereIn('packages.id', $packageIds)
            ->where('vendors.status', 1)
            ->distinct()
            ->get();

    }

    public function getPackageDetails($id)
    {
        $packageDetails = Package::with(['religion', 'themes', 'cityStayPlans', 'inclusions', 'exclusions', 'bulkDiscounts'])
            ->leftJoin('states', 'packages.destination_state_id', '=', 'states.id')
            ->leftJoin('cities', 'packages.destination_city_id', '=', 'cities.id')
            ->leftJoin('users', 'packages.user_id', '=', 'users.id')
            ->join('vendors', 'packages.user_id', '=', 'vendors.user_id')
            ->where('packages.id', $id)
            ->where('vendors.status', 1)
            ->select(
                'packages.*',
                'states.name as state_name',
                'cities.city as cities_name',
                'vendors.name as vendor_name',
            )
            ->groupBy('packages.id', 'vendors.name')
            ->first();
            if (!$packageDetails) {
                return null; // Return null if package is not found
            }

//dd( $packageDetails);
      $packageDetails->cancellation_policy =  isset($packageDetails->cancellation_policy) ? explode(',', $packageDetails->cancellation_policy) : '';
        $packageDetails->payment_policy = explode(',', $packageDetails->payment_policy);

        // fetch stay plan data according itineraries
        $packageDetails->stay_plan = $packageDetails->getItineraries($id);
        $terms = Config::where('name', 'terms_and_conditions')->first();
        $packageDetails->default_terms_and_condition = $terms->value;

        return $packageDetails;
    }

    public function editPackage(array $request)
    {


        $package = Package::findOrFail($request['package_id']);

        $validator = Validator::make($request, [
            'cancellation_policy' => ['required', new CancellationPolicyValidation],
            'payment_policy' => ['required', new PaymentPolicyValidation],
        ]);

        if ($validator->fails()) {
            return ['res' => false, 'msg' => $validator->errors()->first(), 'data' => []];
        }


        $originCityId = $request['origin'];
        $originCity = City::find($originCityId);
        if ($request['origin'] != $package->origin_city_id) {
            City::where('id', $package->origin_city_id)
                ->decrement('packages', 1);

            // if ($request['status'] == 1) {
            //     $originCity->increment('packages', 1);
            // }
        }


        // Update package details
        $package->update([
            'name' => $request['name'],
            'total_days' => $request['total_days'],
            'origin_state_id' => $originCity->state_id,
            'origin_city_id' => $originCityId,
            'destination_state_id' => $request['destination_state_id'],
            'destination_city_id' => $request['destination_city_id'],
            'tour_type' => $request['tour_type'] ?? null,
            'trip_type' => $request['tour_type'] ?? null,
            'themes_id' => $request['themes_id'] ?? null,
            'overview' => $request['overview'] ?? null,
            'terms_and_condition' => $request['terms_and_condition'] ?? null,
            'cancellation_policy' => implode(',', $request['cancellation_policy']) ?? null,
            'payment_policy' => implode(',', $request['payment_policy']) ?? null,
            'keywords' => $request['keywords'] ?? null,
            'total_seat' => $request['total_seat'] ?? null,
            'bulk_no_of_pax' => $request['bulk_no_of_pax'] ?? 0,
            'pax_discount_percent' => $request['pax_discount_percent'] ?? 0,
            'starting_price' => $request['starting_price'] ?? null,
            'child_price' => $request['child_price'] ?? null,
            'infant_price' => $request['infant_price'] ?? null,
            'single_occupancy_price' => $request['single_occupancy_price'] ?? null,
            'triple_occupancy_price' => $request['triple_occupancy_price'] ?? null,
            'religion_id' => $request['religion_id'] ?? null,
            'tour_circuit' => $request['tour_circuit'] ?? null,
            'region_id' => $request['region_id'] ?? null,
        ]);

        // if($package->admin_verified == 1 && $package->vendor_verified == 1){
        //     $package->update([
        //         'admin_verified' => 0,
        //         'vendor_verified' => 0,
        //     ]);
        // }
        if (isset($request['bulk_discount_tiers'])) {
            // Update bulk discount tiers for the package
            $package->bulkDiscounts()->delete(); // Clear existing tiers

            foreach ($request['bulk_discount_tiers'] as $tier) {
                if (isset($tier['pax']) && isset($tier['discount'])) {
                    $package->bulkDiscounts()->create([
                        'min_pax' => $tier['pax'],
                        'discount' => $tier['discount'],
                    ]);
                }
            }
        }

        // Update Handle featured image
//        if (isset($request['featured_image_path'])) {
//            $featuredImage = $request['featured_image_path'];
//
//            // Check if the file is an instance of UploadedFile
//            if ($featuredImage instanceof \Illuminate\Http\UploadedFile && $featuredImage->isValid()) {
//                $filename = time() . '_' . $featuredImage->getClientOriginalName();
//                $featuredImage->storeAs('public/uploads', $filename);
//                $package->update(['featured_image' => 'uploads/' . $filename]);
//            } else {
//                // Log or handle the error
//                return response()->json(['error' => 'Invalid or corrupted file for featured image'], 400);
//            }
//        } else {
//            $package->update(['featured_image' => null]);
//            // return response()->json(['error' => 'Featured image not provided'], 400);
//        }

        // Update Handle gallery images
        if (isset($request['gallery_images'])) {
            // Iterate through the new gallery images
            foreach ($request['gallery_images'] as $image) {
                $filename = uniqid() . '.' . $image->getClientOriginalExtension();
                $path = 'gallery_images/' . $package->id . '/' . $filename;

                // Store the new image
                $image->storeAs('public', $path);

                // Create a new record for the gallery image
                PackageImage::create([
                    'package_id' => $package->id,
                    'path' => $path,
                ]);
            }
        }

        //Update  Handle media links
        if (isset($request['media_links'])) {
            PackageMedia::where('package_id', $package->id)->delete();
            foreach ($request['media_links'] as $mediaLink) {

                PackageMedia::create([
                    'package_id' => $package->id,
                    'media_link' => $mediaLink,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Update Handle addons
        if (isset($request['addons'])) {
            PackageAddon::where('package_id', $package->id)->delete();

            foreach ($request['addons'] as $addon) {

                PackageAddon::create([
                    'package_id' => $package->id,
                    'title' => $addon['title'],
                    'description' => $addon['description'],
                    'price' => $addon['price'],
                    'created_at' => now(),
                    'updated_at' => now(),

                ]);
            }
        }
        // Update Handle package rate
        $packageRates = [];
        if (isset($request['rates'])) {
            PackageRate::where('package_id', $package->id)->delete();
            foreach ($request['rates'] as $rate) {
                $createdRate = PackageRate::create([
                    'package_id' => $package->id,
                    'start_date' => $rate['start_date'],
                    'end_date' => $rate['end_date'],
                    'price' => $rate['price'],
                ]);
                $packageRates[] = $createdRate->toArray();
            }
        }


        // Update Handle seat availability update
        if (isset($request['seat_availability'])) {
            $packageSeatAvailabilityData = [];
            foreach ($request['seat_availability'] as $availability) {
                PackageSeatAvailability::updateOrCreate(
                    [
                        'package_id' => $package->id,
                        'date' => $availability['date']
                    ],
                    [
                        'seat' => $availability['seat'],
                        'cost' => $availability['cost'],
                    ]
                );
//                // Check if the availability data already exists
//                $existingAvailability = PackageSeatAvailability::where('package_id', $package->id)
//                    ->where('date', $availability['date'])
//                    ->first();
//
//                if ($existingAvailability) {
//                    // Update existing availability data
//                    $existingAvailability->update([
//                        'seat' => $availability['seat'],
//                        'cost' => $availability['cost'],
//                    ]);
//                } else {
//                    // Create new availability data
//                    $packageSeatAvailabilityData[] = [
//                        'package_id' => $package->id,
//                        'date' => $availability['date'],
//                        'seat' => $availability['seat'],
//                        'cost' => $availability['cost'],
//                    ];
//                }
            }

            // Insert new availability data
            if (!empty($packageSeatAvailabilityData)) {
                PackageSeatAvailability::insert($packageSeatAvailabilityData);
            }
        }


        // Update Handle seat availability update
        if (isset($request['seat_unavailable'])) {
            $packageUnavailableDate = [];
            foreach ($request['seat_unavailable'] as $unavailability) {
                PackageUnavailableDate::updateOrCreate(
                    [
                        'package_id' => $package->id,
                        'date' => $unavailability['date']
                    ],

                );

            }

            // Insert new availability data
            if (!empty($packageUnavailableDate)) {
                PackageUnavailableDate::insert($packageUnavailableDate);
            }
        }


        Itinerary::where('package_id', $package->id)->delete();
        // Handle itinerary update
        if (isset($request['itinerary'])) {
            foreach ($request['itinerary'] as $itinerary) {

                // Insert new itinerary
                $newItinerary = Itinerary::create([
                    'package_id' => $package->id,
                    'day' => $itinerary['day'],
                    'place_name' => $itinerary['place_name'] ?? null,
                    'itinerary_title' => $itinerary['itinerary_title'] ?? null,
                    'itinerary_description' => $itinerary['itinerary_description'] ?? null,
                    'meal' => $itinerary['meal'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Insert flights
                if (isset($itinerary['flights'])) {
                    $package->update(['is_flight' => 1]);
                    foreach ($itinerary['flights'] as $flight) {
                        PackageFlight::create([
                            'itinerary_id' => $newItinerary->id,
                            'package_id' => $request['package_id'],
                            'depart_destination' => $flight['depart_destination'],
                            'arrive_destination' => $flight['arrive_destination'],
                            'depart_datetime' => $flight['depart_datetime'] ?? null,
                            'arrive_datetime' => $flight['arrive_datetime'] ?? null,
                            'number_of_nights' => $flight['number_of_nights'] ?? 0,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }

                // Insert trains
                if (isset($itinerary['trains'])) {
                    $package->update(['is_train' => 1]);
                    foreach ($itinerary['trains'] as $train) {
                        PackageTrain::create([
                            'itinerary_id' => $newItinerary->id,
                            'package_id' => $request['package_id'],
                            'train_name' => $train['train_name'],
                            'train_number' => $train['train_number'],
                            'class' => $train['class'],
                            'from_station' => $train['from_station'],
                            'to_station' => $train['to_station'],
                            'depart_datetime' => $train['depart_datetime'] ?? null,
                            'arrive_datetime' => $train['arrive_datetime'] ?? null,
                            'number_of_nights' => $train['number_of_nights'] ?? 0,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }

                // Insert local transports
                if (isset($itinerary['local_transports'])) {
                    $package->update(['is_transport' => 1]);
                    foreach ($itinerary['local_transports'] as $transport) {
                        PackageLocalTransport::create([
                            'itinerary_id' => $newItinerary->id,
                            'package_id' => $request['package_id'],
                            'car' => $transport['car'],
                            'model' => $transport['model'],
                            'capacity' => $transport['capacity'],
                            'AC' => $transport['AC'],
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }


                // Insert hotels
                if (isset($itinerary['hotel'])) {
                    $package->update(['is_hotel' => 1]);
                    $packageHotelGalleryImages = [];
                    foreach ($itinerary['hotel'] as $hotel) {
                        $lasthotelInsertedId = DB::table('package_hotel')->insertGetId([
                            'itinerary_id' => $newItinerary->id,
                            'name' => $hotel['name'],
                            'rating' => $hotel['rating'],
                            'is_other_place' => $hotel['is_other_place'],
                            'place_name' => $hotel['place_name'],
                            'distance_from_main_town' => $hotel['distance_from_main_town'],
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        if (isset($hotel['gallery_images'])) {
                            foreach ($hotel['gallery_images'] as $index => $image) {
                                Log::info("Processing image {$index}", ['image' => $image]);

                                $filename = uniqid() . '.' . $image->getClientOriginalExtension();
                                $path = 'hotel_gallery_images/' . $package->id . '/' . $filename;

                                // Create directory if not exists
                                $directory = storage_path('app/public/' . dirname($path));
                                if (!File::exists($directory)) {
                                    File::makeDirectory($directory, 0755, true, true);
                                }

                                // Move the file to the storage path
                                $image->move($directory, $filename);

                                // Save information to the database
                                $packageHotelGalleryImages[] = [
                                    'hotel_id' => $lasthotelInsertedId,
                                    'path' => $path,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ];
                            }
                        }
                    }
                    if (!empty($packageHotelGalleryImages)) {
                        PackageHotelGalleryImage::insert($packageHotelGalleryImages);
                    }
                }

                // Insert sightseeing
                if (isset($itinerary['siteseeing'])) {
                    $package->update(['is_sightseeing' => 1]);
                    $packageSiteseeingGalleryImages = [];
                    foreach ($itinerary['siteseeing'] as $sightseeing) {
                        $lastsightseeingInsertedId = DB::table('package_sightseeing')->insertGetId([
                            'itinerary_id' => $newItinerary->id,
                            'morning' => $sightseeing['morning'] ?? null,
                            'afternoon' => $sightseeing['afternoon'] ?? null,
                            'evening' => $sightseeing['evening'] ?? null,
                            'night' => $sightseeing['night'] ?? null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        if (isset($sightseeing['gallery_images'])) {
                            $directoryPath = storage_path('app/public/siteseeing_gallery_images/' . $package->id);

                            // Remove existing images from the directory
                            if (File::exists($directoryPath)) {
                                File::deleteDirectory($directoryPath);
                            }
                            foreach ($sightseeing['gallery_images'] as $image) {
                                $filename = uniqid() . '.' . $image->getClientOriginalExtension();
                                $path = 'siteseeing_gallery_images/' . $package->id . '/' . $filename;

                                // Manually move the file to the storage path
                                File::makeDirectory(storage_path('app/public/' . dirname($path)), 0755, true, true);
                                $image->move(storage_path('app/public/' . dirname($path)), $filename);

                                // Save information to the database
                                $packageSiteseeingGalleryImages[] = [
                                    'sightseeing_id' => $lastsightseeingInsertedId,
                                    'path' => $path,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ];
                            }
                        }
                    }
                    if (!empty($packageSiteseeingGalleryImages)) {
                        PackageSightseeingGallery::insert($packageSiteseeingGalleryImages);
                    }
                }


            }


        }


        // Handle inclusion, exclusion list
        PackageInclusion::where('package_id', $package->id)->delete();

        if (isset($request['inclusion_list'])) {
            foreach ($request['inclusion_list'] as $inclusion) {
                PackageInclusion::create([
                    'package_id' => $package->id,
                    'name' => $inclusion['text'],
                ]);
            }
        }

        PackageExclusion::where('package_id', $package->id)->delete();

        if (isset($request['exclusion_list'])) {
            foreach ($request['exclusion_list'] as $exclusion) {
                PackageExclusion::create([
                    'package_id' => $package->id,
                    'name' => $exclusion['text'],
                ]);
            }
        }

        // Update is_meal if meal is not null
        if (isset($itinerary['meal']) && $itinerary['meal'] !== null) {
            $package->update(['is_meal' => 1]);
        }

        return ['res' => true, 'msg' => 'Package edited successfully'];
    }

    public function vendorApprovalUpdate(Package $package)
    {
        if ($package->admin_verified == 1) {
            $package->update([
                'vendor_verified' => 1,
                'status' => 1,
                'vendor_verified_at' => now()
            ]);

            return ['res' => true, 'msg' => 'Verified successfully'];
        } else {
            return ['res' => false, 'msg' => 'Admin verification is required'];
        }
    }

    public function getPackageCountsByState(Request $request)
    {
        try {

            $requestedDays = $request->input('total_days', 0);

            $packageCounts = Package::join('bookings', 'packages.id', '=', 'bookings.package_id')
                ->leftJoin('package_images as first_image', function ($join) {
                    $join->on('packages.id', '=', 'first_image.package_id')
                        ->whereRaw('first_image.id = (SELECT id FROM package_images WHERE package_id = packages.id ORDER BY id ASC LIMIT 1)');
                })
                ->leftJoin('vendors', 'packages.user_id', '=', 'vendors.user_id')
                ->select(
                    'packages.id',
                    'packages.name',
                    DB::raw('COUNT(packages.id) as booking_count'),
                    DB::raw('ROUND(MIN(packages.starting_price)) as min_starting_price'),
                    DB::raw('MIN(first_image.path) as last_featured_image_path')
                )
                ->where('packages.status', 1)
                ->where('vendors.status', 1);


            if ($requestedDays > 0) {
                $packageCounts->whereBetween('packages.total_days', [$requestedDays - 2, $requestedDays]);
            }

            $packageCounts = $packageCounts
                ->groupBy('packages.id', 'packages.name')
                ->orderByDesc('booking_count')
                ->get();

            return $packageCounts;
        } catch (\Exception $e) {
            return response()->json(['res' => false, 'msg' => $e->getMessage()], 500);
        }
    }

    public function ____getPopularDestination(Request $request)
    {
        try {

            $lastTwoPackages = Package::orderBy('id', 'desc')
                ->limit(6)
                ->get();

            $lastPackageImages = collect();

            foreach ($lastTwoPackages as $package) {
                $lastGalleryImage = PackageImage::where('package_id', $package->id)
                    ->orderBy('id', 'desc')
                    ->first();

                if ($lastGalleryImage) {
                    $lastPackageImages[$package->destination_state_id] = $lastGalleryImage->path;
                }
            }


            $cityIds = $lastTwoPackages->pluck('destination_city_id');


            $cityData = DB::table('cities')
                ->whereIn('id', $cityIds)
                ->select('id', 'city')
                ->get();


            $stateData = DB::table('states')
                ->whereIn('id', $lastTwoPackages->pluck('destination_state_id'))
                ->select('id', 'name')
                ->get();


            $result = $stateData->map(function ($state) use ($lastPackageImages, $cityData, $lastTwoPackages) {

                $cityId = $lastTwoPackages
                    ->where('destination_state_id', $state->id)
                    ->pluck('destination_city_id')
                    ->first();


                $state->city = $cityData->where('id', $cityId)->pluck('city')->first() ?? null;


                $state->last_package_featured_image_path = $lastPackageImages[$state->id] ?? null;

                return $state;
            });

            return $result;
        } catch (\Exception $e) {
            return response()->json(['res' => false, 'msg' => $e->getMessage()], 500);
        }
    }

    public function getPopularDestination(Request $request)
    {
        try {

            $places = Itinerary::join('cities', 'itineraries.place_name', '=', 'cities.id')
                ->groupBy('itineraries.place_name')
                ->select('itineraries.place_name as place_id', 'cities.city as place_name', DB::raw('COUNT(DISTINCT package_id) as package_count'), DB::raw('MAX(package_id) as max_id'))
                ->orderBy('package_count', 'desc')
                ->limit(6)
                ->get();


            $lastPackageImages = PackageImage::whereIn('package_id', $places->pluck('max_id'))
                ->get()
                ->keyBy('package_id')
                ->map->path;


            $result = $places->map(function ($place) use ($lastPackageImages) {
                return [
                    'destination_id' => $place->place_id,
                    'destination_name' => $place->place_name,
                    'destination_type' => 'city',
                    'last_package_featured_image_path' => $lastPackageImages[$place->max_id] ?? null
                ];
            });
            // dd($result);
            return $result;
        } catch (\Exception $e) {
            return response()->json(['res' => false, 'msg' => $e->getMessage()], 500);
        }
    }

    public function getDestinationsByTheme()
    {
        try {
            $themesData = Themes::leftJoin('packages', 'themes.id', '=', 'packages.themes_id')
                ->select('themes.id as theme_id', 'themes.name', 'themes.image', DB::raw('COUNT(DISTINCT packages.destination_state_id) as destination_state_count'))
                ->groupBy('themes.id', 'themes.name', 'themes.image')
                ->orderBy('themes.id', 'asc')
                ->take(4)
                ->get();

            return $themesData;
        } catch (\Exception $e) {

            throw $e;
        }
    }

    public function getStateList(Request $request)
    {
        $state = State::get();
        return $state;
    }

    public function getCityList(Request $request)
    {
        $query = City::query();


        if ($request->has('state')) {
            $states = $request->input('state');
            $query->whereIn('state_id', $states);
        }


        $cities = $query->get();

        return $cities;
    }


    public function gettripList(Request $request)
    {
        $trip = Trip::get();
        return $trip;
    }

    public function gettypeOfTourPackagesList(Request $request)
    {
        $typeOfTourPackages = TypeOfTourPackages::get();
        return $typeOfTourPackages;
    }

    public function gettypeOfVendorList(Request $request)
    {
        $typeOfVendor = VendorType::get();
        return $typeOfVendor;
    }

    public function getthemesList(Request $request)
    {
        $themes = Themes::get();
        return $themes;
    }

    public function getreligionList(Request $request)
    {
        $religion = Religion::get();
        return $religion;
    }

    public function gettotalday(Request $request)
    {
        $minDay = Package::min('total_days');
        $maxDay = Package::max('total_days');

        return [
            'min_day' => $minDay,
            'max_day' => $maxDay,
        ];
    }

    public function getstartingpriceRange(Request $request)
    {
        $minPrice = round(Package::min('starting_price'));
        $maxPrice = round(Package::max('starting_price'));
        $numSlots = 4;

        $slotRanges = $this->generatePriceSlots($minPrice, $maxPrice, $numSlots);
        $slotCounts = $this->getPackageCountsInSlots($slotRanges);

        return [
            'min_price' => $minPrice,
            'max_price' => $maxPrice,
            // 'slot_ranges' => $slotRanges,
            'slot_counts' => $slotCounts,
        ];
    }

    private function generatePriceSlots($minPrice, $maxPrice, $numSlots)
    {
        $slotRanges = [];
        $slotSize = ($maxPrice - $minPrice) / $numSlots;

        for ($i = 0; $i < $numSlots; $i++) {
            $start = $minPrice + ($i * $slotSize);
            $end = $minPrice + (($i + 1) * $slotSize);

            $slotRanges[] = [
                'start' => round($start),
                'end' => round($end),
            ];
        }

        return $slotRanges;
    }

    private function getPackageCountsInSlots($slotRanges)
    {
        $slotCounts = [];

        foreach ($slotRanges as $slot) {
            $count = Package::whereBetween('starting_price', [$slot['start'], $slot['end']])->count();
            $slotCounts[] = [
                'start' => $slot['start'],
                'end' => $slot['end'],
                'count' => $count,
            ];
        }

        return $slotCounts;
    }


    public function updatePackageStatus(array $packageIds, int $status)
    {
        try {

            DB::table('packages')
                ->whereIn('id', $packageIds)
                ->update(['status' => $status]);

            return true;
        } catch (\Exception $e) {

            throw new \Exception('Failed to update package status: ' . $e->getMessage());
        }
    }


    public function getAddonsByPackageId($packageId)
    {
        return PackageAddon::where('package_id', $packageId)->get();
    }

    public function getseatAvailability($packageId)
    {
        return PackageSeatAvailability::where('package_id', $packageId)->get();
    }


    public function fetchWishPackageDetails($package)
    {


        $galleryImages = $package->gallery_images;

        // Ensure $galleryImages is a collection
        if (!($galleryImages instanceof \Illuminate\Support\Collection)) {
            $galleryImages = collect($galleryImages);
        }

        // Retrieve the path of the first gallery image
        $firstGalleryImage = $galleryImages->isNotEmpty() ? $galleryImages->first()['path'] : null;
        $totalNights = $package->total_days - 1;
        return [
            'package_id' => $package->id,
            'package_name' => $package->name,
            'total_days' => "{$totalNights} night" . ($totalNights > 1 ? 's' : '') . " and {$package->total_days} days",
            'starting_price' => round($package->starting_price * (1 + $package->platform_charges / 100)),
            'stay_plan' => $package->getItineraries($package->id),
            'is_transport' => $package->is_transport,
            'is_flight' => $package->is_flight,
            'is_train' => $package->is_train,
            'is_hotel' => $package->is_hotel,
            'is_meal' => $package->is_meal,
            'is_sightseeing' => $package->is_sightseeing,
            'first_gallery_image' => $firstGalleryImage,
        ];
    }

    public function addMessage(array $data, $userId)
    {
        $validator = Validator::make($data, [
            'receiver_id' => 'required|integer',
            'package_id' => 'required|integer',
            'message' => 'required|string',
        ]);


        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }


        $validatedData = $validator->validated();


        $package = Package::find($validatedData['package_id']);
        if (!$package) {
            return response()->json(['error' => 'Package not found'], 404);
        }


        $validatedData['package_name'] = $package->name;
        $validatedData['sender_id'] = $userId; // Set sender ID


        $message = PackageMessage::create($validatedData);


        return response()->json([
            'res' => true,
            'data' => $message,
        ], 200);
    }

    public function messageList(array $data, $userId)
    {
        $messages = PackageMessage::where('package_id', $data['package_id'])->get();
        if ($messages->isEmpty()) {
            return response()->json(['error' => 'Messages not found'], 404);
        }
        $readCount = $messages->where('is_read', '0')->count();

        return response()->json([
            'res' => true,
            'data' => $messages,
            'read_count' => $readCount,
        ], 200);
    }

    public function messageView(array $data, $userId)
    {
        $messages = PackageMessage::where('package_id', $data['package_id'])->get();

        if ($messages->isEmpty()) {
            return response()->json(['error' => 'Messages not found'], 404);
        }

        // Iterate over each message
        foreach ($messages as $message) {
            // Check if the message is intended for the authenticated user
            if ($message->receiver_id == $userId) {
                // Mark the message as read if it's intended for the user and unread
                if ($message->is_read == '0') {
                    $message->is_read = '1';
                    $message->save();
                }
            }
        }

        // Return the updated messages
        return response()->json([
            'res' => true,
            'data' => $messages,
        ], 200);
    }
}
