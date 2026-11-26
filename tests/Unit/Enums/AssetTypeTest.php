<?php

declare(strict_types=1);

namespace Tests\Unit\Enums;

use App\Enums\AssetCategory;
use App\Enums\AssetType;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class AssetTypeTest extends TestCase
{
    #[Test]
    public function vehicles_and_machinery_belong_to_the_vehicle_category(): void
    {
        $this->assertSame(
            [
                AssetType::Car, AssetType::Van, AssetType::Truck, AssetType::Motorcycle, AssetType::Crane, AssetType::Lift,
                AssetType::Forklift, AssetType::Excavator, AssetType::Generator, AssetType::Compressor, AssetType::OtherMachinery,
            ],
            AssetType::forCategory(AssetCategory::Vehicle)
        );
    }

    #[Test]
    public function only_machinery_types_of_the_vehicle_category_are_machinery(): void
    {
        $machinery = array_values(array_filter(AssetType::cases(), fn (AssetType $type): bool => $type->isMachinery()));

        $this->assertSame(AssetType::machinery(), $machinery);
        $this->assertSame(
            [AssetType::Crane, AssetType::Lift, AssetType::Forklift, AssetType::Excavator, AssetType::Generator, AssetType::Compressor, AssetType::OtherMachinery],
            $machinery
        );
        foreach ($machinery as $type) {
            $this->assertSame(AssetCategory::Vehicle, $type->category());
        }
        $this->assertFalse(AssetType::Truck->isMachinery());
        $this->assertFalse(AssetType::Server->isMachinery());
    }

    #[Test]
    public function every_type_has_its_own_code_prefix(): void
    {
        $prefixes = array_map(fn (AssetType $type): string => $type->codePrefix(), AssetType::cases());

        $this->assertSame(count($prefixes), count(array_unique($prefixes)));
    }

    #[Test]
    public function devices_belong_to_the_device_category(): void
    {
        $this->assertSame(
            [AssetType::Desktop, AssetType::Notebook, AssetType::Server, AssetType::Mobile, AssetType::Tablet, AssetType::Printer, AssetType::Router, AssetType::OtherDevice],
            AssetType::forCategory(AssetCategory::Device)
        );
    }

    #[Test]
    public function only_mobiles_and_tablets_carry_a_phone_line_and_only_mobiles_require_imei(): void
    {
        $withLine = array_filter(AssetType::cases(), fn (AssetType $type): bool => $type->hasPhoneLine());
        $withImei = array_filter(AssetType::cases(), fn (AssetType $type): bool => $type->requiresImei());

        $this->assertSame([AssetType::Mobile, AssetType::Tablet], array_values($withLine));
        $this->assertSame([AssetType::Mobile], array_values($withImei));
    }
}
