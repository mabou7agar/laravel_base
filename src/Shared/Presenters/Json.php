<?php

declare(strict_types=1);

namespace  BasePackage\Shared\Presenters;

use Illuminate\Support\Facades\Response;

class Json
{
    private const SUCCESS_WITH_SINGLE_PAYLOAD_OBJECT = 'SUCCESS_WITH_SINGLE_PAYLOAD_OBJECT';
    private const SUCCESS_WITH_LIST_PAYLOAD_OBJECTS = 'SUCCESS_WITH_LIST_PAYLOAD_OBJECTS';
    private const SUCCESS_WITHOUT_PAYLOAD = 'SUCCESS_WITHOUT_PAYLOAD';

    /**
     * @param string|null     $key
     * @param array|string    $data
     * @param string|null     $description
     * @param string|int|null $code
     * @param string|null     $name
     * @param int             $httpStatus
     * @param array           $extraHeaders
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public static function buildItems(
        $key = null,
        $data = [],
        $description = null,
        $code = null,
        $name = null,
        $httpStatus = 200,
        $extraHeaders = []
    ) {
        $content['status'] = 'object';

        if (!is_null($key) && !empty($key)) {
            if (!empty($data)) {
                if (!is_array($data)) {
                    $data = [$data];
                }

                $content[$key] = array_merge($content, $data);
            } else {
                $content[$key] = $content;
            }

            if (!isset($data['status'])) {
                unset($content[$key]['status']);
            }
        } else {
            if (!empty($data)) {
                $content = array_merge($content, $data);
            } else {
                $content = $content;
            }
        }

        $content['message'] = [
            'type' => 'object',
            'code' => $code,
            'name' => $name,
            'description' => $description,
        ];

        return self::make($content, $httpStatus, $extraHeaders);
    }

    /**
     * @param string          $description
     * @param string|int|null $code
     * @param string|null     $name
     * @param array|string    $data
     * @param int             $httpStatus
     * @param array           $extraHeaders
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public static function error(
        $description = '',
        $code = null,
        $name = null,
        $data = [],
        $httpStatus = 200,
        $extraHeaders = []
    ) {
        $content['status'] = 'error';

        $error = [
            'type' => 'error',
            'code' => $code,
            'name' => $name,
            'description' => $description,
        ];

        if (!is_array($data)) {
            $data = [$data];
        }

        $error = array_merge($error, $data);

        $content['message'] = $error;

        return self::make($content, $httpStatus, $extraHeaders);
    }

    public static function validationError($validationErrors = '')
    {
        $content['status'] = 'validation_error';

        $validations = json_decode($validationErrors, true);

        $errorsBags = [];

        if (isset($validations) && !empty($validations)) {
            foreach ($validations as $field => $errors) {
                $errorsBag = [];
                $errorsBag['field'] = $field;
                $errorsBag['errors'] = $errors;
                $errorsBags[] = $errorsBag;
            }
        }

        $ValidationErrorsMessage = [
            'type' => 'validation_errors',
            'code' => trans('exceptions.ValidationErrorsException.code'),
            'name' => trans('exceptions.ValidationErrorsException.name'),
            'description' => trans('exceptions.ValidationErrorsException'),
            'validations' => $errorsBags
        ];

        $content['message'] = $ValidationErrorsMessage;

        return self::make($content, 422, []);
    }


    /**
     * @param string     $description
     * @param string|int $code
     * @param string     $name
     * @param array      $data
     * @param int        $httpStatus
     * @param array      $extraHeaders
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public static function success(
        $description = '',
        $code = '',
        $name = '',
        array $data = [],
        $httpStatus = 200,
        $extraHeaders = []
    ) {
        $content['status'] = 'success';

        $success = [
            'type' => 'success',
            'code' => $code,
            'name' => $name,
            'description' => $description,
        ];

        if (!is_array($data)) {
            $data = [$data];
        }

        $success = array_merge($success, $data);

        $content['message'] = $success;

        return self::make($content, $httpStatus, $extraHeaders);
    }

    /**
     * @param $content
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public static function make($content, $httpStatus, $extraHeaders = [])
    {
        $arrHeaders = [
            'Content-Type' => 'application/json; charset=utf-8',
            'Accept-Encoding' => 'gzip, deflate'
        ];

        if ($extraHeaders) {
            $arrHeaders = array_merge($arrHeaders, $extraHeaders);
        }

        return Response::json($content, $httpStatus, $arrHeaders, JSON_UNESCAPED_UNICODE);
    }

    //-- Version 2

    public static function done($message = null, $code = self::SUCCESS_WITHOUT_PAYLOAD)
    {
        $data['code'] = $code;

        $data['message'] = $message;

        return self::make($data, 200);
    }

    public static function failed($errors = [], $message = null, $code = null, $httpStatus = 400)
    {
        $data['code'] = $code;

        $data['message'] = $message;

        $data = [];

        if (!is_array($errors)) {
            $errors = [$errors];
        }

        $data['payload'] = $errors;

        return self::make($data, $httpStatus);
    }

    public static function validationFailed($validationErrors = '')
    {
        $validations = json_decode($validationErrors, true);

        $errorsBags = [];

        if (isset($validations) && !empty($validations)) {
            foreach ($validations as $field => $errors) {
                $errorsBag = [];
                $errorsBag['field'] = $field;
                $errorsBag['errors'] = $errors;
                $errorsBags[] = $errorsBag;
            }
        }

        $data = [];

        $data['code'] = 'validation_errors';

        $data['message'] = trans('exceptions.ValidationErrorsException');

        $data['payload'] = $errorsBags;

        return self::make($data, 422, []);
    }

    public static function created(
        $item = [],
        $message = null,
        $code = self::SUCCESS_WITH_SINGLE_PAYLOAD_OBJECT,
        $extraHeaders = []
    ) {
        $data = [];

        if (!is_array($item)) {
            $item = [$item];
        }

        $data['code'] = $code;

        $data['message'] = $message;

        $data['payload'] = $item;

        return self::make($data, 201, $extraHeaders);
    }

    public static function updated($item = [], $message = null, $code = self::SUCCESS_WITH_SINGLE_PAYLOAD_OBJECT)
    {
        $data = [];

        if (!is_array($item)) {
            $item = [$item];
        }

        $data['code'] = $code;

        $data['message'] = $message;

        $data['payload'] = $item;

        return self::make($data, 200);
    }

    public static function deleted()
    {
        return self::make([], 204);
    }

    public static function deletedWithExtraItems($extraItems = [], $message = null, $code = self::SUCCESS_WITHOUT_PAYLOAD)
    {
        $data = [];

        if (!is_array($extraItems)) {
            $extraItems = [$extraItems];
        }

        $data['code'] = $code;

        $data['message'] = $message;

        $data = array_merge($data, $extraItems);

        return self::make($data, 200);
    }

    /**
     * Form an object payload
     *
     * @param        $item
     * @param array  $extraItems
     * @param null   $message
     * @param string $code
     * @param int    $httpStatus
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public static function item(
        $item,
        $extraItems = [],
        $message = null,
        $code = self::SUCCESS_WITH_SINGLE_PAYLOAD_OBJECT,
        $httpStatus = 200
    ) {
        $data = [];

        if (!is_array($item)) {
            $item = [$item];
        }

        $data['code'] = $code;

        $data['message'] = $message;

        $data['payload'] = $item;

        $data = array_merge($data, $extraItems);

        return self::make($data, $httpStatus);
    }

    /**
     * Form an array payload
     *
     * @param        $mainItems
     * @param array  $extraItems
     * @param string $code
     * @param array  $paginationSettings
     * @param null   $message
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public static function items(
        $mainItems,
        $extraItems = [],
        $code = self::SUCCESS_WITH_LIST_PAYLOAD_OBJECTS,
        $paginationSettings = [],
        $message = null
    ) {
        $data = [];

        $mainItems = self::convertToArray($mainItems);

        $data['code'] = $code;

        $data['message'] = $message;

        if (!empty($paginationSettings)) {
            $pagination['page'] = (isset($paginationSettings['page'])) ? $paginationSettings['page'] : 1;

            $pagination['next_page'] = (isset($paginationSettings['next_page'])) ? $paginationSettings['next_page'] : 1;

            $pagination['last_page'] = (isset($paginationSettings['last_page'])) ? $paginationSettings['last_page'] : 1;

            $pagination['result_count'] = (isset($paginationSettings['result_count'])) ? $paginationSettings['result_count'] : count(
                $mainItems
            );

            $data['pagination'] = $pagination;
        }

        $data['payload'] = $mainItems;

        $data = array_merge($data, $extraItems);

        return self::make($data, 200);
    }

    private static function convertToArray($items)
    {
        if (!is_array($items)) {
            $items = [$items];
        }

        return array_values($items);
    }
}
