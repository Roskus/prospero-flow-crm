<?php

/**
 * @version 1.0.1
 */
declare(strict_types=1);

namespace App\Traits;

use OpenApi\Attributes as OAT;

trait VCard
{
    private static string $EOL = "\r\n";

    // #[OAT\Property(type: 'int', example: 1)]
    protected ?int $id = null;

    protected ?int $company_id = null;

    // #[OAT\Property(description: 'Name of the contact', type: 'string', example: 'John')]
    // protected string $first_name = '';

    // #[OAT\Property(description: 'Lastname of the contact', type: 'string', example: 'Smith')]
    // protected ?string $last_name = null;

    // #[OAT\Property(description: 'Job title', type: 'string', example: 'Smith')]

    // #[OAT\Property(description: 'Phone of the contact', type: 'string', example: '+34645000000')]
    // protected ?string $phone = null;

    // #[OAT\Property(description: 'Email of the contact', type: 'string', format: 'email', example: 'john.smith@company.com')]
    // protected ?string $email = null;

    // #[OAT\Property(description: 'LinkedIn url of the contact', type: 'string', format: 'url', example: 'https://likedin.com/in/contactname')]
    // protected ?string $linkedin = null;

    // #[OAT\Property(description: 'Country ISO code of the contact', type: 'string', example: 'UK')]
    // protected ?string $country = null;

    // #[OAT\Property(description: 'Notes of the contact', type: 'string', example: 'We meet last time at the Coffee Shop inside the hotel.')]
    // protected ?string $notes = null;

    /**
     * RFC6350
     * https://www.rfc-editor.org/rfc/rfc6350
     * https://datatracker.ietf.org/doc/id/draft-ietf-vcarddav-vcardrev-02.html
     * Return User/Contact vCard
     * @return string VCARD 4.0
     */
    public function vCard(): string
    {
        /*
        BEGIN:VCARD
        VERSION:4.0
        N:Gump;Forrest;;Mr.;
        FN:Forrest Gump
        ORG:Sheri Nom Co.
        TITLE:Ultimate Warrior
        PHOTO;MEDIATYPE#image/gif:http://www.sherinnom.com/dir_photos/my_photo.gif
        TEL;TYPE=work,voice:tel:+1-111-555-1212
        TEL;TYPE=home,voice:tel:+1-404-555-1212
        ADR;TYPE=WORK;PREF#1;LABEL#"Normality\nBaytown\, LA 50514\nUnited States of America":;;100 Waters Edge;Baytown;LA;50514;United States of America
        ADR;TYPE=HOME;LABEL#"42 Plantation St.\nBaytown\, LA 30314\nUnited States of America":;;42 Plantation St.;Baytown;LA;30314;United States of America
        EMAIL:sherinnom@example.com
        REV:20080424T195243Z
        END:VCARD
        */
        $card = 'BEGIN:VCARD'.self::$EOL;
        $card .= 'VERSION:4.0'.self::$EOL;
        $card .= 'N:'.$this->last_name.';'.$this->first_name.';;;'.self::$EOL;
        $card .= 'FN:'.$this->first_name.' '.$this->last_name.self::$EOL;

        if (! empty($this->company)) {
            $card .= 'ORG:'.$this->company->name.self::$EOL;
        }

        if (! empty($this->job_title)) {
            $card .= 'TITLE:'.$this->job_title.self::$EOL;
        }
        $card .= 'TEL;TYPE=work,voice:'.$this->phone.self::$EOL;
        $card .= 'EMAIL:'.$this->email.self::$EOL;
        if (! empty($this->company->website)) {
            $card .= 'URL:'.$this->company->website.self::$EOL;
        }
        $card .= 'REV:'.$this->updated_at->format('Y-m-dC').self::$EOL;
        $card .= 'END:VCARD'.self::$EOL;

        return $card;
    }
}
