<?php

declare(strict_types=1);

namespace App\Enums;

enum AssetType: string
{
    case Car = 'car';
    case Van = 'van';
    case Truck = 'truck';
    case Motorcycle = 'motorcycle';
    case Crane = 'crane';
    case Lift = 'lift';
    case Forklift = 'forklift';
    case Excavator = 'excavator';
    case Generator = 'generator';
    case Compressor = 'compressor';
    case OtherMachinery = 'other_machinery';
    case Desktop = 'desktop';
    case Notebook = 'notebook';
    case Server = 'server';
    case Mobile = 'mobile';
    case Tablet = 'tablet';
    case Printer = 'printer';
    case Router = 'router';
    case OtherDevice = 'other_device';

    public function category(): AssetCategory
    {
        return match ($this) {
            self::Car, self::Van, self::Truck, self::Motorcycle, self::Crane,
            self::Lift, self::Forklift, self::Excavator, self::Generator, self::Compressor,
            self::OtherMachinery => AssetCategory::Vehicle,
            default => AssetCategory::Device,
        };
    }

    /**
     * Machinery carries its own detail on top of the vehicle one: capacity, certifications and inspections.
     */
    public function isMachinery(): bool
    {
        return in_array($this, self::machinery(), true);
    }

    /**
     * @return list<self>
     */
    public static function machinery(): array
    {
        return [self::Crane, self::Lift, self::Forklift, self::Excavator, self::Generator, self::Compressor, self::OtherMachinery];
    }

    /**
     * @return list<self>
     */
    public static function forCategory(AssetCategory $category): array
    {
        return array_values(array_filter(self::cases(), fn (self $type): bool => $type->category() === $category));
    }

    /**
     * Types that carry a phone line: IMEI, carrier and mobile number.
     */
    public function hasPhoneLine(): bool
    {
        return in_array($this, [self::Mobile, self::Tablet], true);
    }

    public function requiresImei(): bool
    {
        return $this === self::Mobile;
    }

    /**
     * Prefix of the generated codes, e.g. MOB-00001.
     */
    public function codePrefix(): string
    {
        return match ($this) {
            self::Car => 'CAR',
            self::Van => 'VAN',
            self::Truck => 'TRK',
            self::Motorcycle => 'MOT',
            self::Crane => 'CRN',
            self::Lift => 'LFT',
            self::Forklift => 'FRK',
            self::Excavator => 'EXC',
            self::Generator => 'GEN',
            self::Compressor => 'CMP',
            self::OtherMachinery => 'MAC',
            self::Desktop => 'PC',
            self::Notebook => 'NB',
            self::Server => 'SRV',
            self::Mobile => 'MOB',
            self::Tablet => 'TAB',
            self::Printer => 'PRN',
            self::Router => 'RTR',
            self::OtherDevice => 'DEV',
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Car => __('Car'),
            self::Van => __('Van'),
            self::Truck => __('Truck'),
            self::Motorcycle => __('Motorcycle'),
            self::Crane => __('Crane'),
            self::Lift => __('Lift'),
            self::Forklift => __('Forklift'),
            self::Excavator => __('Excavator'),
            self::Generator => __('Generator'),
            self::Compressor => __('Compressor'),
            self::OtherMachinery => __('Other machinery'),
            self::Desktop => __('Desktop PC'),
            self::Notebook => __('Notebook'),
            self::Server => __('Server'),
            self::Mobile => __('Mobile phone'),
            self::Tablet => __('Tablet'),
            self::Printer => __('Printer'),
            self::Router => __('Router'),
            self::OtherDevice => __('Other device'),
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Car => 'las la-car',
            self::Van => 'las la-shuttle-van',
            self::Truck => 'las la-truck',
            self::Motorcycle => 'las la-motorcycle',
            self::Crane, self::Forklift => 'las la-truck-loading',
            self::Lift => 'las la-dolly-flatbed',
            self::Excavator => 'las la-tractor',
            self::Generator => 'las la-bolt',
            self::Compressor => 'las la-wind',
            self::OtherMachinery => 'las la-cogs',
            self::Desktop => 'las la-desktop',
            self::Notebook => 'las la-laptop',
            self::Server => 'las la-server',
            self::Mobile => 'las la-mobile',
            self::Tablet => 'las la-tablet',
            self::Printer => 'las la-print',
            self::Router => 'las la-wifi',
            self::OtherDevice => 'las la-microchip',
        };
    }
}
