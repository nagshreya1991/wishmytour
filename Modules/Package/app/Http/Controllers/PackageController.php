<?php

namespace Modules\Package\app\Http\Controllers;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Modules\Package\app\Http\Services\PackageService;
use Modules\Package\app\Models\City;
use Modules\Package\app\Models\Inclusion;
use Modules\Package\app\Models\Exclusion;
use Modules\Package\app\Models\Itinerary;
use Modules\Package\app\Models\Package;
use Modules\Package\app\Models\PackageAddon;
use Modules\Package\app\Models\PackageExclusion;
use Modules\Package\app\Models\PackageFlight;
use Modules\Package\app\Models\PackageImage;
use Modules\Package\app\Models\PackageHotel;
use Modules\Package\app\Models\PackageHotelGalleryImage;
use Modules\Package\app\Models\PackageInclusion;
use Modules\Package\app\Models\PackageLocalTransport;
use Modules\Package\app\Models\PackageMedia;
use Modules\Package\app\Models\PackageSeatAvailability;
use Modules\Package\app\Models\PackageSightseeing;
use Modules\Package\app\Models\PackageTrain;
use Modules\Package\app\Models\Region;
use Modules\Package\app\Models\State;
use Modules\Package\app\Models\TourismCircuit;
use Modules\Package\app\Models\Wishlist;
use Modules\Package\app\Models\PackageMessage;
use Modules\Package\app\Models\PackageSightseeingGallery;
use Modules\Package\app\Models\PackageRate;
use Modules\User\app\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PackageController extends Controller
{
    private $packageService;

    public function __construct(PackageService $packageService)
    {
        $this->packageService = $packageService;
    }

    public function addPackage(Request $request)
    {

        if (auth()->check()) {
    
            if (auth()->user()->user_type == 3) {

                $result = $this->packageService->addPackage($request->all());

                return response()->json($result);
            } else {
                return response()->json(['error' => 'User type not authorized to add packages'], 403);
            }
        } else {

            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    public function editPackage(Request $request)
    {
        if (auth()->check()) {
            $packageCreatedBy = Package::where('id', $request->package_id)->pluck('user_id')->first();
            if (auth()->user()->user_type == 3 && auth()->user()->id === $packageCreatedBy) {
                $result = $this->packageService->editPackage($request->all());//dd($result);
                return response()->json($result);
            } else {
                return response()->json(['error' => 'You are not authorized'], 403);
            }
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    public function vendorApprovalUpdate(Request $request)
    {
        if (auth()->check()) {
            $user = Auth::user();
    
            $packageId = $request->input('package_id');
    
            $package = Package::find($packageId);
    
            if (!$package) {
                return response()->json(['error' => 'Package not found'], 404);
            }
    
            // Check if the authenticated user has the correct permissions
            if ($user->user_type == 3 && $package->user_id === $user->id) {
                $result = $this->packageService->vendorApprovalUpdate($package);
    
                return response()->json($result);
            } else {
                return response()->json(['error' => 'User not authorized to edit this package'], 403);
            }
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    public function getPackageList()
    {
        try {
            $packages = $this->packageService->getAllPackages(request());


            $packageList = $packages->map(function ($package) {
                $totalNights = $package->total_days - 1;
                $package->total_nights_count = (int)$totalNights;
                $package->total_days_count = (int)$package->total_days;
                $package->total_train_nights_count = (int)$package->trains()->where('number_of_nights', 1)->count();
                $package->total_days = "{$totalNights} night" . ($totalNights > 1 ? 's' : '') . " and {$package->total_days} days";
                $package->starting_price = round($package->starting_price * (1 + $package->platform_charges / 100));;
                $package->package_name = $package->name;
                $package->package_id = $package->id;
                $package->stay_plan = $package->getItineraries($package->id);
                $package->featured_image_path = $package->first_gallery_image;

                return $package;
            });

            return response()->json(['res' => true, 'msg' => 'Packages retrieved successfully', 'data' => $packageList], 200);
        } catch (Exception $e) {

            return response()->json(['res' => false, 'msg' => $e->getMessage()], 500);
        }
    }

    public function vendorPackageList(Request $request)
    {
        try {
            $packages = $this->packageService->vendorPackageList($request);

            $packageList = $packages->map(function ($package) {
                $statusMap = [
                    0 => "Inactive",
                    1 => "Active",
                    2 => "Approval Pending",
                    3 => "Archive"
                ];
                $venVerifiedMap = [
                    0 => "Inactive",
                    1 => "Active"
                ];
                $adVerifiedMap = [
                    0 => "Inactive",
                    1 => "Active"
                ];
                $originCityName = City::where('id', $package->origin_city_id)->value('city');


                // Check for unread messages
                $unreadMessagesCount = PackageMessage::where('package_id', $package->id)
                    ->where('receiver_id', $package->user_id)
                    ->where('is_read', '0')
                    ->count();
                $hasUnreadMessages = $unreadMessagesCount > 0 ? 1 : 0;
                $totalNights = $package->total_days - 1;
                return [
                    'package_id' => $package->id,
                    'package_name' => $package->name,
                    'total_days' => "{$totalNights}N/{$package->total_days}D",
                    'starting_price' => round($package->starting_price),
                    'website_price' => round($package->starting_price * (1 + $package->platform_charges / 100)),
                    'created_at' => Carbon::parse($package->created_at)->format('d M, Y'),
                    'origin' => $originCityName,
                    'status_text' => $statusMap[$package->status],
                    'status' => $package->status,
                    'vendor_verified_status_text' => $venVerifiedMap[$package->vendor_verified],
                    'vendor_verified_status' => $package->vendor_verified,
                    'admin_verified_status_text' => $adVerifiedMap[$package->admin_verified],
                    'admin_verified_status' => $package->admin_verified
                ];
            });

            return response()->json([
                'res' => true,
                'msg' => 'Packages retrieved successfully',
                'data' => $packageList
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'res' => false,
                'msg' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Fetches destination cities for filter .
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function fetchFilterDestinations(Request $request): JsonResponse
    {
        try {
            $destinationId = $request->destination_id ?? null;
            $destinationType = $request->destination_type ?? null;
            $fromCityId = $request->from_city_id ?? null;

            if ($destinationId === null || $destinationType === null) {
                $places = Itinerary::join('cities', 'itineraries.place_name', '=', 'cities.id')
                    ->groupBy('itineraries.place_name')
                    ->select('itineraries.place_name as place_id', 'cities.city as place_name', DB::raw('COUNT(DISTINCT package_id) as package_count'))
                    ->orderBy('package_count', 'desc')
                    ->limit(20)
                    ->get();
            } else {
                switch ($destinationType) {
                    case 'region':
                        $details = Region::find($destinationId);
                        $packagesQuery = Package::where('region_id', $details->id);
                        break;
                    case 'state':
                        $details = State::find($destinationId);
                        $packagesQuery = Package::where('destination_state_id', $details->id);
                        break;
                    case 'city':
                        $details = City::find($destinationId);
                        $packageIds = Itinerary::where('place_name',$details->id)->groupBy('package_id')->pluck('package_id');
                        $packagesQuery = Package::whereIn('id', $packageIds);
                        break;
                    default:
                        // Handle the case if destination type is none of the specified types
                        break;
                }

                // Check if there are packages available from the origin city
                $hasPackagesFromOriginCity = $packagesQuery->where('origin_city_id', $fromCityId)->exists();

                // Reset the query builder to its initial state
                $packagesQuery = Package::query();

                switch ($destinationType) {
                    case 'region':
                        $details = Region::find($destinationId);
                        $packagesQuery->where('region_id', $details->id);
                        break;
                    case 'state':
                        $details = State::find($destinationId);
                        $packages = Package::where('destination_state_id', $details->id)->pluck('id');
                        $places = Itinerary::whereIn('package_id', $packages)
                            ->join('cities', 'itineraries.place_name', '=', 'cities.id')
                            ->groupBy('itineraries.place_name')
                            ->pluck('itineraries.place_name');
                        $packageIds = Itinerary::whereIn('place_name',$places)->groupBy('package_id')->pluck('package_id');
                        $packagesQuery->whereIn('id', $packageIds);
                        break;
                    case 'city':
                        $details = City::find($destinationId);
                        $packageIds = Itinerary::where('place_name',$details->id)->groupBy('package_id')->pluck('package_id');
                        $packagesQuery->whereIn('id', $packageIds);
                        break;
                    default:
                        // Handle the case if destination type is none of the specified types
                        break;
                }

                // Add condition for fromCityId only if there are packages available from the origin city
                if ($fromCityId !== null && $fromCityId !== '' && $hasPackagesFromOriginCity) {
                    $packagesQuery->where('origin_city_id', $fromCityId);
                }
                $packagesQuery->where('status', 1);
                $packages = $packagesQuery->pluck('id');
                //dd($packages);

                $places = Itinerary::whereIn('package_id', $packages)
                    ->join('cities', 'itineraries.place_name', '=', 'cities.id')
                    ->groupBy('itineraries.place_name')
                    ->select('itineraries.place_name as place_id', 'cities.city as place_name', DB::raw('COUNT(DISTINCT package_id) as package_count'))
                    ->orderBy('package_count', 'desc')
                    ->get();
            }


            return response()->json(['res' => true, 'msg' => 'Keywords retrieved successfully', 'data' => $places], 200);
        } catch (Exception $e) {
            return response()->json(['res' => false, 'msg' => 'Failed to retrieve keywords', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Fetches origin cities for filter .
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function fetchFilterOrigins(Request $request): JsonResponse
    {
        try {
            $destinationId = $request->destination_id;
            $destinationType = $request->destination_type;
            $fromCityId = $request->from_city_id;

            if (!$destinationId || !$destinationType) {
                $uniqueCityIds = $fromCityId !== null && $fromCityId !== '' ? [$fromCityId] : [];
            } else {
                switch ($destinationType) {
                    case 'region':
                        $details = Region::find($destinationId);
                        $packagesQuery = Package::where('region_id', $details->id);
                        break;
                    case 'state':
                        $details = State::find($destinationId);
                        $packages = Package::where('destination_state_id', $details->id)->pluck('id');
                        $places = Itinerary::whereIn('package_id', $packages)
                            ->join('cities', 'itineraries.place_name', '=', 'cities.id')
                            ->groupBy('itineraries.place_name')
                            ->pluck('itineraries.place_name');
                        $packageIds = Itinerary::whereIn('place_name',$places)->groupBy('package_id')->pluck('package_id');
                        $packagesQuery = Package::whereIn('id', $packageIds);
                        break;
                    case 'city':
                        $details = City::find($destinationId);
                        $packageIds = Itinerary::where('place_name',$details->id)->groupBy('package_id')->pluck('package_id');
                        $packagesQuery = Package::whereIn('id', $packageIds);
                        break;
                    default:
                        // Handle the case if destination type is none of the specified types
                        break;
                }

                // Check if there are packages available from the origin city
                $hasPackagesFromOriginCity = $packagesQuery->where('origin_city_id', $fromCityId)->exists();

                $packagesQuery = Package::query();

                switch ($destinationType) {
                    case 'region':
                        $details = Region::find($destinationId);
                        $packagesQuery->where('region_id', $details->id);
                        break;
                    case 'state':
                        $details = State::find($destinationId);
                        $packages = Package::where('destination_state_id', $details->id)->pluck('id');
                        $places = Itinerary::whereIn('package_id', $packages)
                            ->join('cities', 'itineraries.place_name', '=', 'cities.id')
                            ->groupBy('itineraries.place_name')
                            ->pluck('itineraries.place_name');
                        $packageIds = Itinerary::whereIn('place_name',$places)->groupBy('package_id')->pluck('package_id');
                        $packagesQuery->whereIn('id', $packageIds);
                        break;
                    case 'city':
                        $details = City::find($destinationId);
                        $packageIds = Itinerary::where('place_name',$details->id)->groupBy('package_id')->pluck('package_id');
                        $packagesQuery->whereIn('id', $packageIds);
                        break;
                    default:
                        // Handle the case if destination type is none of the specified types
                        break;
                }
                // Add condition for fromCityId only if there are packages available from the origin city
                if ($fromCityId !== null && $fromCityId !== '' && $hasPackagesFromOriginCity) {
                    $packagesQuery->where('origin_city_id', $fromCityId);
                    //$packagesQuery->join('cities as origin_city', 'packages.origin_city_id', '=', 'origin_city.id')
                    //    ->orderByRaw('SQRT(POW((origin_city.lat - (SELECT lat FROM cities WHERE id = ?)), 2) + POW((origin_city.lng - (SELECT lng FROM cities WHERE id = ?)), 2))', [$fromCityId, $fromCityId]);
                }
                $packagesQuery->where('status', 1);

                $uniqueCityIds = $packagesQuery->distinct()->pluck('origin_city_id');
            }
            // Fetch city details for unique origin cities
            if ($fromCityId !== null && $fromCityId !== '') {
                $uniqueCities = City::whereIn('id', $uniqueCityIds)
                    ->select('id', 'city')
                    ->orderByRaw(
                        'SQRT(POW((lat - (SELECT lat FROM cities WHERE id = ?)), 2) + POW((lng - (SELECT lng FROM cities WHERE id = ?)), 2))',
                        [$fromCityId, $fromCityId]
                    )
                    ->get();
            } else {
                $uniqueCities = City::whereIn('id', $uniqueCityIds)->select('id', 'city')->get();
            }

            return response()->json(['res' => true, 'msg' => 'Origin cities retrieved successfully', 'data' => $uniqueCities], 200);
        } catch (Exception $e) {
            return response()->json(['res' => false, 'msg' => 'Failed to retrieve origin cities', 'error' => $e->getMessage()], 500);
        }
    }

    public function filterPackages(Request $request)
    {
        try {
            $filters = $request->all();

            $filteredPackages = $this->packageService->fetchFilterPackages($filters);
            if (isset($filters['from_city_id']) && count($filteredPackages) > 0) {
                $hasPackagesFromOriginCity = 1;
            } else {
                $hasPackagesFromOriginCity = 0;
                $filters['from_city_id'] = null;
                $filteredPackages = $this->packageService->fetchFilterPackages($filters);
            }
            $processedPackages = $filteredPackages->map(function ($package) {
                $totalNights = $package->total_days - 1;
                $package->total_nights_count = (int)$totalNights;
                $package->total_days_count = (int)$package->total_days;
                $package->total_train_nights_count = (int)$package->trains()->where('number_of_nights', 1)->count();
                $package->total_days = "{$totalNights} night" . ($totalNights > 1 ? 's' : '') . " and {$package->total_days} days";
                $package->starting_price = round($package->starting_price * (1 + $package->platform_charges / 100));
                $package->package_id = $package->id;
                $package->package_name = $package->name;
                $package->stay_plan = $package->getItineraries($package->id);

                return $package;
            });
            $processedSimilarPackages = [];
            if (isset($filters['duration']) && $filters['duration'] != null) {
                $filters['similar_duration'] = $filters['duration'];
                $filters['duration'] = null;
                $similarPackages = $this->packageService->fetchFilterPackages($filters);
                $processedSimilarPackages = $similarPackages->map(function ($package) {
                    $totalNights = $package->total_days - 1;
                    $package->total_nights_count = (int)$totalNights;
                    $package->total_days_count = (int)$package->total_days;
                    $package->total_train_nights_count = (int)$package->trains()->where('number_of_nights', 1)->count();
                    $package->total_days = "{$totalNights} night" . ($totalNights > 1 ? 's' : '') . " and {$package->total_days} days";
                    $package->starting_price = round($package->starting_price * (1 + $package->platform_charges / 100));;
                    $package->package_id = $package->id;
                    $package->package_name = $package->name;
                    $package->stay_plan = $package->getItineraries($package->id);

                    return $package;
                });
            }

            $data = [
                "has_package_from_city" => $hasPackagesFromOriginCity,
                "packages" => $processedPackages,
                "similar_packages" => $processedSimilarPackages,
            ];

            return response()->json(['res' => true, 'msg' => 'Packages filtered successfully', 'data' => $data], 200);
        } catch (Exception $e) {

            return response()->json(['res' => false, 'msg' => $e->getMessage()], 500);
        }
    }

    public function comparePackages(Request $request)
    {
        try {
            $packageIds = $request->input('package_ids', []);

            if (count($packageIds) > 4) {
                return response()->json(['res' => false, 'msg' => 'You can only compare up to 4 packages.', 'data' => []], 400);
            }

            $packages = $this->packageService->getComparePackagelist($packageIds);
            $comparePackages = $packages->map(function ($package) {
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
                    'galleryImages' => $package->gallery_images,
                ];
            });

            return response()->json(['res' => true, 'msg' => 'Data retrieved successfully', 'data' => $comparePackages], 200);
        } catch (Exception $e) {
            return response()->json(['res' => false, 'msg' => 'Internal Server Error'], 500);
        }
    }

    public function relatedPackages($packageId)
    {
        try {

            $packages = $this->packageService->fetchRelatedPackages($packageId);
            $relatedPackages = $packages->map(function ($package) {
                $totalNights = $package->total_days - 1;
                return [
                    'package_id' => $package->id,
                    'package_name' => $package->name,
                    'total_days' => "{$totalNights} night" . ($totalNights > 1 ? 's' : '') . " and {$package->total_days} days",
                    'starting_price' => round($package->starting_price * (1 + $package->platform_charges / 100)),
                    'stay_plan' => $package->getItineraries($package->id),
                    'featured_image_path' => $package->first_gallery_image,
                ];
            });

            return response()->json(['res' => true, 'msg' => 'Related packages retrieved successfully', 'data' => $relatedPackages], 200);
        } catch (Exception $e) {
            return response()->json(['res' => false, 'msg' => $e->getMessage()], 500);
        }
    }

    public function packageDetails($id)
    {
       
        try {
            $package = $this->packageService->getPackageDetails($id);


            if (!$package) {
                return response()->json(['res' => false, 'msg' => 'Package not found'], 404);
            }


            // $galleryImages = $package->gallery_images ?? [];
            $galleryImages = [];
            foreach ($package->gallery_images as $image) {
                $galleryImages[] = [
                    'id' => $image['id'],
                    'path' => $image['path'],
                ];
            }
            $mediaLinks = $package->mediaLinks ?? [];
            $addons = $package->addons ?? [];
            $seatAvailability = $package->seatAvailability ?? [];
            $seatUnavailable = $package->seatUnavailable ?? [];
            $itinerary = $package->itinerary ?? [];
            $inclusionList = $package->inclusions ?? [];
            $exclusionList = $package->exclusions ?? [];
            $totalDays = $package->total_days;
            $totalNights = $package->total_days - 1;
            $totalDaysAndNights = "{$totalNights} night" . ($totalNights > 1 ? 's' : '') . " and {$package->total_days} days";

            $inclusionIds = explode(',', $package->inclusions_in_package);


            $inclusions = DB::table('package_inclusions')
                ->whereIn('id', $inclusionIds)
                ->select(['name'])
                ->get();


            $themesIds = explode(',', $package->themes_id);


            $themes = DB::table('themes')
                ->whereIn('id', $themesIds)
                ->select(['id', 'name', 'image', 'icon'])
                ->get();


            // Apply platform charges
            $platformCharges = $package->platform_charges; // Dynamic platform charges

            // Fetch GST rate for platform charges
            $platformChargesGSTRate = Helper::getConfig('platform_charges_gst')->value;

            // Closure to calculate price with platform charges
            $calculatePriceWithCharges = fn($price) => ($price * (1 + ($platformCharges / 100))) * (1 + ($platformChargesGSTRate / 100));

            $packageRates = PackageRate::where('package_id', $package->id)->get()->toArray();
            // Update prices in package rates
            foreach ($packageRates as &$rate) {
                $rate['starting_price'] = $calculatePriceWithCharges($package->starting_price + $rate['price']);
                $rate['single_occupancy_price'] = $calculatePriceWithCharges($package->single_occupancy_price + $rate['price']);
                $rate['triple_occupancy_price'] = $calculatePriceWithCharges($package->triple_occupancy_price + $rate['price']);
            }

            $transformedPackage = [
                'package_id' => $package->id,
                'vendor_name' => $package->vendor_name,
                'package_name' => $package->name,
                'total_days' => $totalDaysAndNights,
                'total_days_count' => (int)$totalDays,
                'total_nights_count' => (int)$totalNights,
                'total_train_nights_count' => (int)$package->trains()->where('number_of_nights', 1)->count(),
                'starting_price' => $calculatePriceWithCharges($package->starting_price),
                'child_price' => $calculatePriceWithCharges($package->child_price),
                'infant_price' => $calculatePriceWithCharges($package->infant_price),
                'single_occupancy_price' => $calculatePriceWithCharges($package->single_occupancy_price),
                'triple_occupancy_price' => $calculatePriceWithCharges($package->triple_occupancy_price),
                'platform_charges' => $package->platform_charges,
                'platform_charges_GST' => $package->platform_charges,
                'website_price' => $calculatePriceWithCharges($package->starting_price),
                'origin' => $package->origin_city_id,
                'destination_state_id' => $package->destination_state_id,
                'state_name' => $package->state_name,
                'destination_city_id' => $package->destination_city_id,
                'cities_name' => $package->cities_name,
                'keywords' => $package->keywords,
                'transportation_name' => isset($package->transportation->name) ? ($package->transportation->name) : null,
                'hotel_star' => isset($package->hotel_star_id) ? ($package->hotel_star_id) : null,
                'religion' => isset($package->religion->name) ? ($package->religion->name) : null,
                'tour_type' => $package->tour_type,
                'trip' => $package->tour_type == 1 ? 'Domestic' : 'International',
                'trip_type' => $package->trip_type,
                'type_of_tour_packages' => $package->trip_type == 1 ? 'Standard' : 'Weekend',
                'themes' => $themes,
                'featured_image_path' => $package->featured_image_path,
                'inclusions_in_package' => $inclusions,
                'overview' => $package->overview,
                'inclusions_list' => $inclusionList,
                'exclusions_list' => $exclusionList,
                'terms_and_condition' => $package->terms_and_condition,
                'default_terms_and_condition' => $package->default_terms_and_condition,
                'cancellation_policy' => $package->cancellation_policy,
                'payment_policy' => $package->payment_policy,
                'total_seat' => $package->total_seat,
                'bulk_no_of_pax' => $package->bulk_no_of_pax,
                'pax_discount_percent' => $package->pax_discount_percent,
                'created_at' => date($package->created_at),
                'gallery_images' => $galleryImages,
                'stay_plan' => $package->stay_plan,
                'bulk_discounts' => $package->bulkDiscounts,
                'media_link' => $mediaLinks,
                'addons' => $addons,
                'seat_availability' => $seatAvailability,
                'seat_unavailable' => $seatUnavailable,
                'tour_circuit' => $package->tour_circuit,
                'region_id' => $package->region_id,
                'status' => $package->status,
                'is_transport' => $package->is_transport,
                'is_flight' => $package->is_flight,
                'is_train' => $package->is_train,
                'is_hotel' => $package->is_hotel,
                'is_meal' => $package->is_meal,
                'is_sightseeing' => $package->is_sightseeing,
                'itinerary' => $this->transformItinerary($package->itinerary),
                'vendor_verified' => $package->vendor_verified,
                'admin_verified' => $package->admin_verified,
                'vendor_verified_at' => Carbon::parse($package->vendor_verified_at)->format('d M, Y, h:m a'),
                'admin_verified_at' => Carbon::parse($package->admin_verified_at)->format('d M, Y, h:m a'),
                'packageRates' => $packageRates,

            ];

            $messages = PackageMessage::where('package_id', $package->id)->get();

            $messagesCount = $messages->where('is_read', '0')->where('receiver_id', $package->user_id)->count();

            return response()->json(['res' => true, 'data' => $transformedPackage, 'messagesCount' => $messagesCount], 200);
        } catch (Exception $e) {
            Log::error('Error occurred while fetching package details: ' . $e->getMessage());

            return response()->json(['res' => false, 'msg' => $e->getMessage()], 500);
        }
    }

    private function transformItinerary($itineraries)
    {

        return $itineraries->map(function ($itinerary) {
            $flights = $itinerary->flight ?? [];
            $trains = $itinerary->train ?? [];
            $local_transport = $itinerary->local_transport ?? [];
            $sightseeing = $itinerary->sightseeing ?? [];
            $hotels = $itinerary->hotel ?? [];
            $transformedHotels = [];
            foreach ($hotels as $hotel) {
                // Get hotel gallery images for each hotel
                //$hotelGalleryImages = PackageHotelGalleryImage::where('hotel_id', $hotel->id)->pluck('id','path')->toArray();
                $hotelGalleryImages = PackageHotelGalleryImage::where('hotel_id', $hotel->id)
                    ->select('id', 'path')
                    ->pluck('path', 'id')
                    ->toArray();

                $galleryImages = [];
                foreach ($hotelGalleryImages as $id => $path) {
                    $galleryImages[] = [
                        'id' => $id,
                        'path' => $path,
                    ];
                }


                $transformedHotels[] = [
                    'hotel_id' => $hotel->id,
                    'hotel_name' => $hotel->name,
                    'is_other_place' => $hotel->is_other_place,
                    'place_name' => $hotel->place_name,
                    'distance_from_main_town' => $hotel->distance_from_main_town,
                    'star' => $hotel->rating,
                    'hotel_gallery' => $galleryImages,
                ];
            }

            // Transform sightseeing
            $transformedSightseeing = [];
            foreach ($sightseeing as $st) {
                $sightseeingGalleryImages = PackageSightseeingGallery::where('sightseeing_id', $st->id)
                    ->select('id', 'path')
                    ->pluck('path', 'id')
                    ->toArray();

                $galleryImages = [];
                foreach ($sightseeingGalleryImages as $id => $path) {
                    $galleryImages[] = [
                        'id' => $id,
                        'path' => $path,
                    ];
                }

                $transformedSightseeing[] = [
                    'sightseeing_id' => $st->id,
                    'morning' => $st->morning,
                    'afternoon' => $st->afternoon,
                    'evening' => $st->evening,
                    'night' => $st->night,
                    'sightseeing_gallery' => $galleryImages,
                ];
            }


            $transformedFlights = [];
            foreach ($flights as $flight) {
                $departCity = City::find($flight['depart_destination']);
                $arriveCity = City::find($flight['arrive_destination']);

                $transformedFlights[] = [
                    'id' => $flight['id'],
                    'depart_destination' => [
                        'id' => $flight['depart_destination'],
                        'name' => $departCity ? $departCity->city : null,
                    ],
                    'arrive_destination' => [
                        'id' => $flight['arrive_destination'],
                        'name' => $arriveCity ? $arriveCity->city : null,
                    ],
                    'depart_datetime' => $flight['depart_datetime'] ?? null,
                    'arrive_datetime' => $flight['arrive_datetime'] ?? null,
                    'number_of_nights' => $flight['number_of_nights'] ?? 0,
                ];
            }
            // Transform trains
            $transformedTrains = [];
            foreach ($trains as $train) {
                $departCity = City::find($train['from_station']);
                $arriveCity = City::find($train['to_station']);

                $transformedTrains[] = [
                    'id' => $train['id'],
                    'depart_destination' => [
                        'id' => $train['from_station'],
                        'name' => $departCity ? $departCity->city : null,
                    ],
                    'arrive_destination' => [
                        'id' => $train['to_station'],
                        'name' => $arriveCity ? $arriveCity->city : null,
                    ],
                    'train_name' => $train['train_name'] ?? null,
                    'train_number' => $train['train_number'] ?? null,
                    'class' => $train['class'] ?? null,
                    'depart_datetime' => $train['depart_datetime'] ?? null,
                    'arrive_datetime' => $train['arrive_datetime'] ?? null,
                    'number_of_nights' => $train['number_of_nights'] ?? 0,
                ];
            }
            $placeName = City::select('city')->find($itinerary->place_name);
            return [
                'itinerary_id' => $itinerary->id,
                'day' => $itinerary->day,
                'place_name' => $itinerary->place_name,
                'place_city_name' => $placeName,
                'itinerary_title' => $itinerary->itinerary_title,
                'itinerary_description' => $itinerary->itinerary_description,
                'meal' => $itinerary->meal,
                'flights' => $transformedFlights,
                //'trains' => $trains,
                'trains' => $transformedTrains,
                'local_transport' => $local_transport,
                'sightseeing' => $transformedSightseeing,
                'hotels' => $transformedHotels,
            ];
        });
    }

    public function vendorPackageDetails($id)
    {
        try {
            if (auth()->check()) {
                $user = Auth::user();
                $package = $this->packageService->getPackageDetails($id);
                if ($user->user_type == 3 && $user->id === $package->user_id) {



                    if (!$package) {
                        return response()->json(['res' => false, 'msg' => 'Package not found'], 404);
                    }


                    // $galleryImages = $package->gallery_images ?? [];
                    $galleryImages = [];
                    foreach ($package->gallery_images as $image) {
                        $galleryImages[] = [
                            'id' => $image['id'],
                            'path' => $image['path'],
                        ];
                    }
                    $mediaLinks = $package->mediaLinks ?? [];
                    $addons = $package->addons ?? [];
                    $seatAvailability = $package->seatAvailability ?? [];
                    $seatUnavailable = $package->seatUnavailable ?? [];
                    $itinerary = $package->itinerary ?? [];
                    $inclusionList = $package->inclusions ?? [];
                    $exclusionList = $package->exclusions ?? [];
                    $totalDays = $package->total_days;
                    $totalNights = $package->total_days - 1;
                    $totalDaysAndNights = "{$totalNights} night" . ($totalNights > 1 ? 's' : '') . " and {$package->total_days} days";


                    $inclusionIds = explode(',', $package->inclusions_in_package);


                    $inclusions = DB::table('package_inclusions')
                        ->whereIn('id', $inclusionIds)
                        ->select(['name'])
                        ->get();


                    $themesIds = explode(',', $package->themes_id);


                    $themes = DB::table('themes')
                        ->whereIn('id', $themesIds)
                        ->select(['id', 'name', 'image', 'icon'])
                        ->get();


                    // Apply platform charges
                    $packagePrice = $package->starting_price;
                    $platformCharges = round($package->starting_price * ($package->platform_charges / 100));
                    $packagePriceWithPlatformCharges = round($packagePrice + $platformCharges);
                    $platformChargesGST = round($platformCharges * (Helper::getConfig('platform_charges_gst')->value / 100));


                    //taxes
                    $gstRate = 0;
                    $tdsRate = 0;
                    $tdsRateProprietors = Helper::getConfig('tds_propieters_partners')->value;
                    $tdsRateDirectors = Helper::getConfig('tds_directors')->value;
                    $tcsRate = Helper::getConfig('tcs')->value;
                    $tdsRateText = "{$tdsRateProprietors}% for proprietors and partners, {$tdsRateDirectors}% for directors";
                    if ($user->vendorDetails) {
                        $gstRate = ($user->vendorDetails->gst_rate !== null) ? $user->vendorDetails->gst_rate : 0;
                        $tdsRate = ($user->vendorDetails->organization_type == 1 || $user->vendorDetails->organization_type == 2) ? $tdsRateProprietors : $tdsRateDirectors;
                    }

                    $totalBeforeFinalGST = $packagePriceWithPlatformCharges + $platformChargesGST;
                    $finalGST = $totalBeforeFinalGST * ($gstRate/100);
                    $totalPrice = $totalBeforeFinalGST + $finalGST;

                    // Fetch package rates
                    $packageRates = PackageRate::where('package_id', $package->id)->get()->toArray();
                    $transformedPackage = [
                        'package_id' => $package->id,
                        'vendor_name' => $package->vendor_name,
                        'package_name' => $package->name,
                        'total_days' => $totalDaysAndNights,
                        'total_days_count' => (int)$totalDays,
                        'total_nights_count' => (int)$totalNights,
                        'starting_price' => $package->starting_price,
                        'child_price' => $package->child_price,
                        'infant_price' => $package->infant_price,
                        'single_occupancy_price' => $package->single_occupancy_price,
                        'triple_occupancy_price' => $package->triple_occupancy_price,
                        'platform_charges' => $package->platform_charges,
                        'platform_charges_amount' => $platformCharges,
                        'platform_charges_gst' => $platformChargesGST,
                        'gst_rate' => $gstRate,
                        'gst_amount' => $finalGST,
                        'tcs_rate' => $tcsRate,
                        'tds_rate' => $tdsRate,
                        'tds_rate_text' => $tdsRateText,
                        'website_price' => $totalPrice,
                        'origin' => $package->origin_city_id,
                        'destination_state_id' => $package->destination_state_id,
                        'state_name' => $package->state_name,
                        'destination_city_id' => $package->destination_city_id,
                        'cities_name' => $package->cities_name,
                        'keywords' => $package->keywords,
                        'transportation_name' => isset($package->transportation->name) ? ($package->transportation->name) : null,
                        'hotel_star' => isset($package->hotel_star_id) ? ($package->hotel_star_id) : null,
                        'religion' => isset($package->religion->name) ? ($package->religion->name) : null,
                        'tour_type' => $package->tour_type,
                        'trip' => $package->tour_type == 1 ? 'Domestic' : 'International',
                        'trip_type' => $package->trip_type,
                        'type_of_tour_packages' => $package->trip_type == 1 ? 'Standard' : 'Weekend',
                        'themes' => $themes,
                        'featured_image_path' => $package->featured_image_path,
                        'inclusions_in_package' => $inclusions,
                        'overview' => $package->overview,
                        'inclusions_list' => $inclusionList,
                        'exclusions_list' => $exclusionList,
                        'terms_and_condition' => $package->terms_and_condition,
                        'default_terms_and_condition' => $package->default_terms_and_condition,
                        'payment_policy' => $package->payment_policy,
                        'cancellation_policy' => $package->cancellation_policy,
                        'total_seat' => $package->total_seat,
                        'bulk_no_of_pax' => $package->bulk_no_of_pax,
                        'pax_discount_percent' => $package->pax_discount_percent,
                        'created_at' => date($package->created_at),
                        'gallery_images' => $galleryImages,
                        'stay_plan' => $package->stay_plan,
                        'bulk_discounts' => $package->bulkDiscounts,
                        'media_link' => $mediaLinks,
                        'addons' => $addons,
                        'seat_availability' => $seatAvailability,
                        'seat_unavailable' => $seatUnavailable,
                        'tour_circuit' => $package->tour_circuit,
                        'region_id' => $package->region_id,
                        'status' => $package->status,
                        'is_transport' => $package->is_transport,
                        'is_flight' => $package->is_flight,
                        'is_train' => $package->is_train,
                        'is_hotel' => $package->is_hotel,
                        'is_meal' => $package->is_meal,
                        'is_sightseeing' => $package->is_sightseeing,
                        'itinerary' => $this->transformItinerary($package->itinerary),
                        'vendor_verified' => $package->vendor_verified,
                        'admin_verified' => $package->admin_verified,
                        'vendor_verified_at' => Carbon::parse($package->vendor_verified_at)->format('d M, Y, h:m a'),
                        'admin_verified_at' => Carbon::parse($package->admin_verified_at)->format('d M, Y, h:m a'),
                        'packageRates' => $packageRates,

                    ];

                    $messages = PackageMessage::where('package_id', $package->id)->get();

                    $messagesCount = $messages->where('is_read', '0')->where('receiver_id', $package->user_id)->count();

                    return response()->json(['res' => true, 'data' => $transformedPackage, 'messagesCount' => $messagesCount], 200);
                }
                else{
                    return response()->json(['res' => false, 'msg' => 'User is not authorized'], 403);
                }
            }
        } catch (Exception $e) {
            Log::error('Error occurred while fetching package details: ' . $e->getMessage());

            return response()->json(['res' => false, 'msg' => $e->getMessage()], 500);
        }
    }

    //****************************DELETE FUNCTIONS****************** */

    public function deleteGalleryImages($id)
    {
        if (auth()->check()) {
            $user = auth()->user();
            try {
                $galleryImage = PackageImage::findOrFail($id);
                if ($galleryImage->package->user_id === $user->id) {
                    $galleryImage->delete();
                    return response()->json(['res' => true, 'msg' => 'Gallery image deleted successfully'], 200);
                } else {
                    return response()->json(['error' => 'Unauthorized'], 403);
                }
            } catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'Gallery image not found'], 404);
            } catch (\Exception $e) {
                return response()->json(['error' => 'Internal Server Error'], 500);
            }
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    public function deleteMediaLink($id)
    {
     if (auth()->check()) {
        $user = auth()->user();

        try {
           
            $media = PackageMedia::findOrFail($id);
            if ($media->package->user_id === $user->id) {
                $media->delete();
                return response()->json(['res' => true, 'msg' => 'Media deleted successfully'], 200);
            } else {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Media not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
      } else {
        return response()->json(['error' => 'Unauthorized'], 401);
      }
     }

    public function deleteFlight($id)
    {

        try {
            $flight = PackageFlight::findOrFail($id);
            $flight->delete();

            return response()->json(['res' => true, 'msg' => 'Flight information deleted successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['res' => false, 'msg' => 'Not found'], 404);
        } catch (Exception $e) {
            return response()->json(['res' => false, 'msg' => 'Internal Server Error'], 500);
        }

    }

    public function deleteSightseeing($id)
    {

        try {
            $sightseeing = PackageSightseeing::findOrFail($id);
            $sightseeing->delete();

            return response()->json(['res' => true, 'msg' => 'Sightseeing information deleted successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['res' => false, 'msg' => 'Not found'], 404);
        } catch (Exception $e) {
            return response()->json(['res' => false, 'msg' => 'Internal Server Error'], 500);
        }
    }

    public function removeMeal($id)
    {
        try {

            $itinerary = Itinerary::findOrFail($id);


            $itinerary->update(['meal' => null]);

            return response()->json(['res' => true, 'msg' => 'Meal removed successfully'], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to remove meal', 'details' => $e->getMessage()], 500);
        }
    }

    public function deleteHotel($id)
    {

        try {
            $hotel = PackageHotel::findOrFail($id);
            $hotel->delete();

            return response()->json(['res' => true, 'msg' => 'Hotel information deleted successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['res' => false, 'msg' => 'Not found'], 404);
        } catch (Exception $e) {
            return response()->json(['res' => false, 'msg' => 'Internal Server Error'], 500);
        }
    }

    public function deleteHotelGallery($id)
    {

        try {
            $hotelG = PackageHotelGalleryImage::findOrFail($id);
            $hotelG->delete();

            return response()->json(['res' => true, 'msg' => 'Image deleted successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['res' => false, 'msg' => 'Not found'], 404);
        } catch (Exception $e) {
            return response()->json(['res' => false, 'msg' => 'Internal Server Error'], 500);
        }
    }

    public function deleteTrain($id)
    {

        try {
            $train = PackageTrain::findOrFail($id);
            $train->delete();

            return response()->json(['res' => true, 'msg' => 'Train information deleted successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['res' => false, 'msg' => 'Not found'], 404);
        } catch (Exception $e) {
            return response()->json(['res' => false, 'msg' => 'Internal Server Error'], 500);
        }
    }

    public function deleteLocalTransport($id)
    {
        try {
            $localtransport = PackageLocalTransport::findOrFail($id);
            $localtransport->delete();

            return response()->json(['res' => true, 'msg' => 'Local transport information deleted successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['res' => false, 'msg' => 'Not found'], 404);
        } catch (Exception $e) {
            return response()->json(['res' => false, 'msg' => 'Internal Server Error'], 500);
        }
    }

    public function deleteAddons($id)
    {

        try {
            $addon = PackageAddon::findOrFail($id);
            $addon->delete();

            return response()->json(['res' => true, 'msg' => 'Addon information deleted successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['res' => false, 'msg' => 'List not found'], 404);
        } catch (Exception $e) {
            return response()->json(['res' => false, 'msg' => 'Internal Server Error'], 500);
        }
    }

    public function deleteInclusionList($id)
    {
        try {
            $inclusion = PackageInclusion::findOrFail($id);
            $inclusion->delete();

            return response()->json(['res' => true, 'msg' => 'Inclusion deleted successfully.'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['res' => false, 'msg' => 'List not found'], 404);
        } catch (Exception $e) {
            return response()->json(['res' => false, 'msg' => 'Internal Server Error'], 500);
        }
    }

    public function deleteExclusionList($id)
    {
        try {
            $exclusion = ExclusionList::findOrFail($id);
            $exclusion->delete();

            return response()->json(['res' => true, 'msg' => 'Exclusion deleted successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['res' => false, 'msg' => 'List not found'], 404);
        } catch (Exception $e) {
            return response()->json(['res' => false, 'msg' => 'Internal Server Error'], 500);
        }
    }

    public function deleteItinerary($id)
    {
        try {
            $itinerary = Itinerary::findOrFail($id);
            $itinerary->delete();

            return response()->json(['res' => true, 'msg' => 'Itinerary deleted successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['res' => false, 'msg' => 'List not found'], 404);
        } catch (Exception $e) {
            return response()->json(['res' => false, 'msg' => 'Internal Server Error'], 500);
        }
    }

    public function deleteSeatavailability($id)
    {
        try {
            $seat = PackageSeatAvailability::findOrFail($id);
            $seat->delete();

            return response()->json(['res' => true, 'msg' => 'Seat deleted successfully.'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['res' => false, 'msg' => 'List not found'], 404);
        } catch (Exception $e) {
            return response()->json(['res' => false, 'msg' => 'Internal Server Error'], 500);
        }
    }

    //****************************///DELETE FUNCTIONS****************** */
    public function bestsellingList()
    {
        try {
            $packageCounts = $this->packageService->getPackageCountsByState(request());

            return response()->json(['res' => true, 'msg' => 'Package counts retrieved successfully', 'data' => $packageCounts], 200);
        } catch (Exception $e) {
            // Handle exceptions, log, or return an error response
            return response()->json(['res' => false, 'msg' => 'Internal Server Error'], 500);
        }
    }

    public function popularDestination()
    {
        try {
            $packageCounts = $this->packageService->getPopularDestination(request());

            return response()->json(['res' => true, 'msg' => 'Package  retrieved successfully', 'data' => $packageCounts], 200);
        } catch (Exception $e) {
            // Handle exceptions, log, or return an error response
            return response()->json(['res' => false, 'msg' => 'Internal Server Error'], 500);
        }
    }

    public function durationList()
    {
        try {
            $result = DB::table('packages')->orderBy('total_days')->distinct()->get(['total_days']);

            return response()->json(['res' => true, 'msg' => 'Duration list retrieved successfully', 'data' => $result], 200);
        } catch (Exception $e) {
            // Handle exceptions, log, or return an error response
            return response()->json(['res' => false, 'msg' => 'Internal Server Error'], 500);
        }
    }

    public function destinationsByTheme()
    {
        try {
            $package = $this->packageService->getdestinationsByTheme(request());

            return response()->json(['res' => true, 'msg' => 'Package  retrieved successfully', 'data' => $package], 200);
        } catch (Exception $e) {
            // Handle exceptions, log, or return an error response
            return response()->json(['res' => false, 'msg' => 'Internal Server Error'], 500);
        }
    }

    public function getFrontPackageList()
    {
        try {
            $query = Package::with([
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
                    'users.name as vendor_name'
                )
                //->where('status', 1);
                ->where('packages.status', 1)
                ->where('packages.admin_verified', 1)
                ->where('packages.vendor_verified', 1)
                ->where('vendors.status', 1);


            $packages = $query->latest('packages.created_at')->limit(10)->get();

            $formattedPackages = [];

            foreach ($packages as $package) {

                $featuredImage = PackageImage::where('package_id', $package->id)->first();

                $inclusionIds = explode(',', $package->inclusions_in_package);


                $inclusions = DB::table('package_inclusions')
                    ->whereIn('id', $inclusionIds)
                    ->select(['name'])
                    ->get();


                $themesIds = explode(',', $package->themes_id);


                $themes = DB::table('themes')
                    ->whereIn('id', $themesIds)
                    ->select(['name', 'image', 'icon'])
                    ->get();

                $totalNights = $package->total_days - 1;
                $formattedPackages[] = [
                    'package_id' => $package->id,
                    'featured_image_path' => $featuredImage ? $featuredImage->path : null,
                    'package_name' => $package->name,
                    'inclusionIds' => $inclusions,
                    'total_days' => "{$totalNights} night" . ($totalNights > 1 ? 's' : '') . " and {$package->total_days} days",
                    'total_days_count' => (int)$package->total_days,
                    'total_nights_count' => (int)$totalNights,
                    'themes' => $themes
                ];
            }


            $chunkedPackages = array_chunk($formattedPackages, 2);
            return response()->json(['res' => true, 'msg' => 'Packages retrieved successfully', 'data' => $chunkedPackages], 200);
        } catch (Exception $e) {
            return response()->json(['res' => false, 'msg' => 'Internal Server Error'], 500);
        }
    }


    public function stateList(Request $request)
    {
        try {
            $states = $this->packageService->getStateList($request);
            return response()->json(['res' => true, 'msg' => 'Data retrieved successfully', 'data' => $states], 200);
        } catch (Exception $e) {
            return response()->json(['res' => false, 'msg' => 'Internal Server Error'], 500);
        }
    }

    public function cityList(Request $request)
    {
        try {
            $cities = $this->packageService->getCityList($request);
            return response()->json(['res' => true, 'msg' => 'Data retrieved successfully', 'data' => $cities], 200);
        } catch (Exception $e) {
            return response()->json(['res' => false, 'msg' => 'Internal Server Error'], 500);
        }
    }


    public function tripList(Request $request)
    {
        try {
            $tripList = $this->packageService->gettripList($request);
            return response()->json(['res' => true, 'msg' => 'Data retrieved successfully', 'data' => $tripList], 200);
        } catch (Exception $e) {
            return response()->json(['res' => false, 'msg' => 'Internal Server Error'], 500);
        }
    }

    public function typeOfTourPackagesList(Request $request)
    {
        try {
            $typeOfTourPackagesList = $this->packageService->gettypeOfTourPackagesList($request);
            return response()->json(['res' => true, 'msg' => 'Data retrieved successfully', 'data' => $typeOfTourPackagesList], 200);
        } catch (Exception $e) {
            return response()->json(['res' => false, 'msg' => 'Internal Server Error'], 500);
        }
    }

    public function typeOfVendorList(Request $request)
    {
        try {
            $typeOfVendorList = $this->packageService->gettypeOfVendorList($request);
            return response()->json(['res' => true, 'msg' => 'Data retrieved successfully', 'data' => $typeOfVendorList], 200);
        } catch (Exception $e) {
            return response()->json(['res' => false, 'msg' => 'Internal Server Error'], 500);
        }
    }

    public function themesList(Request $request)
    {
        try {
            $themesList = $this->packageService->getthemesList($request);
            return response()->json(['res' => true, 'msg' => 'Data retrieved successfully', 'data' => $themesList], 200);
        } catch (Exception $e) {
            return response()->json(['res' => false, 'msg' => 'Internal Server Error'], 500);
        }
    }

    public function religionList(Request $request)
    {
        try {
            $religionList = $this->packageService->getreligionList($request);
            return response()->json(['res' => true, 'msg' => 'Data retrieved successfully', 'data' => $religionList], 200);
        } catch (Exception $e) {
            return response()->json(['res' => false, 'msg' => 'Internal Server Error'], 500);
        }
    }

    public function totaldaysRange(Request $request)
    {
        try {
            $totalday = $this->packageService->gettotalday($request);
            return response()->json(['res' => true, 'msg' => 'Data retrieved successfully', 'data' => $totalday], 200);
        } catch (Exception $e) {
            return response()->json(['res' => false, 'msg' => 'Internal Server Error'], 500);
        }
    }

    public function startingpriceRange(Request $request)
    {
        try {
            $startingpriceRange = $this->packageService->getstartingpriceRange($request);
            return response()->json(['res' => true, 'msg' => 'Data retrieved successfully', 'data' => $startingpriceRange], 200);
        } catch (Exception $e) {
            return response()->json(['res' => false, 'msg' => 'Internal Server Error'], 500);
        }
    }


    public function markStatus(Request $request)
    {
        try {
            $packageIds = $request->input('package_ids', []);
            $status = $request->input('status', '');

            $this->packageService->updatePackageStatus($packageIds, $status);

            return response()->json(['res' => true, 'msg' => 'Status updated successfully'], 200);
        } catch (Exception $e) {

            return response()->json(['res' => false, 'msg' => 'Internal Server Error'], 500);
        }
    }

    public function addonListByPackage(Request $request)
    {
        $packageId = $request->input('package_id');
        $addons = $this->packageService->getAddonsByPackageId($packageId);
        return response()->json(['res' => true, 'msg' => '', 'data' => $addons]);
    }

    public function seatAvailabilityByPackage(Request $request)
    {
        // dd("dasd");
        $packageId = $request->input('package_id');
        $seatAvailability = $this->packageService->getseatAvailability($packageId);
        return response()->json(['res' => true, 'msg' => '', 'seatAvailability' => $seatAvailability]);
    }

    public function exploreDestinationCount(Request $request)
    {
        try {
            $packageCounts = Package::whereNotNull('region_id')->groupBy('region_id')
                ->select('region_id as location', \DB::raw('COUNT(*) as package_count'))
                ->get();


            $totalLocations = $packageCounts->count();

            return response()->json([
                'res' => true,
                'msg' => 'Destination counts retrieved successfully',
                'data' => [
                    'package_counts' => $packageCounts,
                    'total_locations' => $totalLocations
                ]
            ], 200);
        } catch (Exception $e) {
            return response()->json(['res' => false, 'msg' => 'Internal Server Error'], 500);
        }
    }

    /**
     * Fetches all tourism circuits from the database.
     *
     * @return JsonResponse
     */
    public function fetchTourismCircuits(): JsonResponse
    {
        try {
            $tourismCircuits = TourismCircuit::all();

            if ($tourismCircuits->isEmpty()) {
                return response()->json(['res' => true, 'msg' => 'No tourism circuits found', 'data' => []], 200);
            }

            return response()->json(['res' => true, 'msg' => 'Tourism circuits retrieved successfully', 'data' => $tourismCircuits], 200);
        } catch (Exception $e) {
            return response()->json(['res' => false, 'msg' => 'Failed to retrieve tourism circuits', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Fetches all locations from the database.
     *
     * @return JsonResponse
     */
    public function fetchRegions(): JsonResponse
    {
        try {
            $regions = Region::all();

            if ($regions->isEmpty()) {
                return response()->json(['res' => true, 'msg' => 'No regions found', 'data' => []], 200);
            }

            return response()->json(['res' => true, 'msg' => 'Regions retrieved successfully', 'data' => $regions], 200);
        } catch (Exception $e) {
            return response()->json(['res' => false, 'msg' => 'Failed to retrieve regions', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Fetches keywords associated with a specific location.
     *
     * @param int $locationId
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function fetchLocationKeywords(int $locationId, Request $request): JsonResponse
    {
        try {
            // Find the location
            $location = Location::findOrFail($locationId);

            // Retrieve keywords associated with the location
            $query = LocationKeyword::where('location_id', $locationId);

            if ($request->has('sort') && $request->sort == 'true') {
                // If sort parameter is true, join with package table and count
                $query->select('location_keywords.*', DB::raw('COUNT(packages.id) as package_count'))
                    ->join('packages', function ($join) {
                        $join->on(DB::raw('FIND_IN_SET(location_keywords.name, packages.keywords)'), '>', DB::raw('0'));
                    })
                    ->groupBy('location_keywords.id')
                    ->orderBy('package_count', 'desc');
            }

            $keywords = $query->pluck('name');

            return response()->json(['res' => true, 'msg' => 'Keywords retrieved successfully', 'data' => $keywords], 200);
        } catch (Exception $e) {
            return response()->json(['res' => false, 'msg' => 'Failed to retrieve keywords', 'error' => $e->getMessage()], 500);
        }
    }


    /**
     * Fetches keywords associated with a specific location.
     *
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function fetchSearchKeywords(Request $request): JsonResponse
    {
        try {
            $query = LocationKeyword::query();
            $locationId = $request->location_id ?? null;
            $searchKeyword = $request->search_keyword ?? null;
            // If sort parameter is true, join with package table and count
            $query->join('packages', function ($join) {
                $join->on(DB::raw('FIND_IN_SET(location_keywords.name, packages.keywords)'), '>', DB::raw('0'));
            })
                ->leftJoin('locations', 'location_keywords.location_id', '=', 'locations.id')
                ->select('location_keywords.*', DB::raw('COUNT(DISTINCT(packages.id)) as package_count'));

            if ($locationId !== null) {
                $location = Location::findOrFail($locationId);
                $query->where('location_keywords.location_id', $locationId);
                $query->where('packages.location', $location->name);
            }
            if ($searchKeyword !== null) {
                $query->where('location_keywords.name', 'like', '%' . $searchKeyword . '%');
            }
            $query->groupBy('location_keywords.id', 'location_keywords.location_id', 'location_keywords.name')->orderBy('package_count', 'desc');
            $keywords = $query->get()->unique('name')->values()->all();
            //$keywords = $query->get();

            return response()->json(['res' => true, 'msg' => 'Keywords retrieved successfully', 'data' => $keywords], 200);
        } catch (Exception $e) {
            return response()->json(['res' => false, 'msg' => 'Failed to retrieve keywords', 'error' => $e->getMessage()], 500);
        }
    }

    public function fetchSearchDestinations(Request $request): JsonResponse
    {
        try {
            $results = [];
            $term = $request->search_keyword;
            $cities = City::where('city', 'like', '%' . $term . '%')->get();
            foreach ($cities as $city) {
                $results[] = ["name" => $city->city, "id" => $city->id, "type" => 'city'];
            }

            $states = State::where('name', 'like', '%' . $term . '%')->get();
            foreach ($states as $state) {
                $results[] = ["name" => $state->name, "id" => $state->id, "type" => 'state'];
            }

            $regions = Region::where('name', 'like', '%' . $term . '%')->get();
            foreach ($regions as $region) {
                $results[] = ["name" => $region->name, "id" => $region->id, "type" => 'region'];
            }


            return response()->json(['res' => true, 'msg' => 'Keywords retrieved successfully', 'data' => $results], 200);
        } catch (Exception $e) {
            return response()->json(['res' => false, 'msg' => 'Failed to retrieve keywords', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Fetches all inclusions from the database.
     *
     * @return JsonResponse
     */
    public function fetchInclusions(): JsonResponse
    {
        try {
            $inclusions = Inclusion::all();

            if ($inclusions->isEmpty()) {
                return response()->json(['res' => true, 'msg' => 'No inclusions found', 'data' => []], 200);
            }
            $blankInclusion = (object)[
                'id' => 0,
                'name' => 'Create New',
                // Add other necessary fields with blank values
            ];
            $inclusions->prepend($blankInclusion);
            return response()->json(['res' => true, 'msg' => 'Inclusions retrieved successfully', 'data' => $inclusions], 200);
        } catch (Exception $e) {
            return response()->json(['res' => false, 'msg' => 'Failed to retrieve inclusions', 'error' => $e->getMessage()], 500);
        }
    }


    /**
     * Fetches all exclusions from the database.
     *
     * @return JsonResponse
     */
    public function fetchExclusions(): JsonResponse
    {
        try {
            $exclusions = Exclusion::all();

            if ($exclusions->isEmpty()) {
                return response()->json(['res' => true, 'msg' => 'No exclusions found', 'data' => []], 200);
            }

            $blankExclusion = (object)[
                'id' => 0,
                'name' => 'Create New',
                // Add other necessary fields with blank values
            ];
            $exclusions->prepend($blankExclusion);

            return response()->json(['res' => true, 'msg' => 'Exclusions retrieved successfully', 'data' => $exclusions], 200);
        } catch (Exception $e) {
            return response()->json(['res' => false, 'msg' => 'Failed to retrieve exclusions', 'error' => $e->getMessage()], 500);
        }
    }


    public function addWishlist(Request $request)
    {
        $request->validate([
            'package_ids' => 'required|string',
        ]);

        $userId = auth()->id();
        $newPackageIds = explode(',', $request->package_ids);

        foreach ($newPackageIds as $packageId) {
            // Check if any wishlist contains the current package ID
            $existingWishlist = Wishlist::where('package_ids', 'like', "%$packageId%")->first();

            if ($existingWishlist) {
                return response()->json(['res' => false, 'msg' => 'Package already exists in  wishlist'], 200);
            }
        }

        $wishlist = Wishlist::where('user_id', $userId)->first();

        if ($wishlist) {
            $currentPackageIds = explode(',', $wishlist->package_ids);
            $newPackageIds = array_unique(array_merge($currentPackageIds, $newPackageIds));
            $wishlist->update([
                'package_ids' => implode(',', $newPackageIds),
            ]);
            return response()->json(['res' => true, 'msg' => 'Package added to wishlist', 'wishlist' => $wishlist], 200);
        } else {
            $wishlist = Wishlist::create([
                'user_id' => $userId,
                'package_ids' => $request->package_ids,
            ]);
        }

        return response()->json(['res' => true, 'msg' => 'Wishlist updated successfully', 'wishlist' => $wishlist], 200);
    }

    public function fetchWishlist(Request $request)
    {
        try {
            // Get the authenticated user's ID
            $userId = auth()->id();

            // Retrieve the wishlist for the authenticated user
            $wishlist = Wishlist::where('user_id', $userId)->first();

            if (!$wishlist) {
                return response()->json(['msg' => 'Wishlist not found'], 404);
            }

            // Wishlist found, extract package IDs
            $packageIds = explode(',', $wishlist->package_ids);

            // Fetch package details based on package IDs
            $packages = Package::with([
                'religion',
                'themes',
                'gallery_images'

            ])
                ->leftJoin('states', 'packages.destination_state_id', '=', 'states.id')
                ->leftJoin('cities', 'packages.destination_city_id', '=', 'cities.id')
                ->leftJoin('users', 'packages.user_id', '=', 'users.id')
                ->leftJoin('vendors', 'packages.user_id', '=', 'vendors.user_id')
                ->addSelect(DB::raw('(SELECT path FROM package_images WHERE package_id = packages.id ORDER BY id ASC LIMIT 1) as first_gallery_image'))
                ->select(
                    'packages.*',
                    'states.name as state_name',
                    'cities.city as city_name',
                    'vendors.name as vendor_name',

                )
                ->whereIn('packages.id', $packageIds)
                ->where('packages.status', 1)
                ->get();

            // Map package details using BookingService
            $wPackages = $packages->map(function ($package) {
                return $this->packageService->fetchWishPackageDetails($package);
            });

            return response()->json(['res' => true, 'msg' => 'Data retrieved successfully', 'data' => $wPackages], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function removeWishlist(Request $request)
    {
        $request->validate([
            'package_id' => 'required|integer',
        ]);

        $userId = auth()->id();
        $packageIdToRemove = $request->package_id;

        $wishlist = Wishlist::where('user_id', $userId)->first();

        if (!$wishlist) {
            return response()->json(['res' => false, 'msg' => 'Wishlist not found'], 404);
        }

        $currentPackageIds = explode(',', $wishlist->package_ids);

        if (!in_array($packageIdToRemove, $currentPackageIds)) {
            return response()->json(['res' => false, 'msg' => 'Package not found in wishlist'], 404);
        }

        $updatedPackageIds = array_filter($currentPackageIds, function ($id) use ($packageIdToRemove) {
            return $id != $packageIdToRemove;
        });

        if (empty($updatedPackageIds)) {
            $wishlist->delete();
            return response()->json(['res' => true, 'msg' => 'Wishlist is empty and has been deleted'], 200);
        }

        $wishlist->update([
            'package_ids' => implode(',', $updatedPackageIds),
        ]);

        return response()->json(['res' => true, 'msg' => 'Package removed from wishlist', 'wishlist' => $wishlist], 200);
    }

    public function wishlistCount(Request $request)
    {
        $userId = auth()->id();

        $wishlist = Wishlist::where('user_id', $userId)->first();

        if (!$wishlist || is_null($wishlist->package_ids) || $wishlist->package_ids === '') {
            return response()->json(['res' => true, 'count' => 0, 'msg' => 'No packages in wishlist'], 200);
        }

        $packageIds = explode(',', $wishlist->package_ids);
        $count = count(array_filter($packageIds, fn($id) => !empty($id)));

        return response()->json(['res' => true, 'count' => $count, 'msg' => 'Wishlist package count retrieved successfully'], 200);
    }

    public function addMessage(Request $request)
    {
        $userId = auth()->id();

        $result = $this->packageService->addMessage($request->all(), $userId);

        return $result;
    }

    public function messageList(Request $request)
    {
        $userId = auth()->id();

        $result = $this->packageService->messageList($request->all(), $userId);

        return $result;
    }

    public function messageView(Request $request)
    {
        $userId = auth()->id();

        $result = $this->packageService->messageView($request->all(), $userId);

        return $result;
    }

}
