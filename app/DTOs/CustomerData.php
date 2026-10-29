<?php

namespace App\DTOs;

use Illuminate\Support\Collection;

class CustomerData
{
    public string $customerId;
    public string $name;
    public string $email;
    public string $phone;
    public string $status;
    public ?CustomerLocationData $location;
    public array $metadata;

    /**
     * Create a new CustomerData instance
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->customerId = $data['customer_id'];
        $this->name = $data['name'];
        $this->email = $data['email'];
        $this->phone = $data['phone'];
        $this->status = $data['status'] ?? 'active';
        $this->location = isset($data['location']) ? new CustomerLocationData($data['location']) : null;
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
            'customer_id' => $this->customerId,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'status' => $this->status,
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
        if (empty($this->customerId)) {
            throw new \InvalidArgumentException('Customer ID is required');
        }

        if (empty($this->name)) {
            throw new \InvalidArgumentException('Customer name is required');
        }

        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email format');
        }

        if (!preg_match('/^[0-9\-\(\)\/\+\s]*$/', $this->phone)) {
            throw new \InvalidArgumentException('Invalid phone number format');
        }

        if (!in_array($this->status, ['active', 'inactive', 'pending', 'blocked'])) {
            throw new \InvalidArgumentException('Invalid customer status');
        }

        if ($this->location) {
            $this->location->validate();
        }

        return true;
    }
} 