<?php

declare(strict_types=1);

namespace App\Core\Faker;

use joshtronic\LoremIpsum;

class LoremIpsumFaker implements FakerInterface
{
    /**
     * @var LoremIpsum
     */
    private $loremIpsum;

    /**
     * LoremIpsumFaker constructor.
     * @param LoremIpsum $loremIpsum
     */
    public function __construct(LoremIpsum $loremIpsum)
    {
        $this->loremIpsum = $loremIpsum;
    }

    public function getUserName(): string
    {
        $words = $this->loremIpsum->words(100, false, true);
        $randomIndexes = array_rand($words, 2);
        return ucwords("{$words[$randomIndexes[0]]} {$words[$randomIndexes[1]]}");
    }

    public function getEmail(): string
    {
        $user = strtolower($this->loremIpsum->word()) . random_int(1000, 9999);
        $secondLevelDomain = strtolower($this->loremIpsum->word());
        $firstLevelDomains = ['com', 'net', 'org'];
        $firstLevelDomain = $firstLevelDomains[array_rand($firstLevelDomains, 1)];
        return "{$user}@{$secondLevelDomain}.{$firstLevelDomain}";
    }

    public function getText(): string
    {
        return $this->loremIpsum->words(random_int(10, 50));
    }
}
