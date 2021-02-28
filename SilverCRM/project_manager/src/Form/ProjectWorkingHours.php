<?php
    /**
     * @file
     * Contains \Drupal\project_manager\Form\ProjectWorkingHours
     */
    namespace Drupal\project_manager\Form;

    use Drupal\Core\Database\Database;
    use Drupal\Core\Form\FormBase;
    use Drupal\Core\Form\FormStateInterface;
    use Drupal\Core\Entity\EntityInterface;

    class ProjectWorkingHours extends FormBase {
        /**
         * (@inheritdoc)
         */
        public function getFormId() {
            return 'project_manager_working_hours_form';
        }

         /**
         * (@inheritdoc)
         */
        public function buildForm(array $form, FormStateInterface $form_state) {
            $node = \Drupal::routeMatch()->getParameter('node');
            $nid  = $node->nid->value;
            
            $form = array();

            $form['working_hours']['start_time'] = array(
                '#type' => 'datetime',
                '#title' => t('Work starting time and date.'),
                '#required' => TRUE,
            );

            $form['working_hours']['end_time'] = array(
                '#type' => 'datetime',
                '#title' => t('Work ending time and date.'),
                '#required' => TRUE,
            );

            /*
            $form['working_hours']['project'] = array(
                '#type' => 'select',
                '#title' => t('Contact to the company'),
                '#options' => array_combine($this->get_projects(0), $this->get_projects(1)),
                '#default_value' => 0,
                '#required' => TRUE,
            );
            */

            $form['working_hours']['submit'] = array(
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
                'Working hours saved to the system database successfully!'
            ));
        }
        
        public function get_projects($value) {
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