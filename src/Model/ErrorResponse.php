<?php

namespace App\Model;

use OpenApi\Annotations as OA;
use App\Model\ErrorValidationDetails;
use Nelmio\ApiDocBundle\Annotation\Model;
use App\Model\ErrorDebugDetails;

class ErrorResponse
{
    public function __construct(private string $message, private mixed $details = null)
    {
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @OA\Property (type="object", oneOf={
     *     @OA\Schema (ref=@Model(type=ErrorValidationDetails::class)),
     *     @OA\Schema (ref=@Model(type=ErrorDebugDetails::class)),
     * })
     */
    public function getDetails(): mixed
    {
        return $this->details;
    }
}
