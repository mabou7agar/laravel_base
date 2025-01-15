<?php

namespace  BasePackage\Shared\Services\Input;

use App\Exceptions\FieldValidationException;
use App\Exceptions\EmailUnavailableException;
use Illuminate\Database\Eloquent\Model;

class Validations
{
    public static function validateEmail($email)
    {
        $email = trim($email);

        if (! isset($email) || empty($email)) {
            throw new FieldValidationException(
                trans('exceptions.FieldValidationException.email.missing'),
                trans('exceptions.FieldValidationException.email.missing.code'),
                trans('exceptions.FieldValidationException.email.missing.name')
            );
        }

        if ((int) strlen($email) > 50) {
            throw new FieldValidationException(
                trans('exceptions.FieldValidationException.email.incorrect.max'),
                trans('exceptions.FieldValidationException.email.incorrect.max.code'),
                trans('exceptions.FieldValidationException.email.incorrect.max.name')
            );
        }

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new FieldValidationException(
                trans('exceptions.FieldValidationException.email.invalid'),
                trans('exceptions.FieldValidationException.email.invalid.code'),
                trans('exceptions.FieldValidationException.email.invalid.name')
            );
        }

        return strtolower($email);
    }

    public static function emailValidationChecker($email)
    {
        $email = trim(strtolower($email));

        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            return false;
        }

        if (! preg_match("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$^", $email)) {
            return false;
        }

        if (is_array($email) || is_numeric($email) || is_bool($email) || is_float($email) || is_file($email) || is_dir($email) || is_int($email)) {
            return false;
        } else {
            if (filter_var($email, FILTER_VALIDATE_EMAIL) !== false) {
                return true;
            } else {
                $pattern = '/^(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){255,})(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){65,}@)(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22))(?:\\.(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-+[a-z0-9]+)*\\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-+[a-z0-9]+)*)|(?:\\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\\]))$/iD';
                return (preg_match($pattern, $email) === 1);
            }
        }
    }

    public static function isEmailDomainExists($email, $record = 'MX')
    {
        list($user, $domain) = explode('@', $email);
        return checkdnsrr($domain, $record);
    }

    public static function validateEmailUniqueness(Model $model, $email, $emailFieldName = 'email', $idValue = null, $idFieldName = 'id')
    {
        if ($idValue) {
            if ($model::where('email', '=', $email)->where($idFieldName, '!=', $idValue)->count()) {
                throw new EmailUnavailableException();
            }
        } elseif ($model::where('email', '=', $email)->count()) {
            throw new EmailUnavailableException();
        }

        return $email;
    }

    public static function validatePassword($password, $field_name = 'password')
    {
        $password = trim($password);

        if (! isset($password) || empty($password)) {
            throw new FieldValidationException(
                trans('exceptions.FieldValidationException.' . $field_name . '.missing'),
                trans('exceptions.FieldValidationException.' . $field_name . '.missing.code'),
                trans('exceptions.FieldValidationException.' . $field_name . '.missing.name')
            );
        }

        if ((int) mb_strlen($password) > 50) {
            throw new FieldValidationException(
                trans('exceptions.FieldValidationException.' . $field_name . '.incorrect.max'),
                trans('exceptions.FieldValidationException.' . $field_name . '.incorrect.max.code'),
                trans('exceptions.FieldValidationException.' . $field_name . '.incorrect.max.name')
            );
        }

        if ((int) mb_strlen($password) < 8) {
            throw new FieldValidationException(
                trans('exceptions.FieldValidationException.' . $field_name . '.incorrect.min'),
                trans('exceptions.FieldValidationException.' . $field_name . '.incorrect.min.code'),
                trans('exceptions.FieldValidationException.' . $field_name . '.incorrect.min.name')
            );
        }

        return $password;
    }

    public static function validateName($name)
    {
        $name = trim($name);

        if (! isset($name) || empty($name)) {
            throw new FieldValidationException(
                trans('exceptions.FieldValidationException.registration.name.missing'),
                trans('exceptions.FieldValidationException.registration.name.missing.code'),
                trans('exceptions.FieldValidationException.registration.name.missing.name')
            );
        }

        if ((int) strlen($name) > 50) {
            throw new FieldValidationException(
                trans('exceptions.FieldValidationException.registration.name.incorrect.max'),
                trans('exceptions.FieldValidationException.registration.name.incorrect.max.code'),
                trans('exceptions.FieldValidationException.registration.name.incorrect.max.name')
            );
        }

        if ((int) strlen($name) < 3) {
            throw new FieldValidationException(
                trans('exceptions.FieldValidationException.registration.name.incorrect.min'),
                trans('exceptions.FieldValidationException.registration.name.incorrect.min.code'),
                trans('exceptions.FieldValidationException.registration.name.incorrect.min.name')
            );
        }

        return $name;
    }

    public static function validateTitle($title)
    {
        $title = trim($title);

        if ((int) strlen($title) > 255) {
            throw new FieldValidationException(
                trans('exceptions.FieldValidationException.title.incorrect.max'),
                trans('exceptions.FieldValidationException.title.incorrect.max.code'),
                trans('exceptions.FieldValidationException.title.incorrect.max.name')
            );
        }
    }

    public static function validateContent($content)
    {
        $content = trim($content);

        if (! isset($content) || empty($content)) {
            throw new FieldValidationException(
                trans('exceptions.FieldValidationException.content.missing'),
                trans('exceptions.FieldValidationException.content.missing.code'),
                trans('exceptions.FieldValidationException.content.missing.name')
            );
        }

        if ((int) strlen($content) > 5000) {
            throw new FieldValidationException(
                trans('exceptions.FieldValidationException.content.incorrect.max'),
                trans('exceptions.FieldValidationException.content.incorrect.max.code'),
                trans('exceptions.FieldValidationException.content.incorrect.max.name')
            );
        }

        if ((int) strlen($content) < 10) {
            throw new FieldValidationException(
                trans('exceptions.FieldValidationException.content.incorrect.min'),
                trans('exceptions.FieldValidationException.content.incorrect.min.code'),
                trans('exceptions.FieldValidationException.content.incorrect.min.name')
            );
        }
    }

    public static function validateGender($gender)
    {
        $gender = trim($gender);

        if (! isset($gender) || empty($gender)) {
            throw new FieldValidationException(
                trans('exceptions.FieldValidationException.gender.missing'),
                trans('exceptions.FieldValidationException.gender.missing.code'),
                trans('exceptions.FieldValidationException.gender.missing.name')
            );
        }

        if (!in_array($gender, ['male', 'female'])) {
            throw new FieldValidationException(
                trans('exceptions.FieldValidationException.gender.incorrect'),
                trans('exceptions.FieldValidationException.gender.incorrect.code'),
                trans('exceptions.FieldValidationException.gender.incorrect.name')
            );
        }
    }

    public static function validateToken($token)
    {
        $token = trim($token);

        if (! isset($token) || empty($token)) {
            throw new FieldValidationException(
                trans('exceptions.FieldValidationException.social.login.token.missing'),
                trans('exceptions.FieldValidationException.social.login.token.missing.code'),
                trans('exceptions.FieldValidationException.social.login.token.missing.name')
            );
        }
    }
}
