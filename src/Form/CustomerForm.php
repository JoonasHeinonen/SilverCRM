<?php
    /**
     * @file
     * Contains \Drupal\customer\Form\CustomerForm
     */
    namespace Drupal\customer\Form;

    use Drupal\Core\Database\Database;
    use Drupal\Core\Form\FormBase;
    use Drupal\Core\Form\FormStateInterface;

    class CustomerForm extends FormBase {
        /**
         * (@inheritdoc)
         */
        public function getFormId() {
            return 'customer_customer_form';
        }

        /**
         * (@inheritdoc)
         */
        public function buildForm(array $form, FormStateInterface $form_state) {
            $node = \Drupal::routeMatch()->getParameter('node');
            $nid  = $node->nid->value;
            $form['customer']['customer_name'] = array(
                '#title' => t('Customer Name'),
                '#type' => 'textfield',
                '#size' => 25,
                '#description' => t('Customer\'s name.'),
                '#required' => TRUE,
            );

            $form['customer']['customer_let'] = array(
                '#title' => t('Legal Entity Type'),
                '#type' => 'select',
                '#options' => array(
                    'Tmi.' => $this->t('Tmi.'),
                    'Ay' => $this->t('Ay'),
                    'Ky' => $this->t('Ky'),
                    'Oy' => $this->t('Oy'),
                    'Oyj' => $this->t('Oyj'),
                ),
                '#required' => TRUE,
            );

            $form['customer']['customer_address'] = array(
                '#title' => t('Address'),
                '#type' => 'textfield',
                '#required' => TRUE
            );

            $form['customer']['customer_postal_code'] = array(
                '#title' => t('Postal Code'),
                '#type' => 'textfield',
                '#required' => TRUE
            );

            $form['customer']['customer_first_date_of_coop'] = array(
                '#title' => t('Co-operated since'),
                '#type' => 'date',
                '#default_value' => array(
                    'year' => 2020,
                    'month' => 2,
                    'day' => 15,
                ),
                '#required' => TRUE,
            );

            $form['customer']['customer_coop_continues'] = array(
                '#title' => t('Is still co-operating'),
                '#type' => 'checkbox',
                '#default_value' => TRUE,
                '#required' => TRUE,
            );

            $form['customer']['customer_last_date_of_coop'] = array(
                '#title' => t('Co-operation terminated'),
                '#type' => 'date',
                '#default_value' => array(
                    'year' => 2020,
                    'month' => 2,
                    'day' => 15,
                ),
            );

            $form['customer']['submit'] = array(
                '#type' => 'submit',
                '#value' => t('Submit'),
            );

            $form['nid'] = array(
                '#type' => 'hidden',
                '#value' => $nid
            );

            return $form;
        }

        /**
         * (@inheritdoc)
         */
        public function submitForm(array &$form, FormStateInterface $form_state) {
            drupal_set_message(t('Customer Form is working!'));
        }

    }