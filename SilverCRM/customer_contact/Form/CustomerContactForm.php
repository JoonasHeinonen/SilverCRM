<?php
    /**
     * @file
     * Contains \Drupal\customer_contact\Form\CustomerForm
     */
    namespace Drupal\customer_contact\Form;

    use Drupal\Core\Database\Database;
    use Drupal\Core\Form\FormBase;
    use Drupal\Core\Form\FormStateInterface;

    class CustomerContactForm extends FormBase {
        /**
         * (@inheritdoc)
         */
        public function getFormId() {
            return 'customer_customer_contact_form';
        }

        /**
         * (@inheritdoc)
         */
        public function buildForm(array $form, FormStateInterface $form_state) {
            $node = \Drupal::routeMatch()->getParameter('node');
            $nid  = $node->nid->value;
            $form['customer_contact']['contact_first_name'] = array(
                '#type' => 'textfield',
                '#title' => t('Contact First Name'),
                '#size' => 25,
                '#description' => t('Customer contact\'s first name.'),
                '#required' => TRUE,
            );

            $form['customer_contact']['contact_last_name'] = array(
                '#type' => 'textfield',
                '#title' => t('Contact Last Name'),
                '#size' => 25,
                '#description' => t('Customer contact\'s last name.'),
                '#required' => TRUE,
            );

            $form['customer_contact']['contact_of_company'] = array(
                '#type' => 'select',
                '#title' => t('Contact to the company'),
                '#options' => array(
                    'Paskaa Romua Oy' => $this->t('Paskaa Romua Oy'),
                ),
                '#required' => TRUE,
            );

            $form['customer_contact']['contact_phonenumber'] = array(
                '#type' => 'textfield',
                '#attributes' => array(
                    ' type' => 'number',
                ),
                '#title' => t('Contact Phonenumber'),
                '#size' => 25,
                '#description' => t('Phonenumber of the contact.'),
                '#required' => TRUE,
                '#maxlength' => 60
            );

            $form['customer_contact']['submit'] = array(
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
            drupal_set_message(t('Customer Contact Form is working!'));
        }

        /**
         * Custom functions.
         */
        private function get_customers() {
            $query = db_select();
        }
    }