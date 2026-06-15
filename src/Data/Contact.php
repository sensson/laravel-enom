<?php

declare(strict_types=1);

namespace Sensson\Enom\Data;

use Spatie\LaravelData\Data;

class Contact extends Data
{
    public function __construct(
        public ?string $handle = null,
        public string $first_name = '',
        public string $last_name = '',
        public ?string $organization = null,
        public string $address = '',
        public ?string $address_2 = null,
        public string $city = '',
        public ?string $state = null,
        public string $postal_code = '',
        public string $country = '',
        public string $phone = '',
        public ?string $fax = null,
        public string $email = '',
    ) {
        //
    }

    /** @return array<string, string> */
    public function toQueryParams(string $prefix): array
    {
        return array_filter([
            "{$prefix}FirstName" => $this->first_name,
            "{$prefix}LastName" => $this->last_name,
            "{$prefix}OrganizationName" => $this->organization,
            "{$prefix}Address1" => $this->address,
            "{$prefix}Address2" => $this->address_2,
            "{$prefix}City" => $this->city,
            "{$prefix}StateProvince" => $this->state,
            "{$prefix}PostalCode" => $this->postal_code,
            "{$prefix}Country" => $this->country,
            "{$prefix}Phone" => $this->phone,
            "{$prefix}Fax" => $this->fax,
            "{$prefix}EmailAddress" => $this->email,
        ]);
    }
}
