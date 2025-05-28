<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Tests\Mocks;

use Swis\JsonApi\Client\Item;

class ItemStub extends Item
{
    /**
     * @var array<int, string>
     */
    protected $hidden = ['password'];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'age' => 'integer',
        'score' => 'float',
        'score_inf' => 'float',
        'score_inf_neg' => 'float',
        'score_nan' => 'float',
        'data' => 'array',
        'active' => 'bool',
        'secret' => 'string',
        'count' => 'int',
        'object_data' => 'object',
        'collection_data' => 'collection',
        'foo' => 'bar',
    ];

    /**
     * @var array<int, string>
     */
    protected $guarded = [
        'secret',
    ];

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'city',
        'age',
        'score',
        'data',
        'active',
        'count',
        'object_data',
        'default',
        'collection_data',
    ];

    /**
     * @return array<int, string>
     */
    public function getListItemsAttribute(string $value): array
    {
        $result = json_decode($value, true);
        assert(is_array($result));

        /** @var array<int, string> $result */
        return $result;
    }

    /**
     * @param  array<int, string>  $value
     */
    public function setListItemsAttribute(array $value): void
    {
        $this->attributes['list_items'] = json_encode($value);
    }

    public function setBirthdayAttribute(string $value): void
    {
        $this->attributes['birthday'] = strtotime($value);
    }

    public function getBirthdayAttribute(int $value): string
    {
        return date('Y-m-d', $value);
    }

    public function getAgeAttribute(mixed $value): int
    {
        assert(is_int($this->attributes['birthday']));
        $date = \DateTime::createFromFormat('U', (string) $this->attributes['birthday']);
        assert($date instanceof \DateTime);

        return $date->diff(new \DateTime('now'))->y;
    }

    public function getTestAttribute(mixed $value): string
    {
        return 'test';
    }
}
