<?php
    /**
     * @file
     * Contains \Drupal\customer_contact\Form\CustomerForm
     */
    namespace Drupal\customer_contact\Form;

    use Drupal\Core\Database\Database;
    use Drupal\Core\Form\FormBase;
    use Drupal\Core\Form\FormStateInterface;
    use Drupal\Core\Session\AccountProxyInterface;

    class CustomerContactForm extends FormBase {
        /**
         * The current user.
         *
         * We'll need this service in order to check if the user is logged in.
         *
         * @var \Drupal\Core\Session\AccountProxyInterface
         */
        protected $currentUser;

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

            $form = array();

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
                '#options' => array_combine($this->get_customers(), $this->get_customers()),
                '#default_value' => 0,
                '#required' => TRUE,
            );

            $form['customer_contact']['contact_email'] = array(
                '#type' => 'email',
                '#title' => t('Contact Email'),
                '#size' => 25,
                '#description' => t('Email of the contact.'),
                '#required' => TRUE,
                '#maxlength' => 60
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
            $account = $this->currentUser;
            
            $entry = db_insert('customer_contact')
                ->fields(array(
                    'contact_first_name' => $form_state->getValue('contact_first_name'),
                    'contact_last_name' => $form_state->getValue('contact_last_name'),
                    'contact_of_company' => $form_state->getValue('contact_of_company'),
                    'contact_email' => $form_state->getValue('contact_email'),
                    'contact_phonenumber' => $form_state->getValue('contact_phonenumber'),
                    'uid' => 0,
                ))
                ->execute();

            drupal_set_message(t(
                'Customer contact added to the database successfully!'
            ));
        }
        
        /**
         * Custom functions.
         */
        public function get_customers_sql_query() {
            $options = array();
            $sql_results = db_query("
                SELECT title FROM node_field_data
                WHERE type = 'customer'
                ORDER BY 'title' DESC
            ");
            
            foreach($results as $result) {
                $options[] = $result->title;
            }

            return $options;
        }

        public function get_customers() {
            $query = db_select('node_field_data', 'nfd');

            $query
                ->fields('nfd', array('title'))
                ->condition('type', 'customer')
                ->range(0, 50)
                ->orderBy('nfd.title', 'ASC');

            $results = $query->execute();

            foreach($results as $result) {
                $rows[] = $result->title;
            }

            return $rows;
        }
    }
?>