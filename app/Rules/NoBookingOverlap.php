<?php

namespace App\Rules;

use App\Models\ChannelAd;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class NoBookingOverlap implements ValidationRule
{
    protected ChannelAd $channelAd;
    protected string $startDate;
    protected ?int $excludeApplicationId;

    public function __construct(ChannelAd $channelAd, string $startDate, ?int $excludeApplicationId = null)
    {
        $this->channelAd = $channelAd;
        $this->startDate = $startDate;
        $this->excludeApplicationId = $excludeApplicationId;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // This rule is deprecated - ChannelAdApplication model removed
        // No validation needed as the model no longer exists
    }
}