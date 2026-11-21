<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OpenApi\Attributes as OAT;

/**
 * @property int $asset_id
 * @property ?string $hostname
 * @property ?string $ip_address
 * @property ?string $mac_address
 * @property ?string $operating_system
 * @property ?string $imei
 * @property ?int $carrier_id
 * @property ?string $phone_number
 * @property ?string $sim_pin
 * @property ?string $sim_puk
 * @property ?string $cpu
 * @property ?string $ram
 * @property ?string $storage
 * @property ?string $specs
 * @property ?Carrier $carrier
 */
#[OAT\Schema(
    schema: 'AssetDevice',
    properties: [
        new OAT\Property(property: 'hostname', description: 'Hostname', type: 'string', example: 'nb-ventas-01'),
        new OAT\Property(property: 'ip_address', description: 'IP address', type: 'string', example: '192.168.1.20'),
        new OAT\Property(property: 'mac_address', description: 'MAC address', type: 'string', example: '00:1A:2B:3C:4D:5E'),
        new OAT\Property(property: 'operating_system', description: 'Operating system', type: 'string', example: 'Windows 11'),
        new OAT\Property(property: 'imei', description: 'IMEI, mobiles and tablets only', type: 'string', example: '356938035643809'),
        new OAT\Property(property: 'carrier_id', description: 'Carrier of the phone line', type: 'integer', example: 1),
        new OAT\Property(property: 'phone_number', description: 'Mobile number of the line', type: 'string', example: '+34600123456'),
        new OAT\Property(property: 'cpu', description: 'CPU', type: 'string', example: 'Intel Core i7-1355U'),
        new OAT\Property(property: 'ram', description: 'RAM', type: 'string', example: '16 GB'),
        new OAT\Property(property: 'storage', description: 'Storage', type: 'string', example: '512 GB SSD'),
        new OAT\Property(property: 'specs', description: 'Other technical specs', type: 'string'),
    ],
    type: 'object',
)]
class AssetDevice extends Model
{
    use HasFactory;

    protected $table = 'asset_device';

    protected $primaryKey = 'asset_id';

    public $incrementing = false;

    protected $fillable = [
        'hostname',
        'ip_address',
        'mac_address',
        'operating_system',
        'imei',
        'carrier_id',
        'phone_number',
        'sim_pin',
        'sim_puk',
        'cpu',
        'ram',
        'storage',
        'specs',
    ];

    /**
     * SIM codes are secrets: encrypted at rest with the app key and never serialized.
     */
    protected $hidden = [
        'sim_pin',
        'sim_puk',
    ];

    protected function casts(): array
    {
        return [
            'sim_pin' => 'encrypted',
            'sim_puk' => 'encrypted',
        ];
    }

    /**
     * @return BelongsTo<Asset, $this>
     */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    /**
     * Deleted carriers keep showing on the devices that already use them.
     *
     * @return BelongsTo<Carrier, $this>
     */
    public function carrier(): BelongsTo
    {
        return $this->belongsTo(Carrier::class)->withTrashed();
    }
}
