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
            [AssetType::Car, AssetType::Van, AssetType::Truck, AssetType::Motorcycle, AssetType::Crane, AssetType::Lift, AssetType::Forklift, AssetType::OtherMachinery],
            AssetType::forCategory(AssetCategory::Vehicle)
        );
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
