<?php

namespace  BasePackage\Shared\Services\Input\ThrottleInputs;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

trait ThrottleInputs
{
    //use ThrottlesLogins {
    //    ThrottlesLogins::throttleKey as parentThrottleKey;
    //}

    protected $decayMinutes = 5;

    /**
     * Get the throttle key for the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function throttleKey(Request $request)
    {
        return Str::lower($request->attributes->get('throttle_key')) . '|' . $request->ip();
    }

    protected function sendLockoutResponse(Request $request)
    {
        $seconds = $this->limiter()->availableIn(
            $this->throttleKey($request)
        );

        throw new ThrottledInputException($this->getThrottleMsg($seconds));
    }

    protected function throttle(Request $request, $key): void
    {
        $request->attributes->set('throttle_key', $key);
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            /* @throws ThrottledInputException */
            $this->sendLockoutResponse($request);
        }
        $this->incrementLoginAttempts($request);
    }

    private function getThrottleMsg(int $seconds): string
    {
        if ($seconds <= 90) {
            $throttleMsg = trans('auth.throttle', ['seconds' => $seconds]);
        } elseif ($seconds > 90 && $seconds < 660) {
            $throttleMsg = trans(
                'auth.throttleInMinutesForMediumSeconds',
                [
                    'minutes' => (int) ($seconds / 60),
                    'seconds' => $seconds % 60
                ]
            );
        } elseif ($seconds >= 660) {
            $throttleMsg = trans(
                'auth.throttleInMinutesForBigSeconds',
                [
                    'minutes' => (int) ($seconds / 60),
                    'seconds' => $seconds % 60
                ]
            );
        }
        return $throttleMsg;
    }

    private function hitLimiterIncrement(string $key, int $incrementLimit, int $decaySeconds = 60): void
    {
        for ($increment = 1; $increment <= $incrementLimit; $increment++) {
            $this->limiter()->hit($key, $decaySeconds);
        }
    }
}
