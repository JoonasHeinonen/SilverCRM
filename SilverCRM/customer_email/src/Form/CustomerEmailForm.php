<?php
    /**
     * @file
     * Contains \Drupal\customer_email\Form\CustomerEmailForm
     */
    namespace Drupal\customer_email\Form;

    use Drupal\Core\Database\Database;
    use Drupal\Core\Form\FormBase;
    use Drupal\Core\Form\FormStateInterface;

    class CustomerEmailForm extends FormBase {
        /**
         * (@inheritdoc)
         */
        public function getFormId() {
            return 'customer_customer_email_form';
        }

         /**
         * (@inheritdoc)
         */
        public function buildForm(array $form, FormStateInterface $form_state) {
            $node = \Drupal::routeMatch()->getParameter('node');
            $nid  = $node->nid->value;

            $form = array();

            $form['customer_email_form']['customer_email_title'] = array(
                '#type' => 'textfield',
                '#title' => t('Email title'),
                '#size' => 25,
                '#description' => t('Title of the email message.'),
                '#required' => TRUE,
            );

            $form['customer_email_form']['customer_email_content'] = array(
                '#type' => 'textarea',
                '#title' => t('Email message'),
                '#description' => t('Message of the email message.'),
                '#required' => TRUE,
            );

            $form['customer_email_form']['contact_of_company'] = array(
                '#type' => 'select',
                '#title' => t('Contact to the company'),
                '#options' => array_combine($this->get_customer_contact_email(), $this->get_customer_contact_email()),
                '#default_value' => 0,
                '#required' => TRUE,
            );

            $form['customer_email_form']['submit'] = array(
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
            drupal_set_message(t(
                'Sent email to the contact successfully!'
            ));
        }

        public function get_customer_contact_email() {
            $query = db_select('customer_contact', 'cc');

            $query
                ->fields('cc', array('contact_email', 'contact_of_company'))
                ->range(0, 50)
                ->orderBy('cc.contact_of_company', 'ASC');

            $results = $query->execute();

            foreach($results as $result) {
                $result = $result->contact_email;
                $rows[] = $result;
            }

            return $rows;
        }
    }
?>