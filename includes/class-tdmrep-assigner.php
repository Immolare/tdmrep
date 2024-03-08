<?php
declare(strict_types=1);

class Tdmrep_Assigner
{
    private ?string $uid;
    private ?string $fn;
    private ?string $nickname;
    private ?string $has_email;
    private ?array $has_address;
    private ?string $has_telephone;
    private ?string $has_url;

    public function __construct() {
        $this->has_address = null;
    }

    public function set_fn(string $fn): void
    {
        $this->fn = $fn;
    }

    public function set_nickname(?string $nickname): void
    {
        $this->nickname = $nickname;
    }

    public function set_has_email(?string $has_email): void
    {
        $this->has_email = $has_email;
    }

    public function set_has_address(?array $has_address): void
    {
        $this->has_address = [
            'street' => $has_address['street'] ?? '',
            'postal-code' => $has_address['postal-code'] ?? '',
            'locality' => $has_address['locality'] ?? '',
            'country-name' => $has_address['country-name'] ?? ''
        ];
    }

    public function set_has_telephone(?string $has_telephone): void
    {
        $this->has_telephone = $has_telephone;
    }

    public function set_has_url(?string $has_url): void
    {
        $this->has_url = $has_url;
    }

    public function get_fn(): ?string
    {
        return $this->fn;
    }

    public function get_nickname(): ?string
    {
        return $this->nickname;
    }

    public function get_has_email(): ?string
    {
        return $this->has_email;
    }

    public function get_has_address(): ?array
    {
        return $this->has_address;
    }

    public function get_has_telephone(): ?string
    {
        return $this->has_telephone;
    }

    public function get_has_url(): ?string
    {
        return $this->has_url;
    }

    public function to_array_or_null(): ?array
    {
        $data_to_array = [
            'fn' => $this->get_fn(),
            'nickname' => $this->get_nickname(),
            'has_email' => $this->get_has_email(),
            'has_telephone' => $this->get_has_telephone(),
            'has_url' => $this->get_has_url(),
        ];

        if($this->get_has_address()) {
            $has_address = [
                'has_address_street' => $this->get_has_address()['street'],
                'has_address_postal-code' => $this->get_has_address()['postal-code'],
                'has_address_locality' => $this->get_has_address()['locality'],
                'has_address_country-name' => $this->get_has_address()['country-name']
            ];
        
            if(array_filter($has_address)) {
                return array_merge($has_address, $data_to_array);
            }
        }
        
        return $data_to_array;
    }

    public function to_json_ld(): array
    {
        $json_ld = [];

        $json_ld['uid'] = get_site_url();

        if (!empty($this->get_fn())) {
            $json_ld['vcard:fn'] = $this->get_fn();
        }

        if (!empty($this->get_nickname())) {
            $json_ld['vcard:nickname'] = $this->get_nickname();
        }

        if (!empty($this->get_has_email())) {
            $json_ld['vcard:hasEmail'] = 'mailto:' . $this->get_has_email();
        }

        $has_address = $this->get_has_address();
        $has_address_filtered = array_filter([
            'vcard:street-address' => $has_address['street'] ?? null,
            'vcard:postal-code' => $has_address['postal-code'] ?? null,
            'vcard:locality' => $has_address['locality'] ?? null,
            'vcard:country-name' => $has_address['country-name'] ?? null
        ]);
    
        if (!empty($has_address_filtered)) {
            $json_ld['vcard:hasAddress'] = $has_address_filtered;
        }

        if (!empty($this->get_has_telephone())) {
            $json_ld['vcard:hasTelephone'] = 'tel:' . $this->get_has_telephone();
        }

        if (!empty($this->get_has_url())) {
            $json_ld['vcard:hasURL'] = $this->get_has_url();
        }

        return $json_ld;
    }

    public static function from_json_ld(array $data): self
    {
        $assigner = new self();

        $assigner->set_fn($data['vcard:fn'] ?? null);
        $assigner->set_nickname($data['vcard:nickname'] ?? null);
        $assigner->set_has_email(str_replace('mailto:', '', $data['vcard:hasEmail'] ?? ''));
        $assigner->set_has_telephone(str_replace('tel:', '', $data['vcard:hasTelephone'] ?? ''));
        $assigner->set_has_url($data['vcard:hasURL'] ?? null);

        if (isset($data['vcard:hasAddress'])) {
            $assigner->set_has_address([
                'street' => $data['vcard:hasAddress']['vcard:street-address'] ?? '',
                'postal-code' => $data['vcard:hasAddress']['vcard:postal-code'] ?? '',
                'locality' => $data['vcard:hasAddress']['vcard:locality'] ?? '',
                'country-name' => $data['vcard:hasAddress']['vcard:country-name'] ?? ''
            ]);
        }

        return $assigner;
    }
}