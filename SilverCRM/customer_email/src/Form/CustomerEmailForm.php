<?php
    /**
     * @file
     * Contains \Drupal\customer_email\Form\CustomerEmailForm
     */
    namespace Drupal\customer_email\Form;

    use Drupal\Core\Database\Database;
    use Drupal\Core\Form\FormBase;
    use Drupal\Core\Form\FormStateInterface;
    use Drupal\Core\Entity\EntityInterface;
    use Drupal\Core\Mail\MailManagerInterface;
    use Drupal\Component\Utility\SafeMarkup;
    use Drupal\Component\Utility\Html;

    $customer = 0;

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
                '#options' => array_combine($this->get_customer_contact_email(0), $this->get_customer_contact_email(1)),
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
            $account = $this->currentUser;
        

            $entry = db_insert('customer_email')
                ->fields(array(
                    'customer_email_title' => $form_state->getValue('customer_email_title'),
                    'customer_email_content' => $form_state->getValue('customer_email_content'),
                    'customer_email_contact' => $form_state->getValue('contact_of_company'),
                    'uid' => 0,
                ))
                ->execute();
            
            $this->send_mail(
                $form_state->getValue('customer_email_content'), 
                $form_state->getValue('customer_email_title'),
                $form_state->getValue('contact_of_company')
            );
            
            drupal_set_message(t(
                'Email saved to the system database successfully!'
            ));
        }

        /**
         * Implements hook_mail().
         */
        function customer_email_mail($key, &$message, $params) {
            $options = array(
                'langcode' => $message['langcode'],
            );
            switch ($key) {
                case 'node_insert':
                    $message['from'] = \Drupal::config('system.site')->get('mail');
                    $message['subject'] = t('Your mail subject Here: @title', array('@title' => $params['title']), $options);
                    $message['body'][] = Html::escape($params['message']);
                    break;
            }
        }
  
        function send_mail($message, $label, $contact) {
            $mailManager = \Drupal::service('plugin.manager.mail');
            $module = 'customer_email';
            $key = 'node_insert'; // Replace with Your key
            $params['message'] = $message;
            $params['title'] = $label;
            $to = $contact;
            $langcode = \Drupal::currentUser()->getPreferredLangcode();
            $send = true;
          
            $result = $mailManager->mail($module, $key, $to, $langcode, $params, NULL, $send);
            if ($result['result'] != true) {
                $message = t('There was a problem sending your email with the following parameters:<br />
                    <b>Title:</b> \'@title\'<br />
                    <b>Message:</b> \'@message\'<br />
                    <b>To:</b> @email.', 
                    array('@email' => $to, '@title' => $params['title'], '@message' => $params['message'])
                );
                drupal_set_message($message, 'error');
                \Drupal::logger('mail-log')->error($message);
                return;
            }
          
            $message = t('An email notification has been sent to @email ', array('@email' => $to));
            drupal_set_message($message);
            \Drupal::logger('mail-log')->notice($message);
        }

        public function get_customer_contact_email($value) {
            $query = db_select('customer_contact', 'cc');

            $query
                ->fields('cc', array('contact_of_company', 'contact_email'))
                ->range(0, 50)
                ->orderBy('cc.contact_of_company', 'ASC');
            
            $results = $query->execute();
            
            /**
             * @switch-statement.
             * 
             * 0 represents select's value for sending email.
             * 1 represents select's frontend value.
             */
            switch($value) {
                case 0:
                    foreach($results as $result) {
                        $result = $result->contact_email;
                        $rows[] = $result;
                    }
        
                    if (count($rows) < 1) {
                        $rows[] = null;
                    }
                    break;
                case 1:
                    foreach($results as $result) {
                        $result = '' . $result->contact_of_company . ' - ' . $result->contact_email;
                        $rows[] = $result;
                    }
        
                    if (count($rows) < 1) {
                        $rows[] = 'No customer contacts available.';
                    }
                    break;
                default:
                    break;
            }

            return $rows;
        }
    }
?>