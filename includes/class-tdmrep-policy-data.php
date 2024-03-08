<?php
declare(strict_types=1);

class Tdmrep_Policy_Data
{
    private const OPTION_NAME = 'tdmrep_policies';

    public function __construct()
    {
        // Rien à faire ici pour l'instant
    }

    public function get_policies(): array
    {
        return get_option(self::OPTION_NAME, array());
    }

    public function get_policy(string $policy_uid): ?Tdmrep_Policy
    {
        $policies = $this->get_policies();

        $filtered_policies = array_filter($policies, function($policy) use ($policy_uid) {
            return $policy['uid'] === $policy_uid;
        });

        return !empty($filtered_policies) ? Tdmrep_Policy::from_json_ld(reset($filtered_policies)) : null;
    }

    public function add_policy(Tdmrep_Policy $policy): bool
    {
        $policies = $this->get_policies();

        $uid = wp_generate_uuid4();
        $policy->set_uid($uid);

        $policies[] = $policy->to_json_ld();
        $policies = $this->sort_policies($policies);

        return $this->update_policies($policies);
    }

    public function update_policy(string $policy_uid, Tdmrep_Policy $policy): bool
    {
        $policies = $this->get_policies();
        $index = $this->find_policy_index($policies, $policy_uid);

        if ($index !== null) {
            $policies[$index] = $policy->to_json_ld();
            $policies = $this->sort_policies($policies);
            return $this->update_policies($policies);
        }

        return false;
    }
    
    public function delete_policy(string $policy_uid): bool
    {
        $policies = $this->get_policies();
        $index = $this->find_policy_index($policies, $policy_uid);

        if ($index !== null) {
            unset($policies[$index]);
            return $this->update_policies($policies);
        }

        return false;
    }

    public function delete_all_policies(): bool
    {
        return delete_option(self::OPTION_NAME);
    }

    public function update_all_policies(): bool
    {
        $policies = $this->get_policies();
        foreach ($policies as &$policy) {
            $policy['uid'] = wp_generate_uuid4();
        }
        return update_option(self::OPTION_NAME, $policies);
    }

    private function sort_policies(array $policies): array
    {
        usort($policies, function ($a, $b) {
            return strcmp($a['location'], $b['location']);
        });

        return $policies;
    }

    private function update_policies(array $policies): bool
    {
        return update_option(self::OPTION_NAME, $policies);
    }

    private function find_policy_index(array $policies, string $policy_uid): ?int
    {
        foreach ($policies as $index => $existingPolicy) {
            if ($existingPolicy['uid'] === $policy_uid) {
                return $index;
            }
        }

        return null;
    }
}