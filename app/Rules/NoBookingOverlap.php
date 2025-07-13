<?php

namespace App\Rules;

use App\Models\ChannelAd;
use App\Models\ChannelAdApplication;
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
        $endDate = $value;
        
        // Check for overlapping confirmed bookings
        $query = ChannelAdApplication::where('channel_ad_id', $this->channelAd->id)
            ->where('booking_status', 'confirmed')
            ->where(function ($q) use ($endDate) {
                $q->whereBetween('start_date', [$this->startDate, $endDate])
                  ->orWhereBetween('end_date', [$this->startDate, $endDate])
                  ->orWhere(function ($subQ) use ($endDate) {
                      $subQ->where('start_date', '<=', $this->startDate)
                           ->where('end_date', '>=', $endDate);
                  });
            });

        // Exclude current application if updating
        if ($this->excludeApplicationId) {
            $query->where('id', '!=', $this->excludeApplicationId);
        }

        if ($query->exists()) {
            $fail('The selected dates overlap with an existing confirmed booking for this channel.');
        }
    }
}