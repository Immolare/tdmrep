<?php
declare(strict_types=1);

class Tdmrep_Policy
{
    private const TDM_FILE_NAME = 'tdmrep.json';
    private const WELL_KNOWN_PATH = '/.well-known/';

    private array $context;
    private ?string $uid;
    private string $type;
    private string $profile;
    private ?Tdmrep_Assigner $assigner;
    private array $permissions;
    private string $location;
    private string $tdm_reservation;

    public function __construct(
        ?string $uid = null,
        ?Tdmrep_Assigner $assigner = null,
        ?Tdmrep_Permission $permission = null
    ) {
        $this->context = ['http://www.w3.org/ns/odrl.jsonld', ['tdm' => 'http://www.w3.org/ns/tdmrep#']];
        $this->type = 'Offer';
        $this->profile = 'http://www.w3.org/ns/tdmrep';
        $this->uid = $uid;
        $this->assigner = $assigner;
        $this->permissions = $permission ? [$permission] : [];
        $this->location = '/';
        $this->tdm_reservation = '1';
    }

    public function get_tdm_file_path(): string
    {
        return self::WELL_KNOWN_PATH . self::TDM_FILE_NAME;
    }

    public function set_assigner(Tdmrep_Assigner $assigner): void
    {
        $this->assigner = $assigner;
    }

    public function add_permission(Tdmrep_Permission $permission): void
    {
        $this->permissions[] = $permission;
    }

    public function get_location(): string
    {
        return $this->location;
    }

    public function set_location(string $location): void
    {
        $this->location = $location;
    }

    public function get_tdm_reservation(): string
    {
        return $this->tdm_reservation;
    }

    public function set_tdm_reservation(string $tdm_reservation): void
    {
        $this->tdm_reservation = $tdm_reservation;
    }

    public function get_context(): array
    {
        return $this->context;
    }

    public function set_uid(string $uid): void
    {
        $this->uid = $uid;
    }

    public function get_uid(bool $uniqid_only = false): ?string
    {
        if ($uniqid_only) {
            $parts = explode('/', $this->uid);
            return end($parts);
        }

        return $this->uid;
    }

    public function get_type(): string
    {
        return $this->type;
    }

    public function get_profile(): string
    {
        return $this->profile;
    }

    public function get_assigner(): ?Tdmrep_Assigner
    {
        return $this->assigner;
    }

    public function get_first_permission(): ?Tdmrep_Permission
    {
        return !empty($this->permissions) ? reset($this->permissions) : null;
    }

    public function get_permissions(): array
    {
        return $this->permissions;
    }

    public function to_json_ld(bool $api = false): array
    {
        $uid = $this->get_uid();
        $json_ld = [];
        
        if ($api) {
            $resource_uid = home_url('wp-json/' . Tdmrep_Admin::POLICY_API_PREFIX . 'policies/' . $uid);
            $json_ld['@context'] = $this->get_context();
            $json_ld['@type'] = $this->get_type();
            $json_ld['profile'] = $this->get_profile();
            $json_ld['uid'] = $resource_uid;
        }
        else {
            $json_ld['uid'] = $uid;
            $json_ld['location'] = $this->get_location();
            $json_ld['tdm-reservation'] = $this->get_tdm_reservation();
        }

        if ($assigner = $this->get_assigner()) {
            $json_ld['assigner'] = $assigner->to_json_ld();
        }

        if ($permissions = $this->get_permissions()) {
            $json_ld['permission'] = array_map(fn($permission) => $permission->to_json_ld(), $permissions);
        }

        return $json_ld;
    }

    public static function from_json_ld(array $data): self
    {
        $policy = new self();
        
        $policy->uid = $data['uid'];
        $policy->location = $data['location'];
        $policy->tdm_reservation = $data['tdm-reservation'];
        
        if (isset($data['assigner'])) {
            $policy->assigner = Tdmrep_Assigner::from_json_ld($data['assigner']);
        }

        if (isset($data['permission'])) {
            $policy->permissions = array_map(fn($permission) => Tdmrep_Permission::from_json_ld($permission), $data['permission']);
        }

        return $policy;
    }
}