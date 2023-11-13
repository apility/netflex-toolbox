<?php

namespace Netflex\Toolbox\Validations;

use Illuminate\Contracts\Validation\Rule;
use Throwable;

class RecaptchaV2 implements Rule
{
    /**
     * @var string
     */
    private $captchaKey;
    /**
     * @var float
     */
    private $minScore;

    /**
     * Create a new rule instance.
     *
     * @param null $captchaKey
     * @param float|null $minScore
     */
    public function __construct($captchaKey = null, float $minScore = null)
    {
        $this->captchaKey = $captchaKey ?: config("recaptcha-v2.secret_key");
        $this->minScore = $minScore;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if(config('recaptcha-v2.disable_locally', false) && \App::environment() === 'local')
            return true;

        // It should work, but its not been tested due to me having issues finding keys that worked on localhost
        return $this->verifyResponse($value);
    }

    private function verifyResponse($response)
    {

        try {
            $post = [
                'secret' => $this->captchaKey,
                'response' => $response
            ];

            $ch = curl_init('https://www.google.com/recaptcha/api/siteverify');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
            $response = curl_exec($ch);

            curl_close($ch);
            $response = json_decode($response, true);

            return $response['success'] && ($this->minScore ? $response['score'] >= $this->minScore : true);
        } catch (Throwable $t) {
            error_log("Failed captcha: " . $t->getMessage());
            return false;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('Vi kunne ikke verifisere at du er et menneske');
    }
}