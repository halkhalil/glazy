<?php

namespace App\Api\V1\Controllers\Glazy;

use Dingo\Api\Routing\Helpers;
//use Illuminate\Routing\Controller;
use App\Http\Controllers\Controller;

use League\Fractal\Manager as FractalManager;

use App\Api\V1\Serializers\GlazySerializer;


use App\Api\V1\Controllers\Glazy\StatusCodes\ApiStatusCodes;
use App\Api\V1\Controllers\Glazy\StatusCodes\ApiStatusCodeTraits;


class ApiBaseController extends Controller implements ApiStatusCodes
{
    use Helpers;
    use ApiStatusCodeTraits;

    /**
     * @var int
     */
    protected $statusCode = self::HTTP_ACCEPTED;

    /**
     * @var FractalManager
     */
    protected $manager;

    /**
     * @var DataArraySerializer
     */
    protected $serializer;

    public function __construct(FractalManager $manager, GlazySerializer $serializer)
    {
        $this->manager = $manager;
        $this->serializer = $serializer;
        $this->manager->setSerializer($this->serializer);
        //        Auth::shouldUse('api');
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    public function respondCreated($message)
    {
        if (!$message)
        {
            $message = self::$statusTexts[self::HTTP_CREATED];
        }
        return $this->setStatusCode(self::HTTP_CREATED)->respondSimpleMessage($message);
    }

    public function respondUpdated($message)
    {
        if (!$message)
        {
            $message = self::$statusTexts[self::HTTP_OK];
        }
        return $this->setStatusCode(self::HTTP_OK)->respondSimpleMessage($message);
    }

    public function respondDeleted($message)
    {
        if (!$message)
        {
            $message = "Item deleted successfully";
        }
        return $this->setStatusCode(self::HTTP_OK)->respondSimpleMessage($message);
    }

    public function respondUnprocessableEntity($message)
    {
        if (!$message)
        {
            $message = self::$statusTexts[self::HTTP_UNPROCESSABLE_ENTITY];
        }
        return $this->setStatusCode(self::HTTP_UNPROCESSABLE_ENTITY)->respondWithError($message);
    }

    public function respondNotFound($message)
    {
        if (!$message)
        {
            $message = self::$statusTexts[self::HTTP_NOT_FOUND];
        }
        return $this->setStatusCode(self::HTTP_NOT_FOUND)->respondWithError($message);
    }

    public function respondUnauthorized($message)
    {
        if (!$message)
        {
            $message = self::$statusTexts[self::HTTP_UNAUTHORIZED];
        }
        return $this->setStatusCode(self::HTTP_UNAUTHORIZED)->respondWithError($message);
    }

    public function respondInternalError($message)
    {
        if (!$message)
        {
            $message = self::$statusTexts[self::HTTP_INTERNAL_SERVER_ERROR];
        }
        return $this->setStatusCode(self::HTTP_INTERNAL_SERVER_ERROR)->respondWithError($message);
    }

    public function respondSimpleMessage($data, $headers = [])
    {
        return response()->json(['message' => $data], $this->getStatusCode(), $headers);
    }

    public function respond($data, $headers = [])
    {
        return response()->json($data, $this->getStatusCode(), $headers);
    }

    public function respondWithError($message)
    {
        return response()->json([
            'error' => [
                'message' => $message,
                'status_code' => $this->getStatusCode()
            ]
        ]);
    }

}