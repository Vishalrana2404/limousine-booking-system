<?php

namespace App;

use App\Mail\SendEmail;
use App\Mail\EmailTemplateTest;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class CustomHelper
{

    public static function base64url_encode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    public static function base64url_decode($data)
    {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }

    public static function random()
    {
        $random = str_shuffle('1234567890');

        return $password = substr($random, 0, 5);
    }
    public static function alertResponse($message, $alertType)
    {
        toastr($message, $alertType);
    }


    public static function handleException($exception)
    {
        if ($exception instanceof \Throwable) {
            app(ExceptionHandler::class)->report($exception);
            Log::error($exception->getMessage(), [
                'file' => $exception->getFile(),
                'type' => 'Handled Exception',
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ]);
        } else {
            Log::error($exception);
        }
    }
    public static function getFullName($firstName = '', $lastName = '')
    {
        if ($firstName || $lastName) {
            return $firstName . ' ' . $lastName;
        }
    }
    public static function formatTime($timeString)
    {
        if ($timeString) {
            try {
                $carbonTime = Carbon::createFromFormat('H:i:s', $timeString);
                return $carbonTime->format('H:i');
            } catch (Exception $e) {
                return null;
            }
        }
    }

    public static function formatDate($dateString)
    {
        if ($dateString) {
            try {
                $date = Carbon::parse($dateString);
                return $date->format('d/m/Y');
            } catch (Exception $e) {
                return null;
            }
        }
    }
    public static function formatDateTime($dateTimeString)
    {
        if ($dateTimeString) {
            try {
                $datetime = Carbon::parse($dateTimeString);
                return $datetime->format('d/m/Y H:i');
            } catch (Exception $e) {
                return null;
            }
        }
    }

    public static function formatSingaporeDate($dateString)
    {
        if ($dateString) {
            try {
                $carbonDate = Carbon::parse($dateString);
                return $carbonDate->setTimezone('Asia/Singapore')->toDateTimeString();
            } catch (Exception $e) {
                return null;
            }
        }
    }

    public static function parseDateTime($dateString, $format)
    {
        if ($dateString) {
            try {
                return Carbon::parse($dateString)->format($format);
            } catch (Exception $e) {
                return null;
            }
        }
    }

    public static function isAddressWithinRecords($address)
    {
        // Get coordinates (latitude and longitude) of the provided address
        $apiKey = env('GOOGLE_MAPS_API_KEY');
        $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
            'address' => $address,
            'key' => $apiKey,
        ]);
        $data = $response->json();
        if ($data['status'] === 'OK') {
            $latitude = $data['results'][0]['geometry']['location']['lat'];
            $longitude = $data['results'][0]['geometry']['location']['lng'];
            // Fetch coordinates from the database
            $coordinates = DB::table('outside_city_surcharges')->select('coordinates')->get();
            // Check if the latitude and longitude lie within any of the defined coordinate boundaries
            foreach ($coordinates as $coordinate) {
                $isInside = self::checkInsideCoordinates(json_decode($coordinate->coordinates, true), $latitude, $longitude);
                if ($isInside) {
                    return true;
                }
            }
        }
        return false;
    }

    private static function checkInsideCoordinates($coordinates, $latitude, $longitude)
    {
        $numPoints = count($coordinates);
        $isInside = false;
        for ($i = 0, $j = $numPoints - 1; $i < $numPoints; $j = $i++) {
            $xi = $coordinates[$i]['lng'];
            $yi = $coordinates[$i]['lat'];
            $xj = $coordinates[$j]['lng'];
            $yj = $coordinates[$j]['lat'];
            $intersect = (($yi > $latitude) != ($yj > $latitude)) &&
                ($longitude < ($xj - $xi) * ($latitude - $yi) / ($yj - $yi) + $xi);
            if ($intersect) {
                $isInside = !$isInside;
            }
        }

        return $isInside;
    }
    public static function checkDriverOffDay($pickupDate, $driverId, $driverOffDays)
    {
        if (!$pickupDate || !$driverId) {
            return false;
        }
        $pickupDate =  self::parseDateTime($pickupDate, "Y-m-d");
        foreach ($driverOffDays as $offDay) {
            if ($driverId === $offDay->driver_id && $offDay->off_date === $pickupDate) {
                return true;
            }
        }
        return false;
    }

    public static function sendEmail($to, $mailData)
    {
        try {
            Mail::to($to)->send(new SendEmail($mailData));
            return "true";
        } catch (Exception $e) {
            return "false";
        }
    }

    public static function sendEmailTemplateTestEmail($to, $mailData)
    {
        try {
            Mail::to($to)->send(new EmailTemplateTest($mailData));
            return "true";
        } catch (Exception $e) {
            return "false";
        }
    }

    public static function queueEmail($to, $mailData)
    {
        try {
            Mail::to($to)->queue(new SendEmail($mailData));
            return "true";
        } catch (Exception $e) {
            return "false";
        }
    }
}
