<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\MainUsers as MainUser;
use App\Models\MainUsers;
use App\Models\Property;
use App\Models\UserSubscription;
use App\Models\Setting;
use App\Models\EmailTemplate;
use App\Models\AddressModal;
use App\Models\DriverVehicleModal;
use App\Models\UserConversation;
use App\Models\DriverDocumentModal;
use App\Models\UserFavouriteProperty;
use App\Models\PropertyImages;
use App\Models\ReportUser;

use App\Helpers\Helper;
use File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Mail;
use Hash;
use PDO;
use DB;
use Carbon\Carbon;

class DriverController extends Controller
{
    public function upload_document(Request $request)
    {
    	// echo "<pre>";print_r($request->toArray());exit();
    	$user_id = $request->user_id;
    	$token = $request->token;
    	$upload_document1 = $request->upload_document1;
    	$upload_document2 = $request->upload_document2;
    	$upload_document3 = $request->upload_document3;
    	$upload_document4 = $request->upload_document4;
    	$upload_document5 = $request->upload_document5;
    	$upload_document6 = $request->upload_document6;
    	$upload_document7 = $request->upload_document7;
    	$upload_document8 = $request->upload_document8;

    	if (@$user_id && @$token) {
		$userToken = Helper::getusercheckToken($user_id, $token);
		if(!empty($userToken)){
			

			if ($upload_document1) {
			$driver_document = new DriverDocumentModal();
			$formFileName = "upload_document1";
        	$fileFinalName_ar = "";

			if ($request->$formFileName) {

				try {
					$image = $request->file($formFileName);
					$fileFinalName_ar = time(). '-' . rand(0,10000) .'.'.$image->getClientOriginalExtension();
					// $destinationPath = public_path( '/' . 'uploads/general_users/' );
					$pathImg =  public_path( 'uploads/driver_document/' );
					// echo "<pre>";print_r($pathImg);exit;
					//$request->file($formFileName)->move($destinationPath, $fileFinalName_ar);
					$request->file($formFileName)->move($pathImg, $fileFinalName_ar);
					// $img = Image::make($image->getRealPath());
					// $img->resize(500, null, function ($constraint) {
					//     $constraint->aspectRatio();
					// })->save($destinationPath , $fileFinalName_ar);
				} 
				catch (\Throwable $th) {
					// throw $th;
				}
	
	
			}
			
			$driver_document->document_image = $fileFinalName_ar;

			$driver_document->user_id = isset($user_id) ? $user_id : '';
			$driver_document->document_type = 1;
			$driver_document->is_approve = 0;
			$driver_document->status = 1;
			$driver_document->save();

		 	}

		 	if ($upload_document2) {
			$driver_document = new DriverDocumentModal();
			$formFileName = "upload_document2";
        	$fileFinalName_ar = "";

			if ($request->$formFileName) {

				try {
					$image = $request->file($formFileName);
					$fileFinalName_ar = time(). '-' . rand(0,10000) .'.'.$image->getClientOriginalExtension();
					// $destinationPath = public_path( '/' . 'uploads/general_users/' );
					$pathImg =  public_path( 'uploads/driver_document/' );
					// echo "<pre>";print_r($pathImg);exit;
					//$request->file($formFileName)->move($destinationPath, $fileFinalName_ar);
					$request->file($formFileName)->move($pathImg, $fileFinalName_ar);
					// $img = Image::make($image->getRealPath());
					// $img->resize(500, null, function ($constraint) {
					//     $constraint->aspectRatio();
					// })->save($destinationPath , $fileFinalName_ar);
				} 
				catch (\Throwable $th) {
					// throw $th;
				}
	
	
			}
			
			$driver_document->document_image = $fileFinalName_ar;

			$driver_document->user_id = isset($user_id) ? $user_id : '';
			$driver_document->document_type = 1;
			$driver_document->is_approve = 0;
			$driver_document->status = 1;
			$driver_document->save();

		 	}


		 	if ($upload_document3) {
		 	$driver_document = new DriverDocumentModal();
			$formFileName = "upload_document3";
        	$fileFinalName_ar = "";

			if ($request->$formFileName) {

				try {
					$image = $request->file($formFileName);
					$fileFinalName_ar = time(). '-' . rand(0,10000) .'.'.$image->getClientOriginalExtension();
					// $destinationPath = public_path( '/' . 'uploads/general_users/' );
					$pathImg =  public_path( 'uploads/driver_document/' );
					// echo "<pre>";print_r($pathImg);exit;
					//$request->file($formFileName)->move($destinationPath, $fileFinalName_ar);
					$request->file($formFileName)->move($pathImg, $fileFinalName_ar);
					// $img = Image::make($image->getRealPath());
					// $img->resize(500, null, function ($constraint) {
					//     $constraint->aspectRatio();
					// })->save($destinationPath , $fileFinalName_ar);
				} 
				catch (\Throwable $th) {
					// throw $th;
				}
	
	
			}
			
			$driver_document->document_image = $fileFinalName_ar;

			$driver_document->user_id = isset($user_id) ? $user_id : '';
			$driver_document->document_type = 2;
			$driver_document->is_approve = 0;
			$driver_document->status = 1;
			$driver_document->save();

		 	}

		 	if ($upload_document4) {
		 	$driver_document = new DriverDocumentModal();
			$formFileName = "upload_document4";
        	$fileFinalName_ar = "";

			if ($request->$formFileName) {

				try {
					$image = $request->file($formFileName);
					$fileFinalName_ar = time(). '-' . rand(0,10000) .'.'.$image->getClientOriginalExtension();
					// $destinationPath = public_path( '/' . 'uploads/general_users/' );
					$pathImg =  public_path( 'uploads/driver_document/' );
					// echo "<pre>";print_r($pathImg);exit;
					//$request->file($formFileName)->move($destinationPath, $fileFinalName_ar);
					$request->file($formFileName)->move($pathImg, $fileFinalName_ar);
					// $img = Image::make($image->getRealPath());
					// $img->resize(500, null, function ($constraint) {
					//     $constraint->aspectRatio();
					// })->save($destinationPath , $fileFinalName_ar);
				} 
				catch (\Throwable $th) {
					// throw $th;
				}
	
	
			}
			
			$driver_document->document_image = $fileFinalName_ar;

			$driver_document->user_id = isset($user_id) ? $user_id : '';
			$driver_document->document_type = 2;
			$driver_document->is_approve = 0;
			$driver_document->status = 1;
			$driver_document->save();

		 	}

		 	if ($upload_document5) {
		 	$driver_document = new DriverDocumentModal();
			$formFileName = "upload_document5";
        	$fileFinalName_ar = "";

			if ($request->$formFileName) {

				try {
					$image = $request->file($formFileName);
					$fileFinalName_ar = time(). '-' . rand(0,10000) .'.'.$image->getClientOriginalExtension();
					// $destinationPath = public_path( '/' . 'uploads/general_users/' );
					$pathImg =  public_path( 'uploads/driver_document/' );
					// echo "<pre>";print_r($pathImg);exit;
					//$request->file($formFileName)->move($destinationPath, $fileFinalName_ar);
					$request->file($formFileName)->move($pathImg, $fileFinalName_ar);
					// $img = Image::make($image->getRealPath());
					// $img->resize(500, null, function ($constraint) {
					//     $constraint->aspectRatio();
					// })->save($destinationPath , $fileFinalName_ar);
				} 
				catch (\Throwable $th) {
					// throw $th;
				}
	
	
			}
			
			$driver_document->document_image = $fileFinalName_ar;

			$driver_document->user_id = isset($user_id) ? $user_id : '';
			$driver_document->document_type = 3;
			$driver_document->is_approve = 0;
			$driver_document->status = 1;
			$driver_document->save();

		 	}

		 	if ($upload_document6) {
		 	$driver_document = new DriverDocumentModal();
			$formFileName = "upload_document6";
        	$fileFinalName_ar = "";

			if ($request->$formFileName) {

				try {
					$image = $request->file($formFileName);
					$fileFinalName_ar = time(). '-' . rand(0,10000) .'.'.$image->getClientOriginalExtension();
					// $destinationPath = public_path( '/' . 'uploads/general_users/' );
					$pathImg =  public_path( 'uploads/driver_document/' );
					// echo "<pre>";print_r($pathImg);exit;
					//$request->file($formFileName)->move($destinationPath, $fileFinalName_ar);
					$request->file($formFileName)->move($pathImg, $fileFinalName_ar);
					// $img = Image::make($image->getRealPath());
					// $img->resize(500, null, function ($constraint) {
					//     $constraint->aspectRatio();
					// })->save($destinationPath , $fileFinalName_ar);
				} 
				catch (\Throwable $th) {
					// throw $th;
				}
	
	
			}
			
			$driver_document->document_image = $fileFinalName_ar;

			$driver_document->user_id = isset($user_id) ? $user_id : '';
			$driver_document->document_type = 3;
			$driver_document->is_approve = 0;
			$driver_document->status = 1;
			$driver_document->save();

		 	}

		 	if ($upload_document7) {
		 	$driver_document = new DriverDocumentModal();
			$formFileName = "upload_document7";
        	$fileFinalName_ar = "";

			if ($request->$formFileName) {

				try {
					$image = $request->file($formFileName);
					$fileFinalName_ar = time(). '-' . rand(0,10000) .'.'.$image->getClientOriginalExtension();
					// $destinationPath = public_path( '/' . 'uploads/general_users/' );
					$pathImg =  public_path( 'uploads/driver_document/' );
					// echo "<pre>";print_r($pathImg);exit;
					//$request->file($formFileName)->move($destinationPath, $fileFinalName_ar);
					$request->file($formFileName)->move($pathImg, $fileFinalName_ar);
					// $img = Image::make($image->getRealPath());
					// $img->resize(500, null, function ($constraint) {
					//     $constraint->aspectRatio();
					// })->save($destinationPath , $fileFinalName_ar);
				} 
				catch (\Throwable $th) {
					// throw $th;
				}
	
	
			}
			
			$driver_document->document_image = $fileFinalName_ar;

			$driver_document->user_id = isset($user_id) ? $user_id : '';
			$driver_document->document_type = 4;
			$driver_document->is_approve = 0;
			$driver_document->status = 1;
			$driver_document->save();

		 	}

		 	if ($upload_document8) {
		 	$driver_document = new DriverDocumentModal();
			$formFileName = "upload_document8";
        	$fileFinalName_ar = "";

			if ($request->$formFileName) {

				try {
					$image = $request->file($formFileName);
					$fileFinalName_ar = time(). '-' . rand(0,10000) .'.'.$image->getClientOriginalExtension();
					// $destinationPath = public_path( '/' . 'uploads/general_users/' );
					$pathImg =  public_path( 'uploads/driver_document/' );
					// echo "<pre>";print_r($pathImg);exit;
					//$request->file($formFileName)->move($destinationPath, $fileFinalName_ar);
					$request->file($formFileName)->move($pathImg, $fileFinalName_ar);
					// $img = Image::make($image->getRealPath());
					// $img->resize(500, null, function ($constraint) {
					//     $constraint->aspectRatio();
					// })->save($destinationPath , $fileFinalName_ar);
				} 
				catch (\Throwable $th) {
					// throw $th;
				}
	
	
			}
			
			$driver_document->document_image = $fileFinalName_ar;

			$driver_document->user_id = isset($user_id) ? $user_id : '';
			$driver_document->document_type = 4;
			$driver_document->is_approve = 0;
			$driver_document->status = 1;
			$driver_document->save();

		 	}

			$result['code']     = (string) 1;
			$result['message']  = 'success';
			$result['result']   = [];
			
		}else{
			$result['code']     = (string) 0;
			$result['message']  = 'invalid_token';
			$result['result']   = [];
		} }else{
			$result['code']     = (string) 0;
			$result['message']  = 'server_not_responding';
			$result['result']   = [];
		}
	$mainResult[] = $result;
	return response()->json($mainResult);
    }


    public function car_details(Request $request)
    {
    	// echo "<pre>";print_r($request->toArray());exit();
    	$user_id = $request->user_id;
    	$token = $request->token;
    	if (@$user_id && @$token) {
    		$userToken = Helper::getusercheckToken($user_id, $token);
				// echo "<pre>";print_r($userToken);exit();
			if(!empty($userToken)){
				$carTypeData = Helper::getCarTypeData();
				// echo "<pre>";print_r($carTypeData);exit;
				// $mainArr = [];
				$responseArr = [];
				foreach ($carTypeData as $carData) {
					$response['id'] = isset($carData->id) ? $carData->id : '';
					$response['car_type'] = isset($carData->car_type) ? urldecode($carData->car_type) : '';
					$response['description'] = isset($carData->description) ? urldecode($carData->description) : '';
					$response['image'] = isset($carData->image) ? $carData->image : '';
					$response['base_fare'] = isset($carData->base_fare) ? $carData->base_fare : '';
					$response['per_km_charge'] = isset($carData->per_km_charge) ? $carData->per_km_charge : '';
					$response['per_km_charge_pool'] = isset($carData->per_km_charge_pool) ? $carData->per_km_charge_pool : '';
					$responseArr[] = $response;
				}

				$mainArr['car_type'] = $responseArr;

				$vehicleMakeData = Helper::getVehicleData();
				// echo "<pre>";print_r($vehicleMakeData);exit();
				$vehicleArr = [];
				foreach ($vehicleMakeData as $vehicleData) {

					$vehicleModalData = Helper::getVehicleModalData($vehicleData->id);
					// echo "<pre>";print_r($vehicleModalData);exit();
					$modalArr = [];
					foreach ($vehicleModalData as $modalData) {
						$modal['id'] = isset($modalData->id) ? $modalData->id : '' ;
						$modal['name'] = isset($modalData->name) ? urldecode($modalData->name) : '' ;
						$modal['vehicle_type_id'] = isset($modalData->vehicle_type_id) ? $modalData->vehicle_type_id : '' ;
						$modalArr[] = $modal;
					}
					// echo "<pre>";print_r($modalArr);exit();

					$vehicle['id'] = isset($vehicleData->id) ? $vehicleData->id : '';
					$vehicle['name'] = isset($vehicleData->name) ? urldecode($vehicleData->name) : '';
					$vehicle['vehicle_type'] = $modalArr;
					$vehicleArr[] = $vehicle;
				}

				$mainArr['vehicle_modal'] = $vehicleArr;
				// echo "<pre>";print_r($vehicleMakeData);exit();
				// $
				$result['code']     = (string) 1;
				$result['message']  = 'success';
				$result['result']   = $mainArr;
			}else{
				$result['code']     = (string) 0;
				$result['message']  = 'invalid_token';
				$result['result']   = [];
			}
    	}else{
			$result['code']     = (string) 0;
			$result['message']  = 'server_not_responding';
			$result['result']   = [];
		}
			$mainResult[] = $result;
		return response()->json($mainResult);
    }


    public function vehicle_details(Request $request)
    {
    	// echo "<pre>";print_r($request->toArray());exit();
    	$user_id = $request->user_id;
		$token = $request->token;
		$vehicle_type_id = $request->vehicle_type_id;
		$vehicle_model_id = $request->vehicle_model_id;
		$vehicle_seat_capacity = $request->vehicle_seat_capacity;
		$vehicle_number = $request->vehicle_number;
		$car_type_id = $request->car_type_id;
		$vehicle_document = $request->vehicle_document;

		if (@$user_id && @$token) {
		$userToken = Helper::getusercheckToken($user_id, $token);
		if(!empty($userToken)){
			$driver_vehicle = new DriverVehicleModal();

			$driver_vehicle->driver_id = isset($user_id) ? $user_id : '';
			$driver_vehicle->vehicle_type_id = isset($vehicle_type_id) ? $vehicle_type_id : '';
			$driver_vehicle->car_type_id = isset($car_type_id) ? $car_type_id : '';
			$driver_vehicle->vehicle_model_id = isset($vehicle_model_id) ? $vehicle_model_id : '';
			$driver_vehicle->vehicle_number = isset($vehicle_number) ? $vehicle_number : '';
			$driver_vehicle->vehicle_seat_capacity = isset($vehicle_seat_capacity) ? $vehicle_seat_capacity : '';
			$driver_vehicle->status = 1;

			$driver_vehicle->save();


			foreach ($vehicle_document as $document) {
			$driver_vehicle_document = new DriverDocumentModal();
			// echo "<pre>";print_r($document);exit();
			$formFileName = "vehicle_document";
        	$fileFinalName_ar = "";

			if ($request->$formFileName) {

				try {
					$image = $document;
					// echo "<pre>";print_r($image);exit();
					$fileFinalName_ar = time(). '-' . rand(0,10000) .'.'.$image->getClientOriginalExtension();
					// $destinationPath = public_path( '/' . 'uploads/general_users/' );
					$pathImg =  public_path( 'uploads/driver_document/' );
					// echo "<pre>";print_r($pathImg);exit;
					//$request->file($formFileName)->move($destinationPath, $fileFinalName_ar);
					// $request->file($formFileName)->move($pathImg, $fileFinalName_ar);
					$document->move($pathImg, $fileFinalName_ar);
					// $img = Image::make($image->getRealPath());
					// $img->resize(500, null, function ($constraint) {
					//     $constraint->aspectRatio();
					// })->save($destinationPath , $fileFinalName_ar);
				} 
				catch (\Throwable $th) {
					// throw $th;
				}
	
	
			}
			$driver_vehicle_document->document_image = $fileFinalName_ar;
			$driver_vehicle_document->user_id = isset($user_id) ? $user_id : '';
			$driver_vehicle_document->document_type = 5;
			$driver_vehicle_document->is_approve = 1;
			$driver_vehicle_document->status = 1;
			$driver_vehicle_document->save();
				
			}

			if ($driver_vehicle->save()) {
				$result['code']     = (string) 1;
				$result['message']  = 'success';
				$result['result']   = [];
			}else{
				$result['code']     = (string) 0;
				$result['message']  = 'server_not_responding';
				$result['result']   = [];
			}
			// echo "string";exit();
		}else{
			$result['code']     = (string) 0;
			$result['message']  = 'invalid_token';
			$result['result']   = [];
		}
		}
		else{
			$result['code']     = (string) 0;
			$result['message']  = 'server_not_responding';
			$result['result']   = [];
		}
	$mainResult[] = $result;
	return response()->json($mainResult);
    } 
}
