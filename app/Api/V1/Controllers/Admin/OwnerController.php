<?php
namespace App\Api\V1\Controllers\Admin;

use App\Owner;
use App\Api\V1\Requests\OwnerRequest;
use App\Transformers\Admin\OwnerTransformer;
use Illuminate\Database\Eloquent\ModelNotFoundException;
/**
 * @group Legal Owners
 *
 * Endpoints for manipulating copyright owners.
 */
class OwnerController extends BaseController 
{
   /**
	 * Get all owners
	 *
    * @authenticated
    * @queryParam lang required string Example: en
    */
   public function all() {

      $owner = Owner::where([
         'lang' => config('avorg.default_lang'),
         'active' => 1
      ])->orderBy('created', 'desc')
         ->paginate(config('avorg.page_size'));

      if ( $owner->count() == 0 ) {
         return $this->response->errorNotFound("Owners not found.");
      }

      return $this->response->paginator($owner, new OwnerTransformer);
   }
   /**
	 * Get one owner
	 *
    * @authenticated
    * @queryParam id required int
    */
   public function one($ownerId) {

      try {
         $item = Owner::where(['active' => 1])->findOrFail($ownerId);
         return $this->response->item($item, new OwnerTransformer);
      } catch( ModelNotFoundException $e) {
         return $this->response->errorNotFound("Owner {$ownerId} not found.");
      }
   }

   /**
	 * Create owner
	 *
    * @authenticated
    * @queryParam title required string
    * @queryParam summary required string
    * @queryParam description required string
    * @queryParam logo required string
    * @queryParam location required string
    * @queryParam website required string
    * @queryParam publicAddress required string
    * @queryParam publicPhone required string
    * @queryParam publicEmail required string
    * @queryParam contactName required string
    * @queryParam contactAddress required string
    * @queryParam contactPhone required string
    * @queryParam contactEmail required string
    * @queryParam notes required string
    * @queryParam lang required string Example: en
    */
   public function create(OwnerRequest $request) 
   {
      try {
         $owner = new Owner();
         $this->setFields($request, $owner);
         $owner->save();

         return response()->json([
            'message' => 'Owner added.',
            'status_code' => 201
         ], 201);
      } catch (ModelNotFoundException $e) {
         return $this->response->errorNotFound($e->getMessage());
      }
   }
   /**
	 * Update owner
	 *
    * @authenticated
    * @queryParam id required int
    * @queryParam title required string
    * @queryParam summary required string
    * @queryParam description required string
    * @queryParam logo required string
    * @queryParam location required string
    * @queryParam website required string
    * @queryParam publicAddress required string
    * @queryParam publicPhone required string
    * @queryParam publicEmail required string
    * @queryParam contactName required string
    * @queryParam contactAddress required string
    * @queryParam contactPhone required string
    * @queryParam contactEmail required string
    * @queryParam notes required string
    * @queryParam lang required string Example: en
    */
   public function update(OwnerRequest $request) {

      try {
         $owner = Owner::where(['active' => 1])->findOrFail($request->id);
         $this->setFields($request, $owner);
         $owner->update();

         return response()->json([
            'message' => "Owner {$request->id} updated.",
            'status_code' => 201
         ], 201);

      } catch (ModelNotFoundException $e) {
         return $this->response->errorNotFound("Owner {$request->id} not found.");
      }
   }
   /**
	 * Delete owner
	 *
    * @authenticated
    * @queryParam id required int
    */
   public function delete(OwnerRequest $request) {

      try {

         $owner = Owner::where(['active' => 1])->findOrFail($request->id);

         if (!$owner->agreements()->exists()) {

            $owner->active = 0;
            $owner->save();

            return response()->json([
               'message' => "Owner {$request->id} deleted.",
               'status_code' => 201
            ], 201);

         } else {
            return $this->response->errorNotFound("Owner {$request->id} is referenced in another table thus can not be deleted.");
         }
      } catch (ModelNotFoundException $e) {
         return $this->response->errorNotFound("Owner {$request->id} not found.");
      }
   }

   private function setFields(OwnerRequest $request, Owner $owner) {

      $owner->title = $request->title;
      $owner->summary = $request->summary;
      $owner->description = $request->description;
      $owner->logo = $request->logo;
      $owner->location = $request->location;
      $owner->website = $request->website;
      $owner->publicAddress = $request->publicAddress;
      $owner->publicPhone = $request->publicPhone;
      $owner->publicEmail = $request->publicEmail;
      $owner->contactName = $request->contactName;
      $owner->contactAddress = $request->contactAddress;
      $owner->contactPhone = $request->contactPhone;
      $owner->contactEmail = $request->contactEmail;
      $owner->lang = $request->lang;
      $owner->notes = $request->notes;
      $owner->active = 1;
   }
}