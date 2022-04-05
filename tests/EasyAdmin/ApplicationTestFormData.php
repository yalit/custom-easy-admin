<?php

declare(strict_types=1);

namespace App\Tests\EasyAdmin;

final class ApplicationTestFormData
{
    public string $filterName = '';
    public string $value = '';

    public function __construct(string $filterName, string $value)
    {
        $this->filterName = $filterName;
        $this->value = $value;
    }

    public function isFilterNameId(): bool {
        return str_starts_with($this->filterName, "#");
    }

    public function isFilterNameClass(): bool {
        return str_starts_with($this->filterName, ".");
    }
}
