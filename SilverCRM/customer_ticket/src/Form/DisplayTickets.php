<?php
    /**
     * @file
     * Contains \Drupal\customer_ticket\Form\DisplayTickets
     */
    namespace Drupal\customer_ticket\Form;

    use Drupal\Core\Database\Database;
    use Drupal\Core\Form\FormBase;
    use Drupal\Core\Form\FormStateInterface;

    /**
     * Class DisplayTickets.
     */
    class DisplayTickets extends FormBase {
        /**
         * (@inheritdoc)
         */
        public function getFormId() {
            return 'customer_customer_ticket_display_tickets';
        }

        /**
         * (@inheritdoc)
         */
        public function buildForm(array $form, FormStateInterface $form_state) {
            $form = array();

            $form['display_tickets']['header'] = array(
                '#type' => 'markup',
                '#prefix' => '<h2>Display all customer tickets',
                '#suffix' => '</h2>'
            );

            $form['display_tickets']['tickets'] = array(
                $this->showTickets()
            );

            $form['display_tickets']['submit'] = array(
                '#type' => 'submit',
                '#value' => t('Submit'),
            );

            return $form;
        }

        /**
         * (@inheritdoc)
         */
        public function submitForm(array &$form, FormStateInterface $form_state) {
            drupal_set_message(t(
                'Ticket saved to the system database successfully!'
            ));
        }

        /**
         * Custom functions
         */

        /**
         * showTickets.
         * 
         * @return string
         *  Return Table format data.
         */
        public function showTickets() {

            $result = \Drupal::database()->select('customer_ticket', 'ct')
                ->fields('ct', array('pid', 'customer_ticket_title', 'customer_ticket_content', 'customer_ticket_contact', 'customer_ticket_priority', 'customer_ticket_solved'))
                ->execute()->fetchAllAssoc('pid');

            $solved = \Drupal::database()->select('customer_ticket', 'ct')
                ->fields('ct', array('pid', 'customer_ticket_solved'))
                ->execute()->fetchAllAssoc('pid');

            $solved_status_text = 'Mark as solved';

            $form['add_btn'] = array(
                '#type' => 'submit',
                '#value' => t($solved_status_text),
            );

            $button = \Drupal::service('renderer')
                ->render($form['add_btn']);

            $rows = array();
            foreach($result as $row => $content) {
                $rows[] = array(
                    'data' => array(
                        $content->customer_ticket_title, 
                        $content->customer_ticket_content, 
                        $content->customer_ticket_contact, 
                        $content->customer_ticket_priority, 
                        $content->customer_ticket_solved, 
                        $button
                    ),
                );
            }

            // Initialize header and output.
            $header = array('Ticket Title', 'Ticket Content', 'Customer Contact', 'Priority Level', 'Ticket Solved', 'Actions');
            $output = array();
            
            $output['tickets'] = array(
                '#theme' => 'table',
                '#class' => 'table-striped',
                '#header' => $header,
                '#rows' => $rows,
            );

            return $output;
        }
    }
?>