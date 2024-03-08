<?php
declare(strict_types=1);

class Tdmrep_Policy_Form
{
    private Tdmrep_Policy $policy;
    private array $allowed_html;

    public function __construct(Tdmrep_Policy $policy)
    {
        $this->policy = $policy; 
        $this->allowed_html = [
            'button' => [
                'type' => [],
                'id' => [],
                'class' => []
            ],
            'option' => [
                'value' => [],
                'selected' => []
            ],
            'select' => [
                'name' => [],
                'id' => [],
                'required' => []
            ],
            'tr' => [
                'data-%1$s-field' => []
            ],
            'th' => [
                'scope' => [],
                'row' => []
            ],
            'td' => [],
            'label' => [
                'for' => []
            ],
            'input' => [
                'type' => [],
                'name' => [],
                'id' => [],
                'value' => [],
                'required' => []
            ],
        ];
    }

    public function render(): void
    {
        printf(
            '<p>%s <a title="%s" href="https://www.w3.org/community/reports/tdmrep/CG-FINAL-tdmrep-20240202/#sec-tdm-file-example" target="_blank" rel="nofollow">%s</a></p>',
            esc_html__('Need help? Check out the official documentation:', 'tdmrep'),
            esc_html__('TDM Reservation Protocol (TDMRep)', 'tdmrep'),
            esc_html__('TDM Reservation Protocol (TDMRep)', 'tdmrep')
        );

        printf(
            '<form method="post" action="%s" id="tdmrep-policy-form">',
            esc_url(admin_url('admin-post.php'))
        );

        echo '<input type="hidden" name="action" value="tdmrep_save_policy">';
        wp_nonce_field('tdmrep_save_policy');

        if ($this->policy->get_uid() !== null) {
            printf(
                '<input type="hidden" name="policy[uid]" value="%s">',
                esc_attr($this->policy->get_uid())
            );
        }

        echo '<table class="form-table">';
        $this->render_input_field('location', __('Location', 'tdmrep'), (string)$this->policy->get_location(), 'text', 'policy', [], true);
        $this->render_input_field('tdm_reservation', __('TDM Reservation', 'tdmrep'), (string)$this->policy->get_tdm_reservation(), 'select', 'policy', ['0' => '0', '1' => '1'], true, $this->policy->get_tdm_reservation() ?? '1');
        echo '</table>';

        $this->render_button('assigner-add', __('Add Assigner', 'tdmrep'), 'add-button');
        echo '<div id="assigner-fields"></div>';
        $this->render_button('permission-add', __('Add Permission', 'tdmrep'), 'add-button');
        echo '<div id="permission-fields"></div>';

        submit_button(esc_html__('Save Policy', 'tdmrep'));

        echo '</form>';
    }

    private function render_input_field(string $name, string $label, string $value = '', string $type = 'text', string $prefix = '', array $options = [], bool $required = false, string $defaultValue = null): void
    {
        $requiredAttribute = $required ? ' required' : '';
        $requiredClass = $required ? ' class="required"' : '';
        $idPrefix = $prefix !== '' ? "$prefix-" : '';

        if ($type === 'select') {
            $field = '<tr data-%1$s-field="%2$s">
                    <th scope="row"><label for="%3$s"%9$s>%4$s</label></th>
                    <td><select name="%5$s[%6$s]" id="%7$s"%8$s>%10$s</select></td>
                  </tr>';

            $option_tags = '';
            foreach ($options as $option_value => $option_label) {
                $selected = ($option_value == $defaultValue) ? ' selected' : '';
                $option_tags .= sprintf('<option value="%1$s"%2$s>%3$s</option>', esc_attr($option_value), $selected, esc_html($option_label));
            }

            echo wp_kses(sprintf(
                $field,
                esc_attr($prefix),
                esc_attr($name),
                esc_attr($idPrefix . $name),
                esc_html($label),
                esc_attr($prefix),
                esc_attr($name),
                esc_attr($idPrefix . $name),
                esc_attr($requiredAttribute),
                esc_attr($requiredClass),
                $option_tags
            ), $this->allowed_html);
        } else {
            $field = '<tr data-%1$s-field="%2$s">
                    <th scope="row"><label for="%3$s"%10$s>%4$s</label></th>
                    <td><input type="%5$s" name="%6$s[%7$s]" id="%8$s" value="%9$s"%11$s></td>
                  </tr>';

                  echo wp_kses(sprintf(
                    $field,
                    esc_attr($prefix),
                    esc_attr($name),
                    esc_attr($idPrefix . $name),
                    esc_html($label),
                    esc_attr($type),
                    esc_attr($prefix),
                    esc_attr($name),
                    esc_attr($idPrefix . $name),
                    esc_attr($value),
                    esc_attr($requiredClass),
                    esc_attr($requiredAttribute)
                ), $this->allowed_html);
        }
    }
    
    private function render_button(string $id, string $label, string $class = ''): void
    {
        $button = '<button type="button" id="%1$s" class="button %2$s">%3$s</button>';

        echo wp_kses(sprintf(
            $button,
            esc_attr($id),
            esc_attr($class),
            esc_html($label)
        ), $this->allowed_html);
    }
}