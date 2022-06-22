<?php
	
	namespace App\Http\Controllers;
	
	use Illuminate\Http\Request;
	use Illuminate\Http\Resources\Json\JsonResource;
	use Illuminate\Validation\ValidationException;
	
	class WatermarkController extends Controller {
		// Upload watermark and get public_id
		public function upload(Request $request): JsonResource {
			$data = $this->validate($request, [
				'watermark' => [
					'required',
					'image',
					'mimes:png',
				],
				'public_id' => [
					'required',
					'string'
				]
			]);
			
			$watermark = $data['watermark'];
			$public_id = $data['public_id'];
			cloudinary()->upload($watermark->getRealPath(), [
				'folder'    => 'watermark-api',
				'public_id' => $public_id
			])->getSecurePath();
			
			return JsonResource::make([
				'message'   => "Watermark uploaded successfully",
				'watermark' => ['public_id' => $public_id]
			]);
		}
		
		public function create(Request $request): JsonResource {
			$data = $this->validate($request, [
				'media'     => ['required', 'image', 'max:1024'],
				'public_id' => [
					'required',
					'string'
				]
			]);
			
			$media = $data['media'];
			$public_id = $data['public_id'];
			$branded = cloudinary()->upload($media->getRealPath(), [
				'folder'         => 'watermark-api',
				'transformation' => [
					'overlay' => $public_id,
					'gravity' => 'south_east', // watermark location bottom right
					'x'       => 0.02, // 2 percent offset horizontally
					'y'       => 0.02, // 2 percent offset vertically
					'crop'    => 'scale',
				],
			])->getSecurePath();
			
			return JsonResource::make([
				'message' => "Watermark created successfully",
				'url'     => $branded
			]);
		}
	}
