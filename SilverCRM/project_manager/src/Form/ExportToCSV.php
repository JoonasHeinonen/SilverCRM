<?php
    /**
     * @file
     * Contains \Drupal\project_manager\Form\ExportToCSV
     */
    namespace Drupal\project_manager\Form;

    use Drupal\Core\Form\FormBase;
    use Drupal\Core\Form\FormInterface;
    use Drupal\Core\Form\FormStateInterface;
    use Drupal\Core\Session\AccountProxyInterface;

    class ExportToCSV extends FormBase {

        /**
         * (@inheritdoc)
         */
        public function getFormId() {
            return 'project_manager_export_to_csv_form';
        }

        /**
         * (@inheritdoc)
         */
        public function buildForm(array $form, FormStateInterface $form_state) {
            $node = \Drupal::routeMatch()->getParameter('node');
            $nid  = $node->nid->value;

            $form = array();

            $name_cleaned = $this->get_clean_name();
            $csv_filename = $this->get_file_name();

            $form['export_to_csv']['div'] = array(
                '#type' => 'fieldset',
                '#title' => t('<b>Export to CSV</b>'),
                '#required' => TRUE,
                '#prefix' => '<div class="myclass">',
                '#attributes' => array('class' => array('')), 
            );

            $form['export_to_csv']['div']['desc'] = array(
                '#markup' => t('<br/><p>This tool is used in <b>Project Manager Module</b> for CSV-export. Use this <b>feature</b> to export your working hours to a <em><b>CSV-file.</b></em></p>'),
            );

            $form['export_to_csv']['div']['file_name'] = array(
                '#prefix' => t('<p><em><ul><li>' . $csv_filename . '.csv'),
                '#suffix' => $this->t('</li></em></p>'),
            );

            $form['export_to_csv']['div']['download'] = array(
                '#prefix' => $this->t('<a href="'. $csv_filename . '.csv" download="'. $csv_filename . '.csv"><b><em>Click here to download the CSV-file.</em></b>'),
                '#suffix' => $this->t('</a><br/>'),
            );

            $form['export_to_csv']['div']['export'] = array(
                '#type' => 'submit',
                '#value' => $this->t('Export to CSV'),
                '#suffix' => '</div></br>',
            );
        
            return $form;
        }

        /**
         * (@inheritdoc)
         */
        public function submitForm(array &$form, FormStateInterface $form_state) {
            $user = \Drupal::currentUser();
            $user_id = $user->id();

            $name_cleaned = $this->get_clean_name();
            $csv_filename = $this->get_file_name();

            $f_open = fopen($csv_filename . '.csv', 'w');
            $name_cleaned = $this->get_clean_name();

            $result = \Drupal::database()->select('project_working_hours', 'pwh')
                ->fields('pwh', array('uid', 'project', 'work_date', 'start_hours', 'start_minutes', 'end_hours', 'end_minutes', 'work_hours', 'description'))
                ->condition('uid', $user_id)
                ->execute()->fetchAllAssoc('uid');            

            // Initialize row-element.
            $rows = array();
            foreach($result as $row => $content) {
                fputcsv($f_open, $rows[] = array(
                    $content->uid,
                    $content->project, 
                    $content->work_date, 
                    $content->start_hours, 
                    $content->start_minutes, 
                    $content->end_hours, 
                    $content->end_minutes, 
                    $content->work_hours, 
                    $content->description,
                ));
            }
            
            fclose($fp);

            drupal_set_message(t(
                $name_cleaned . ': Exported working hours to <em><b>CSV-file</b></em> successfully!'
            ));
        }

        // Other functions

        public function get_clean_name() {
            $user = \Drupal::currentUser();
            $user_id = $user->id();

            $account = \Drupal\user\Entity\User::load($user_id); // pass your uid
            $name = $account->getUsername();
            $name_cleaned = preg_replace('/[^a-zA-Z0-9\']/', '_', $name);

            return $name_cleaned;
        }

        public function get_file_name() {
            $name_cleaned = $this->get_clean_name();
            $csv_filename = $name_cleaned . '_working_hours';

            return $csv_filename;
        }
    }
?>