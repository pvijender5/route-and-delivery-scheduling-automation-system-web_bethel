<?php

namespace App\DTOs;

use Illuminate\Support\Collection;

class CustomerLocationData
{
    public string $name;
    public string $addressLine1;
    public ?string $addressLine2;
    public string $city;
    public string $state;
    public string $zipcode;
    public string $contactPhone;
    public bool $isDefault;
    public array $metadata;

    /**
     * Create a new CustomerLocationData instance
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->name = $data['name'];
        $this->addressLine1 = $data['address_line_1'];
        $this->addressLine2 = $data['address_line_2'] ?? null;
        $this->city = $data['city'];
        $this->state = $data['state'];
        $this->zipcode = $data['zipcode'];
        $this->contactPhone = $data['contact_phone'];
        $this->isDefault = $data['is_default'] ?? false;
        $this->metadata = $data['metadata'] ?? [];
    }

    /**
     * Convert the DTO to an array
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'address_line_1' => $this->addressLine1,
            'address_line_2' => $this->addressLine2,
            'city' => $this->city,
            'state' => $this->state,
            'zipcode' => $this->zipcode,
            'contact_phone' => $this->contactPhone,
            'is_default' => $this->isDefault,
            'metadata' => $this->metadata,
        ];
    }

    /**
     * Create a new instance from a collection
     *
     * @param Collection $collection
     * @return self
     */
    public static function fromCollection(Collection $collection): self
    {
        return new self($collection->toArray());
    }

    /**
     * Validate the DTO data
     *
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function validate(): bool
    {
        if (empty($this->name)) {
            throw new \InvalidArgumentException('Location name is required');
        }

        if (empty($this->addressLine1)) {
            throw new \InvalidArgumentException('Address line 1 is required');
        }

        if (empty($this->city)) {
            throw new \InvalidArgumentException('City is required');
        }

        if (empty($this->state)) {
            throw new \InvalidArgumentException('State is required');
        }

        if (empty($this->zipcode)) {
            throw new \InvalidArgumentException('Zipcode is required');
        }

        if (!preg_match('/^[0-9\-\(\)\/\+\s]*$/', $this->contactPhone)) {
            throw new \InvalidArgumentException('Invalid contact phone format');
        }

        if (!is_bool($this->isDefault)) {
            throw new \InvalidArgumentException('Is default must be a boolean');
        }

        return true;
    }
} 