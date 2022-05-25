<?php
/**
 * GymBeam s.r.o.
 *
 * Copyright © GymBeam, All rights reserved.
 *
 * @copyright Copyright © 2021 GymBeam (https://gymbeam.com/)
 * @category GymBeam
 */
declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Api\BoxWeightRepositoryInterface;
use App\Models\BoxWeight;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class PickerController extends Controller
{

    protected BoxWeightRepositoryInterface $boxWeightRepository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(BoxWeightRepositoryInterface $boxWeightRepository)
    {
        $this->boxWeightRepository = $boxWeightRepository;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function setBoxUid(Request $request): JsonResponse
    {
        Log::info(__CLASS__.': incoming request from picker: '.json_encode($request->input()));

        $this->validate($request, [
            'order_id' => 'required',
            'box_uid' => 'required',
            'picker_id' => 'required'
        ]);

        try {
            $this->persistPickerData($request->input());
        } catch  (\Exception $e) {
            Log::error($e->getMessage());
            $response = response()->json(
                $this->getErrorMessage($e->getMessage()),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
        return $response ?? response()->json($this->getSuccessMessage());
    }

    /**
     * @param array $input
     * @throws \App\Exceptions\Model\CouldNotSaveException
     */
    public function persistPickerData(array $input): void
    {
        $boxWeight = $this->boxWeightRepository->getByOrderId($input['order_id']);
        if (!$boxWeight) {
            $boxWeight = new BoxWeight();
        }
        $boxWeight->setOrderId((int)$input['order_id']);
        $boxWeight->setBoxUID((int)$input['box_uid']);
        $boxWeight->setPickerId((int)$input['picker_id']);
	    $boxWeight->setPickedAt(gmdate('Y-m-d H:i:s'));

        $this->boxWeightRepository->save($boxWeight);
    }

    /**
     * @param string $message
     * @return string[]
     */
    private function getErrorMessage(string $message): array
    {
        return [
            "status" => "error",
            "message" => $message
        ];
    }

    /**
     * @return string[]
     */
    private function getSuccessMessage(): array
    {
        return [
            "status" => "success"
        ];
    }
}
